<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import LoadingState from '@/components/LoadingState.vue';
import { useRouter } from 'vue-router';
import { store } from '@/utils/store';

const router = useRouter();


import { apiBase } from '@/utils/api';
const auctions = ref([]);
const loading = ref(true);
const error = ref(null);

const loadAuctions = async () => {
    try {
        const response = await axios.get(`${apiBase}/auctions`);
        auctions.value = response.data.activeAuctions?.data || [];
    } catch (err) {
        error.value = "Error al cargar las subastas.";
    } finally {
        loading.value = false;
    }
};

let pollInterval = null;

onMounted(() => {
    if (!localStorage.getItem('token')) {
        router.push('/register');
        return;
    }
    loadAuctions();
    pollInterval = setInterval(loadAuctions, 5000); // 5s auto-refresh
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});

const calculateTimeRemaining = (endTime) => {
    const end = new Date(endTime).getTime();
    const now = new Date().getTime();
    const distance = end - now;

    if (distance < 0) return "Finalizada";

    const hours = Math.floor(distance / (1000 * 60 * 60));
    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    return `${hours}h ${minutes}m`;
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 text-white">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-black uppercase italic tracking-tighter border-l-4 border-neon-blue pl-4">Subastas <span class="text-neon-blue">Activas</span></h1>
        <router-link to="/" class="text-neon-blue hover:underline text-sm font-bold flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Volver a Inicio
        </router-link>
    </div>

    <LoadingState v-if="loading" />
    <div v-else-if="error" class="text-center text-red-500 bg-red-950/20 border border-red-900 p-4 rounded-xl mb-8">{{ error }}</div>
    <div v-else-if="auctions.length === 0" class="text-center text-gray-400 py-12 bg-gamer-card border border-gray-800 rounded-2xl shadow-xl flex flex-col items-center gap-2">
        <svg class="w-12 h-12 text-gray-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        No hay subastas activas en este momento.
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="auction in auctions" :key="auction.id" class="bg-gamer-card rounded-2xl border border-gray-800 shadow-2xl overflow-hidden hover:border-neon-blue/50 transition duration-300 flex flex-col h-full group">
            <div class="h-48 bg-gray-900 flex items-center justify-center relative border-b border-gray-800/80 overflow-hidden">
                <!-- Efecto Zoom en Hover -->
                <div class="absolute inset-0 bg-gradient-to-t from-gray-950 to-transparent z-10 opacity-70"></div>
                
                <div class="relative w-full h-full flex items-center justify-center overflow-hidden">
                    <img v-if="auction.image_url" :src="auction.image_url" loading="lazy" class="max-h-[400px] w-auto object-contain rounded-xl transition duration-500" :class="{'blur-3xl scale-125': auction.is_censored && (!store.user || !store.user.show_censored_content)}" alt="Producto">
                    <img v-else-if="auction.image" :src="`/storage/${auction.image}`" loading="lazy" class="max-h-[400px] w-auto object-contain rounded-xl transition duration-500" :class="{'blur-3xl scale-125': auction.is_censored && (!store.user || !store.user.show_censored_content)}" alt="Producto">
                    <span v-else class="text-gray-600 text-xl font-bold">Sin Imagen</span>
                    
                    <div v-if="auction.is_censored && (!store.user || !store.user.show_censored_content)" class="absolute inset-0 flex flex-col items-center justify-center bg-black/20 backdrop-blur-sm rounded-xl">
                        <i class="fas fa-eye-slash text-red-500 text-4xl mb-4 opacity-80"></i>
                        <span class="bg-red-600 text-white text-xs font-black px-6 py-2 rounded-full uppercase tracking-widest shadow-xl shadow-red-600/40">Contenido Sensible</span>
                    </div>
                </div>
                
                <!-- Badge Tiempo -->
                <div class="absolute top-4 right-4 bg-neon-red/90 text-white font-black px-3 py-1.5 rounded-xl text-xs flex items-center gap-1 z-20 shadow-lg shadow-neon-red/20 border border-neon-red/30">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ calculateTimeRemaining(auction.auction_end_time) }}</span>
                </div>
            </div>
            
            <div class="p-6 flex flex-col flex-grow">
                <span v-if="auction.category" class="text-xs font-black text-neon-purple uppercase tracking-wider mb-2 block">
                    {{ auction.category.name }}
                </span>
                <h3 class="font-bold text-lg text-white mb-2 line-clamp-1 group-hover:text-neon-blue transition" :title="auction.name">{{ auction.name }}</h3>
                
                <div class="mt-4 bg-gray-950/50 border border-gray-800/80 rounded-xl p-3 mb-4">
                    <p class="text-[11px] text-gray-500 uppercase tracking-wider">Puja Actual</p>
                    <p class="text-2xl font-black text-white italic">{{parseFloat(auction.price).toFixed(2)}}€</p>
                </div>
                
                <!-- Spacer pushes button to bottom -->
                <div class="mt-auto">
                    <router-link :to="`/auctions/${auction.id}`" class="block w-full text-center bg-neon-blue text-gamer-dark py-2.5 rounded-xl font-black hover:bg-white hover:shadow-neon-blue transition duration-300 uppercase text-xs tracking-wider shadow-md">
                        Ver y Pujar
                    </router-link>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>
