<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';
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
  <div class="container mx-auto px-4 py-8 max-w-7xl">
    
    <!-- Hero / Carousel Section -->
    <section class="mb-12 rounded-xl overflow-hidden shadow-2xl relative h-[400px] sm:h-[500px] flex items-center bg-gray-950 text-white border border-gray-800 shadow-neon-blue/20">
        <!-- Slides -->
        <div v-for="(product, index) in featuredProducts" :key="product.id" 
             v-show="currentSlide === index"
             class="absolute inset-0 w-full h-full flex items-center transition-opacity duration-1000 ease-in-out">
            
            <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="absolute inset-0 w-full h-full object-cover opacity-30">
            <div v-else class="absolute inset-0 w-full h-full bg-gray-900 opacity-50"></div>

            <div class="relative z-10 px-8 md:px-16 w-full max-w-3xl">
                <span class="inline-block bg-neon-blue text-gamer-dark px-3 py-1 rounded-full text-xs font-black uppercase mb-4 shadow-neon-blue">Destacado</span>
                <h1 class="text-4xl md:text-6xl font-black mb-4 leading-tight text-neon-blue drop-shadow-lg">{{ product.name }}</h1>
                <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-lg line-clamp-2">{{ product.description }}</p>
                <div class="flex items-center gap-4">
                    <span class="text-3xl font-black text-neon-green">${{ product.price }}</span>
                    <router-link to="/products" class="inline-block bg-gradient-to-r from-neon-purple to-neon-blue text-white px-8 py-3 rounded-full font-bold uppercase tracking-wide hover:scale-105 transition transform shadow-neon-purple">
                        Ver Producto
                    </router-link>
                </div>
            </div>
        </div>

        <!-- Fallback static if empty -->
        <div v-if="featuredProducts.length === 0" class="absolute inset-0 w-full h-full flex items-center">
            <img src="https://images.unsplash.com/photo-1550537687-c9a0c270da09?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80" alt="Banner Principal" class="absolute inset-0 w-full h-full object-cover opacity-40">
            <div class="relative z-10 px-8 md:px-16 w-full max-w-3xl">
                <h1 class="text-4xl md:text-6xl font-black mb-4 leading-tight drop-shadow-lg text-neon-blue">Bienvenido a Soul Guild</h1>
                <p class="text-lg md:text-xl text-gray-200 mb-8 drop-shadow">Encuentra los mejores productos digitales y acceso a subastas que no verás en otro lado.</p>
            </div>
        </div>

        <!-- Dots Navigation -->
        <div v-if="featuredProducts.length > 1" class="absolute bottom-5 left-1/2 transform -translate-x-1/2 flex space-x-2 z-20">
            <button v-for="(_, index) in featuredProducts" :key="index"
                    @click="currentSlide = index"
                    :class="{'bg-neon-blue w-6': currentSlide === index, 'bg-gray-600 w-2': currentSlide !== index}"
                    class="h-2 rounded-full transition-all duration-300"></button>
        </div>
    </section>

    <div v-if="loading" class="text-center py-16 text-gray-500">Cargando catálogo...</div>
    <div v-else-if="error" class="text-center py-16 text-red-500 font-bold bg-red-50 rounded-lg">{{ error }}</div>
    
    <div v-else>
      <!-- Featured -->
      <section class="mb-16">
        <div class="flex justify-between items-end mb-6">
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">Destacados</h2>
            <router-link to="/products" class="text-emerald-600 font-bold hover:underline">Ver más &rarr;</router-link>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
          <router-link :to="`/products`" v-for="product in featuredProducts" :key="product.id" class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-2xl transition duration-300 flex flex-col h-full">
             <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                 <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                 <span v-else class="text-gray-400">Sin foto</span>
                 <div class="absolute top-2 left-2 bg-yellow-400 text-yellow-900 text-xs font-black uppercase px-2 py-1 rounded">Top</div>
             </div>
             <div class="p-4 flex flex-col flex-grow">
                 <h3 class="font-bold text-gray-800 line-clamp-2 mb-2 group-hover:text-emerald-600 transition">{{ product.name }}</h3>
                 <div class="mt-auto">
                     <p class="text-emerald-600 font-black text-xl">${{ product.price }}</p>
                 </div>
             </div>
          </router-link>
        </div>
      </section>

      <!-- Offers -->
      <section class="mb-16">
        <div class="flex items-center gap-3 mb-6">
            <h2 class="text-3xl font-black text-gray-800 tracking-tight">Ofertas Especiales</h2>
            <span class="bg-red-500 text-white font-bold text-xs px-2 py-1 rounded-full animate-pulse">¡Corre que vuelan!</span>
        </div>
        
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-6">
          <router-link :to="`/products`" v-for="product in offerProducts" :key="product.id" class="group bg-white border border-gray-100 rounded-xl overflow-hidden hover:shadow-2xl transition duration-300 flex flex-col h-full border-t-4 border-t-red-500">
             <div class="h-48 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                 <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                 <span v-else class="text-gray-400">Sin foto</span>
                 
                 <!-- Descuento Badge -->
                 <div v-if="product.original_price" class="absolute top-2 right-2 bg-red-500 text-white text-xs font-black px-2 py-1 rounded-full">
                    -{{ Math.round((1 - (product.price / product.original_price)) * 100) }}%
                 </div>
             </div>
             <div class="p-4 flex flex-col flex-grow">
                 <h3 class="font-bold text-gray-800 line-clamp-2 mb-2 group-hover:text-emerald-600 transition">{{ product.name }}</h3>
                 <div class="mt-auto flex flex-col">
                     <p class="text-gray-400 line-through text-sm font-bold flex-1">${{ product.original_price }}</p>
                     <p class="text-red-500 font-black text-2xl">${{ product.price }}</p>
                 </div>
             </div>
          </router-link>
        </div>
      </section>
    </div>
  </div>
</template>
