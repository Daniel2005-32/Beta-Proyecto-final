<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

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
        await axios.post(`${import.meta.env.VITE_API_URL || 'http://localhost:8000/api'}/logout`, {}, {
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
  <div class="min-h-screen bg-gray-100 font-sans flex flex-col">
    <nav class="bg-white shadow">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <router-link to="/" class="text-2xl font-black text-emerald-600 tracking-tighter">Soul Guild</router-link>
        <div class="flex items-center gap-6 font-semibold text-gray-600">
          <router-link to="/" class="hover:text-emerald-600 transition">Inicio</router-link>
          <router-link to="/products" class="hover:text-emerald-600 transition">Productos</router-link>
          <router-link to="/auctions" class="hover:text-emerald-600 transition">Subastas</router-link>
          
          <router-link to="/cart" class="flex items-center hover:text-emerald-600 transition relative">
            <span>Carrito</span>
            <span v-if="cartCount > 0" class="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ cartCount }}</span>
          </router-link>
          
          <div v-if="isAuthenticated" class="flex items-center gap-4 ml-4 pl-4 border-l border-gray-200">
            <span class="text-sm">Hola, <span class="text-emerald-600">{{ user?.name }}</span></span>
            <router-link to="/profile" class="text-sm font-medium text-emerald-600 hover:text-emerald-500 underline">Mi Perfil</router-link>
            <router-link v-if="user?.is_admin" to="/admin" class="text-sm font-black text-purple-600 hover:text-purple-800 transition underline tracking-wide">Admin Panel</router-link>
            <button @click="logout" class="text-sm bg-gray-100 px-3 py-1 rounded hover:bg-red-50 hover:text-red-500 transition">Salir</button>
          </div>
          <div v-else class="flex gap-4 ml-4 pl-4 border-l border-gray-200">
            <router-link to="/login" class="text-emerald-600 hover:text-emerald-700">Ingresar</router-link>
            <router-link to="/register" class="bg-emerald-600 text-white px-4 py-1 rounded-full hover:bg-emerald-700 transition shadow-sm">Registrarse</router-link>
          </div>
        </div>
      </div>
    </nav>
    <main class="flex-grow">
      <router-view></router-view>
    </main>
    <footer class="bg-gray-800 text-gray-400 py-8 text-center text-sm border-t border-gray-700">
      Soul Guild &copy; 2026. Todos los derechos reservados.
    </footer>
  </div>
</template>

<style scoped>
</style>