<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import { store } from '../../utils/store';
import { apiBase } from '../../utils/api';

const props = defineProps({
    cooldownRemaining: { type: String, default: null }
});

const emit = defineEmits(['game-completed']);

const isAdmin = computed(() => store.user?.is_admin || store.user?.is_super_admin || false);

// Battle State
const playerHP = ref(10);
const maxPlayerHP = ref(10);
const enemyHP = ref(50);
const maxEnemyHP = ref(50);
const enemyProduct = ref(null);
const battleLog = ref([]);
const isGameOver = ref(false);
const isVictory = ref(false);
const isPlaying = ref(false);
const isAttacking = ref(false);
const currentTurn = ref('player'); // 'player' or 'enemy'
const difficulty = ref('normal'); // 'normal' or 'hard' or 'impossible'
const isStunned = ref(false);
const isOnePunching = ref(false);
const showCritical = ref(false);
const showGuide = ref(false);
const afkTimer = ref(null);
const isSuperAdmin = computed(() => store.user?.is_super_admin || false);
const isDesperation = computed(() => playerHP.value <= 3 && playerHP.value > 0);

const fetchRandomEnemy = async () => {
    try {
        const res = await axios.get(`${apiBase}/products?limit=50`);
        const products = res.data.products?.data || res.data.products || [];
        if (products.length > 0) {
            enemyProduct.value = products[Math.floor(Math.random() * products.length)];
        }
    } catch (err) {
        console.error("Error fetching enemy", err);
    }
};

const addLog = (msg, type = 'info') => {
    battleLog.value.unshift({ msg, type, id: Date.now() });
    if (battleLog.value.length > 5) battleLog.value.pop();
};

const startGame = (mode = 'normal') => {
    difficulty.value = mode;
    playerHP.value = 10;
    if (mode === 'impossible') {
        enemyHP.value = 200;
        maxEnemyHP.value = 200;
    } else {
        enemyHP.value = mode === 'hard' ? 100 : 50;
        maxEnemyHP.value = mode === 'hard' ? 100 : 50;
    }
    
    battleLog.value = [{ msg: `¡Un enemigo salvaje ha aparecido en modo ${mode.toUpperCase()}!`, type: 'info', id: Date.now() }];
    isGameOver.value = false;
    isVictory.value = false;
    isPlaying.value = true;
    currentTurn.value = 'player';
    isStunned.value = false;
    fetchRandomEnemy();
    startAfkTimer();
};

const startAfkTimer = () => {
    clearTimeout(afkTimer.value);
    if (!isPlaying.value || isGameOver.value || currentTurn.value !== 'player') return;

    afkTimer.value = setTimeout(() => {
        if (currentTurn.value === 'player' && !isGameOver.value) {
            addLog("¡Dudas demasiado! El enemigo aprovecha tu parálisis.", 'enemy-crit');
            enemyTurn();
        }
    }, 10000);
};

const enemyTurn = async () => {
    if (isGameOver.value) return;
    currentTurn.value = 'enemy';
    isAttacking.value = true;
    
    await new Promise(resolve => setTimeout(resolve, 1500));
    
    const random = Math.random() * 100;
    let damage = 0;
    
    if (difficulty.value === 'impossible') {
        // MODO IMPOSSIBLE: No falla. 50/50. 10% Mareo.
        if (random < 50) {
            damage = 1;
            playerHP.value -= damage;
            addLog(`${enemyProduct.value?.name || 'El BOSS'} ataca brutalmente: -${damage} HP.`, 'enemy-hit');
        } else {
            damage = 3;
            playerHP.value -= damage;
            addLog(`¡LLAMARADA INFERNAL! Pierdes ${damage} de vida.`, 'enemy-crit');
        }
        
        // Stun logic (10%)
        if (Math.random() < 0.1) {
            isStunned.value = true;
            addLog("¡Estás MAREADO! Perderás tu próximo turno.", 'enemy-crit');
        }
    } else if (difficulty.value === 'hard') {
        // MODO DIFÍCIL NERF: 10% fallos. 40% fuego. 50% normal.
        if (random < 10) {
            addLog(`${enemyProduct.value?.name || 'El enemigo'} ha fallado su golpe.`, 'miss');
        } else if (random < 60) {
            damage = 1;
            playerHP.value -= damage;
            addLog(`${enemyProduct.value?.name || 'El Jefe'} ataca: -${damage} HP.`, 'enemy-hit');
        } else {
            damage = 3;
            playerHP.value -= damage;
            addLog(`¡ATAQUE DE FUEGO! Pierdes ${damage} de vida.`, 'enemy-crit');
        }
    } else {
        // MODO NORMAL: Con fallos
        if (random < 33.33) {
            addLog(`${enemyProduct.value?.name || 'El enemigo'} se queda observando...`, 'miss');
        } else if (random < 66.66) {
            damage = 1;
            playerHP.value -= damage;
            addLog(`${enemyProduct.value?.name || 'El enemigo'} ataca y quita ${damage} de vida.`, 'enemy-hit');
        } else {
            damage = 3;
            playerHP.value -= damage;
            addLog(`¡ATAQUE DE FUEGO! Pierdes ${damage} de vida.`, 'enemy-crit');
        }
    }
    
    isAttacking.value = false;
    
    if (playerHP.value <= 0) {
        playerHP.value = 0;
        endGame(false);
    } else {
        currentTurn.value = 'player';
        startAfkTimer();
    }
};

