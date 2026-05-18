<script setup>
import { ref, onMounted, computed } from 'vue';
import { store } from '../../utils/store';
import SoulMemory from '../../components/games/SoulMemory.vue';
import SoulLeaderboard from '../../components/games/SoulLeaderboard.vue';
import SoulRoulette from '../../components/games/SoulRoulette.vue';
import SoulBattle from '../../components/games/SoulBattle.vue';
import { formatDistanceToNow, addHours, isAfter } from 'date-fns';
import { es } from 'date-fns/locale';
import axios from 'axios';
import { apiBase } from '../../utils/api';

const activeGame = ref(null);
const currentTab = ref('games'); // 'games' or 'ranking'
const userPoints = computed(() => store.user?.points || 0);
const isSuperAdmin = computed(() => store.user?.is_super_admin || false);
const isAuthenticated = computed(() => !!store.token);

const onPointsEarned = (points) => {
    store.updatePoints(points);
};

const getCooldown = (lastDate) => {
    const isAdmin = store.user?.is_admin || store.user?.is_super_admin;
    if (!lastDate || isAdmin) return null;
    const nextAvailable = addHours(new Date(lastDate), 24);
    if (isAfter(new Date(), nextAvailable)) return null;
    return formatDistanceToNow(nextAvailable, { locale: es });
};

const memoryCooldown = computed(() => getCooldown(store.user?.last_memory_at));
const rouletteCooldown = computed(() => getCooldown(store.user?.last_roulette_at));
const battleCooldown = computed(() => getCooldown(store.user?.last_rpg_at));

onMounted(async () => {
    if (store.token) {
        try {
            const res = await axios.get(`${apiBase}/profile`, {
                headers: { Authorization: `Bearer ${store.token}` }
            });
            store.user = res.data.user;
            localStorage.setItem('user', JSON.stringify(res.data.user));
        } catch (err) {
            console.error("Error refreshing arcade profile", err);
        }
    }
});
</script>

