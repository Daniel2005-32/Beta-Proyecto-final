<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
import ProductCard from '@/components/ProductCard.vue';
import LoadingState from '@/components/LoadingState.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { store } from '../utils/store';

import { apiBase } from '@/utils/api';
const products = ref([]);
const categories = ref([]);
const currentCategory = ref(route.query.category || null);
const currentSort = ref(route.query.sort || 'latest');
const currentExclusive = ref(route.query.exclusive === '1');
const currentAnime = ref(route.query.anime === '1');
const currentMarvel = ref(route.query.marvel === '1');
const currentStarWars = ref(route.query.star_wars === '1');
const currentDC = ref(route.query.dc === '1');
const currentOffers = ref(route.query.offers === '1');
const loading = ref(true);
const error = ref(null);

const pagination = ref({
    current_page: 1,
    last_page: 1,
    total: 0
});

const showFilters = ref(false);

const changePage = (newPage) => {
    if (newPage >= 1 && newPage <= pagination.value.last_page) {
        pagination.value.current_page = newPage;
        fetchProducts();
    }
};

const addToCart = (product) => {
    store.addToCart(product);
    store.notify('Producto añadido al carrito');
};

const fetchProducts = async () => {
    loading.value = true;
    error.value = null;
    currentCategory.value = null;
    try {
        const params = {};
        if (route.query.q) params.q = route.query.q;
        if (route.query.offers) params.offers = route.query.offers;
        if (route.query.category) params.category = route.query.category;
        
        if (currentSort.value) params.sort = currentSort.value;
        if (currentExclusive.value) params.exclusive = 1;
        if (currentAnime.value) params.anime = 1;
        if (currentMarvel.value) params.marvel = 1;
        if (currentStarWars.value) params.star_wars = 1;
        if (currentDC.value) params.dc = 1;
        if (currentOffers.value) params.offers = 1;
        params.page = pagination.value.current_page;

        const response = await axios.get(`${apiBase}/products`, { params });
        products.value = response.data.products?.data || response.data.products || [];
        categories.value = response.data.categories || [];

        if (response.data.products?.last_page) {
            pagination.value.current_page = response.data.products.current_page;
            pagination.value.last_page = response.data.products.last_page;
            pagination.value.total = response.data.products.total;
        }

        if (route.query.category && categories.value.length > 0) {
            const cat = categories.value.find(c => c.slug === route.query.category);
            currentCategory.value = cat ? cat.name : null;
        }

    } catch (err) {
        error.value = "Hubo un error al cargar los productos.";
    } finally {
        loading.value = false;
    }
};

const filterByCategory = async (slug) => {
    loading.value = true;
    error.value = null;
    try {
        const response = await axios.get(`${apiBase}/products/category/${slug}`);
        products.value = response.data.products.data || [];
        currentCategory.value = response.data.category.name;
    } catch (err) {
        error.value = "Hubo un error al cargar esta categoría.";
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchProducts();
    store.fetchWishlist();
});

watch([currentSort, currentExclusive, currentAnime, currentMarvel, currentStarWars, currentDC, currentOffers], () => {
    const query = { ...route.query };
    
    if (currentSort.value && currentSort.value !== 'latest') query.sort = currentSort.value; else delete query.sort;
    if (currentExclusive.value) query.exclusive = '1'; else delete query.exclusive;
    if (currentAnime.value) query.anime = '1'; else delete query.anime;
    if (currentMarvel.value) query.marvel = '1'; else delete query.marvel;
    if (currentStarWars.value) query.star_wars = '1'; else delete query.star_wars;
    if (currentDC.value) query.dc = '1'; else delete query.dc;
    if (currentOffers.value) query.offers = '1'; else delete query.offers;
    
    query.page = 1;
    router.replace({ query });
});

watch(() => route.query, () => {
    pagination.value.current_page = Number(route.query.page) || 1;
    currentSort.value = route.query.sort || 'latest';
    currentExclusive.value = route.query.exclusive === '1';
    currentAnime.value = route.query.anime === '1';
    currentMarvel.value = route.query.marvel === '1';
    currentStarWars.value = route.query.star_wars === '1';
    currentDC.value = route.query.dc === '1';
    currentOffers.value = route.query.offers === '1';
    currentCategory.value = route.query.category || null;
    
    fetchProducts();
}, { deep: true });

