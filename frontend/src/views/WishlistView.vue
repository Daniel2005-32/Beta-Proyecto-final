<script setup>
import { ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { store } from '../utils/store';
import ProductCard from '../components/ProductCard.vue';
import LoadingState from '../components/LoadingState.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { apiBase } from '../utils/api';

const wishlistProducts = ref([]);
const loading = ref(true);

const fetchWishlistDetailed = async () => {
    loading.value = true;
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get(`${apiBase}/wishlist`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        // Extraemos solo los productos de la relación, filtrando los nulos si el producto fue eliminado
        wishlistProducts.value = res.data.wishlist.map(item => item.product).filter(p => p !== null);
        // Actualizamos los IDs en el store por si acaso
        store.wishlist = wishlistProducts.value.map(p => p.id);
    } catch (err) {
        console.error("Error fetching detailed wishlist:", err);
        store.notify("Error al cargar tu lista de deseos", "error");
    } finally {
        loading.value = false;
    }
};

// Reactividad: Si el usuario quita de favoritos desde el store, lo quitamos de la vista
watch(() => [...store.wishlist], (newIds) => {
    wishlistProducts.value = wishlistProducts.value.filter(p => newIds.includes(p.id));
}, { deep: true });

onMounted(() => {
    fetchWishlistDetailed();
});
</script>

<template>
  <div class="container mx-auto px-4 py-8 text-white min-h-[60vh]">
    <Breadcrumbs :items="[{ label: 'Mi Lista de Deseos', path: '', active: true }]" />
    
    <header class="mb-12 border-b border-gray-800 pb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-4xl font-black uppercase italic tracking-tighter border-l-4 border-red-500 pl-4">
                Mi Lista de <span class="text-red-500">Deseos</span>
            </h1>
            <p class="text-gray-500 text-xs mt-1 pl-4 uppercase font-bold tracking-widest leading-relaxed">
                Tus tesoros guardados de la Guild.
            </p>
        </div>
        
        <div class="bg-gamer-card border border-gray-800 px-6 py-3 rounded-2xl flex items-center gap-4">
            <i class="fas fa-heart text-red-500 animate-pulse"></i>
            <span class="text-sm font-black uppercase italic">{{ wishlistProducts.length }} <small class="text-[10px] opacity-50 not-italic">Items</small></span>
        </div>
    </header>

    <LoadingState v-if="loading" />

    <div v-else>
        <div v-if="wishlistProducts.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <ProductCard v-for="product in wishlistProducts" :key="product.id" :product="product" />
        </div>

        <div v-else class="text-center py-32 bg-gamer-card rounded-3xl border border-dashed border-gray-800">
            <div class="w-20 h-20 bg-gray-900 rounded-full flex items-center justify-center text-gray-700 text-4xl mx-auto mb-6">
                <i class="far fa-heart"></i>
            </div>
            <h2 class="text-2xl font-black uppercase italic text-gray-500 mb-4">Tu lista está vacía</h2>
            <p class="text-gray-600 text-xs uppercase font-bold tracking-widest mb-8">¡Explora el catálogo y guarda lo que más te guste!</p>
            <router-link to="/products" class="inline-block bg-neon-blue text-gamer-dark px-10 py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:scale-105 transition">
                Explorar Catálogo
            </router-link>
        </div>
    </div>
  </div>
</template>
