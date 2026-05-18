<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LoadingState from '@/components/LoadingState.vue';


import { apiBase } from '@/utils/api';

const raffles = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchRaffles = async () => {
    try {
        const token = localStorage.getItem('token');
        const headers = token ? { Authorization: `Bearer ${token}` } : {};
        const response = await axios.get(`${apiBase}/raffles`, { headers });
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

    <LoadingState v-if="loading" />
    <div v-else-if="error" class="text-center py-16 bg-red-500/10 border border-red-500/20 rounded-3xl">
        <p class="text-red-400 font-bold mb-4">{{ error }}</p>
        <button @click="fetchRaffles" class="bg-red-500 text-white px-6 py-2 rounded-xl text-xs font-black uppercase hover:bg-white hover:text-red-600 transition">Reintentar Conexión</button>
    </div>
    
    <div v-else-if="raffles.length === 0" class="text-center py-24 bg-gamer-card border border-gray-800 rounded-3xl text-gray-400">
        <p class="text-sm">No hay sorteos activos en este momento. ¡Vuelve pronto!</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="raffle in raffles" :key="raffle.id" class="group bg-gamer-card border border-gray-800/80 rounded-2xl overflow-hidden hover:border-neon-purple/30 hover:shadow-2xl hover:shadow-neon-purple/5 transition duration-500 flex flex-col h-full relative">
            
            <!-- Promo Item Info image if exists -->
            <div class="h-48 bg-black/10 flex items-center justify-center overflow-hidden relative border-b border-gray-800/50">
                <img v-if="raffle.image_url" :src="raffle.image_url" :alt="raffle.title" loading="lazy" class="w-full h-full object-contain group-hover:scale-105 transition duration-700" :class="{'blur-2xl scale-125': raffle.is_censored && (!store.user || !store.user.show_censored_content)}">
                
                <div v-if="raffle.is_censored && (!store.user || !store.user.show_censored_content)" class="absolute inset-0 z-30 flex items-center justify-center bg-black/30 backdrop-blur-sm">
                    <span class="bg-red-600/30 border border-red-500/50 text-red-500 text-[10px] font-black px-4 py-1 rounded-full uppercase tracking-widest shadow-lg shadow-red-600/30">Censurado</span>
                </div>
                <div v-else-if="!raffle.image_url" class="flex flex-col items-center text-gray-600 text-xs">
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
                        <span class="text-neon-cyan font-bold">{{raffle.ticket_price}}€</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Tickets Vendidos:</span>
                        <span class="text-white">{{ raffle.total_entries }} <span v-if="raffle.max_entries">/ {{ raffle.max_entries }}</span></span>
                    </div>
                    <div class="flex justify-between text-xs font-bold pt-2 border-t border-gray-800/50 mt-2">
                        <span class="text-gray-500 uppercase text-[9px]">Probabilidad:</span>
                        <span class="text-neon-cyan">{{ raffle.user_chance ?? 0 }}%</span>
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
