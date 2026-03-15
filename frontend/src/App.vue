<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import ChatWidget from './components/ChatWidget.vue';

const router = useRouter();
const cartCount = computed(() => {
   const items = JSON.parse(localStorage.getItem('cart') || '[]');
   return items.reduce((total, item) => total + item.quantity, 0);
});

const isAuthenticated = computed(() => {
    return !!localStorage.getItem('token');
});

const user = computed(() => {
    const usr = localStorage.getItem('user');
    return usr ? JSON.parse(usr) : null;
});

const logout = async () => {
    try {
        const base = import.meta.env.VITE_API_URL; const apiBase = base ? (base.endsWith('/api') ? base : base + '/api') : 'http://localhost:8000/api';
        await axios.post(`${apiBase}/logout`, {}, {
            headers: { Authorization: `Bearer ${localStorage.getItem('token')}` }
        });
    } catch(e) { console.error(e); }
    
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    // axios default header update not needed on full reload, but good practice
    delete axios.defaults.headers.common['Authorization'];
    router.push('/login');
};
</script>

<template>
  <div class="min-h-screen bg-gamer-dark font-sans flex flex-col text-white">
    <nav class="bg-gamer-dark/95 backdrop-blur-md border-b border-gray-800 sticky top-0 z-50 shadow-lg shadow-neon-blue/10">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <router-link to="/" class="text-2xl font-black tracking-tighter hover:scale-105 transition">
          <span class="text-neon-blue">SOUL</span><span class="text-neon-purple">GUILD</span>
        </router-link>
        
        <div class="flex items-center gap-6 font-bold uppercase text-xs tracking-wider text-gray-300">
          <router-link to="/" class="hover:text-neon-blue hover:drop-shadow-[0_0_5px_#00d2ff] transition">Inicio</router-link>
          <router-link to="/products" class="hover:text-neon-blue hover:drop-shadow-[0_0_5px_#00d2ff] transition">Productos</router-link>
          <router-link to="/auctions" class="hover:text-neon-purple hover:drop-shadow-[0_0_5px_#9d00ff] transition">Subastas</router-link>
          <router-link to="/raffles" class="hover:text-neon-purple hover:drop-shadow-[0_0_5px_#9d00ff] transition">Sorteos</router-link>
          
          <router-link to="/cart" class="flex items-center hover:text-neon-blue transition relative">
            <span>Carrito</span>
            <span v-if="cartCount > 0" class="absolute -top-2 -right-3 bg-neon-red text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center shadow-neon-red">{{ cartCount }}</span>
          </router-link>
          
          <div v-if="isAuthenticated" class="flex items-center gap-4 ml-4 pl-4 border-l border-gray-800">
            <span class="normal-case text-sm text-gray-400">Hola, <span class="text-neon-blue">{{ user?.name }}</span></span>
            <router-link to="/profile" class="text-xs font-bold text-neon-purple hover:underline">Perfil</router-link>
            <router-link v-if="user?.is_admin" to="/admin" class="text-xs font-black text-neon-green hover:underline glow-neon-green">Admin</router-link>
            <button @click="logout" class="text-xs bg-gray-900 border border-gray-700 px-3 py-1 rounded hover:bg-neon-red hover:text-white transition cursor-pointer">Salir</button>
          </div>
          <div v-else class="flex gap-4 ml-4 pl-4 border-l border-gray-800">
            <router-link to="/login" class="hover:text-white transition">Ingresar</router-link>
            <router-link to="/register" class="bg-gradient-to-r from-neon-purple to-neon-blue text-white px-4 py-1 rounded-full hover:scale-105 transition shadow-neon-purple text-[11px]">Registrarse</router-link>
          </div>
        </div>
      </div>
    </nav>
    <main class="flex-grow">
      <router-view></router-view>
    </main>
    <footer class="bg-gamer-card text-gray-500 py-8 text-center text-xs border-t border-gray-800/50">
      <div class="flex justify-center gap-4 mb-4">
        <span class="text-neon-blue">Soul Guild</span> &copy; 2026. Todos los derechos reservados.
      </div>
    </footer>
    <ChatWidget v-if="isAuthenticated" />
  </div>
</template>

<style scoped>
</style>