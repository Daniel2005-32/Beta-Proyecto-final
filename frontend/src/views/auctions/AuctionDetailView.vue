<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import LoadingState from '../../components/LoadingState.vue';
import { store } from '../../utils/store';

const route = useRoute();
const router = useRouter();
import { apiBase } from '@/utils/api';
const product = ref(null);
const loading = ref(true);
const error = ref(null);
const bidAmount = ref('');
const bidError = ref('');
const bidSuccess = ref('');
const timeRemaining = ref('');

let timer = null;

const loadAuction = async () => {
    try {
        const res = await axios.get(`${apiBase}/auctions/${route.params.id}`);
        product.value = res.data.product;
        const minIncrement = Math.ceil(parseFloat(product.value.price) * 0.10);
        bidAmount.value = (Math.ceil(parseFloat(product.value.price)) + (minIncrement > 0 ? minIncrement : 1)).toString();
        updateTimeRemaining();
    } catch (err) {
        error.value = err.response?.data?.error || "Error al cargar la subasta";
    } finally {
        loading.value = false;
    }
};

const currentUser = ref(null);
const showExtendForm = ref(false);
const extendHours = ref('24');
const showReduceForm = ref(false);
const reduceHours = ref('1');
const checkUser = async () => {

    const token = localStorage.getItem('token');
    if (token) {
        try {
            const res = await axios.get(`${apiBase}/me`, { headers: { Authorization: `Bearer ${token}` } });
            currentUser.value = res.data.user;
        } catch (err) {}
    }
};

