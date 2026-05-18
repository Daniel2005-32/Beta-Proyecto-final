<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';

import { useRouter } from 'vue-router';
import axios from 'axios';
import ChatWidget from './components/ChatWidget.vue';
import { store } from './utils/store';
import { apiBase } from './utils/api';
import logo from './logo.png';

const toggleChat = () => {
    window.dispatchEvent(new Event('toggle-chat'));
};



const router = useRouter();

const cartCount = computed(() => {
   return store.cart.reduce((total, item) => total + item.quantity, 0);
});

const isDropdownOpen = ref(false);


const isAuthenticated = computed(() => !!store.token);

const user = computed(() => store.user);

const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);
const showLiveSearch = ref(false);
let searchTimeout = null;

const onSearch = () => {
    if (searchQuery.value.trim()) {
        router.push({ path: '/products', query: { q: searchQuery.value.trim() } });
        showLiveSearch.value = false;
    }
};

const performLiveSearch = async () => {
    if (searchQuery.value.length < 2) {
        searchResults.value = [];
        showLiveSearch.value = false;
        return;
    }

    isSearching.value = true;
    showLiveSearch.value = true;
    try {
        const res = await axios.get(`${apiBase}/products`, { params: { q: searchQuery.value } });
        const all = res.data.products?.data || res.data.products || [];
        searchResults.value = all.slice(0, 5);
    } catch (e) {
        console.error(e);
    } finally {
        isSearching.value = false;
    }
};

watch(searchQuery, () => {
    if (searchQuery.value.length === 0) {
        searchResults.value = [];
        showLiveSearch.value = false;
        return;
    }
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(performLiveSearch, 500);
});

const leftImages = [
    'https://tienda-dragon-ball.com/wp-content/uploads/2024/08/figura-de-goku-ultra-instinto-1.webp',
    'https://i.pinimg.com/736x/bc/a3/80/bca380011a5a682a9e7766c1d7c2db82.jpg'
];

const rightImages = [
    'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=60&w=1000&auto=format&fit=crop',
    'https://i.redd.it/my-blood-red-commander-igris-cosplay-game-version-v0-zrx40f3kpcrc1.jpg?width=4016&format=pjpg&auto=webp&s=8665949fe51b172e02ec01b74f059a0cc460cbcf',
    'https://cdn1.epicgames.com/offer/e9a679451d094c1ba3d008b6a01adec5/EGS_FINALFANTASYVIIREBIRTH_SquareEnix_S1_2560x1440-e254f978084058f898118dc49728d04c',
    'https://m.media-amazon.com/images/I/810UZa3-MpL._UF1000,1000_QL80_.jpg'
];


const leftIndex = ref(0);
const rightIndex = ref(0);
let rotateInterval = null;

const isDarkMode = ref(true);

const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.body.classList.remove('light-mode');
        localStorage.setItem('theme', 'dark');
    } else {
        document.body.classList.add('light-mode');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    const theme = localStorage.getItem('theme');
    if (theme === 'light') {
        isDarkMode.value = false;
        document.body.classList.add('light-mode');
    }

    rotateInterval = setInterval(() => {
        leftIndex.value = (leftIndex.value + 1) % leftImages.length;
        rightIndex.value = (rightIndex.value + 1) % rightImages.length;
    }, 20000);

    // Escuchar cambios de estado del usuario (ban/eliminación)
    if (window.Echo) {
        // window.Echo.channel('user-status')
        window.Echo.channel('user-status')
            .listen('.UserStatusChanged', (e) => {
                const currentUserId = store.user?.id;
                if (currentUserId && currentUserId === e.userId) {
                    store.notify(`Tu cuenta ha sido ${e.status === 'banned' ? 'suspendida' : 'eliminada'} permanentemente.`, 'error');
                    setTimeout(() => {
                        logout();
                    }, 3000);
                }
            });

        // Notificaciones de Pedidos
        if (store.user) {
            window.Echo.private(`orders.${store.user.id}`)

            .listen('.OrderStatusUpdated', (e) => {
                const statusMap = {
                    'pending': 'Pendiente',
                    'completed': 'Completado ✅',
                    'cancelled': 'Cancelado ❌'
                };
                store.notify(`Tu pedido #${e.order.id} ha cambiado a: ${statusMap[e.order.status] || e.order.status}`, 'info');
            });
        }


        // Notificaciones de Subastas (Superado)
        if (store.user) {
            window.Echo.private(`users.${store.user.id}`)

            .listen('.AuctionOutbid', (e) => {
                store.notify(`¡Te han superado en la subasta de "${e.auction.name}"! 😱`, 'error');
            });
        }

    }
});


