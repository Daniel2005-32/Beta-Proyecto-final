<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';
const auctions = ref([]);
const loading = ref(true);
const error = ref(null);

onMounted(async () => {
    try {
        const response = await axios.get(`${apiBase}/auctions`);
        // The API returns activeAuctions with pagination data
        auctions.value = response.data.activeAuctions?.data || [];
    } catch (err) {
        error.value = "Error al cargar las subastas.";
    } finally {
        loading.value = false;
    }
});

const calculateTimeRemaining = (endTime) => {
    const end = new Date(endTime).getTime();
    const now = new Date().getTime();
    const distance = end - now;

    if (distance < 0) return "Finalizada";

    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    return `${hours}h ${minutes}m`;
};
</script>

<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Subastas Activas</h1>
        <router-link to="/" class="text-emerald-600 hover:underline">Volver a Inicio</router-link>
    </div>

    <div v-if="loading" class="text-center text-gray-500 py-8">Cargando subastas...</div>
    <div v-else-if="error" class="text-center text-red-500 py-8">{{ error }}</div>
    <div v-else-if="auctions.length === 0" class="text-center text-gray-500 py-8 bg-gray-50 rounded shadow">
        No hay subastas activas en este momento.
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="auction in auctions" :key="auction.id" class="border rounded-lg shadow-lg bg-white overflow-hidden hover:shadow-xl transition">
            <div class="h-48 bg-gray-200 flex items-center justify-center relative">
                <span class="text-gray-400">Sin Imagen</span>
                <div class="absolute top-2 right-2 bg-red-600 text-white font-bold px-3 py-1 rounded-full text-sm flex items-center">
                    ⏳ {{ calculateTimeRemaining(auction.auction_end_time) }}
                </div>
            </div>
            <div class="p-6">
                <span v-if="auction.category" class="text-xs font-bold text-indigo-600 uppercase tracking-wider mb-2 block">
                    {{ auction.category.name }}
                </span>
                <h3 class="font-bold text-xl text-gray-800 mb-2 line-clamp-1" :title="auction.name">{{ auction.name }}</h3>
                <div class="flex justify-between items-center mt-4">
                    <div>
                        <p class="text-sm text-gray-500">Puja Actual</p>
                        <p class="text-2xl font-black text-emerald-600">${{ parseFloat(auction.price).toFixed(2) }}</p>
                    </div>
                </div>
                <router-link :to="`/auctions/${auction.id}`" class="mt-6 block w-full text-center bg-gray-900 text-white py-2 rounded font-bold hover:bg-emerald-600 transition cursor-pointer">
                    Ver y Pujar
                </router-link>
            </div>
        </div>
    </div>
  </div>
</template>