const userAttack = async () => {
    if (currentTurn.value !== 'player' || isGameOver.value) return;
    
    clearTimeout(afkTimer.value);
    if (isStunned.value) {
        addLog("Estás demasiado mareado para reaccionar...", 'miss');
        isStunned.value = false;
        enemyTurn();
        return;
    }

    // Probabilidad de Crítico: 50% en desesperación, 10% normal
    const critChance = isDesperation.value ? 0.50 : 0.10;
    const isCritical = Math.random() < critChance;
    const damage = isCritical ? 10 : 5;

    if (isCritical) {
        showCritical.value = true;
        addLog(`¡GOLPE CRÍTICO${isDesperation.value ? ' DESESPERADO' : ''}! Haces ${damage} de daño.`, 'player-crit');
        setTimeout(() => showCritical.value = false, 1000);
    } else {
        addLog(`Atacas con normalidad. ¡Haces ${damage} de daño!`, 'player-hit');
    }

    enemyHP.value -= damage;
    
    if (enemyHP.value <= 0) {
        enemyHP.value = 0;
        endGame(true);
    } else {
        enemyTurn();
    }
};

const specialAttack = async () => {
    if (currentTurn.value !== 'player' || isGameOver.value) return;
    
    if (isStunned.value) {
        addLog("El mareo te impide realizar el ataque especial...", 'miss');
        isStunned.value = false;
        enemyTurn();
        return;
    }

    // Éxito: 100% en desesperación, 75% normal
    const successChance = isDesperation.value ? 1.0 : 0.75;
    const success = Math.random() < successChance;
    
    if (success) {
        // Crítico: 50% en desesperación, 10% normal
        const critChance = isDesperation.value ? 0.50 : 0.10;
        const isCritical = Math.random() < critChance;
        const damage = isCritical ? 20 : 10;

        if (isCritical) {
            showCritical.value = true;
            addLog(`¡ATAQUE ESPECIAL CRÍTICO DEVASTADOR${isDesperation.value ? '!' : ''}! Haces ${damage} de daño.`, 'player-crit');
            setTimeout(() => showCritical.value = false, 1000);
        } else {
            addLog(`¡GOLPE ESPECIAL! Haces ${damage} de daño.`, 'player-crit');
        }

        enemyHP.value -= damage;
    } else {
        addLog(`Tu ataque especial ha fallado...`, 'miss');
    }
    
    if (enemyHP.value <= 0) {
        enemyHP.value = 0;
        endGame(true);
    } else {
        enemyTurn();
    }
};

const heal = async () => {
    if (currentTurn.value !== 'player' || isGameOver.value) return;
    
    clearTimeout(afkTimer.value);
    if (isStunned.value) {
        addLog("No puedes concentrarte para curarte...", 'miss');
        isStunned.value = false;
        enemyTurn();
        return;
    }

    // Éxito: 80% en desesperación, 50% normal
    const successChance = isDesperation.value ? 0.8 : 0.5;
    const success = Math.random() < successChance;
    if (success) {
        const amount = 6;
        playerHP.value = Math.min(maxPlayerHP.value, playerHP.value + amount);
        addLog(`Utilizas magia curativa. ¡Recuperas ${amount} de vida!`, 'heal');
    } else {
        addLog(`La curación ha fallado...`, 'miss');
    }
    
    enemyTurn();
};

const onePunch = async () => {
    if (currentTurn.value !== 'player' || isGameOver.value || !isSuperAdmin.value) return;
    
    isOnePunching.value = true;
    addLog("¡MODO SAITAMA ACTIVADO! ONE PUNCH!!", 'player-crit');
    
    // Wait for dramatic effect
    await new Promise(r => setTimeout(r, 800));
    
    const damage = 1000;
    enemyHP.value -= damage;
    
    if (enemyHP.value <= 0) {
        enemyHP.value = 0;
        endGame(true);
    } else {
        enemyTurn();
    }
    
    setTimeout(() => {
        isOnePunching.value = false;
    }, 500);
};

