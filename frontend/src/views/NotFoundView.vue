<script setup>
import { useRouter } from 'vue-router';
const router = useRouter();
</script>

<template>
  <div class="min-h-screen bg-gamer-dark flex flex-center flex-col text-center p-6 bg-gradient-to-b from-gamer-dark to-black relative overflow-hidden">
    <!-- Retro background effects -->
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute top-0 left-0 w-full h-full bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')]"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-neon-purple/20 via-transparent to-neon-blue/20 animate-pulse"></div>
    </div>

    <div class="relative z-10 max-w-lg mx-auto">
        <div class="mb-8">
            <h1 class="text-[120px] font-black text-transparent bg-clip-text bg-gradient-to-b from-red-500 to-red-900 leading-none drop-shadow-[0_0_30px_rgba(239,68,68,0.5)] italic tracking-tighter animate-bounce">
                404
            </h1>
            <div class="mt-[-20px] bg-red-600 text-white font-black text-3xl uppercase tracking-[0.2em] px-4 py-2 skew-x-[-10deg] shadow-[5px_5px_0_#991b1b]">
                Game Over
            </div>
        </div>

        <p class="text-gray-400 font-bold mb-12 uppercase tracking-widest text-sm">
            La ruta solicitada ha sido eliminada o nunca existió en este servidor arcade.
        </p>

        <div class="space-y-4">
            <button @click="router.push('/')" class="w-full bg-neon-blue text-gamer-dark font-black py-4 rounded-2xl uppercase tracking-[0.1em] shadow-[0_0_20px_rgba(0,242,255,0.4)] hover:scale-105 transition transform duration-300 flex items-center justify-center gap-3">
                <i class="fas fa-home"></i> Insert Coin to Restart
            </button>
            <button @click="router.back()" class="w-full bg-transparent border-2 border-gray-800 text-gray-500 font-black py-4 rounded-2xl uppercase tracking-[0.1em] hover:border-gray-600 hover:text-gray-300 transition duration-300">
                Go Back One Level
            </button>
        </div>

        <div class="mt-20 flex justify-center gap-8 opacity-20">
            <i class="fas fa-ghost text-4xl animate-float"></i>
            <i class="fas fa-pacman text-4xl animate-float delay-100"></i>
            <i class="fas fa-gamepad text-4xl animate-float delay-200"></i>
        </div>
    </div>
  </div>
</template>

<style scoped>
.animate-float {
    animation: float 3s ease-in-out infinite;
}
.delay-100 { animation-delay: 1s; }
.delay-200 { animation-delay: 2s; }

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-15px); }
}

@keyframes pulse {
    0%, 100% { opacity: 0.1; }
    50% { opacity: 0.3; }
}

.flex-center {
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