const extendAuction = async () => {
    if (!extendHours.value) return;
    try {
        const token = localStorage.getItem('token');
        await axios.post(`${apiBase}/auctions/${product.value.id}/extend`, { hours: extendHours.value }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        store.notify("Subasta extendida correctamente.");
        showExtendForm.value = false;
        loadAuction();
    } catch (err) { store.notify("Error al extender.", 'error'); }
};

const reduceAuction = async () => {
    if (!reduceHours.value) return;
    try {
        const token = localStorage.getItem('token');
        const res = await axios.post(`${apiBase}/auctions/${product.value.id}/reduce`, { hours: reduceHours.value }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        store.notify("Subasta reducida correctamente.");
        showReduceForm.value = false;
        loadAuction();
    } catch (err) { store.notify(err.response?.data?.error || "Error al reducir.", 'error'); }
};


const forceEndAuction = async () => {
    store.confirm("Finalizar Subasta", "¿Seguro que quieres forzar el fin de esta subasta?", async () => {
        try {
            await axios.post(`${apiBase}/auctions/${product.value.id}/force-end`);
            store.notify("Subasta finalizada.");
            loadAuction();
        } catch (err) { store.notify("Error al finalizar.", 'error'); }
    });
};

const updateTimeRemaining = () => {
    if (!product.value || !product.value.auction_end_time) return;
    
    const end = new Date(product.value.auction_end_time).getTime();
    const now = new Date().getTime();
    const distance = end - now;

    if (distance < 0) {
        timeRemaining.value = "Subasta Finalizada";
        clearInterval(timer);
        return;
    }

    const hours = Math.floor(distance / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    timeRemaining.value = `${hours}h ${minutes}m ${seconds}s`;
};

onMounted(() => {
    const token = localStorage.getItem('token');
    if (!token) {
        router.push({ path: '/register', query: { message: 'Primero debes registrarte para entrar a una subasta.' } });
        return;
    }
    checkUser();
    loadAuction();
    timer = setInterval(updateTimeRemaining, 1000);
    
    // Escuchar pujas en tiempo real (WebSockets)
    if (window.Echo) {
        window.Echo.channel('auction.' + route.params.id)
            .listen('AuctionBidPlaced', (e) => {
                if (e.product) {
                    product.value = e.product;
                    const minIncrement = Math.ceil(parseFloat(e.product.price) * 0.10);
                    bidAmount.value = (Math.ceil(parseFloat(e.product.price)) + (minIncrement > 0 ? minIncrement : 1)).toString();
                }
            });
    }
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
    if (window.Echo) {
        window.Echo.leave('auction.' + route.params.id);
    }
});

const placeBid = async () => {
    bidError.value = '';
    bidSuccess.value = '';
    
    const token = localStorage.getItem('token');
    if (!token) {
        router.push({ path: '/register', query: { message: 'Primero debes registrarte para pujar en una subasta.' } });
        return;
    }

    try {
        const res = await axios.post(`${apiBase}/auctions/${product.value.id}/bid`, {
            amount: parseFloat(bidAmount.value)
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        bidSuccess.value = res.data.message;
        product.value = res.data.product; 
        const minIncrement = Math.ceil(parseFloat(product.value.price) * 0.10);
        bidAmount.value = (Math.ceil(parseFloat(product.value.price)) + (minIncrement > 0 ? minIncrement : 1)).toString();
    } catch (err) {
        bidError.value = err.response?.data?.error || "Error al realizar la puja";
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-5xl text-white">
    <div class="mb-6">
        <router-link to="/auctions" class="text-neon-blue hover:underline flex items-center gap-1 text-sm font-bold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver a Subastas
        </router-link>
    </div>

    <LoadingState v-if="loading" />
    <div v-else-if="error" class="text-center py-12 text-red-500 bg-red-950/20 border border-red-900 rounded-xl">{{ error }}</div>
    
    <div v-else-if="product" class="bg-gamer-card rounded-2xl border border-gray-800 shadow-2xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left Column: Image -->
        <div class="md:w-1/2 bg-gray-900 flex items-center justify-center p-4 min-h-[300px] border-r border-gray-800">
             <div class="relative w-full h-full flex items-center justify-center overflow-hidden">
                <img v-if="product.image_url" :src="product.image_url" loading="lazy" class="max-h-[400px] w-auto object-contain rounded-xl transition duration-500" :class="{'blur-3xl scale-125': product.is_censored && (!store.user || !store.user.show_censored_content)}" alt="Producto">
                <img v-else-if="product.image" :src="`/storage/${product.image}`" loading="lazy" class="max-h-[400px] w-auto object-contain rounded-xl transition duration-500" :class="{'blur-3xl scale-125': product.is_censored && (!store.user || !store.user.show_censored_content)}" alt="Producto">
                <span v-else class="text-gray-600 text-xl font-bold">Sin Imagen</span>
                
                <div v-if="product.is_censored && (!store.user || !store.user.show_censored_content)" class="absolute inset-0 flex flex-col items-center justify-center bg-black/20 backdrop-blur-sm rounded-xl">
                    <i class="fas fa-eye-slash text-red-500 text-4xl mb-4 opacity-80"></i>
                    <span class="bg-red-600 text-white text-xs font-black px-6 py-2 rounded-full uppercase tracking-widest shadow-xl shadow-red-600/40">Contenido Sensible</span>
                </div>
             </div>
        </div>


        <!-- Right Column: Details & Bid Form -->
        <div class="md:w-1/2 p-8">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span v-if="product.category" class="text-xs font-black text-neon-purple uppercase tracking-wider">{{ product.category.name }}</span>
                    <h1 class="text-3xl font-black uppercase italic tracking-tighter text-white mt-1">{{ product.name }}</h1>
                </div>
                <div class="bg-neon-red/10 border border-neon-red/30 text-neon-red px-4 py-2 rounded-xl font-mono font-bold text-base text-center min-w-[140px] shadow-neon-red/10">
                    {{ timeRemaining }}
                </div>
            </div>

            <p class="text-gray-400 text-sm mb-8 leading-relaxed">{{ product.description }}</p>

            <div class="bg-gray-950/80 border border-gray-800 rounded-xl p-6 mb-6 shadow-inner">
                <p class="text-sm text-gray-500 mb-1">Puja Actual</p>
                <p class="text-4xl font-black text-white mb-2">{{parseFloat(product.price).toFixed(2)}}€</p>
                <p v-if="product.auction_winner" class="text-sm text-gray-400">
                    Mejor postor: <span class="font-bold text-white block md:inline">{{ product.auction_winner.name }}</span>
                </p>
                <p v-else class="text-sm text-gray-500 italic">No hay pujas todavía. ¡Sé el primero!</p>
            </div>

            <div v-if="bidSuccess" class="bg-green-900/50 border border-green-500 text-white p-3 rounded-lg text-sm mb-4">{{ bidSuccess }}</div>
            <div v-if="bidError" class="bg-red-900/50 border border-red-500 text-white p-3 rounded-lg text-sm mb-4">{{ bidError }}</div>

            <form v-if="timeRemaining !== 'Subasta Finalizada'" @submit.prevent="placeBid" class="mt-4">
                <label class="block text-gray-400 font-bold mb-2 text-xs uppercase tracking-wider">Tu Puja ($)</label>
                <div class="flex gap-3">
                    <input v-model="bidAmount" type="number" step="1" required min="1" class="flex-grow bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-lg font-mono text-white focus:outline-none focus:border-neon-blue transition">
                    <button type="submit" class="bg-neon-blue text-gamer-dark font-black px-6 py-2 rounded-xl hover:bg-white hover:shadow-neon-blue transition duration-300 uppercase text-sm">
                        Pujar
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">Debe ser mayor a la puja actual.</p>
            </form>
             <div v-else class="text-center p-4 bg-gray-900 border border-gray-800 text-gray-400 rounded-xl font-bold mt-4">
                La subasta ha finalizado.
            </div>

            <!-- Admin Controls -->
            <div v-if="currentUser && currentUser.is_admin" class="mt-8 bg-gray-950 border border-gray-800 rounded-xl p-5 shadow-inner">
                <h4 class="text-neon-blue font-black uppercase text-xs mb-3 tracking-wider flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Panel de Administración
                </h4>
                <div class="flex flex-wrap gap-2">
                    <button @click="showExtendForm = !showExtendForm; showReduceForm = false" class="bg-gray-900 hover:bg-gray-800 text-gray-300 px-3 py-1.5 rounded-lg font-bold text-xs transition border border-gray-800">
                        + Extender Tiempo
                    </button>
                    <button @click="showReduceForm = !showReduceForm; showExtendForm = false" class="bg-gray-900 hover:bg-gray-800 text-gray-300 px-3 py-1.5 rounded-lg font-bold text-xs transition border border-gray-800">
                        - Reducir Tiempo
                    </button>
                    <div v-if="showExtendForm" class="w-full mt-3 p-3 bg-gray-900/40 border border-gray-800 rounded-xl flex items-center gap-2">
                        <label class="text-[10px] uppercase text-gray-500">Horas:</label>
                        <input type="number" v-model="extendHours" class="w-20 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-xs text-white focus:outline-none">
                        <button @click="extendAuction" class="bg-neon-blue text-gamer-dark font-black text-xs px-3 py-1.5 rounded-lg shadow-neon-blue/20 hover:scale-105 transition">Aplicar</button>
                    </div>
                    <div v-if="showReduceForm" class="w-full mt-3 p-3 bg-gray-900/40 border border-gray-800 rounded-xl flex items-center gap-2">
                        <label class="text-[10px] uppercase text-gray-500">Horas:</label>
                        <input type="number" v-model="reduceHours" class="w-20 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-xs text-white focus:outline-none">
                        <button @click="reduceAuction" class="bg-neon-red text-white font-black text-xs px-3 py-1.5 rounded-lg shadow-neon-red/20 hover:scale-105 transition">Aplicar</button>
                    </div>

                    <button @click="forceEndAuction" class="bg-neon-red/10 border border-neon-red/30 text-neon-red px-3 py-1.5 rounded-lg font-bold text-xs hover:bg-neon-red hover:text-white transition">
                        Forzar Fin
                    </button>
                    <button @click="loadAuction" class="bg-gray-900 text-gray-300 px-3 py-1.5 rounded-lg font-bold text-xs hover:bg-gray-800 transition border border-gray-800">
                        Refrescar
                    </button>

                </div>
            </div>

        </div>
    </div>
  </div>
</template>
