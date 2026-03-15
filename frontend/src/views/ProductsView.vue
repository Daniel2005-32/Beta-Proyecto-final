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
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">
            <span v-if="currentCategory">{{ currentCategory }}</span>
            <span v-else>Catálogo de Productos</span>
        </h1>
        <router-link to="/" class="text-emerald-600 hover:underline">Volver a Inicio</router-link>
    </div>

    <div v-if="loading" class="text-center text-gray-500">Cargando productos...</div>
    <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <aside class="col-span-1">
            <div class="bg-gray-50 p-4 rounded shadow">
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Categorías</h3>
                <ul>
                    <li class="mb-2">
                        <a href="#" @click.prevent="fetchProducts" class="text-gray-700 hover:text-emerald-600 font-bold" :class="{'text-emerald-600': !currentCategory}">Todos los Productos</a>
                    </li>
                    <li v-for="category in categories" :key="category.id" class="mb-2">
                        <a href="#" @click.prevent="filterByCategory(category.slug)" class="text-gray-700 hover:text-emerald-600" :class="{'text-emerald-600 font-bold': currentCategory === category.name}">
                            {{ category.name }} ({{ category.products_count }})
                        </a>
                    </li>
                </ul>
            </div>
        </aside>

        <main class="col-span-1 lg:col-span-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="product in products" :key="product.id" class="border rounded shadow bg-white p-4 hover:shadow-lg transition">
                    <div class="h-48 bg-gray-200 mb-4 rounded flex items-center justify-center overflow-hidden relative">
                        <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover">
                        <span v-else class="text-gray-400">Sin Imagen</span>
                        
                        <!-- Etiqueta de Nuevo -->
                        <span v-if="new Date(product.created_at) > new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)" class="absolute top-2 right-2 bg-emerald-500 text-white text-xs font-bold px-2 py-1 rounded">
                            NUEVO
                        </span>
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 line-clamp-2" :title="product.name">{{ product.name }}</h3>
                    <p class="text-emerald-600 font-black mt-2 text-xl">${{ product.price }}</p>
                    <button @click="addToCart(product)" class="mt-4 w-full bg-gray-800 text-white py-2 rounded font-bold hover:bg-emerald-600 transition">
                        Añadir al carrito
                    </button>
                </div>
            </div>
        </main>
    </div>
  </div>
</template>
