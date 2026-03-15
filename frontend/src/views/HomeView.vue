<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
const featuredProducts = ref([]);
const offerProducts = ref([]);
const trendingProducts = ref([]);
const loading = ref(true);
const error = ref(null);

const currentSlide = ref(0);
let slideInterval = null;

onMounted(async () => {
    try {
        const response = await axios.get(`${apiBase}/home`);
        featuredProducts.value = response.data.featuredProducts || [];
        offerProducts.value = response.data.offerProducts || [];
        trendingProducts.value = response.data.trendingProducts || [];

        if (featuredProducts.value.length > 0) {
            slideInterval = setInterval(() => {
                currentSlide.value = (currentSlide.value + 1) % featuredProducts.value.length;
            }, 5000);
        }
    } catch (err) {
        error.value = "Hubo un error al cargar los datos de inicio.";
        console.error(err);
    } finally {
        loading.value = false;
    }
});

onUnmounted(() => {
    if (slideInterval) clearInterval(slideInterval);
});
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    
    <!-- Hero / Carousel Section -->
    <section class="mb-16 rounded-3xl overflow-hidden relative h-[450px] sm:h-[550px] flex items-center bg-gradient-to-r from-gamer-dark to-gray-950 border border-gray-800 shadow-2xl shadow-neon-blue/10">
        <!-- Slides -->
        <div v-for="(product, index) in featuredProducts" :key="product.id" 
             v-show="currentSlide === index"
             class="absolute inset-0 w-full h-full flex items-center transition-opacity duration-1000 ease-in-out">
            
            <!-- Circular Portal Frame for Frame Image -->
            <div class="absolute right-[-5%] lg:right-[10%] top-1/2 -translate-y-1/2 w-[400px] h-[400px] lg:w-[480px] lg:h-[480px] rounded-full border border-neon-blue/30 shadow-[0_0_50px_rgba(0,210,255,0.15)] overflow-hidden hidden md:block">
                <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-gamer-dark via-transparent to-transparent"></div>
            </div>

            <div class="relative z-10 px-8 md:px-16 w-full max-w-xl">
                <span class="inline-block border border-neon-blue/50 bg-neon-blue/10 text-neon-blue px-3 py-1 rounded-full text-xs font-black uppercase mb-4 shadow-neon-blue/20">NOW PRE-ORDER</span>
                <h1 class="text-4xl md:text-6xl font-black mb-4 leading-tight text-white drop-shadow-lg tracking-tight">
                    {{ product.name }}
                </h1>
                <p class="text-base md:text-lg text-gray-400 mb-8 max-w-md line-clamp-3 leading-relaxed">{{ product.description }}</p>
                <div class="flex items-center gap-4">
                    <router-link to="/products" class="inline-block bg-neon-blue text-gamer-dark px-8 py-3 rounded-xl font-bold uppercase tracking-wide hover:scale-105 transition transform shadow-neon-blue">
                        Comprar Ahora
                    </router-link>
                    <button class="border border-gray-700 hover:border-gray-500 px-6 py-3 rounded-xl font-bold uppercase text-xs transition">Ver Tráiler</button>
                </div>
            </div>
        </div>

        <!-- Fallback static if empty -->
        <div v-if="featuredProducts.length === 0" class="absolute inset-0 w-full h-full flex items-center bg-gradient-to-br from-gamer-dark via-gray-950 to-gamer-dark">
            <div class="absolute right-[10%] top-1/2 -translate-y-1/2 w-[450px] h-[450px] rounded-full border-2 border-neon-blue/20 shadow-[0_0_60px_rgba(0,210,255,0.08)] bg-[url('https://images.unsplash.com/photo-1621600411666-4ec9c8f25808?auto=format&fit=crop&w=800')] bg-cover bg-center hidden md:block"></div>
            <div class="relative z-10 px-8 md:px-16 w-full max-w-xl">
                <span class="inline-block border border-neon-purple/50 bg-neon-purple/10 text-neon-purple px-3 py-1 rounded-full text-xs font-black uppercase mb-4">Bienvenido</span>
                <h1 class="text-4xl md:text-6xl font-black mb-4 leading-tight drop-shadow-lg text-white">SOUL GUILD VAULT</h1>
                <p class="text-base md:text-lg text-gray-400 mb-8 leading-relaxed">Encuentra los lanzamientos más codiciados, merchandising exclusivo y subastas limitadas de tus juegos favoritos.</p>
                <router-link to="/products" class="inline-block bg-neon-purple text-white px-8 py-3 rounded-xl font-bold uppercase tracking-wide hover:scale-105 transition transform shadow-neon-purple">
                    Explorar Catálogo
                </router-link>
            </div>
        </div>

        <!-- Dots Navigation -->
        <div v-if="featuredProducts.length > 1" class="absolute bottom-8 left-16 flex space-x-2 z-20">
            <button v-for="(_, index) in featuredProducts" :key="index"
                    @click="currentSlide = index"
                    :class="{'bg-neon-blue w-8': currentSlide === index, 'bg-gray-700 w-3': currentSlide !== index}"
                    class="h-1.5 rounded-full transition-all duration-300"></button>
        </div>
    </section>

    <!-- Categories section -->
    <section class="mb-16">
        <div class="flex items-center gap-2 mb-6">
            <div class="w-1 h-4 bg-neon-blue"></div>
            <h2 class="text-lg font-bold uppercase tracking-wider text-gray-200">Explorar Categorías</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <!-- Consolas -->
            <router-link to="/products" class="group relative h-48 rounded-xl overflow-hidden border border-gray-800/80 bg-gamer-card cursor-pointer hover:border-neon-blue/30 transition duration-500">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=500')] bg-cover bg-center opacity-30 group-hover:scale-110 transition duration-500"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-gamer-dark via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4">
                    <h3 class="font-black text-lg group-hover:text-neon-blue transition">Consolas</h3>
                    <p class="text-xs text-gray-400">Next-Gen & Retro</p>
                </div>
            </router-link>
            <!-- Videojuegos -->
            <router-link to="/products" class="group relative h-48 rounded-xl overflow-hidden border border-gray-800/80 bg-gamer-card cursor-pointer hover:border-neon-purple/30 transition duration-500">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=500')] bg-cover bg-center opacity-30 group-hover:scale-110 transition duration-500"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-gamer-dark via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4">
                    <h3 class="font-black text-lg group-hover:text-neon-purple transition">Videojuegos</h3>
                    <p class="text-xs text-gray-400">Físicos & Digitales</p>
                </div>
            </router-link>
            <!-- Manga -->
            <router-link to="/products" class="group relative h-48 rounded-xl overflow-hidden border border-gray-800/80 bg-gamer-card cursor-pointer hover:border-neon-red/30 transition duration-500">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1578632292335-df3abbb0d586?w=500')] bg-cover bg-center opacity-30 group-hover:scale-110 transition duration-500"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-gamer-dark via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4">
                    <h3 class="font-black text-lg group-hover:text-neon-red transition">Manga</h3>
                    <p class="text-xs text-gray-400">Colecciones</p>
                </div>
            </router-link>
            <!-- Merch -->
            <router-link to="/products" class="group relative h-48 rounded-xl overflow-hidden border border-gray-800/80 bg-gamer-card cursor-pointer hover:border-neon-green/30 transition duration-500">
                <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1613376023733-0a44915a269c?w=500')] bg-cover bg-center opacity-30 group-hover:scale-110 transition duration-500"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-gamer-dark via-transparent to-transparent"></div>
                <div class="absolute bottom-4 left-4">
                    <h3 class="font-black text-lg group-hover:text-neon-green transition">Anime Merch</h3>
                    <p class="text-xs text-gray-400">Figuras & Accesorios</p>
                </div>
            </router-link>
        </div>
    </section>

    <!-- State handlers -->
    <div v-if="loading" class="text-center py-16 text-gray-500">Cargando catálogo...</div>
    <div v-else-if="error" class="text-center py-16 text-red-400 font-bold bg-red-500/10 border border-red-500/20 rounded-xl">{{ error }}</div>
    
    <div v-else>
      <!-- Featured Releases -->
      <section class="mb-16">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-2">
                <div class="w-1 h-4 bg-neon-purple"></div>
                <h2 class="text-lg font-bold uppercase tracking-wider text-gray-200">Lanzamientos Destacados</h2>
            </div>
            <router-link to="/products" class="text-xs font-bold text-neon-blue hover:underline">Ver Todo &rarr;</router-link>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
          <router-link :to="`/products/${product.id}`" v-for="product in featuredProducts" :key="product.id" class="group bg-gamer-card border border-gray-800/80 rounded-2xl overflow-hidden hover:border-neon-blue/30 hover:scale-[1.02] hover:shadow-2xl hover:shadow-neon-blue/5 transition duration-300 flex flex-col h-full relative">
             <div class="h-56 flex items-center justify-center overflow-hidden relative bg-black/5">
                 <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                 <span v-else class="text-gray-600 text-xs">Sin foto</span>
                 <div class="absolute top-3 left-3 bg-neon-purple/20 border border-neon-purple/50 text-neon-purple text-[10px] font-black uppercase px-2 py-0.5 rounded-md">HOT</div>
             </div>
             
             <div class="p-4 flex flex-col flex-grow">
                 <h3 class="font-bold text-sm text-gray-200 line-clamp-2 mb-2 group-hover:text-neon-blue transition">{{ product.name }}</h3>
                 
                 <div class="mt-auto flex items-center justify-between">
                     <p class="text-neon-green font-black text-sm">${{ product.price }}</p>
                     <button class="bg-neon-blue/20 border border-neon-blue/30 text-neon-blue p-2 rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition group-hover:scale-105">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
                     </button>
                 </div>
             </div>
          </router-link>
        </div>
      </section>

      <!-- Offers -->
      <section class="mb-16">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-2">
                <div class="w-1 h-4 bg-neon-red"></div>
                <h2 class="text-lg font-bold uppercase tracking-wider text-gray-200">Ofertas Especiales</h2>
                <span class="bg-neon-red text-white font-black text-[9px] px-1.5 py-0.5 rounded animate-pulse shadow-neon-red">SALE</span>
            </div>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
          <router-link :to="`/products/${product.id}`" v-for="product in offerProducts" :key="product.id" class="group bg-gamer-card border border-gray-800/80 rounded-2xl overflow-hidden hover:border-neon-red/30 hover:scale-[1.02] hover:shadow-2xl hover:shadow-neon-red/5 transition duration-300 flex flex-col h-full relative">
             <div class="h-56 flex items-center justify-center overflow-hidden relative bg-black/5">
                 <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                 <span v-else class="text-gray-600 text-xs">Sin foto</span>
                 
                 <!-- Descuento Badge -->
                 <div v-if="product.original_price" class="absolute top-3 right-3 bg-neon-red text-white text-[10px] font-black px-2 py-0.5 rounded-md shadow-neon-red">
                    -{{ Math.round((1 - (product.price / product.original_price)) * 100) }}%
                 </div>
             </div>
             <div class="p-4 flex flex-col flex-grow">
                 <h3 class="font-bold text-sm text-gray-200 line-clamp-2 mb-2 group-hover:text-neon-red transition">{{ product.name }}</h3>
                 <div class="mt-auto flex items-end justify-between">
                     <div>
                         <p class="text-gray-500 line-through text-xs font-bold">${{ product.original_price }}</p>
                         <p class="text-neon-red font-black text-base">${{ product.price }}</p>
                     </div>
                     <button class="bg-neon-red/20 border border-neon-red/30 text-neon-red p-2 rounded-lg hover:bg-neon-red hover:text-white transition group-hover:scale-105 shadow-neon-red/10">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
                     </button>
                 </div>
             </div>
          </router-link>
        </div>
      </section>

      <!-- Join the Resistance (Newsletter) -->
      <section class="mb-16 bg-gradient-to-br from-gamer-card to-gray-950 border border-gray-800 rounded-3xl p-8 md:p-12 text-center relative overflow-hidden">
          <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1621600411666-4ec9c8f25808?auto=format&fit=crop&w=1200')] bg-cover bg-center opacity-5"></div>
          <div class="relative z-10">
              <h2 class="text-2xl md:text-3xl font-black mb-2 tracking-wide">ÚNETE A LA <span class="text-neon-blue drop-shadow-[0_0_10px_rgba(0,210,255,0.5)]">RESISTENCIA NEÓN</span></h2>
              <p class="text-gray-400 text-sm md:text-base max-w-md mx-auto mb-6 leading-relaxed">Accede a drops exclusivos de edición limitada, códigos de descuento secretos y adelantos de preventas.</p>
              
              <div class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                  <input type="email" placeholder="Tu correo electrónico" class="flex-grow bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-neon-purple/50 text-gray-200">
                  <button class="bg-neon-purple hover:bg-neon-purple/80 text-white font-bold px-6 py-3 rounded-xl transition shadow-neon-purple cursor-pointer text-sm">Suscribirme</button>
              </div>
          </div>
      </section>
    </div>
  </div>
</template>
