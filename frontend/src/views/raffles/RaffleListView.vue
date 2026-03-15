<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';

const raffles = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchRaffles = async () => {
    try {
        const response = await axios.get(`${apiBase}/raffles`);
        raffles.value = response.data.raffles || [];
    } catch (err) {
        error.value = "Error al cargar la lista de sorteos.";
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchRaffles();
});
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    <div class="mb-8 border-b border-gray-800 pb-4">
        <h1 class="text-3xl font-black uppercase tracking-tight text-neon-purple">Sorteos Exclusivos</h1>
        <p class="text-gray-400 text-xs mt-1">Participa y gana ediciones de colección, consolas y merchandising único.</p>
    </div>

    <div v-if="loading" class="text-center py-16 text-gray-500">Cargando sorteos...</div>
    <div v-else-if="error" class="text-center py-16 text-red-400 font-bold bg-red-500/10 border border-red-500/20 rounded-xl">{{ error }}</div>
    
    <div v-else-if="raffles.length === 0" class="text-center py-24 bg-gamer-card border border-gray-800 rounded-3xl text-gray-400">
        <p class="text-sm">No hay sorteos activos en este momento. ¡Vuelve pronto!</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="raffle in raffles" :key="raffle.id" class="group bg-gamer-card border border-gray-800/80 rounded-2xl overflow-hidden hover:border-neon-purple/30 hover:shadow-2xl hover:shadow-neon-purple/5 transition duration-500 flex flex-col h-full relative">
            
            <!-- Promo Item Info image if exists -->
            <div class="h-48 bg-black/10 flex items-center justify-center overflow-hidden relative border-b border-gray-800/50">
                <img v-if="raffle.product?.image_url" :src="raffle.product.image_url" :alt="raffle.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                <div v-else class="flex flex-col items-center text-gray-600 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 opacity-20 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Sin imagen de producto</span>
                </div>

                <span class="absolute top-3 left-3 bg-neon-purple/20 border border-neon-purple/50 text-neon-purple text-[10px] font-black uppercase px-2 py-0.5 rounded-md">LIVE</span>
            </div>

            <div class="p-5 flex flex-col flex-grow">
                <h3 class="font-bold text-base text-gray-200 line-clamp-1 mb-2 group-hover:text-neon-purple transition">{{ raffle.title }}</h3>
                <p class="text-xs text-gray-400 line-clamp-2 mb-4 flex-grow">{{ raffle.description }}</p>

                <!-- Status details -->
                <div class="space-y-2 mb-4 border-t border-gray-800/50 pt-3">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Precio Ticket:</span>
                        <span class="text-neon-green font-bold">${{ raffle.ticket_price }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Tickets Vendidos:</span>
                        <span class="text-white">{{ raffle.total_entries }} <span v-if="raffle.max_entries">/ {{ raffle.max_entries }}</span></span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Tiempo restante:</span>
                        <span class="text-neon-blue">{{ raffle.time_left }}</span>
                    </div>
                </div>

                <!-- Action Button -->
                <router-link :to="`/raffles/${raffle.id}`" class="w-full bg-gradient-to-r from-neon-purple/80 to-neon-blue/80 hover:from-neon-purple hover:to-neon-blue text-white py-2.5 rounded-xl font-black text-xs uppercase tracking-wider text-center transition duration-300 shadow-neon-purple/10">
                    Participar Ahora
                </router-link>
            </div>
        </div>
    </div>
  </div>
</template>