</script>

<template>
  <div class="container mx-auto px-4 py-8 text-white">
    <Breadcrumbs :items="[
        { label: 'Catálogo', path: '/products', active: !currentCategory },
        ...(currentCategory ? [{ label: currentCategory, path: '', active: true }] : [])
    ]" />
    
    <div class="mb-8 border-b border-gray-800 pb-4">
        <h1 class="text-4xl font-black uppercase italic tracking-tighter border-l-4 border-neon-blue pl-4">
            <span v-if="currentCategory" class="text-neon-purple">{{ currentCategory }}</span>
            <span v-else class="text-white">Catálogo <span class="text-neon-blue">Completo</span></span>
        </h1>
        <p class="text-gray-300 text-xs mt-1 pl-4">Descubre los últimos lanzamientos y productos exclusivos.</p>
    </div>

    <LoadingState v-if="loading" />
    <div v-else-if="error" class="text-center py-16 text-red-400 font-bold bg-red-500/10 border border-red-500/20 rounded-xl">{{ error }}</div>
    
    <div v-else>
        <!-- Botón de Filtros para Móviles -->
        <button @click="showFilters = !showFilters" class="lg:hidden flex items-center justify-center gap-2 mb-4 w-full py-2.5 bg-gray-900 border border-gray-800 rounded-xl text-xs font-black hover:border-neon-blue transition text-white">
            <i class="fas fa-filter text-neon-blue"></i>
            {{ showFilters ? 'OCULTAR FILTROS' : 'MOSTRAR FILTROS' }}
        </button>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Filtros -->
            <aside :class="{'hidden lg:block': !showFilters, 'block mb-4': showFilters}" class="col-span-1">
            <div class="bg-gamer-card border border-gray-800 rounded-2xl p-6 sticky top-24">
                <div class="flex items-center gap-2 mb-6 pb-2 border-b border-gray-800">
                    <div class="w-1 h-3 bg-neon-blue"></div>
                    <h3 class="font-bold text-sm uppercase tracking-wider text-gray-200">Filtros</h3>
                </div>

                <!-- Categorías -->
                <div class="mb-6">
                    <h4 class="text-xs font-black text-gray-300 uppercase mb-3">Categorías</h4>
                    <ul class="space-y-2">
                        <li>
                            <router-link to="/products" class="flex items-center gap-2 text-sm w-full text-left transition duration-200" :class="{'text-neon-blue font-bold': !currentCategory, 'text-gray-300 hover:text-white': currentCategory}">
                                <div class="w-2 h-2 rounded-full" :class="{'bg-neon-blue shadow-neon-blue': !currentCategory, 'bg-gray-700': currentCategory}"></div>
                                Todos los Productos
                            </router-link>
                        </li>
                        <li v-for="category in categories" :key="category.id">
                            <router-link :to="{ name: 'products', query: { category: category.slug } }" class="flex items-center gap-2 text-sm w-full text-left transition duration-200" :class="{'text-neon-purple font-bold': currentCategory === category.name, 'text-gray-300 hover:text-white': currentCategory !== category.name}">
                                <div class="w-2 h-2 rounded-full" :class="{'bg-neon-purple shadow-neon-purple': currentCategory === category.name, 'bg-gray-700': currentCategory !== category.name}"></div>
                                {{ category.name }}
                            </router-link>
                        </li>
                    </ul>
                </div>

                <!-- Exclusivos -->
                <div class="mb-4 border-t border-gray-800 pt-4 space-y-3">
                     <label class="flex items-center gap-2 text-xs font-black text-gray-300 uppercase cursor-pointer hover:text-white transition">
                        <input type="checkbox" v-model="currentExclusive" class="w-4 h-4 accent-neon-blue rounded bg-gray-900 border-gray-800 focus:ring-0">
                        <span>Exclusivos</span>
                     </label>

                     <label class="flex items-center gap-2 text-xs font-black text-gray-300 uppercase cursor-pointer hover:text-white transition">
                        <input type="checkbox" v-model="currentOffers" class="w-4 h-4 accent-neon-green rounded bg-gray-900 border-gray-800 focus:ring-0">
                        <span>Ofertas Especiales</span>
                     </label>
                </div>

                <!-- Franquicias -->
                <div class="mb-6 space-y-3">
                     <h4 class="text-xs font-black text-gray-300 uppercase mb-3">Franquicias</h4>
                     
                     <label class="flex items-center gap-2 text-xs font-black text-gray-300 uppercase cursor-pointer hover:text-white transition group">
                        <input type="checkbox" v-model="currentAnime" class="w-4 h-4 accent-neon-cyan rounded bg-gray-900 border-gray-800 focus:ring-0">
                        <span class="group-hover:text-neon-cyan duration-200">Anime</span>
                     </label>

                     <label class="flex items-center gap-2 text-xs font-black text-gray-300 uppercase cursor-pointer hover:text-white transition group">
                        <input type="checkbox" v-model="currentMarvel" class="w-4 h-4 accent-neon-red rounded bg-gray-900 border-gray-800 focus:ring-0">
                        <span class="group-hover:text-neon-red duration-200">Marvel</span>
                     </label>

                     <label class="flex items-center gap-2 text-xs font-black text-gray-300 uppercase cursor-pointer hover:text-white transition group">
                        <input type="checkbox" v-model="currentStarWars" class="w-4 h-4 accent-neon-blue rounded bg-gray-900 border-gray-800 focus:ring-0">
                        <span class="group-hover:text-neon-blue duration-200">Star Wars</span>
                     </label>

                     <label class="flex items-center gap-2 text-xs font-black text-gray-300 uppercase cursor-pointer hover:text-white transition group">
                        <input type="checkbox" v-model="currentDC" class="w-4 h-4 accent-neon-purple rounded bg-gray-900 border-gray-800 focus:ring-0">
                        <span class="group-hover:text-neon-purple duration-200">DC</span>
                     </label>
                </div>
            </div>
        </aside>

        <!-- Main Product Grid -->
        <main class="col-span-1 lg:col-span-3">
            <div class="flex justify-between items-center mb-6 text-xs text-gray-300">
                <p>Mostrando <span class="text-white font-bold">{{ products.length }}</span> productos</p>
                <div class="flex items-center gap-2">
                    <span>Ordenar por:</span>
                    <select v-model="currentSort" class="bg-white/5 backdrop-blur-md border border-white/10 rounded-md px-2 py-1 text-gray-200 focus:outline-none text-[11px] cursor-pointer">
                        <option value="latest" class="bg-gamer-dark">Más recientes</option>
                        <option value="oldest" class="bg-gamer-dark">Menos recientes</option>
                        <option value="price_desc" class="bg-gamer-dark">Precio: Mayor a Menor</option>
                        <option value="price_asc" class="bg-gamer-dark">Precio: Menor a Mayor</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <ProductCard v-for="product in products" :key="product.id" :product="product" />
            </div>

            <!-- Paginación -->
            <div v-if="pagination.last_page > 1" class="flex justify-center items-center gap-4 mt-12 border-t border-gray-800 pt-6">
                <button 
                    @click="changePage(pagination.current_page - 1)" 
                    :disabled="pagination.current_page === 1"
                    class="px-4 py-2 bg-gray-900 border border-gray-800 rounded-xl text-xs font-bold hover:border-neon-blue/50 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    Anterior
                </button>
                <span class="text-xs text-gray-300">Página {{ pagination.current_page }} de {{ pagination.last_page }}</span>
                <button 
                    @click="changePage(pagination.current_page + 1)" 
                    :disabled="pagination.current_page === pagination.last_page"
                    class="px-4 py-2 bg-gray-900 border border-gray-800 rounded-xl text-xs font-bold hover:border-neon-blue/50 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    Siguiente
                </button>
            </div>
        </main>
        </div>
    </div>
  </div>
</template>