onUnmounted(() => {
    if (rotateInterval) clearInterval(rotateInterval);
});


const logout = async () => {
    try {
        await axios.post(`${apiBase}/logout`, {}, {
            headers: { Authorization: `Bearer ${store.token}` }
        });
    } catch(e) { console.error(e); }
    
    store.clearAuth();
    delete axios.defaults.headers.common['Authorization'];
    router.push('/login');
};

const newsletterEmail = ref('');
const subscribing = ref(false);
const subscribeToNewsletter = async () => {
    if (!newsletterEmail.value) return;
    subscribing.value = true;
    try {
        const res = await axios.post(`${apiBase}/newsletter/subscribe`, { email: newsletterEmail.value });
        store.notify(res.data.message, 'success');
        newsletterEmail.value = '';
    } catch (err) {
        store.notify(err.response?.data?.error || "Error al suscribirse", 'error');
    } finally {
        subscribing.value = false;
    }
};

</script>

<template>
  <div class="min-h-screen bg-gamer-dark font-sans flex flex-col text-white">
    <nav class="bg-gamer-card/90 backdrop-blur-md border-b border-neon-purple/20 sticky top-0 z-50 shadow-xl shadow-black/5 dark:shadow-neon-blue/10 transition-colors duration-300">
      <div class="container mx-auto px-4 py-4 flex justify-between items-center h-20">
        <!-- Logo -->
        <router-link to="/" class="text-xl md:text-2xl font-black tracking-tighter hover:scale-105 transition flex-shrink-0 flex items-center">
          <img :src="logo" class="h-9 w-9 md:h-11 md:w-11 mr-1.5 md:mr-2" alt="Logo">
          <div class="flex flex-col md:flex-row leading-none md:leading-normal">
            <span class="text-neon-cyan">SOUL</span><span class="text-neon-blue">GUILD</span>
          </div>
        </router-link>

        
        <!-- Centro: Menú y Buscador Centrados -->
        <div class="flex-1 flex justify-center items-center gap-8 mx-4 hidden md:flex">
            <!-- Menú Principal -->
            <div class="flex items-center gap-5 font-bold uppercase text-sm tracking-wider text-gray-300 dark:text-gray-200 flex-shrink-0">
              <router-link to="/products" class="hover:text-neon-blue transition">Catálogo</router-link>
              <router-link v-if="isAuthenticated" to="/wishlist" class="hover:text-red-500 transition flex items-center gap-1">
                <i class="far fa-heart text-xs"></i> Favoritos
              </router-link>
              <router-link to="/auctions" class="hover:text-neon-blue transition">Subastas</router-link>
              <router-link to="/raffles" class="hover:text-neon-blue transition">Sorteos</router-link>
              <router-link to="/games" class="hover:text-neon-cyan transition flex items-center gap-1">
                <i class="fas fa-gamepad text-xs"></i> Juegos
              </router-link>
            </div>

            <!-- Barra de Búsqueda -->
            <div class="w-full max-w-xs relative">
                <form @submit.prevent="onSearch" class="relative">
                    <input type="text" 
                           v-model="searchQuery" 
                           @focus="showLiveSearch = true"
                           @blur="setTimeout(() => showLiveSearch = false, 200)"
                           placeholder="Buscar productos..." 
                           class="w-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-full pl-6 pr-12 py-2 text-gray-800 dark:text-white focus:outline-none focus:border-neon-blue transition text-sm">
                    <button type="submit" class="absolute right-4 top-2 text-gray-400 hover:text-neon-blue transition">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </form>

                <!-- Resultados en Vivo -->
                <transition name="list">
                    <div v-if="showLiveSearch && (searchResults.length > 0 || isSearching)" class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gamer-card border border-gray-200 dark:border-gray-800 rounded-2xl shadow-2xl overflow-hidden z-[60] backdrop-blur-xl">
                        <div v-if="isSearching" class="p-4 text-center">
                            <i class="fas fa-circle-notch animate-spin text-neon-blue"></i>
                        </div>
                        <div v-else-if="searchResults.length > 0">
                            <router-link v-for="res in searchResults" :key="res.id" :to="`/products/${res.slug}`" class="flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-white/5 transition border-b border-gray-100 dark:border-gray-800 last:border-0" @click="showLiveSearch = false">
                                <img :src="res.image_url" class="w-10 h-10 object-contain bg-black/5 dark:bg-black/20 rounded-lg">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-gray-800 dark:text-white line-clamp-1">{{ res.name }}</span>
                                    <span class="text-[10px] text-neon-blue">{{ res.price }}€</span>
                                </div>
                            </router-link>
                            <router-link :to="{ path: '/products', query: { q: searchQuery } }" class="block p-3 text-center text-[10px] font-black uppercase text-gray-400 hover:text-neon-blue transition bg-gray-50/50 dark:bg-black/20" @click="showLiveSearch = false">
                                Ver todos los resultados
                            </router-link>
                        </div>
                    </div>
                </transition>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex items-center gap-1 md:gap-4 text-xs font-bold uppercase tracking-wider text-gray-300 dark:text-gray-200 flex-shrink-0">

          <!-- Theme Toggle -->
          <button @click="toggleTheme" class="flex items-center hover:text-neon-blue transition p-2 cursor-pointer focus:outline-none">
            <i :class="isDarkMode ? 'fas fa-sun text-yellow-500' : 'fas fa-moon text-neon-purple'" class="text-lg"></i>
          </button>

          <router-link to="/cart" class="flex items-center hover:text-neon-blue transition relative p-2">


            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span v-if="cartCount > 0" class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] rounded-full h-4 w-4 flex items-center justify-center shadow-lg shadow-red-600/20">{{ cartCount }}</span>
          </router-link>

          
          <div v-if="isAuthenticated" class="relative ml-2 pl-4 border-l border-gray-200 dark:border-gray-800">
             <button @click="isDropdownOpen = !isDropdownOpen" class="flex items-center gap-2 cursor-pointer focus:outline-none group">
                <span class="normal-case text-sm text-gray-400 dark:text-gray-300 group-hover:text-neon-blue transition">Hola, <span class="text-neon-blue font-bold">{{ user?.name }}</span></span>
                <svg class="h-3 w-3 text-gray-300 dark:text-gray-400 group-hover:text-neon-blue transition transform" :class="{'rotate-180': isDropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
             </button>
             
             <!-- Dropdown Menu -->
             <div v-if="isDropdownOpen" class="absolute right-0 mt-4 w-48 bg-white dark:bg-gamer-card backdrop-blur-xl border border-gray-200 dark:border-white/10 rounded-2xl shadow-2xl py-2 z-50 animate-scale-in">
                <router-link to="/profile" @click="isDropdownOpen = false" class="block px-4 py-2 text-xs font-bold text-gray-500 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-neon-blue transition flex items-center gap-2">
                    <i class="fas fa-user-circle"></i> Mi Perfil
                </router-link>
                <router-link v-if="user?.is_admin" to="/admin" @click="isDropdownOpen = false" class="block px-4 py-2 text-xs font-black text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 hover:text-neon-green transition flex items-center gap-2">
                    <i class="fas fa-cog"></i> Panel Control
                </router-link>
                <div class="border-t border-gray-100 dark:border-white/5 my-1"></div>
                <button @click="logout(); isDropdownOpen = false" class="w-full text-left px-4 py-2 text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-sign-out-alt"></i> Salir de la Cuenta
                </button>
             </div>
          </div>
          <div v-else class="flex items-center gap-2 md:gap-4 ml-1 md:ml-2 pl-2 md:pl-4 border-l border-gray-200 dark:border-gray-800">
            <router-link to="/register" class="bg-[#00D2FF] text-gamer-dark px-3 md:px-4 py-1.5 rounded-full hover:bg-white transition text-[10px] md:text-[11px] font-black uppercase tracking-wider shadow-lg shadow-neon-blue/20 btn-glow">
              <span class="hidden xs:inline">Acceder</span>
              <i class="fas fa-sign-in-alt xs:hidden px-1"></i>
            </router-link>
            <router-link to="/register" class="bg-gradient-to-r from-neon-purple to-neon-blue text-white px-3 md:px-4 py-1.5 rounded-full hover:scale-105 transition shadow-lg shadow-neon-purple/20 text-[10px] md:text-[11px] btn-glow hidden sm:flex">
              Registrarse
            </router-link>
          </div>
        </div>
      </div>
    </nav>

    <!-- Artículos de fondo Fijos -->
    <div class="relative flex-grow flex flex-col">
        <!-- FONDO IZQUIERDO -->
        <div class="fixed left-0 top-0 h-full w-1/2 pointer-events-none overflow-hidden z-0">
            <img :src="leftImages[leftIndex]" loading="lazy" class="w-full h-full object-cover opacity-20 transition-opacity duration-1000">
            <div class="absolute inset-0 bg-gradient-to-r from-transparent to-gamer-dark from-transparent-l"></div>
        </div>


        <!-- FONDO DERECHO -->
        <div class="fixed right-0 top-0 h-full w-1/2 pointer-events-none overflow-hidden z-0">
            <img :src="rightImages[rightIndex]" loading="lazy" class="w-full h-full object-cover opacity-20 transition-opacity duration-1000">
            <div class="absolute inset-0 bg-gradient-to-l from-transparent to-gamer-dark from-transparent-r"></div>
        </div>


        <main class="flex-grow relative z-10 pb-16 md:pb-0">
          <router-view />
        </main>
    </div>

    <!-- Barra de Navegación Inferior (Móvil) - Oculto en md+ -->
    <div class="fixed bottom-0 left-0 right-0 bg-gamer-card/98 backdrop-blur-lg border-t border-gray-800 flex justify-between items-center py-3 px-2 z-50 md:hidden shadow-[0_-5px_20px_rgba(0,0,0,0.6)]">
        <router-link to="/" class="flex flex-col items-center gap-1 text-gray-400 hover:text-neon-blue transition flex-1 min-w-0">
            <i class="fas fa-home text-lg"></i>
            <span class="text-[6.5px] uppercase font-black tracking-tight whitespace-nowrap overflow-hidden">Inicio</span>
        </router-link>
        <router-link to="/products" class="flex flex-col items-center gap-1 text-gray-400 hover:text-neon-blue transition flex-1 min-w-0">
          <i class="fas fa-th text-lg"></i>
          <span class="text-[6.5px] uppercase font-black tracking-tight whitespace-nowrap overflow-hidden">Catálogo</span>
        </router-link>
        <router-link to="/wishlist" class="flex flex-col items-center gap-1 text-gray-400 hover:text-red-500 transition flex-1 min-w-0">
          <i class="fas fa-heart text-lg"></i>
          <span class="text-[6.5px] uppercase font-black tracking-tight whitespace-nowrap overflow-hidden">Favs</span>
        </router-link>
        <router-link to="/games" class="flex flex-col items-center gap-1 text-gray-400 hover:text-neon-cyan transition flex-1 min-w-0">
          <i class="fas fa-gamepad text-lg"></i>
          <span class="text-[6.5px] uppercase font-black tracking-tight whitespace-nowrap overflow-hidden">Juegos</span>
        </router-link>
        <router-link to="/auctions" class="flex flex-col items-center gap-1 text-gray-400 hover:text-neon-blue transition flex-1 min-w-0">
          <i class="fas fa-gavel text-lg"></i>
          <span class="text-[6.5px] uppercase font-black tracking-tight whitespace-nowrap overflow-hidden">Subastas</span>
        </router-link>
        <router-link to="/raffles" class="flex flex-col items-center gap-1 text-gray-400 hover:text-neon-blue transition flex-1 min-w-0">
          <i class="fas fa-ticket-alt text-lg"></i>
          <span class="text-[6.5px] uppercase font-black tracking-tight whitespace-nowrap overflow-hidden">Sorteos</span>
        </router-link>
        <router-link to="/profile" class="flex flex-col items-center gap-1 text-gray-400 hover:text-neon-green transition flex-1 min-w-0">
            <i class="fas fa-user text-lg"></i>
            <span class="text-[6.5px] uppercase font-black tracking-tight whitespace-nowrap overflow-hidden">Perfil</span>
        </router-link>
    </div>
    <footer class="bg-gamer-card/90 backdrop-blur-md text-gray-400 py-10 text-center text-xs border-t border-gray-800 relative overflow-hidden">
      <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1/2 h-[1px] bg-gradient-to-r from-transparent via-neon-blue/30 to-transparent"></div>
      <div class="max-w-4xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-6 text-left items-start">
        <div>
          <span class="text-neon-blue font-black uppercase tracking-widest text-sm block mb-1">Soul Guild</span>
          <p class="text-[10px] text-gray-400">Santuario definitivo para la cultura gamer y otaku.</p>
          <span class="text-gray-400 text-[10px] block mt-1">&copy; 2026. Todos los derechos reservados.</span>
        </div>
        <div>
          <span class="text-white font-bold text-xs uppercase mb-3 block">Soporte y Ayuda</span>
          <p class="text-[10px] text-gray-500 mb-4">¿Tienes dudas o algún problema con tu pedido? Nuestro equipo técnico está disponible 24/7.</p>
          <div class="flex flex-col gap-2">
             <router-link to="/support" class="text-neon-blue hover:text-white transition flex items-center gap-2 text-[10px] font-bold">
                <i class="fas fa-headset"></i> CENTRO DE SOPORTE
             </router-link>
          </div>
        </div>
        <div>
          <span class="text-white font-bold text-xs uppercase mb-2 block">Enlaces rápidos</span>
          <ul class="space-y-1 text-[10px] text-gray-500">
            <li><router-link to="/" class="hover:text-neon-blue transition">Inicio</router-link></li>
            <li><router-link to="/products" class="hover:text-neon-blue transition">Productos</router-link></li>
            <li><router-link to="/auctions" class="hover:text-neon-blue transition">Subastas</router-link></li>
            <li><router-link to="/raffles" class="hover:text-neon-blue transition">Sorteos</router-link></li>
          </ul>
        </div>
      </div>
    </footer>

    <!-- Banned Overlay -->
    <div v-if="user?.is_banned" class="fixed inset-0 bg-black/98 backdrop-blur-md z-[9999] flex flex-col items-center justify-center p-4">
        <div class="max-w-md text-center">
            <i class="fas fa-gavel text-6xl text-red-500 mb-4 animate-pulse"></i>
            <h1 class="text-3xl font-black uppercase text-white tracking-widest mb-2">CUENTA SUSPENDIDA</h1>
            <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl mb-6 max-w-sm mx-auto">
                <span class="text-xs text-red-400 font-black uppercase tracking-widest block mb-1">Motivo de la suspensión:</span>
                <p class="text-white text-sm leading-relaxed">
                    {{ user?.ban_reason || 'Infracción de las condiciones del servicio de la comunidad.' }}
                </p>
            </div>

            <button @click="logout" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl text-xs font-black transition shadow-lg shadow-red-600/20">
                Cerrar Sesión
            </button>
        </div>
    </div>

    <ChatWidget v-if="isAuthenticated" />

    <!-- Notificaciones Flotantes -->
    <div class="fixed bottom-20 right-4 z-[9999] space-y-3 pointer-events-none">
        <transition-group name="list">
            <div v-for="notif in store.notifications" :key="notif.id" 
                 class="pointer-events-auto min-w-[280px] max-w-sm bg-gamer-card/95 backdrop-blur-xl border border-white/10 rounded-2xl p-4 shadow-2xl flex items-center justify-between gap-4 animate-slide-in"
                 :class="{'border-neon-green/30 shadow-neon-green/10': notif.type === 'success', 'border-neon-red/30 shadow-neon-red/10': notif.type === 'error'}">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full shadow-lg" :class="{'bg-neon-green shadow-neon-green': notif.type === 'success', 'bg-neon-red shadow-neon-red': notif.type === 'error'}"></div>
                    <p class="text-xs font-bold text-white">{{ notif.message }}</p>
                </div>
            </div>
        </transition-group>
    </div>

    <!-- Modal de Confirmación -->
    <div v-if="store.modal.show" class="fixed inset-0 z-[10000] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="store.modal.onCancel"></div>
        <div class="relative w-full max-w-md bg-gamer-card border border-gray-800 rounded-3xl p-8 shadow-2xl animate-scale-in">
            <div class="mb-6">
                <h3 class="text-xl font-black uppercase text-neon-blue tracking-tighter italic mb-2">{{ store.modal.title }}</h3>
                <p class="text-gray-200 text-sm leading-relaxed">{{ store.modal.message }}</p>
            </div>
            <div class="flex gap-4">
                <button @click="store.modal.onCancel" class="flex-1 px-6 py-2.5 rounded-xl border border-gray-800 text-gray-300 hover:text-white hover:bg-white/5 transition font-black uppercase text-xs">Cancelar</button>
                <button @click="store.modal.onConfirm" class="flex-1 px-6 py-2.5 rounded-xl bg-neon-blue text-gamer-dark hover:bg-white transition font-black uppercase text-xs shadow-neon-blue/20">Confirmar</button>
            </div>
        </div>
    </div>

    <!-- Modal de Entrada (Prompt) -->
    <div v-if="store.prompt.show" class="fixed inset-0 z-[10000] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="store.prompt.onCancel"></div>
        <div class="relative w-full max-w-md bg-gamer-card border border-gray-800 rounded-3xl p-8 shadow-2xl animate-scale-in">
            <div class="mb-6">
                <h3 class="text-xl font-black uppercase text-neon-purple tracking-tighter italic mb-2">{{ store.prompt.title }}</h3>
                <p class="text-gray-400 text-sm leading-relaxed mb-4">{{ store.prompt.message }}</p>
                <input type="text" v-model="store.prompt.value" 
                       class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-white focus:border-neon-purple focus:outline-none transition text-sm"
                       @keyup.enter="store.prompt.onConfirm(store.prompt.value)">
            </div>
            <div class="flex gap-4">
                <button @click="store.prompt.onCancel" class="flex-1 px-6 py-2.5 rounded-xl border border-gray-800 text-gray-400 hover:text-white hover:bg-white/5 transition font-black uppercase text-xs">Cancelar</button>
                <button @click="store.prompt.onConfirm(store.prompt.value)" class="flex-1 px-6 py-2.5 rounded-xl bg-neon-purple text-white hover:bg-white hover:text-gamer-dark transition font-black uppercase text-xs shadow-neon-purple/20">Aceptar</button>
            </div>
        </div>
    </div>

  </div>
