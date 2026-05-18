<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import LoadingState from '../../components/LoadingState.vue';
import { store } from '../../utils/store';

const route = useRoute();
const router = useRouter();
import { apiBase } from '@/utils/api';

const raffle = ref(null);
const loading = ref(true);
const error = ref(null);
const quantity = ref(1);
const submittionLoading = ref(false);

const isAuthenticated = computed(() => !!localStorage.getItem('token'));

const fetchRaffle = async () => {
    loading.value = true;
    error.value = null;
    try {
        const token = localStorage.getItem('token');
        const headers = token ? { Authorization: `Bearer ${token}` } : {};
        
        const response = await axios.get(`${apiBase}/raffles/${route.params.id}`, { headers });
        raffle.value = response.data.raffle;
    } catch (err) {
        error.value = "Sorteo no encontrado o error al cargar.";
    } finally {
        loading.value = false;
    }
};

const buyTickets = async () => {
    if (!isAuthenticated.value) {
        store.notify("Debes iniciar sesión para comprar boletos.", 'error');
        router.push('/register');
        return;
    }

    submittionLoading.value = true;
    try {
        const token = localStorage.getItem('token');
        await axios.post(`${apiBase}/raffles/${route.params.id}/enter`, {
            quantity: quantity.value
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });

        store.notify("¡Has comprado tus boletos con éxito!");
        fetchRaffle(); // Reload stats
    } catch (err) {
        const msg = err.response?.data?.message || "Hubo un error al comprar los boletos.";
        store.notify(msg, 'error');
    } finally {
        submittionLoading.value = false;
    }
};

const nextChance = computed(() => {
    if (!raffle.value) return 0;
    const currentEntries = raffle.value.user_entries || 0;
    const totalEntries = raffle.value.total_entries || 0;
    const nextTotal = totalEntries + quantity.value;
    const nextUser = currentEntries + quantity.value;
    return ((nextUser / nextTotal) * 100).toFixed(2);
});

