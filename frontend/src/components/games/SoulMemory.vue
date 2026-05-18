<script setup>
import { ref, onMounted, computed, defineEmits } from 'vue';
import axios from 'axios';
import { store } from '../../utils/store';

const emit = defineEmits(['points']);


const symbols = ref([]);
const allProductsCached = ref([]); // Cache for all products
const cards = ref([]);
const flippedCards = ref([]);
const matchedPairs = ref(0);
const moves = ref(0);
const gameActive = ref(false);
const gameFinished = ref(false);
const showSuccessModal = ref(false);
const earnedPoints = ref(0);
const loading = ref(true);

import { apiBase } from '@/utils/api';

const fetchProducts = async () => {
    try {
        const res = await axios.get(`${apiBase}/products`);
        allProductsCached.value = res.data.products?.data || res.data.products || [];
        pickNewSymbols();
    } catch (err) {
        console.error("Error fetching products for game:", err);
    } finally {
        loading.value = false;
    }
};

const pickNewSymbols = () => {
    // Pick 8 random products from cache
    if (allProductsCached.value.length > 0) {
        const shuffled = [...allProductsCached.value].sort(() => 0.5 - Math.random());
        symbols.value = shuffled.slice(0, 8).map(p => ({
            id: p.id,
            image: p.image_url || p.image,
            name: p.name,
            is_censored: p.is_censored
        }));
    }
    
    if (symbols.value.length < 8) {
        // Fallback to generic symbols if not enough products
        const fallbacks = ['fa-star', 'fa-heart', 'fa-bolt', 'fa-cloud', 'fa-moon', 'fa-sun', 'fa-ghost', 'fa-dragon'];
        while (symbols.value.length < 8) {
            const icon = fallbacks.pop();
            symbols.value.push({ id: Math.random(), image: null, icon, name: 'Item' });
        }
    }
    
    // Start the actual board setup
    startBoard();
};

const initGame = () => {
    // Clear previous state
    cards.value = [];
    flippedCards.value = [];
    matchedPairs.value = 0;
    moves.value = 0;
    gameActive.value = false;
    gameFinished.value = false;
    showSuccessModal.value = false;
    
    if (allProductsCached.value.length === 0) {
        fetchProducts();
    } else {
        pickNewSymbols();
    }
};

const startBoard = () => {
    const doubleSymbols = [...symbols.value, ...symbols.value];
    const shuffled = doubleSymbols.sort(() => Math.random() - 0.5);
    
    cards.value = shuffled.map((symbol, index) => ({
        uniqueId: index,
        ...symbol,
        isFlipped: false,
        isMatched: false
    }));
    
    gameActive.value = true;
};

const flipCard = (card) => {
    if (!gameActive.value || card.isFlipped || card.isMatched || flippedCards.value.length === 2) return;
    
    card.isFlipped = true;
    flippedCards.value.push(card);
    
    if (flippedCards.value.length === 2) {
        moves.value++;
        checkMatch();
    }
};

const checkMatch = () => {
    const [card1, card2] = flippedCards.value;
    
    if (card1.id === card2.id && card1.uniqueId !== card2.uniqueId) {
        card1.isMatched = true;
        card2.isMatched = true;
        matchedPairs.value++;
        flippedCards.value = [];
        
        if (matchedPairs.value === symbols.value.length) {
            finishGame();
        }
    } else {
        setTimeout(() => {
            card1.isFlipped = false;
            card2.isFlipped = false;
            flippedCards.value = [];
        }, 800);
    }
};

const isAdmin = computed(() => store.user?.is_admin || store.user?.is_super_admin || false);
const isSuperAdmin = isAdmin; // Alias for backward compatibility if needed in templates
const lastGameAt = computed(() => store.user?.last_memory_at ? new Date(store.user.last_memory_at) : null);
const cooldownActive = computed(() => {
    if (isAdmin.value || !lastGameAt.value) return false;
    const now = new Date();
    const diff = now.getTime() - lastGameAt.value.getTime();
    return diff < 24 * 60 * 60 * 1000;
});

const finishGame = async () => {
    gameActive.value = false;
    gameFinished.value = true;
    
    // NUEVA LÓGICA DE PUNTUACIÓN SIN PÉRDIDAS
    if (moves.value <= 10) {
        earnedPoints.value = 5;
    } else if (moves.value <= 13) {
        earnedPoints.value = 4;
    } else if (moves.value <= 16) {
        earnedPoints.value = 3;
    } else if (moves.value <= 18) {
        earnedPoints.value = 2;
    } else {
        earnedPoints.value = 1;
    }
    
    try {
        const token = localStorage.getItem('token');
        if (token) {
            const res = await axios.post(`${apiBase}/loyalty/add-points`, {
                points: earnedPoints.value,
                game_id: 'soul_memory'
            }, {
                headers: { Authorization: `Bearer ${token}` }
            });
            
            // Sync with store
            emit('points', earnedPoints.value);
            if (res.data.last_game_at) {
                store.updateLastGameAt(res.data.last_game_at, 'soul_memory');
            }
            
            if (earnedPoints.value >= 0) {
                store.notify(`¡Ganas ${earnedPoints.value} puntos por tu victoria!`, 'success');
            } else {
                store.notify(`Has perdido ${Math.abs(earnedPoints.value)} puntos por exceso de movimientos.`, 'error');
            }
        }
    } catch (err) {
        console.error("Error saving points:", err);
    }
    
    showSuccessModal.value = true;
};

onMounted(() => {
    initGame();
});
</script>

