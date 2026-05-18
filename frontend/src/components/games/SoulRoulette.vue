<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';
import { store } from '../../utils/store';

const spinning = ref(false);
const rotation = ref(0);
const result = ref(null);
const showResult = ref(false);

import { apiBase } from '@/utils/api';

const prizes = [
    { label: '1 PT', value: 1, type: 'points', color: '#1e40af' },
    { label: '2 PTS', value: 2, type: 'points', color: '#60a5fa' },
    { label: '3 PTS', value: 3, type: 'points', color: '#8b5cf6' },
    { label: '4 PTS', value: 4, type: 'points', color: '#ec4899' },
    { label: '5 PTS', value: 5, type: 'points', color: '#ef4444' },
    { label: '1 PT', value: 1, type: 'points', color: '#1e40af' },
    { label: '2 PTS', value: 2, type: 'points', color: '#60a5fa' },
    { label: '3 PTS', value: 3, type: 'points', color: '#8b5cf6' },
    { label: 'CUPÓN 5%', value: 5, type: 'coupon', color: '#facc15' },
    { label: '4 PTS', value: 4, type: 'points', color: '#ec4899' },
    { label: '1 PT', value: 1, type: 'points', color: '#1e40af' },
    { label: '2 PTS', value: 2, type: 'points', color: '#60a5fa' },
    { label: '3 PTS', value: 3, type: 'points', color: '#8b5cf6' },
    { label: '5 PTS', value: 5, type: 'points', color: '#ef4444' },
    { label: '1 PT', value: 1, type: 'points', color: '#1e40af' },
    { label: '2 PTS', value: 2, type: 'points', color: '#60a5fa' },
    { label: '4 PTS', value: 4, type: 'points', color: '#ec4899' },
    { label: '1 PT', value: 1, type: 'points', color: '#1e40af' },
    { label: '2 PTS', value: 2, type: 'points', color: '#60a5fa' },
    { label: '3 PTS', value: 3, type: 'points', color: '#8b5cf6' },
    { label: '1 PT', value: 1, type: 'points', color: '#1e40af' }
];

const segmentAngle = 360 / prizes.length; // ~17.14° for 21 segments

const isAdmin = computed(() => store.user?.is_admin || store.user?.is_super_admin || false);
const isSuperAdmin = isAdmin;
const lastGameAt = computed(() => store.user?.last_roulette_at ? new Date(store.user.last_roulette_at) : null);
const cooldownActive = computed(() => {
    if (isAdmin.value || !lastGameAt.value) return false;
    const now = new Date();
    const diff = now.getTime() - lastGameAt.value.getTime();
    return diff < 24 * 60 * 60 * 1000; // 24H COOLDOWN
});

