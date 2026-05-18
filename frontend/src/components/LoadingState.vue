<script setup>
import { ref, onMounted } from 'vue';
import logo from '../logo.png';

const messages = [
    "Invocando gremios...",
    "Preparando inventario legendario...",
    "Sincronizando con el servidor de almas...",
    "Cargando suministros épicos...",
    "Forjando la experiencia Soul Guild..."
];

const currentMessage = ref(messages[0]);

onMounted(() => {
    let i = 0;
    setInterval(() => {
        i = (i + 1) % messages.length;
        currentMessage.value = messages[i];
    }, 2500);
});
</script>

<template>
  <div class="flex flex-col items-center justify-center py-20 animate-fadeIn">
    <div class="relative mb-8">
      <!-- Glow effect -->
      <div class="absolute inset-0 bg-neon-purple/20 blur-2xl rounded-full scale-110 animate-pulse-neon"></div>
      
      <!-- Pulsing Logo -->
      <img :src="logo" loading="lazy" class="h-24 w-24 relative z-10 animate-pulse-neon" alt="Logo">
    </div>

    <!-- Pulsing Text -->
    <h2 class="text-xl font-black italic tracking-tighter uppercase mb-2">
      <span class="text-neon-cyan">SOUL</span>
      <span class="text-neon-blue">GUILD</span>
    </h2>
    <p class="text-xs text-gray-400 uppercase tracking-widest font-bold h-4">
      {{ currentMessage }}
    </p>
    
    <!-- Progress Bar (Fake but fits the aesthetic) -->
    <div class="w-48 h-1 bg-gray-800 rounded-full mt-6 overflow-hidden">
      <div class="h-full bg-gradient-to-r from-neon-green to-neon-cyan animate-skeleton-loading" style="width: 100%; background-size: 200% 100%;"></div>
    </div>
  </div>
</template>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fadeIn {
    animation: fadeIn 0.8s ease-out forwards;
}
@keyframes skeleton-loading {
    0% { background-position: 150% 50%; }
    100% { background-position: -50% 50%; }
}
.animate-skeleton-loading {
    animation: skeleton-loading 2s linear infinite;
}
</style>
