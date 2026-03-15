<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';

const product = ref(null);
const relatedProducts = ref([]);
const loading = ref(true);
const error = ref(null);
const quantity = ref(1);

const fetchProduct = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await axios.get(`${apiBase}/products`); 
        // fallback fetch: find individual in general list if no /products/:id endpoint exists
        const allProducts = response.data.products.data || [];
        const found = allProducts.find(p => p.id === parseInt(route.params.id));
        
        if (found) {
            product.value = found;
            // Fetch related products of same category
            relatedProducts.value = allProducts.filter(p => p.category_id === found.category_id && p.id !== found.id).slice(0, 4);
        } else {
            error.value = "Producto no encontrado.";
        }
    } catch (err) {
        error.value = "Hubo un error al cargar los detalles del producto.";
    } finally {
        loading.value = false;
    }
};

const addToCart = () => {
    const defaultCart = JSON.parse(localStorage.getItem('cart') || '[]');
    const existingEntry = defaultCart.find(p => p.id === product.value.id);

    if (existingEntry) {
        existingEntry.quantity += quantity.value;
    } else {
        defaultCart.push({
            id: product.value.id,
            name: product.value.name,
            price: product.value.price,
            quantity: quantity.value,
            image_url: product.value.image_url
        });
    }

    localStorage.setItem('cart', JSON.stringify(defaultCart));
    window.dispatchEvent(new Event('cart-updated'));
    alert('Producto añadido al carrito');
};

onMounted(() => {
    fetchProduct();
});
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    
    <div v-if="loading" class="text-center py-16 text-gray-500">Cargando producto...</div>
    <div v-else-if="error" class="text-center py-16 text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl">{{ error }}</div>
    
    <div v-else-if="product">
        <!-- Main Layout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
            
            <!-- Left: Image frame container -->
            <div class="space-y-4">
                <div class="aspect-square bg-gamer-card border border-gray-800 rounded-3xl overflow-hidden shadow-2xl flex items-center justify-center relative group">
                    <img v-if="product.image_url" :src="product.image_url" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    <span v-else class="text-gray-500">Sin Imagen</span>
                    
                    <span v-if="product.featured" class="absolute top-4 left-4 bg-neon-purple/20 border border-neon-purple/50 text-neon-purple text-xs font-black uppercase px-2.5 py-1 rounded-md shadow-neon-purple/30">COLLECTOR’S EDITION</span>
                </div>
            </div>

            <!-- Right: Item Details container -->
            <div class="flex flex-col">
                <div class="mb-6">
                    <span class="text-xs font-bold text-neon-blue uppercase tracking-widest">{{ product.category_id === 1 ? 'Consolas' : 'Producto' }}</span>
                    <h1 class="text-3xl md:text-5xl font-black mt-2 mb-4 tracking-tight leading-tight text-white">{{ product.name }}</h1>
                    
                    <!-- Stars Aesthetic -->
                    <div class="flex items-center gap-1 text-neon-blue mb-4 text-sm">
                        <span>★★★★★</span>
                        <span class="text-xs text-gray-500 ml-2">(128 opiniones)</span>
                    </div>

                    <p class="text-gray-400 text-sm md:text-base leading-relaxed mb-6">{{ product.description }}</p>
                </div>

                <!-- Price and Buy Layout -->
                <div class="bg-gamer-card border border-gray-800 rounded-2xl p-6 mb-8 shadow-xl shadow-neon-blue/5">
                    <div class="flex justify-between items-baseline mb-4 border-b border-gray-800 pb-4">
                        <span class="text-xs font-bold text-gray-400">PRECIO</span>
                        <p class="text-3xl font-black text-neon-green">${{ product.price }}</p>
                    </div>

                    <!-- Quantity counter selector -->
                    <div class="flex items-center gap-4 mb-6">
                        <span class="text-xs font-bold text-gray-400 uppercase">Cantidad:</span>
                        <div class="flex items-center bg-gray-900 border border-gray-800 rounded-xl px-2 py-1">
                            <button @click="quantity > 1 ? quantity-- : null" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white">-</button>
                            <span class="w-8 text-center text-sm font-bold">{{ quantity }}</span>
                            <button @click="quantity++" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white">+</button>
                        </div>
                    </div>

                    <!-- Buy actions -->
                    <button @click="addToCart" class="w-full bg-gradient-to-r from-neon-purple to-neon-blue hover:from-neon-purple/90 hover:to-neon-blue/90 text-white py-3 rounded-xl font-black text-sm uppercase tracking-wider transition duration-300 shadow-neon-purple/20 flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
                        Añadir al Carrito
                    </button>
                </div>

                <!-- Inside the box list layout -->
                <div class="border border-gray-800 rounded-2xl p-6 bg-gamer-card/30">
                    <h4 class="text-xs font-black text-gray-300 uppercase mb-3">En la caja:</h4>
                    <ul class="space-y-1.5 text-xs text-gray-400">
                        <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-blue rounded-full"></div> 1x {{ product.name }}</li>
                        <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-blue rounded-full"></div> 1x Manual de instrucciones</li>
                        <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-blue rounded-full"></div> 1x Código de garantía de 2 años</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Related products rows slider -->
        <section v-if="relatedProducts.length > 0">
            <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <div class="w-1 h-3 bg-neon-purple"></div>
                    <h2 class="text-sm font-bold uppercase tracking-wider text-gray-200">Completa tu escuadrón</h2>
                </div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <router-link :to="`/products/${rel.id}`" v-for="rel in relatedProducts" :key="rel.id" class="group bg-gamer-card border border-gray-800/80 rounded-2xl overflow-hidden hover:border-neon-purple/30 hover:scale-[1.01] transition duration-300 flex flex-col h-full relative">
                    <div class="h-48 flex items-center justify-center overflow-hidden relative bg-black/5">
                        <img v-if="rel.image_url" :src="rel.image_url" :alt="rel.name" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <span v-else class="text-gray-600 text-xs">Sin foto</span>
                    </div>

                    <div class="p-4 flex flex-col flex-grow">
                        <h3 class="font-bold text-xs text-gray-200 line-clamp-1 mb-1 group-hover:text-neon-purple transition">{{ rel.name }}</h3>
                        <p class="mt-auto text-neon-green font-black text-sm">${{ rel.price }}</p>
                    </div>
                </router-link>
            </div>
        </section>
    </div>
  </div>
</template>
