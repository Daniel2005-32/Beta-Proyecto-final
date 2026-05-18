<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import ProductCard from '@/components/ProductCard.vue';
import ProductCarousel from '@/components/ProductCarousel.vue';
import LoadingState from '@/components/LoadingState.vue';


import { apiBase } from '@/utils/api';
const featuredProducts = ref([]);
const offerProducts = ref([]);
const trendingProducts = ref([]);
const exclusiveProducts = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchHomeData = async () => {
    try {
        const response = await axios.get(`${apiBase}/home`);
        featuredProducts.value = response.data.featuredProducts || [];
        offerProducts.value = response.data.offerProducts || [];
        trendingProducts.value = response.data.trendingProducts || [];

        // Cargar exclusivos de productos general
        try {
            const excRes = await axios.get(`${apiBase}/products`, { params: { exclusive: 1 } });
            exclusiveProducts.value = (excRes.data.products?.data || excRes.data.products || [])
                .filter(p => p.is_exclusive && p.stock > 0)
                .slice(0, 10);
        } catch (err) {}


    } catch (err) {
        error.value = "Hubo un error al cargar los datos de inicio.";
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchHomeData();
});

onUnmounted(() => {
    //
});

</script>

<template>
  <div class="relative overflow-hidden w-full h-full">
    <!-- Ambient Mesh Light Grids -->
    <div class="absolute top-[10%] left-1/4 w-[600px] h-[600px] bg-neon-blue/10 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="absolute top-[45%] right-1/4 w-[600px] h-[600px] bg-neon-purple/5 rounded-full blur-[140px] pointer-events-none z-0"></div>
    <div class="absolute top-[75%] left-1/3 w-[500px] h-[500px] bg-neon-red/10 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <div class="container mx-auto px-4 py-8 max-w-7xl text-white relative z-10">
    
    <!-- Hero Section -->
    <div class="relative rounded-3xl overflow-hidden mb-12 border border-neon-blue/20 shadow-[0_0_30px_rgba(0,210,255,0.1)]">
        <div class="min-h-[450px] md:h-[580px] bg-gradient-to-br from-neon-blue/15 via-neon-purple/5 to-transparent flex items-center justify-center px-4 py-12 md:py-0 relative z-10">
            <div class="max-w-4xl text-center">
                <h1 class="text-4xl sm:text-6xl md:text-8xl lg:text-9xl font-black text-white leading-[0.9] mb-4 md:mb-6 tracking-tighter uppercase italic">
                    <span class="text-neon-cyan block">Soul</span>
                    <span class="text-neon-blue block">GUILD</span>
                </h1>
                <p class="text-sm sm:text-lg md:text-xl text-gray-200 mb-6 md:mb-8 leading-relaxed font-medium max-w-2xl mx-auto px-2">
                    Tu santuario definitivo para la cultura gamer y otaku. Lo último en videojuegos, manga de colección, figuras y cosplay. ¡Únete a nuestra hermandad!
                </p>
                <!-- BOTONES -->
                <div class="flex flex-wrap gap-3 md:gap-8 justify-center">
                    <router-link to="/products" class="flex-1 sm:flex-none px-5 py-3 md:px-8 md:py-4 bg-neon-blue text-white font-black uppercase tracking-widest rounded-full hover:scale-105 transition shadow-[0_0_30px_rgba(157,0,255,0.6)] text-[10px] md:text-xs flex items-center justify-center btn-glow">
                        Catálogo
                    </router-link>
                    <router-link to="/auctions" class="flex-1 sm:flex-none px-5 py-3 md:px-8 md:py-4 bg-[#00D2FF] text-gamer-dark font-black uppercase tracking-widest rounded-full hover:scale-105 transition shadow-[0_0_30px_rgba(0,210,255,0.6)] text-[10px] md:text-xs flex items-center justify-center btn-glow">
                        Subastas
                    </router-link>
                    <router-link to="/raffles" class="flex-1 sm:flex-none px-5 py-3 md:px-8 md:py-4 bg-neon-purple text-white font-black uppercase tracking-widest rounded-full hover:scale-105 transition shadow-[0_0_30px_rgba(30,64,175,0.6)] text-[10px] md:text-xs flex items-center justify-center btn-glow">
                        Sorteos
                    </router-link>
                </div>
            </div>
        </div>
    </div>




    <!-- State handlers -->
    <LoadingState v-if="loading" />
    <div v-else-if="error" class="text-center py-16 text-red-400 font-bold bg-red-500/10 border border-red-500/20 rounded-xl">{{ error }}</div>
    
    <div v-else>
      <ProductCarousel 
        v-if="featuredProducts.length > 0"
        title="Productos Destacados"
        accentColor="neon-blue"
        :products="featuredProducts"
      />

      <ProductCarousel 
        v-if="offerProducts.length > 0"
        title="Ofertas Especiales"
        accentColor="neon-red"
        :products="offerProducts"
      />

      <ProductCarousel 
        v-if="trendingProducts.length > 0"
        title="En Tendencia"
        accentColor="neon-purple"
        :products="trendingProducts"
      />

      <ProductCarousel 
        v-if="exclusiveProducts.length > 0"
        title="Artículos Exclusivos"
        accentColor="neon-red"
        :products="exclusiveProducts"
      />

    </div>
    </div>
  </div>
</template>