const endGame = async (victory) => {
    isGameOver.value = true;
    isVictory.value = victory;
    isPlaying.value = false;
    
    const rewardPoints = victory ? (difficulty.value === 'hard' ? 10 : 5) : 0;
    const token = localStorage.getItem('token');
    const headers = { Authorization: `Bearer ${token}` };
    
    if (victory) {
        addLog("¡HAZAÑA DE LEYENDA! Has derrotado al Boss supremo.", 'victory');
        try {
            // Recompensa de puntos (0 para imposible ya que da cupón)
            const pts = difficulty.value === 'impossible' ? 0 : rewardPoints;
            await axios.post(`${apiBase}/loyalty/add-points`, {
                points: pts,
                game_id: 'soul_battle'
            }, { headers });

            // Recompensa especial Modo Imposible
            if (difficulty.value === 'impossible') {
                const cpnRes = await axios.post(`${apiBase}/loyalty/claim-battle-coupon`, {
                    difficulty: 'impossible'
                }, { headers });
                store.notify(cpnRes.data.message, "success");
            } else {
                store.notify(`¡Has ganado ${rewardPoints} Soul Points!`, "success");
            }
            
            emit('game-completed');
        } catch (err) {
            console.error("Error saving points/coupon", err);
        }
    } else {
        addLog("Has sido derrotado... No consigues puntos.", 'loss');
        try {
            await axios.post(`${apiBase}/loyalty/add-points`, {
                points: 0,
                game_id: 'soul_battle'
            }, { headers });
            emit('game-completed');
        } catch (err) {}
    }
};
</script>

