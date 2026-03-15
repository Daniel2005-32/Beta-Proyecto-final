<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
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
        bidAmount.value = (parseFloat(product.value.price) + 0.01).toFixed(2);
        updateTimeRemaining();
    } catch (err) {
        error.value = err.response?.data?.error || "Error al cargar la subasta";
    } finally {
        loading.value = false;
    }
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

    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((distance % (1000 * 60)) / 1000);
    
    timeRemaining.value = `${hours}h ${minutes}m ${seconds}s`;
};

onMounted(() => {
    loadAuction();
    timer = setInterval(updateTimeRemaining, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});

const placeBid = async () => {
    bidError.value = '';
    bidSuccess.value = '';
    
    const token = localStorage.getItem('token');
    if (!token) {
        router.push('/login');
        return;
    }

    try {
        const res = await axios.post(`${apiBase}/auctions/${product.value.id}/bid`, {
            amount: parseFloat(bidAmount.value)
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        bidSuccess.value = res.data.message;
        product.value = res.data.product; // Update product with new bid data
        bidAmount.value = (parseFloat(product.value.price) + 0.01).toFixed(2);
    } catch (err) {
        bidError.value = err.response?.data?.error || "Error al realizar la puja";
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="mb-6">
        <router-link to="/auctions" class="text-emerald-600 hover:underline">← Volver a Subastas</router-link>
    </div>

    <div v-if="loading" class="text-center py-12 text-gray-500">Cargando detalles...</div>
    <div v-else-if="error" class="text-center py-12 text-red-500 bg-red-50 rounded">{{ error }}</div>
    
    <div v-else-if="product" class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col md:flex-row">
        
        <!-- Left Column: Image placeholder -->
        <div class="md:w-1/2 bg-gray-100 flex items-center justify-center p-12 min-h-[300px]">
             <span class="text-gray-400 text-xl">Sin Imagen</span>
        </div>

        <!-- Right Column: Details & Bid Form -->
        <div class="md:w-1/2 p-8">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <span v-if="product.category" class="text-sm font-bold text-indigo-600 uppercase tracking-wider">{{ product.category.name }}</span>
                    <h1 class="text-3xl font-bold text-gray-800 mt-1">{{ product.name }}</h1>
                </div>
                <div class="bg-red-100 text-red-800 px-4 py-2 rounded-lg font-mono font-bold text-lg text-center min-w-[140px]">
                    {{ timeRemaining }}
                </div>
            </div>

            <p class="text-gray-600 mb-8">{{ product.description }}</p>

            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-6">
                <p class="text-sm text-gray-500 mb-1">Puja Actual</p>
                <p class="text-4xl font-black text-emerald-600 mb-2">${{ parseFloat(product.price).toFixed(2) }}</p>
                <p v-if="product.auction_winner" class="text-sm text-gray-600">
                    Mejor postor: <span class="font-bold block md:inline">{{ product.auction_winner.name }}</span>
                </p>
                <p v-else class="text-sm text-gray-600 italic">No hay pujas todavía. ¡Sé el primero!</p>
            </div>

            <div v-if="bidSuccess" class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ bidSuccess }}</div>
            <div v-if="bidError" class="bg-red-100 text-red-800 p-3 rounded mb-4">{{ bidError }}</div>

            <form v-if="timeRemaining !== 'Subasta Finalizada'" @submit.prevent="placeBid" class="mt-4">
                <label class="block text-gray-700 font-bold mb-2">Tu Puja ($)</label>
                <div class="flex gap-4">
                    <input v-model="bidAmount" type="number" step="0.01" required min="0.01" class="flex-grow border rounded-lg px-4 py-3 text-lg font-mono">
                    <button type="submit" class="bg-gray-900 text-white font-bold px-8 py-3 rounded-lg hover:bg-emerald-600 transition text-lg">
                        Pujar
                    </button>
                </div>
                <p class="text-xs text-gray-500 mt-2">Debe ser mayor a la puja actual.</p>
            </form>
            <div v-else class="text-center p-4 bg-gray-200 text-gray-700 rounded font-bold mt-4">
                La subasta ha finalizado.
            </div>

        </div>
    </div>
  </div>
</template>