<template>
  <div class="container mx-auto px-4 py-12 max-w-6xl text-white">
    <!-- Unauthenticated Guest State -->
    <div v-if="!isAuthenticated" class="text-center py-20 bg-gamer-card border border-gray-800 rounded-3xl p-12 max-w-2xl mx-auto shadow-2xl">
        <div class="w-16 h-16 bg-neon-blue/20 rounded-full flex items-center justify-center text-neon-blue text-3xl mx-auto mb-6 shadow-neon-blue/20">
            <i class="fas fa-lock-open"></i>
        </div>
        <h2 class="text-3xl font-black uppercase italic tracking-tighter mb-4">Acceso <span class="text-neon-blue">Exclusivo</span></h2>
        <p class="text-gray-400 max-w-md mx-auto text-sm leading-relaxed mb-8">
            Únete a la élite de <span class="text-neon-cyan font-bold uppercase italic">Soul Guild</span>. Inicia sesión para acceder al Arcade y empezar a ganar puntos reales.
        </p>
        <div class="flex flex-wrap gap-4 justify-center">
            <router-link to="/register" class="bg-neon-blue text-gamer-dark px-10 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:scale-105 transition shadow-neon-blue/20">
                Registrarse y Jugar
            </router-link>
        </div>
    </div>

    <!-- Authenticated User State -->
    <template v-else>
        <!-- Tab Navigation (Only visible if no game is active) -->
        <div v-if="!activeGame" class="flex justify-center mb-12">
            <div class="bg-gamer-card p-1 rounded-2xl border border-gray-800 flex gap-1">
                <button @click="currentTab = 'games'" 
                        :class="currentTab === 'games' ? 'bg-neon-blue text-gamer-dark' : 'text-gray-500 hover:text-white'"
                        class="px-8 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                    <i class="fas fa-gamepad mr-2"></i> Arcade
                </button>
                <button @click="currentTab = 'ranking'" 
                        :class="currentTab === 'ranking' ? 'bg-neon-blue text-gamer-dark' : 'text-gray-500 hover:text-white'"
                        class="px-8 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300">
                    <i class="fas fa-trophy mr-2"></i> Ranking
                </button>
            </div>
        </div>

        <!-- ARCADE TAB -->
        <div v-if="currentTab === 'games' && !activeGame">
            <header class="mb-12 text-center">
                <h1 class="text-6xl font-black uppercase italic tracking-tighter mb-4 animate-glow">
                    <span class="text-neon-cyan">Soul</span> <span class="text-neon-blue">Arcade</span>
                </h1>
                <p class="text-gray-500 max-w-2xl mx-auto text-xs uppercase font-bold tracking-widest leading-relaxed">
                    Juega, Compite y Canjea. <span class="text-neon-green">Puntos convertibles en descuentos reales</span>.
                </p>
                
                <div class="mt-10 inline-flex flex-col md:flex-row items-center gap-6 bg-gamer-card border border-neon-blue/20 px-10 py-5 rounded-3xl shadow-2xl relative overflow-hidden">
                    <!-- Background Glow -->
                    <div class="absolute -top-10 -right-10 w-20 h-20 bg-neon-blue/10 blur-3xl animate-pulse"></div>
                    
                    <div class="flex items-center gap-6">
                        <div class="text-left">
                            <span class="text-[9px] uppercase font-black text-gray-600 tracking-widest block mb-1">Tu Saldo Soul</span>
                            <span class="text-4xl font-black text-neon-blue italic leading-none">{{ userPoints }} <small class="text-[10px] uppercase not-italic opacity-40">PTS</small></span>
                        </div>
                        
                        <div class="h-10 w-[1px] bg-gray-800 hidden md:block"></div>
                        
                        <div class="text-left flex flex-col items-center md:items-start min-w-[120px]">
                            <span class="text-[9px] uppercase font-black text-gray-600 tracking-widest block mb-1">Rango Actual</span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-black uppercase tracking-widest"
                                :class="{
                                    'text-orange-400 border-orange-400/30': store.user?.rank_name === 'Bronce',
                                    'text-gray-300 border-gray-300/30': store.user?.rank_name === 'Plata',
                                    'text-yellow-400 border-yellow-400/30': store.user?.rank_name === 'Oro',
                                    'text-cyan-400 border-cyan-400/30 shadow-neon-cyan/20': store.user?.rank_name === 'Platino'
                                }">
                                <i class="fas fa-crown text-[8px]"></i>
                                {{ store.user?.rank_name || 'Iniciante' }}
                            </span>
                        </div>
                    </div>

                    <!-- Level Progress Bar -->
                    <div class="w-full md:w-48 mt-2 md:mt-0">
                        <div class="flex justify-between text-[8px] font-black uppercase text-gray-600 mb-1">
                            <span>Progreso Nivel</span>
                            <span>{{ Math.round(store.user?.rank_progress || 0) }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-gradient-to-r from-neon-purple to-neon-blue transition-all duration-1000 shadow-neon-blue/20" 
                                 :style="{ width: (store.user?.rank_progress || 0) + '%' }"></div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Game 1: Soul Memory -->
                <div class="group bg-gamer-card border border-gray-800 rounded-3xl p-8 hover:border-neon-purple/50 transition-all duration-500 flex flex-col relative overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-neon-purple/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    <div class="w-14 h-14 bg-neon-purple/20 rounded-2xl flex items-center justify-center text-neon-purple text-2xl mb-6 shadow-neon-purple/10 z-10">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h2 class="text-xl font-black uppercase italic mb-2 z-10 text-white">Soul Memory</h2>
                    <p class="text-gray-500 text-[10px] uppercase font-bold mb-4 z-10">Entrenamiento de memoria visual.</p>
                    <div class="z-10 mb-6 flex items-center gap-1.5 px-3 py-1 bg-white/5 border border-white/10 rounded-full w-fit">
                        <i class="fas fa-clock text-[8px] text-neon-purple"></i>
                        <span class="text-[8px] font-black uppercase text-gray-400 tracking-widest">Límite: 24 Horas</span>
                    </div>
                    <button @click="activeGame = 'memory'" 
                            class="w-full py-4 bg-neon-purple/80 hover:bg-neon-purple text-white rounded-xl font-black uppercase text-[10px] tracking-widest transition-all duration-300 z-10 shadow-neon-purple/20">
                        Jugar Ahora
                    </button>
                    <div class="absolute top-2 right-4 text-[9px] font-black text-neon-purple/40 italic uppercase tracking-widest">Skill Based</div>
                </div>

                <!-- Game 2: Soul Roulette -->
                <div class="group bg-gamer-card border border-gray-800 rounded-3xl p-8 hover:border-neon-cyan/50 transition-all duration-500 flex flex-col relative overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-neon-cyan/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    <div class="w-14 h-14 bg-neon-cyan/20 rounded-2xl flex items-center justify-center text-neon-cyan text-2xl mb-6 shadow-neon-cyan/10 z-10">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h2 class="text-xl font-black uppercase italic mb-2 z-10 text-white">Soul Roulette</h2>
                    <p class="text-gray-500 text-[10px] uppercase font-bold mb-4 z-10">Prueba tu suerte diariamente.</p>
                    <div class="z-10 mb-6 flex items-center gap-1.5 px-3 py-1 bg-white/5 border border-white/10 rounded-full w-fit">
                        <i class="fas fa-clock text-[8px] text-neon-cyan"></i>
                        <span class="text-[8px] font-black uppercase text-gray-400 tracking-widest">Límite: 24 Horas</span>
                    </div>
                    <button @click="activeGame = 'roulette'" 
                            class="w-full py-4 bg-neon-purple/80 hover:bg-neon-purple text-white rounded-xl font-black uppercase text-[10px] tracking-widest transition-all duration-300 z-10 shadow-neon-purple/20">
                        Girar Ahora
                    </button>
                    <div class="absolute top-2 right-4 text-[9px] font-black text-neon-cyan/40 italic uppercase tracking-widest">Lucky Shot</div>
                </div>

                <!-- Game 3: Soul Battle -->
                <div class="group bg-gamer-card border border-gray-800 rounded-3xl p-8 hover:border-neon-red/50 transition-all duration-500 flex flex-col relative overflow-hidden shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-neon-red/10 to-transparent opacity-0 group-hover:opacity-100 transition duration-500"></div>
                    <div class="w-14 h-14 bg-neon-red/20 rounded-2xl flex items-center justify-center text-neon-red text-2xl mb-6 shadow-neon-red/10 z-10">
                        <i class="fas fa-fist-raised"></i>
                    </div>
                    <h2 class="text-xl font-black uppercase italic mb-2 z-10 text-white">Soul Battle</h2>
                    <p class="text-gray-500 text-[10px] uppercase font-bold mb-4 z-10">Duelo RPG contra Bosses del catálogo.</p>
                    <div class="bg-neon-red/10 border border-neon-red/20 rounded-full px-3 py-0.5 w-fit mb-4 text-[7px] text-neon-red font-black uppercase tracking-widest">
                        5-10 PTS + SPECIAL REWARD
                    </div>
                    <div class="z-10 mb-6 flex items-center gap-1.5 px-3 py-1 bg-white/5 border border-white/10 rounded-full w-fit">
                        <i class="fas fa-clock text-[8px] text-neon-red"></i>
                        <span class="text-[8px] font-black uppercase text-gray-400 tracking-widest">Límite: 24 Horas</span>
                    </div>
                    <button @click="activeGame = 'battle'" 
                            class="w-full py-4 bg-neon-red/80 hover:bg-neon-red text-white rounded-xl font-black uppercase text-[10px] tracking-widest transition-all duration-300 z-10 shadow-neon-red/20">
                        Pelear Ahora
                    </button>
                    <div class="absolute top-2 right-4 text-[9px] font-black text-neon-red/40 italic uppercase tracking-widest">RPG Combat</div>
                </div>

            </div>
        </div>

        <!-- RANKING TAB -->
        <div v-if="currentTab === 'ranking' && !activeGame" class="max-w-2xl mx-auto animate-scale-in">
             <SoulLeaderboard />
        </div>

        <!-- ACTIVE GAME LAYOUT -->
        <div v-if="activeGame" class="animate-scale-in relative">
            <button @click="activeGame = null" class="absolute -top-14 left-0 bg-white/5 hover:bg-white/10 border border-white/10 px-6 py-2 rounded-xl text-gray-300 hover:text-white transition-all flex items-center gap-3 text-xs font-black uppercase tracking-widest shadow-xl backdrop-blur-md">
                <i class="fas fa-long-arrow-alt-left text-lg text-neon-blue"></i> Salir del Juego
            </button>
            <div class="bg-gamer-card border border-gray-800 rounded-3xl p-6 md:p-12 shadow-3xl">
                <SoulMemory v-if="activeGame === 'memory'" @points="onPointsEarned" :cooldownRemaining="memoryCooldown" />
                <SoulRoulette v-if="activeGame === 'roulette'" @points="onPointsEarned" :cooldownRemaining="rouletteCooldown" />
                <SoulBattle v-if="activeGame === 'battle'" @game-completed="activeGame = null" :cooldownRemaining="battleCooldown" />
            </div>
        </div>

        <!-- Info Footer -->
        <footer v-if="!activeGame" class="mt-20 border-t border-gray-900 pt-10 text-center opacity-40">
            <p class="text-[9px] text-gray-600 uppercase font-black tracking-[0.5em]">Soul Guild Elite Loyalty System © 2026</p>
        </footer>
    </template>
  </div>
</template>