<template>
    <div class="bg-gamer-card border border-gray-800 rounded-3xl p-6 relative overflow-hidden group" :class="{'shake-screen': isOnePunching, 'border-neon-red shadow-[inset_0_0_50px_rgba(255,0,0,0.2)]': isDesperation}">
        <!-- Desperation Aura Overlay -->
        <div v-if="isDesperation && isPlaying" class="absolute inset-0 bg-gradient-to-t from-red-600/10 to-transparent pointer-events-none animate-pulse"></div>
        
        <!-- One Punch Overlay -->
        <div v-if="isOnePunching" class="absolute inset-0 bg-white z-[100] animate-pulse flex items-center justify-center">
            <i class="fas fa-hand-fist text-9xl text-black"></i>
        </div>
        <!-- Header -->
        <div class="flex justify-between items-start mb-6">
            <div>
                <h3 class="text-xl font-black text-white italic tracking-tighter uppercase flex items-center gap-2">
                    <span class="text-neon-red">Soul</span> Battle
                    <span class="bg-neon-red/20 text-neon-red text-[8px] px-2 py-0.5 rounded-full">+10 PTS</span>
                    <button @click="showGuide = true" class="ml-2 w-5 h-5 bg-white/5 border border-white/10 rounded-full flex items-center justify-center text-[10px] text-gray-400 hover:text-neon-blue hover:border-neon-blue transition cursor-pointer" title="Guía de juego">
                        <i class="fas fa-question"></i>
                    </button>
                </h3>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">Gamer vs Producto RPG Boss</p>
            </div>
            <div v-if="cooldownRemaining" class="px-3 py-1 bg-gray-900 border border-gray-800 rounded-full text-[9px] font-black text-red-500">
                BLOQUEADO: {{ cooldownRemaining }}
            </div>
        </div>

        <!-- Initial Screen -->
        <div v-if="!isPlaying && !isGameOver" class="py-12 flex flex-col items-center justify-center text-center space-y-4">
            <div class="w-20 h-20 bg-neon-red/10 border border-neon-red/30 rounded-full flex items-center justify-center animate-pulse shadow-2xl shadow-neon-red/20">
                <i class="fas fa-fist-raised text-3xl text-neon-red"></i>
            </div>
            <div class="max-w-xl w-full px-4">
                <p class="text-[10px] text-gray-400 uppercase font-black tracking-[0.4em] mb-12 text-center">Selecciona tu nivel de poder</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Normal Card -->
                    <button 
                        @click="startGame('normal')"
                        :disabled="!isAdmin && !!cooldownRemaining"
                        class="group relative overflow-hidden bg-black/60 border border-emerald-500/20 rounded-2xl p-6 text-center hover:border-emerald-500/60 hover:scale-105 transition-all duration-500 disabled:opacity-30 disabled:grayscale cursor-pointer"
                    >
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-400 text-2xl mb-4 group-hover:rotate-12 transition-transform">
                                <i class="fas fa-shield-halved"></i>
                            </div>
                            <h4 class="text-sm font-black uppercase italic text-white group-hover:text-emerald-400 transition-colors">NORMAL</h4>
                            <span class="text-[7px] text-emerald-400 font-black uppercase tracking-widest mt-2">RECOMPENSA: 5 PTS</span>
                        </div>
                    </button>

                    <!-- Hard Card -->
                    <button 
                        @click="startGame('hard')"
                        :disabled="!isAdmin && !!cooldownRemaining"
                        class="group relative overflow-hidden bg-black/60 border border-neon-red/20 rounded-2xl p-6 text-center hover:border-neon-red/60 hover:scale-105 transition-all duration-500 disabled:opacity-30 disabled:grayscale cursor-pointer"
                    >
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-12 h-12 bg-neon-red/10 rounded-xl flex items-center justify-center text-neon-red text-2xl mb-4 group-hover:-rotate-12 transition-transform">
                                <i class="fas fa-skull"></i>
                            </div>
                            <h4 class="text-sm font-black uppercase italic text-white group-hover:text-neon-red transition-colors">DIFÍCIL</h4>
                            <span class="text-[7px] text-neon-red font-black uppercase tracking-widest mt-2">RECOMPENSA: 10 PTS</span>
                        </div>
                    </button>

                    <!-- Impossible Mode Card -->
                    <button 
                        @click="startGame('impossible')"
                        :disabled="!isAdmin && !!cooldownRemaining"
                        class="group relative overflow-hidden bg-black/60 border border-neon-purple/20 rounded-2xl p-6 text-center hover:border-neon-purple/60 hover:scale-110 transition-all duration-500 disabled:opacity-30 disabled:grayscale cursor-pointer shadow-neon-purple/20"
                    >
                        <div class="absolute inset-0 bg-gradient-to-t from-neon-purple/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        <div class="relative z-10 flex flex-col items-center">
                            <div class="w-12 h-12 bg-neon-purple/20 rounded-xl flex items-center justify-center text-neon-purple text-2xl mb-4 group-hover:animate-ping transition-transform">
                                <i class="fas fa-ghost"></i>
                            </div>
                            <h4 class="text-sm font-black uppercase italic text-white group-hover:text-neon-purple transition-colors tracking-tighter underline decoration-neon-purple/50">IMPOSIBLE</h4>
                            <span class="text-[7px] text-neon-purple font-black uppercase tracking-widest mt-2">PREMIO: CUPÓN 10%</span>
                        </div>
                    </button>
                </div>

                <div v-if="isAdmin" class="mt-12 text-center line-pulse">
                    <span class="text-[8px] font-black uppercase text-neon-blue tracking-[0.3em] px-4 py-2 bg-neon-blue/10 rounded-full border border-neon-blue/20">
                        Bypass de Admin Activo
                    </span>
                </div>
            </div>
        </div>

        <!-- Battle Arena -->
        <div v-if="isPlaying || isGameOver" class="space-y-6 animate-in fade-in zoom-in duration-500">
            <!-- Enemy Area -->
            <div class="relative flex flex-col items-center p-4 bg-black/40 border border-gray-800 rounded-2xl">
                <div class="w-40 h-40 mb-4 relative group">
                    <div v-if="difficulty === 'hard'" class="absolute inset-0 bg-neon-red/20 rounded-full blur-3xl animate-pulse -z-10"></div>
                    <img v-if="enemyProduct" 
                         :src="enemyProduct.image_url" 
                         class="w-full h-full object-contain filter drop-shadow-2xl transition-all duration-700" 
                         :class="{
                            'animate-bounce': isAttacking && currentTurn === 'enemy', 
                            'grayscale sepia opacity-50': isGameOver && isVictory,
                            'blur-3xl saturate-0 opacity-40': enemyProduct?.is_censored && (!store.user || !store.user.show_censored_content),
                            'brightness-75 contrast-125 hue-rotate-[-10deg] drop-shadow-[0_0_15px_rgba(255,0,0,0.5)]': difficulty === 'hard' && !isGameOver && !(enemyProduct?.is_censored && (!store.user || !store.user.show_censored_content)),
                            'brightness-50 contrast-150 saturate-200 hue-rotate-[280deg] scale-110 drop-shadow-[0_0_25px_rgba(168,85,247,0.6)] animate-pulse': difficulty === 'impossible' && !isGameOver && !(enemyProduct?.is_censored && (!store.user || !store.user.show_censored_content))
                         }">
                    <div v-else class="w-full h-full bg-gray-800 animate-pulse rounded-xl"></div>
                    
                    <!-- Critical Hit Notification -->
                    <div v-if="showCritical" class="absolute inset-0 flex items-center justify-center pointer-events-none z-50">
                        <div class="text-4xl font-black text-neon-red italic tracking-tighter uppercase drop-shadow-[0_0_15px_rgba(255,0,0,0.8)] animate-crit-pop">
                            ¡CRÍTICO!
                        </div>
                    </div>

                    <div v-if="isAttacking && currentTurn === 'enemy'" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                         <div class="w-full h-full" :class="difficulty === 'impossible' ? 'bg-purple-900/40 animate-ping rounded-full' : 'bg-orange-500/20 animate-ping rounded-full'"></div>
                    </div>
                </div>

                <div class="w-full px-4 text-center">
                    <div v-if="isStunned" class="px-2 py-0.5 bg-yellow-400 text-black text-[7px] font-black uppercase rounded mb-2 animate-bounce">¡ESTÁS MAREADO! 😵‍💫</div>
                    <div class="inline-flex items-center gap-2 mb-2">
                        <span v-if="difficulty === 'impossible'" class="px-2 py-0.5 bg-neon-purple text-white border border-neon-purple/30 rounded text-[7px] font-black uppercase tracking-tighter animate-pulse shadow-lg shadow-neon-purple/50">Anomalía: 200 HP Detectados</span>
                        <span v-else-if="difficulty === 'hard'" class="px-2 py-0.5 bg-neon-red/20 text-neon-red border border-neon-red/30 rounded text-[7px] font-black uppercase tracking-tighter animate-pulse">Threat Level: High</span>
                        <span class="text-[8px] font-black uppercase text-gray-500 italic tracking-widest">{{ enemyProduct?.name || 'Cargando Boss...' }}</span>
                    </div>
                    <div class="flex justify-between text-[8px] font-black uppercase text-gray-400 mb-1 italic">
                        <span :class="difficulty === 'impossible' ? 'text-neon-purple' : 'text-neon-red'">{{ difficulty.toUpperCase() }}</span>
                        <span>{{ enemyHP }} / {{ maxEnemyHP }} HP</span>
                    </div>
                    <div class="h-1.5 w-full bg-gray-900 rounded-full overflow-hidden border border-gray-800">
                        <div class="h-full bg-gradient-to-r from-red-600 to-orange-500 transition-all duration-500" :style="{ width: (enemyHP / maxEnemyHP * 100) + '%' }"></div>
                    </div>
                </div>
            </div>

            <!-- VS Divider -->
            <div class="flex items-center justify-center gap-4">
                <div class="h-px flex-grow bg-gradient-to-r from-transparent to-neon-red"></div>
                <span class="text-[10px] font-black text-white italic tracking-tighter">V.S.</span>
                <div class="h-px flex-grow bg-gradient-to-l from-transparent to-neon-blue"></div>
            </div>

            <!-- Player Area -->
            <div class="p-4 bg-black/40 border border-gray-800 rounded-2xl">
                <div class="flex justify-between items-end mb-4">
                    <div>
                        <span class="text-[8px] font-black uppercase text-gray-500 tracking-widest block mb-1">Tu Salud</span>
                        <div class="flex items-center gap-1">
                            <div v-for="i in 10" :key="i" class="w-3 h-3 rounded-sm border" :class="i <= playerHP ? 'bg-neon-blue border-neon-blue shadow-neon-blue/40' : 'bg-gray-800 border-gray-700'"></div>
                        </div>
                    </div>
                    <div class="text-right">
                         <div v-if="isDesperation" class="text-[7px] font-black text-neon-red uppercase tracking-widest animate-pulse mb-1">
                            <i class="fas fa-burst mr-1"></i> Desesperación Activa
                         </div>
                         <p class="text-[10px] font-black text-white italic tracking-tighter">{{ playerHP }} / {{ maxPlayerHP }} HP</p>
                    </div>
                </div>

                <!-- Controls -->
                <div v-if="!isGameOver" class="grid gap-2" :class="isSuperAdmin ? 'grid-cols-4' : 'grid-cols-3'">
                    <button @click="userAttack" :disabled="currentTurn !== 'player'" class="flex flex-col items-center justify-center p-4 bg-gray-900 border border-gray-800 rounded-xl hover:border-neon-blue transition group disabled:opacity-30 cursor-pointer">
                        <i class="fas fa-hand-fist text-base text-gray-400 group-hover:text-neon-blue mb-1"></i>
                        <span class="text-xs font-black uppercase text-gray-400 group-hover:text-white">Atacar</span>
                    </button>
                    <button @click="specialAttack" :disabled="currentTurn !== 'player'" class="flex flex-col items-center justify-center p-4 bg-gray-900 border border-gray-800 rounded-xl hover:border-yellow-400 transition group disabled:opacity-30 cursor-pointer">
                        <i class="fas fa-bolt text-base text-gray-400 group-hover:text-yellow-400 mb-1"></i>
                        <span class="text-xs font-black uppercase text-gray-400 group-hover:text-white">Especial</span>
                    </button>
                    <button @click="heal" :disabled="currentTurn !== 'player'" class="flex flex-col items-center justify-center p-4 bg-gray-900 border border-gray-800 rounded-xl hover:border-neon-green transition group disabled:opacity-30 cursor-pointer">
                        <i class="fas fa-heart-pulse text-base text-gray-400 group-hover:text-neon-green mb-1"></i>
                        <span class="text-xs font-black uppercase text-gray-400 group-hover:text-white">Curar</span>
                    </button>
                    <button v-if="isSuperAdmin" @click="onePunch" :disabled="currentTurn !== 'player'" class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-yellow-400 to-orange-600 border border-yellow-400/50 rounded-xl hover:scale-105 transition group shadow-lg shadow-yellow-400/20 cursor-pointer">
                        <i class="fas fa-explosion text-base text-white mb-1"></i>
                        <span class="text-[8px] font-black uppercase text-white leading-none">ONE PUNCH</span>
                    </button>
                </div>

                <!-- Game Over Stats -->
                <div v-else class="text-center py-2 animate-bounce">
                    <h4 class="text-xl font-black uppercase italic tracking-tighter" :class="isVictory ? 'text-neon-green' : 'text-red-500'">
                        {{ isVictory ? '¡HAS GANADO EL COMBATE!' : 'GAME OVER - DERROTADO' }}
                    </h4>
                </div>
            </div>

            <!-- Battle Log -->
            <div class="bg-gray-900 rounded-xl p-3 border border-gray-800 h-28 overflow-hidden">
                <div class="flex flex-col gap-1.5">
                    <div v-for="log in battleLog" :key="log.id" class="text-xs font-bold animate-in slide-in-from-left duration-300" :class="{
                        'text-gray-400': log.type === 'info',
                        'text-neon-blue': log.type === 'player-hit',
                        'text-yellow-400 font-black': log.type === 'player-crit',
                        'text-orange-500': log.type === 'enemy-hit',
                        'text-red-500 font-black': log.type === 'enemy-crit',
                        'text-neon-green': log.type === 'heal' || log.type === 'victory',
                        'text-gray-600 italic': log.type === 'miss'
                    }">
                        > {{ log.msg }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Glass Overlay Reflection -->
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-white/5 to-transparent"></div>

        <!-- Game Guide Modal -->
        <div v-if="showGuide" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[200] flex items-center justify-center p-4 md:p-8 animate-in fade-in duration-300">
            <div class="bg-gamer-card border border-gray-800 rounded-[2rem] w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col shadow-[0_0_100px_rgba(0,0,0,0.8)] relative">
                
                <!-- Close Button (Top Right Floating) -->
                <button @click="showGuide = false" class="absolute top-6 right-6 w-12 h-12 flex items-center justify-center bg-white/5 hover:bg-neon-red border border-white/10 rounded-full text-gray-400 hover:text-white transition-all duration-300 cursor-pointer z-50 group">
                    <i class="fas fa-times text-xl group-hover:rotate-90 transition-transform"></i>
                </button>

                <!-- Modal Header -->
                <div class="p-8 border-b border-gray-800 bg-black/40">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-neon-blue/20 rounded-2xl flex items-center justify-center text-neon-blue text-2xl shadow-neon-blue/20">
                            <i class="fas fa-scroll"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-black text-white italic tracking-tighter uppercase leading-none">
                                Guía de <span class="text-neon-blue">Supervivencia</span>
                            </h3>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.3em] mt-1">Manual Oficial de Combate Soul Guild</p>
                        </div>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="flex-1 p-10 overflow-y-auto space-y-12 custom-scrollbar">
                    
                    <!-- Basic Rules -->
                    <section>
                        <h4 class="text-sm font-black text-neon-blue uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                             <div class="w-8 h-[2px] bg-neon-blue"></div>
                             SISTEMA DE COMBATE
                        </h4>
                        <div class="space-y-4">
                            <p class="text-sm md:text-base text-gray-300 leading-relaxed uppercase font-black italic">
                                EL DESTINO DE TUS PUNTOS SE DECIDE EN ESTA ARENA. DERROTA AL BOSS ANTES DE QUE TUS <span class="text-neon-red underline">10 PUNTOS DE VIDA</span> SE AGOTEN.
                            </p>
                            <div class="bg-neon-red/10 border border-neon-red/30 p-4 rounded-xl">
                                <h5 class="text-[10px] font-black text-neon-red uppercase mb-1">⚠️ PENALIZACIÓN POR INACTIVIDAD (MAREO)</h5>
                                <p class="text-[9px] text-gray-400 font-bold uppercase">
                                    SI NO ELIGES UNA ACCIÓN EN <span class="text-white">10 SEGUNDOS</span>, ENTRARÁS EN UN ESTADO DE DUDA Y EL ENEMIGO <span class="text-white">VOLVERÁ A ATACAR AUTOMÁTICAMENTE</span>. ¡MANTÉN EL RITMO!
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Player Actions -->
                    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-black/60 border border-gray-800 p-8 rounded-3xl hover:border-neon-blue transition-colors group">
                            <i class="fas fa-hand-fist text-4xl text-neon-blue mb-4 group-hover:scale-110 transition-transform"></i>
                            <h5 class="text-lg font-black text-white uppercase mb-2">ATACAR</h5>
                            <p class="text-xs text-gray-500 leading-relaxed font-bold uppercase">
                                DAÑO NORMAL: <span class="text-white">5</span><br>
                                DAÑO CRÍTICO: <span class="text-yellow-400">10</span><br>
                                <span class="text-emerald-400 text-[10px]">PRESIÓN GARANTIZADA</span>
                            </p>
                        </div>
                        <div class="bg-black/60 border border-gray-800 p-8 rounded-3xl hover:border-yellow-400 transition-colors group">
                            <i class="fas fa-bolt text-4xl text-yellow-400 mb-4 group-hover:scale-110 transition-transform"></i>
                            <h5 class="text-lg font-black text-white uppercase mb-2">ESPECIAL</h5>
                            <p class="text-xs text-gray-500 leading-relaxed font-bold uppercase">
                                DAÑO NORMAL: <span class="text-white">10</span><br>
                                DAÑO CRÍTICO: <span class="text-red-500">20</span><br>
                                <span class="text-yellow-400 text-[10px]">75% PROBABILIDAD DE ÉXITO</span>
                            </p>
                        </div>
                        <div class="bg-black/60 border border-gray-800 p-8 rounded-3xl hover:border-neon-green transition-colors group">
                            <i class="fas fa-heart-pulse text-4xl text-neon-green mb-4 group-hover:scale-110 transition-transform"></i>
                            <h5 class="text-lg font-black text-white uppercase mb-2">CURAR</h5>
                            <p class="text-xs text-gray-500 leading-relaxed font-bold uppercase">
                                RECUPERA: <span class="text-neon-green">6 HP</span><br>
                                <span class="text-white/40 text-[10px]">50% PROBABILIDAD DE ÉXITO</span>
                            </p>
                        </div>
                    </section>

                    <!-- Status: Desperation -->
                    <section class="bg-gradient-to-br from-neon-red/10 to-transparent border-2 border-neon-red/30 p-8 rounded-[2rem] relative overflow-hidden group">
                        <div class="absolute -right-10 -top-10 text-9xl text-neon-red/5 rotate-12 group-hover:rotate-45 transition-transform duration-1000">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="flex items-center gap-3 mb-4">
                             <i class="fas fa-warning text-2xl text-neon-red animate-bounce"></i>
                             <h4 class="text-xl font-black text-neon-red uppercase tracking-tighter italic">LÍMITE: MODO DESESPERACIÓN</h4>
                        </div>
                        <p class="text-xs md:text-sm text-gray-200 font-bold uppercase leading-relaxed mb-6 max-w-2xl">
                            CUANDO TU VIDA CAE A <span class="text-neon-red text-lg underline">3 HP O MENOS</span>, TU PODER SE DESBORDA. TUS INSTINTOS GAMER SE ACTIVAN AL MÁXIMO:
                        </p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-black/40 p-4 rounded-xl border border-neon-red/20 text-center">
                                <span class="block text-[10px] text-gray-500 uppercase font-black mb-1">CRÍTICO</span>
                                <span class="text-xl font-black text-neon-red">50%</span>
                            </div>
                            <div class="bg-black/40 p-4 rounded-xl border border-neon-red/20 text-center">
                                <span class="block text-[10px] text-gray-500 uppercase font-black mb-1">ESPECIAL</span>
                                <span class="text-xl font-black text-neon-red">100%</span>
                            </div>
                            <div class="bg-black/40 p-4 rounded-xl border border-neon-red/20 text-center">
                                <span class="block text-[10px] text-gray-500 uppercase font-black mb-1">CURACIÓN</span>
                                <span class="text-xl font-black text-neon-red">80%</span>
                            </div>
                        </div>
                    </section>

                    <!-- Difficulties & Boss Intel -->
                    <section class="space-y-6">
                         <h4 class="text-sm font-black text-neon-purple uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                             <div class="w-8 h-[2px] bg-neon-purple"></div>
                             INTELIGENCIA DEL ENEMIGO
                         </h4>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <!-- Normal Boss -->
                             <div class="p-6 bg-black/40 rounded-2xl border border-emerald-500/10 hover:border-emerald-500/30 transition-colors">
                                 <div class="flex justify-between items-center mb-4">
                                     <span class="text-xs font-black text-emerald-400 uppercase">NORMAL (50 HP)</span>
                                     <span class="text-lg font-black text-white italic">5 PTS</span>
                                 </div>
                                 <ul class="text-[9px] font-bold text-gray-500 uppercase space-y-1">
                                     <li>33% PROB. DE <span class="text-white">FALLO</span></li>
                                     <li>33% ATAQUE <span class="text-white">NORMAL (1 Daño)</span></li>
                                     <li>33% ATAQUE <span class="text-white">FUEGO (3 Daño)</span></li>
                                 </ul>
                             </div>

                             <!-- Hard Boss -->
                             <div class="p-6 bg-black/40 rounded-2xl border border-neon-red/10 hover:border-neon-red/30 transition-colors">
                                 <div class="flex justify-between items-center mb-4">
                                     <span class="text-xs font-black text-neon-red uppercase">HARD (100 HP)</span>
                                     <span class="text-lg font-black text-white italic">10 PTS</span>
                                 </div>
                                 <ul class="text-[9px] font-bold text-gray-500 uppercase space-y-1">
                                     <li>10% PROB. DE <span class="text-white">FALLO</span></li>
                                     <li>50% ATAQUE <span class="text-white">NORMAL (1 Daño)</span></li>
                                     <li>40% ATAQUE <span class="text-white">FUEGO (3 Daño)</span></li>
                                 </ul>
                             </div>

                             <!-- Impossible Boss -->
                             <div class="md:col-span-2 p-6 bg-gradient-to-r from-neon-purple/20 to-transparent rounded-2xl border-2 border-neon-purple/30">
                                 <div class="flex justify-between items-center mb-4">
                                     <div class="flex items-center gap-3">
                                         <i class="fas fa-ghost text-neon-purple text-xl animate-pulse"></i>
                                         <span class="text-sm font-black text-neon-purple uppercase italic tracking-tighter">Impossible Mode (200 HP)</span>
                                     </div>
                                     <span class="px-4 py-1 bg-yellow-400 text-black text-[10px] font-black uppercase rounded-full shadow-[0_0_15px_rgba(250,204,21,0.4)]">PREMIO: CUPÓN 10%</span>
                                 </div>
                                 <div class="grid md:grid-cols-2 gap-6">
                                     <ul class="text-[9px] font-bold text-gray-500 uppercase space-y-1">
                                         <li>0% PROB. DE <span class="text-neon-red">FALLO (Nunca Fallara)</span></li>  
                                         <li>50% ATAQUE <span class="text-white">NORMAL (1 Daño)</span></li>
                                         <li>50% ATAQUE <span class="text-white">FUEGO (3 Daño)</span></li>
                                         <li>10% PROB. DE <span class="text-neon-purple">ATURDIR/MAREAR</span></li>
                                     </ul>
                                     <p class="text-[10px] text-gray-500 leading-tight uppercase font-black italic border-l border-gray-800 pl-4">
                                         EL BOSS ES IMPLACABLE. ADEMÁS DE SU ALTA VIDA, SUS ATAQUES NO PUEDEN FALLAR Y CADA GOLPE TIENE UNA <span class="text-neon-purple">PROBABILIDAD DE MAREARTE</span>, HACIÉNDOTE PERDER TU SIGUIENTE TURNO.
                                     </p>
                                 </div>
                             </div>
                         </div>
                    </section>
                </div>

                <!-- Modal Footer -->
                <div class="p-8 bg-black/60 border-t border-gray-800 flex justify-center">
                    <button @click="showGuide = false" class="bg-neon-blue text-gamer-dark px-16 py-4 rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:scale-105 active:scale-95 transition-all shadow-2xl shadow-neon-blue/20 cursor-pointer">
                        SALIR DE LA GUÍA
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes shake { 0%, 100% { transform: translate(0,0); } 10%, 30%, 50%, 70%, 90% { transform: translate(-5px,0); } 20%, 40% , 60%, 80% { transform: translate(5px,0); } }
.shake-screen { animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both; }

@keyframes critPop {
    0% { transform: scale(0.5) rotate(-10deg); opacity: 0; }
    20% { transform: scale(1.4) rotate(10deg); opacity: 1; }
    80% { transform: scale(1.2) rotate(5deg); opacity: 1; }
    100% { transform: scale(1) rotate(0deg); opacity: 0; }
}
.animate-crit-pop {
    animation: critPop 1s ease-out forwards;
}
</style>