</template>


<style>
.list-enter-active, .list-leave-active {
  transition: all 0.5s ease;
}
.list-enter-from {
  opacity: 0;
  transform: translateX(30px);
}
.list-leave-to {
  opacity: 0;
  transform: translateX(30px);
}

@keyframes slide-in {
    from { transform: translateX(30px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes scale-in {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

.animate-slide-in { animation: slide-in 0.4s cubic-bezier(0.23, 1, 0.32, 1); }
.animate-scale-in { animation: scale-in 0.3s cubic-bezier(0.23, 1, 0.32, 1); }

/* Page Transitions */
.page-enter-active, .page-leave-active {
  transition: all 0.3s ease;
}
.page-enter-from {
  opacity: 0;
  transform: translateY(10px);
}
.page-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

/* Global Premium Utilities */
.btn-glow {
  transition: all 0.3s ease;
  position: relative;
  overflow: hidden;
}
.btn-glow:hover {
  box-shadow: 0 0 15px var(--neon-blue);
  transform: translateY(-2px);
}
.btn-glow::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
  transform: rotate(45deg);
  transition: 0.5s;
}
.btn-glow:hover::after {
  left: 120%;
}

.text-glow {
  text-shadow: 0 0 10px rgba(0, 242, 255, 0.5);
}

.card-hover {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
}
.card-hover:hover {
  transform: translateY(-5px) scale(1.02);
  box-shadow: 0 10px 30px rgba(0, 242, 255, 0.1);
}
</style>