const spin = async () => {
    if (spinning.value || cooldownActive.value) return;
    
    spinning.value = true;
    showResult.value = false;
    
    // Pick a random prize
    const prizeIndex = Math.floor(Math.random() * prizes.length);
    const selectedPrize = prizes[prizeIndex];
    
    // Calculate rotation: 12 extra turns for maximum tension
    const extraTurns = Math.floor(Math.random() * 5) + 12; 
    
    const currentNormalize = rotation.value % 360;
    const targetAngleOnCircle = 360 - (prizeIndex * segmentAngle + segmentAngle / 2);
    const moveNeeded = (360 - currentNormalize + targetAngleOnCircle) % 360;
    const finalIncrement = (360 * extraTurns) + moveNeeded;
    
    rotation.value += finalIncrement;
    
    setTimeout(async () => {
        spinning.value = false;
        result.value = selectedPrize;
        showResult.value = true;
        
        // Guardar en backend si es puntos
        if (selectedPrize.type === 'points' && selectedPrize.value !== 0) {
            try {
                const token = localStorage.getItem('token');
                const res = await axios.post(`${apiBase}/loyalty/add-points`, {
                    points: selectedPrize.value,
                    game_id: 'soul_roulette'
                }, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                
                store.updatePoints(selectedPrize.value);
                if (res.data.last_game_at) {
                    store.updateLastGameAt(res.data.last_game_at, 'soul_roulette');
                }
            } catch (err) {
                console.error("Error saving roulette points:", err);
            }
        }

        // Guardar en backend si es cupón
        if (selectedPrize.type === 'coupon') {
            try {
                const token = localStorage.getItem('token');
                const res = await axios.post(`${apiBase}/loyalty/claim-coupon`, {}, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                store.notify(res.data.message, "success");
                
                // También activamos el cooldown para la ruleta al ganar cupón
                const resPoints = await axios.post(`${apiBase}/loyalty/add-points`, {
                    points: 0,
                    game_id: 'soul_roulette'
                }, {
                    headers: { Authorization: `Bearer ${token}` }
                });
                if (resPoints.data.last_game_at) {
                    store.updateLastGameAt(resPoints.data.last_game_at, 'soul_roulette');
                }
            } catch (err) {
                console.error("Error claiming coupon:", err);
                store.notify("Error al guardar el cupón", "error");
            }
        }
    }, 5000);
};

const resetWheel = () => {
    // We don't reset rotation.value to 0 because it causes a slow reverse spin.
    // Instead, we just hide the result and let the next spin accumulate.
    showResult.value = false;
};
</script>

<template>
  <div class="flex flex-col items-center w-full max-w-md mx-auto text-white">
    <header class="text-center mb-8">
        <h3 class="text-2xl font-black uppercase italic tracking-tighter">
            Soul <span class="text-neon-purple font-black">Roulette</span>
        </h3>
        <p class="text-[10px] text-gray-500 uppercase font-black tracking-widest">Gira y gana premios al instante</p>
    </header>

    <div class="relative w-[400px] h-[400px] md:w-[520px] md:h-[520px] mb-12 flex items-center justify-center">
        <!-- Arrow Pointer -->
        <div class="absolute -top-6 left-1/2 -translate-x-1/2 z-20 text-neon-purple text-5xl filter drop-shadow-neon-purple">
            <i class="fas fa-caret-down"></i>
        </div>

        <!-- The Wheel -->
        <div class="w-full h-full rounded-full border-8 border-gray-800 shadow-3xl shadow-neon-blue/20 relative transition-transform duration-[5000ms] cubic-bezier-out overflow-hidden"
             :style="{ transform: `rotate(${rotation}deg)` }">
            
            <div v-for="(prize, index) in prizes" :key="index" 
                 class="absolute top-0 left-1/2 w-1/2 h-1/2 origin-bottom-left"
                 :style="{ 
                    transform: `rotate(${index * segmentAngle}deg) skewY(${-(90 - segmentAngle)}deg)`,
                    backgroundColor: prize.color 
                 }">
            </div>

            <!-- Prize Labels -->
            <div v-for="(prize, index) in prizes" :key="'label-' + index" 
                 class="absolute top-0 left-1/2 w-1/2 h-1/2 origin-bottom text-center pt-10 pointer-events-none"
                 :style="{ transform: `translateX(-50%) rotate(${index * segmentAngle + segmentAngle / 2}deg)` }">
                <span class="text-[9px] md:text-[11px] font-black uppercase tracking-tighter whitespace-nowrap block" style="transform: rotate(180deg)">{{ prize.label }}</span>
            </div>
        </div>

        <!-- Center Hub -->
        <div class="absolute inset-0 m-auto w-16 h-16 bg-gamer-dark border-4 border-gray-800 rounded-full flex items-center justify-center z-10 shadow-2xl">
            <div class="w-8 h-8 rounded-full bg-neon-purple animate-pulse"></div>
        </div>
    </div>

    <!-- Controls / Result -->
    <div v-if="!showResult" class="text-center w-full">
        <div v-if="cooldownActive && !isSuperAdmin" class="bg-red-500/10 border border-red-500/20 px-6 py-4 rounded-3xl mb-4">
             <p class="text-[10px] uppercase font-black text-red-400 mb-1">Cooldown Activo</p>
             <p class="text-sm font-bold text-white">Vuelve en unas horas para girar de nuevo.</p>
        </div>
        
        <button 
            @click="spin" 
            :disabled="spinning || (cooldownActive && !isSuperAdmin)"
            class="w-full py-4 rounded-2xl font-black uppercase tracking-widest text-sm transition shadow-neon-blue/30 relative z-10 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
            :class="spinning || (cooldownActive && !isSuperAdmin) ? 'bg-gray-800 text-gray-500' : 'bg-neon-purple text-[#fdfafb] hover:scale-105 active:scale-95'"
        >
            {{ spinning ? 'Girando...' : 'Girar Ruleta Gratis' }}
        </button>
    </div>

    <transition name="fade">
        <div v-if="showResult" class="flex flex-col items-center bg-gamer-card border border-gray-800 p-8 rounded-3xl shadow-2xl w-full text-center">
            <div class="w-16 h-16 rounded-full flex items-center justify-center text-3xl mb-4" :style="{ backgroundColor: result.color + '33', color: result.color }">
                <i :class="result.type === 'coupon' ? 'fas fa-ticket-alt' : (result.value > 0 ? 'fas fa-gift' : (result.value < 0 ? 'fas fa-skull-crossbones' : 'fas fa-frown'))"></i>
            </div>
            <h4 class="text-xl font-black uppercase italic mb-2">
                {{ result.value > 0 ? '¡Felicidades!' : (result.value < 0 ? '¡Qué mala suerte!' : 'Casi...') }}
            </h4>
            <p class="text-gray-400 text-sm mb-6">Resultado: <span class="text-white font-bold">{{ result.label }}</span></p>
            
            <button @click="showResult = false" class="bg-gray-800 hover:bg-white hover:text-gamer-dark px-8 py-3 rounded-xl font-bold text-xs uppercase tracking-widest transition">
                Entendido
            </button>
        </div>
    </transition>
  </div>
</template>

<style scoped>
.cubic-bezier-out {
    transition-timing-function: cubic-bezier(0.15, 0, 0.15, 1);
}

.shadow-neon-blue\/20 {
    box-shadow: 0 0 30px rgba(0, 240, 255, 0.15);
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease, transform 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(20px); }
</style>