onMounted(() => {
    fetchRaffle();
});
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    <LoadingState v-if="loading" />
    <div v-else-if="error" class="text-center py-16 bg-red-500/10 border border-red-500/20 rounded-3xl">
        <p class="text-red-400 font-bold mb-4">{{ error }}</p>
        <button @click="fetchRaffle" class="bg-red-500 text-white px-6 py-2 rounded-xl text-xs font-black uppercase hover:bg-white hover:text-red-600 transition">Reintentar Conexión</button>
    </div>
    
    <div v-else-if="raffle" class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
        
        <!-- Left: Image frame layout -->
        <div class="space-y-4">
            <div class="aspect-square bg-gamer-card border border-gray-800 rounded-3xl overflow-hidden shadow-2xl flex items-center justify-center relative group">
                <img v-if="raffle.product?.image_url" :src="raffle.product.image_url" :alt="raffle.title" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" :class="{'blur-3xl scale-125': (raffle.is_censored || raffle.product?.is_censored) && (!store.user || !store.user.show_censored_content)}">
                
                <div v-if="(raffle.is_censored || raffle.product?.is_censored) && (!store.user || !store.user.show_censored_content)" class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-black/40 backdrop-blur-md">
                    <i class="fas fa-eye-slash text-red-500 text-5xl mb-4"></i>
                    <span class="bg-red-600 text-white text-[10px] font-black px-6 py-2 rounded-full uppercase tracking-widest shadow-xl shadow-red-600/40">Contenido Sensible</span>
                </div>
                <div v-else class="flex flex-col items-center text-gray-600 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 009.5 3H9a2 2 0 00-2 2v1h2m3 2h1a2 2 0 012 2v1h2m-5-3a3 3 0 01-3 3H7m5-3a3 3 0 00-3 3H5a2 2 0 00-2 2v5a2 2 0 002 2h2m10-7h2a2 2 0 012 2v5a2 2 0 01-2 2h-2"/></svg>
                    <span>Sin imagen de producto</span>
                </div>
                
                <span v-if="raffle.is_active" class="absolute top-4 left-4 bg-neon-purple/20 border border-neon-purple/50 text-neon-purple text-xs font-black uppercase px-2.5 py-1 rounded-md shadow-neon-purple/30">SORTEO EN VIVO</span>
            </div>
        </div>

        <!-- Right: Detail interaction column -->
        <div class="flex flex-col">
            <div class="mb-6">
                <span class="text-xs font-bold text-neon-cyan uppercase tracking-widest">Sorteo Especial de Colección</span>
                <h1 class="text-3xl md:text-5xl font-black mt-2 mb-4 tracking-tight leading-tight text-white">{{ raffle.title }}</h1>
                
                <p class="text-gray-400 text-sm md:text-base leading-relaxed mb-6">{{ raffle.description }}</p>
                
                <div v-if="raffle.product" class="p-3 bg-black/20 border border-gray-800 rounded-xl inline-flex items-center gap-2 mb-4">
                    <span class="text-xs text-gray-500">Producto asociado:</span>
                    <span class="text-xs text-neon-blue font-bold">{{ raffle.product.name }}</span>
                </div>
            </div>

            <!-- Ticket Pricing Summary frame -->
            <div class="bg-gamer-card border border-gray-800 rounded-2xl p-6 mb-8 shadow-xl shadow-neon-purple/5">
                <div class="flex justify-between items-baseline mb-4 border-b border-gray-800 pb-4">
                    <span class="text-xs font-bold text-gray-400">PRECIO POR TICKET</span>
                    <p class="text-3xl font-black text-neon-cyan">{{raffle.ticket_price}}€</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 text-xs border-b border-gray-800 pb-4 text-gray-400">
                    <div>
                        <span class="block text-gray-600 mb-1">Tickets Vendidos:</span>
                        <span class="text-white font-black">{{ raffle.total_entries }} <span v-if="raffle.max_entries">/ {{ raffle.max_entries }}</span></span>
                    </div>
                    <div>
                        <span class="block text-gray-600 mb-1">Cierra en:</span>
                        <span class="text-neon-blue font-bold">{{ raffle.time_left }}</span>
                    </div>
                </div>

                <!-- Personal user analytics stats context -->
                <div v-if="isAuthenticated" class="bg-gradient-to-br from-neon-purple/20 via-black/40 to-black/20 border border-neon-purple/40 rounded-2xl p-5 mb-8 shadow-lg shadow-neon-purple/10">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="h-10 w-10 rounded-full bg-neon-purple/20 flex items-center justify-center text-neon-purple">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-white uppercase tracking-wider">Tu Probabilidad Actual</h4>
                            <p class="text-[10px] text-gray-500 uppercase">Basado en tus {{ raffle.user_entries || 0 }} tickets</p>
                        </div>
                        <div class="ml-auto text-3xl font-black text-neon-cyan italic">{{ raffle.user_chance || 0 }}%</div>
                    </div>

                    <div class="flex justify-between items-center text-xs pt-3 border-t border-gray-800/50">
                        <div class="flex flex-col">
                            <span class="text-gray-500 uppercase text-[9px] font-bold">Inversión Total</span>
                            <span class="text-neon-cyan font-black text-sm">{{ ((raffle.user_entries || 0) * raffle.ticket_price).toFixed(2) }}€</span>
                        </div>
                        <div class="text-right">
                             <router-link to="/profile" class="text-neon-blue hover:underline font-bold text-[10px] uppercase">Ver en mi perfil</router-link>
                        </div>
                    </div>
                </div>

                <!-- Buy controls if active -->
                <div v-if="raffle.is_active" class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-gray-400 uppercase">Cantidad:</span>
                        <div class="flex items-center bg-gray-900 border border-gray-800 rounded-xl px-2 py-1">
                            <button @click="quantity > 1 ? quantity-- : null" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white">-</button>
                            <span class="w-8 text-center text-sm font-bold">{{ quantity }}</span>
                            <button @click="quantity++" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white">+</button>
                        </div>
                        <span class="text-xs text-gray-500 ml-auto">Total: <span class="text-neon-cyan font-bold">{{raffle.ticket_price * quantity}}€</span></span>
                    </div>
                    
                    <div v-if="isAuthenticated && raffle.is_active" class="text-[10px] text-gray-500 italic bg-black/20 p-2 rounded-lg border border-gray-800/50">
                        <span class="text-neon-blue font-bold">Preview:</span> Si compras {{ quantity }} más, tu probabilidad subirá al <span class="text-white font-black">{{ nextChance }}%</span>
                    </div>

                    <button @click="buyTickets" :disabled="submittionLoading" class="w-full bg-gradient-to-r from-neon-purple to-neon-blue hover:from-neon-purple/90 hover:to-neon-blue/90 text-white py-3 rounded-xl font-black text-sm uppercase tracking-wider transition duration-300 shadow-neon-purple/20 flex items-center justify-center gap-2 disabled:opacity-50">
                        <span v-if="submittionLoading">Procesando...</span>
                        <span v-else class="flex items-center gap-2">
                             Adquirir Tickets
                        </span>
                    </button>
                </div>
                <div v-else class="text-center py-4 bg-neon-purple/20 border border-neon-purple/40 rounded-xl font-bold shadow-lg shadow-neon-purple/10">
                    <p class="text-xs text-gray-400 mb-1">SORTEO FINALIZADO</p>
                    <p v-if="raffle.winner" class="text-base text-white">Ganador: <span class="text-neon-purple font-black">{{ raffle.winner.name }}</span> 🎉</p>
                    <p v-else-if="raffle.status === 'cancelled'" class="text-base text-red-500">Sorteo Cancelado</p>
                    <p v-else class="text-sm text-gray-500">Esperando Elegir Ganador...</p>
                </div>

            </div>

            <!-- Rules checklist frame -->
            <div class="border border-gray-800 rounded-2xl p-6 bg-gamer-card/30">
                <h4 class="text-xs font-black text-gray-300 uppercase mb-3">Términos y Condiciones:</h4>
                <ul class="space-y-1.5 text-xs text-gray-500">
                    <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-purple rounded-full"></div> El sorteo se realiza automáticamente en la fecha indicada.</li>
                    <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-purple rounded-full"></div> Puedes comprar múltiples tickets para aumentar tus probabilidades.</li>
                    <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-purple rounded-full"></div> Transacción no reembolsable una vez emitidos los boletos.</li>
                </ul>
            </div>
        </div>
    </div>
  </div>
</template>