<template>
  <div class="flex flex-col items-center w-full">
    <!-- Header: Always visible if not loading to avoid jumps -->
    <div v-if="!loading" class="mb-8 flex justify-between w-full max-w-md items-center bg-black/40 p-4 rounded-2xl border border-gray-800 transition-all">
        <div>
            <span class="text-[10px] uppercase font-black text-gray-500 block">Movimientos</span>
            <span class="text-2xl font-black text-white italic transition-all">{{ moves }}</span>
        </div>
        <div class="text-center">
             <span class="text-[10px] uppercase font-black text-gray-500 block">Progreso</span>
             <span class="text-2xl font-black text-neon-purple italic transition-all">{{ matchedPairs }} / {{ symbols.length }}</span>
        </div>
        <button 
            v-if="!cooldownActive || isSuperAdmin"
            @click="initGame" 
            class="bg-gray-800 hover:bg-neon-blue transition px-4 py-2 rounded-xl text-[10px] font-black uppercase cursor-pointer"
        >
            {{ moves > 0 ? 'Reiniciar' : 'Nuevo' }}
        </button>
    </div>

    <!-- Main Game Area with stable height -->
    <div class="w-full max-w-xl min-h-[460px] flex flex-col items-center justify-center">
        <div v-if="loading" class="flex flex-col items-center justify-center py-20">
            <div class="w-12 h-12 border-4 border-neon-blue border-t-transparent rounded-full animate-spin mb-4"></div>
            <p class="text-xs font-black uppercase tracking-widest text-gray-500">Cargando Productos...</p>
        </div>

        <template v-else>
            <div class="grid grid-cols-4 gap-3 md:gap-5 w-full max-w-xl">
                <div v-for="card in cards" :key="card.uniqueId" 
                     @click="flipCard(card)" 
                     class="aspect-square perspective cursor-pointer group relative z-10">
                    
                    <div class="relative w-full h-full transition-transform duration-500 transform-style-3d shadow-xl pointer-events-none"
                         :class="{'rotate-y-180': card.isFlipped || card.isMatched}">
                        
                        <!-- Front (Hidden) -->
                        <div class="absolute inset-0 backface-hidden bg-gamer-card border-2 border-gray-800 rounded-xl flex items-center justify-center group-hover:border-neon-blue/30 transition pointer-events-none overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-white/10 to-transparent"></div>
                            <span class="text-neon-blue text-4xl font-black italic select-none">S</span>
                        </div>
                        
                        <!-- Back (Product Image) -->
                        <div class="absolute inset-0 backface-hidden rotate-y-180 bg-gray-900 border-2 border-neon-blue/50 rounded-xl flex items-center justify-center shadow-lg shadow-neon-blue/10 pointer-events-none overflow-hidden"
                             :class="{'border-neon-green/50 shadow-neon-green/20': card.isMatched}">
                            <img v-if="card.image" :src="card.image" :alt="card.name" class="w-full h-full object-cover opacity-80" :class="{'blur-xl saturate-0 opacity-40': card.is_censored && (!store.user || !store.user.show_censored_content)}">
                            <i v-else-if="card.icon" :class="['fas', card.icon]" class="text-3xl text-white"></i>
                            <div v-if="card.isMatched" class="absolute inset-0 bg-neon-green/20 flex items-center justify-center">
                                <i class="fas fa-check text-neon-green text-3xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Victory Overlay -->
    <transition name="fade">
        <div v-if="showSuccessModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
            <div class="bg-gamer-card border border-neon-green/30 rounded-3xl p-8 max-w-sm w-full text-center shadow-2xl shadow-neon-green/10">
                <div class="w-16 h-16 bg-neon-green/20 rounded-full flex items-center justify-center text-neon-green text-3xl mx-auto mb-4">
                    <i class="fas fa-trophy"></i>
                </div>
                <h2 class="text-2xl font-black tracking-tighter uppercase italic mb-2">
                    {{ earnedPoints >= 0 ? '¡Victoria Magistral!' : 'Juego Completado' }}
                </h2>
                <p class="text-gray-400 text-sm mb-6">Has completado el tablero en {{ moves }} movimientos.</p>
                
                <div class="bg-black/40 rounded-2xl p-4 mb-8">
                    <span class="text-[10px] text-gray-500 uppercase font-black block mb-1">
                        {{ earnedPoints >= 0 ? 'Recompensa' : 'Penalización' }}
                    </span>
                    <span class="text-3xl font-black italic" :class="earnedPoints >= 0 ? 'text-neon-green' : 'text-red-500'">
                        {{ earnedPoints >= 0 ? '+' : '' }}{{ earnedPoints }} <small class="text-xs uppercase not-italic">PTS</small>
                    </span>
                </div>
                
                <div v-if="cooldownActive && !isSuperAdmin" class="mb-4 text-xs font-bold text-red-400 uppercase tracking-widest animate-pulse">
                    Cooldown de 24h Activo
                </div>

                <button 
                    @click="initGame" 
                    :disabled="cooldownActive && !isSuperAdmin"
                    class="w-full py-3 rounded-xl font-black uppercase text-xs tracking-widest transition shadow-neon-green/20 cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed disabled:bg-gray-800"
                    :class="cooldownActive && !isSuperAdmin ? 'bg-gray-800 text-gray-500' : 'bg-neon-green text-gamer-dark hover:scale-105'"
                >
                    {{ cooldownActive && !isSuperAdmin ? 'Bloqueado' : 'Jugar de Nuevo' }}
                </button>
            </div>
        </div>
    </transition>
  </div>
</template>

<style scoped>
.perspective { perspective: 1000px; }
.transform-style-3d { transform-style: preserve-3d; }
.backface-hidden { backface-visibility: hidden; }
.rotate-y-180 { transform: rotateY(180deg); }

.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
