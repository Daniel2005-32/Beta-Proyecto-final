<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';

const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
const products = ref([]);
const categories = ref([]);
const currentCategory = ref(null);
const loading = ref(true);
const error = ref(null);

const addToCart = (product) => {
    const defaultCart = JSON.parse(localStorage.getItem('cart') || '[]');
    const existingEntry = defaultCart.find(p => p.id === product.id);

    if (existingEntry) {
        existingEntry.quantity += 1;
    } else {
        defaultCart.push({
            id: product.id,
            name: product.name,
            price: product.price,
            quantity: 1
        });
    }

    localStorage.setItem('cart', JSON.stringify(defaultCart));
    window.dispatchEvent(new Event('cart-updated'));
    alert('Producto añadido al carrito');
};

const fetchProducts = async () => {
    loading.value = true;
    error.value = null;
    currentCategory.value = null;
    try {
        const response = await axios.get(`${apiBase}/products`);
        products.value = response.data.products.data || [];
        categories.value = response.data.categories || [];
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
});
</script>

<template>
  <div class="container mx-auto px-4 py-8 text-white">
    <div class="mb-8 border-b border-gray-800 pb-4">
        <h1 class="text-3xl font-black uppercase tracking-tight">
            <span v-if="currentCategory" class="text-neon-purple">{{ currentCategory }}</span>
            <span v-else class="text-neon-blue">Catálogo</span>
        </h1>
        <p class="text-gray-400 text-xs mt-1">Descubre los últimos lanzamientos y productos exclusivos.</p>
    </div>

    <div v-if="loading" class="text-center py-16 text-gray-500">Cargando productos...</div>
    <div v-else-if="error" class="text-center py-16 text-red-400 font-bold bg-red-500/10 border border-red-500/20 rounded-xl">{{ error }}</div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Filtros -->
        <aside class="col-span-1">
            <div class="bg-gamer-card border border-gray-800 rounded-2xl p-6 sticky top-24">
                <div class="flex items-center gap-2 mb-6 pb-2 border-b border-gray-800">
                    <div class="w-1 h-3 bg-neon-blue"></div>
                    <h3 class="font-bold text-sm uppercase tracking-wider text-gray-200">Filtros</h3>
                </div>

                <!-- Categorías -->
                <div class="mb-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase mb-3">Categorías</h4>
                    <ul class="space-y-2">
                        <li>
                            <button @click="fetchProducts" class="flex items-center gap-2 text-sm w-full text-left transition duration-200" :class="{'text-neon-blue font-bold': !currentCategory, 'text-gray-400 hover:text-white': currentCategory}">
                                <div class="w-2 h-2 rounded-full" :class="{'bg-neon-blue shadow-neon-blue': !currentCategory, 'bg-gray-700': currentCategory}"></div>
                                Todos los Productos
                            </button>
                        </li>
                        <li v-for="category in categories" :key="category.id">
                            <button @click="filterByCategory(category.slug)" class="flex items-center gap-2 text-sm w-full text-left transition duration-200" :class="{'text-neon-purple font-bold': currentCategory === category.name, 'text-gray-400 hover:text-white': currentCategory !== category.name}">
                                <div class="w-2 h-2 rounded-full" :class="{'bg-neon-purple shadow-neon-purple': currentCategory === category.name, 'bg-gray-700': currentCategory !== category.name}"></div>
                                {{ category.name }}
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- Price Range (Visual fallback) -->
                <div class="mb-6">
                    <h4 class="text-xs font-black text-gray-400 uppercase mb-3">Rango de Precio</h4>
                    <div class="h-1 bg-gray-800 rounded-full relative">
                        <div class="absolute inset-0 bg-neon-blue w-2/3 rounded-full"></div>
                        <div class="absolute left-2/3 top-1/2 -translate-y-1/2 w-3 h-3 rounded-full bg-white shadow-neon-blue cursor-pointer"></div>
                    </div>
                </div>

                <!-- Plataformas -->
                <div>
                    <h4 class="text-xs font-black text-gray-400 uppercase mb-3">Plataforma</h4>
                    <ul class="space-y-2 text-xs text-gray-400">
                        <li class="flex items-center gap-2"><input type="checkbox" class="accent-neon-blue"> PlayStation 5</li>
                        <li class="flex items-center gap-2"><input type="checkbox" class="accent-neon-blue" checked> PC Master Race</li>
                        <li class="flex items-center gap-2"><input type="checkbox" class="accent-neon-blue"> Nintendo Switch</li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Product Grid -->
        <main class="col-span-1 lg:col-span-3">
            <div class="flex justify-between items-center mb-6 text-xs text-gray-400">
                <p>Mostrando <span class="text-white font-bold">{{ products.length }}</span> productos</p>
                <div class="flex items-center gap-2">
                    <span>Ordenar por:</span>
                    <select class="bg-gray-900 border border-gray-800 rounded-md px-2 py-1 text-gray-200 focus:outline-none">
                        <option>Más recientes</option>
                        <option>Precio: Mayor a Menor</option>
                        <option>Precio: Menor a Mayor</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <router-link :to="`/products/${product.id}`" v-for="product in products" :key="product.id" class="group bg-gamer-card border border-gray-800/80 rounded-2xl overflow-hidden hover:border-neon-blue/30 hover:scale-[1.01] hover:shadow-2xl hover:shadow-neon-blue/5 transition duration-300 flex flex-col h-full relative">
                    <div class="h-56 flex items-center justify-center overflow-hidden relative bg-black/5">
                        <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span v-else class="text-gray-600 text-xs">Sin foto</span>
                        
                        <!-- Etiqueta de Nuevo -->
                        <span v-if="new Date(product.created_at) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)" class="absolute top-3 left-3 bg-neon-blue/20 border border-neon-blue/50 text-neon-blue text-[10px] font-black uppercase px-2 py-0.5 rounded-md shadow-neon-blue/20">
                            NEW
                        </span>
                    </div>

                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-bold text-sm text-gray-200 line-clamp-2 mb-2 group-hover:text-neon-blue transition" :title="product.name">{{ product.name }}</h3>
                        
                        <div class="mt-auto flex items-center justify-between">
                            <p class="text-neon-green font-black text-sm">${{ product.price }}</p>
                            <span class="text-[10px] text-gray-500">{{ currentCategory || 'Producto' }}</span>
                        </div>

                        <button @click.stop="addToCart(product)" class="mt-4 w-full bg-gradient-to-r from-neon-purple/80 to-neon-blue/80 hover:from-neon-purple hover:to-neon-blue text-white py-2 rounded-xl font-bold text-xs transition duration-300 shadow-neon-blue/10">
                            Añadir al Carrito
                        </button>
                    </div>
                </router-link>
            </div>
        </main>
    </div>
  </div>
</template>
