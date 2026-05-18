<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const ranking = ref([]);
const loading = ref(true);

import { apiBase } from '@/utils/api';

const fetchRanking = async () => {
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get(`${apiBase}/loyalty/ranking`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        ranking.value = res.data.ranking;
    } catch (err) {
        console.error("Error fetching ranking:", err);
    } finally {
        loading.value = false;
    }
};

const getRankColor = (index) => {
    if (index === 0) return 'text-yellow-400'; // Gold
    if (index === 1) return 'text-gray-300';   // Silver
    if (index === 2) return 'text-orange-400'; // Bronze
    return 'text-gray-500';
};

const getRankIcon = (index) => {
    if (index === 0) return 'fa-crown';
    if (index === 1) return 'fa-medal';
    if (index === 2) return 'fa-medal';
    return 'fa-circle';
};

onMounted(() => {
    fetchRanking();
});
</script>

<template>
  <div class="bg-gamer-card border border-gray-800 rounded-3xl p-8 shadow-2xl relative overflow-hidden">
    <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
        <i class="fas fa-trophy text-8xl text-neon-blue"></i>
    </div>

    <header class="mb-8 border-b border-gray-800 pb-6">
        <h3 class="text-xl font-black uppercase italic tracking-tighter">
            Ranking <span class="text-neon-blue font-black">Global</span>
        </h3>
        <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest mt-1">Los Top 10 Guardianes de Soul</p>
    </header>

    <div v-if="loading" class="py-10 flex flex-col items-center">
        <div class="w-8 h-8 border-2 border-neon-blue border-t-transparent rounded-full animate-spin mb-4"></div>
    </div>

    <div v-else class="space-y-3">
        <div v-for="(user, index) in ranking" :key="index" 
             class="flex items-center gap-4 bg-black/40 p-3 rounded-2xl border border-gray-900 group hover:border-neon-blue/30 transition-all duration-300">
            
            <div class="w-8 flex justify-center items-center">
                <i :class="['fas', getRankIcon(index), getRankColor(index), 'text-lg']"></i>
            </div>

            <div class="w-10 h-10 rounded-full border border-gray-800 overflow-hidden bg-gray-900 flex-shrink-0">
                <img v-if="user.avatar_url" :src="user.avatar_url" loading="lazy" class="w-full h-full object-cover">
                <div v-else class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-600 text-xs">
                    <i class="fas fa-user"></i>
                </div>
            </div>

            <div class="flex-grow">
                <span class="text-xs font-bold uppercase tracking-tight block group-hover:text-neon-blue transition-colors">{{ user.name }}</span>
                <span v-if="index === 0" class="text-[9px] text-yellow-400/70 font-black uppercase tracking-widest italic">Campeón Actual</span>
            </div>

            <div class="text-right">
                <span class="text-lg font-black text-white italic leading-none block">{{ user.points }}</span>
                <span class="text-[8px] text-gray-600 uppercase font-black tracking-widest">PTS</span>
            </div>
        </div>
        
        <p v-if="ranking.length === 0" class="text-center text-gray-600 text-xs py-10 uppercase font-black italic">Aún no hay competitores...</p>
    </div>
  </div>
</template>
