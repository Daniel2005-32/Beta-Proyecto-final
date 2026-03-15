<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const cartItems = ref(JSON.parse(localStorage.getItem('cart') || '[]'));
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
const loading = ref(false);

const cartTotal = computed(() => {
    return cartItems.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const increaseQuantity = (index) => {
    cartItems.value[index].quantity += 1;
    localStorage.setItem('cart', JSON.stringify(cartItems.value));
    window.dispatchEvent(new Event('cart-updated'));
};

const decreaseQuantity = (index) => {
    if (cartItems.value[index].quantity > 1) {
        cartItems.value[index].quantity -= 1;
    } else {
        cartItems.value.splice(index, 1);
    }
    localStorage.setItem('cart', JSON.stringify(cartItems.value));
    window.dispatchEvent(new Event('cart-updated'));
};

const removeFromCart = (index) => {
    cartItems.value.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cartItems.value));
    window.dispatchEvent(new Event('cart-updated'));
};

const proceedToCheckout = async () => {
    if (!localStorage.getItem('token')) {
        alert("Debes iniciar sesión para finalizar la compra.");
        router.push('/login');
        return;
    }
    router.push('/checkout');
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    <div class="mb-8 border-b border-gray-800 pb-4 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-tight text-neon-blue">Carrito de Compras</h1>
            <p class="text-gray-400 text-xs mt-1">Gestiona tus artículos antes de proceder al pago.</p>
        </div>
        <router-link to="/products" class="text-xs font-bold text-gray-400 hover:text-white transition">&larr; Volver al catálogo</router-link>
    </div>

    <div v-if="cartItems.length === 0" class="text-center py-24 bg-gamer-card border border-gray-800 rounded-3xl">
        <div class="mb-4 text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
            <p class="text-sm">Tu carrito está completamente vacío.</p>
        </div>
        <router-link to="/products" class="inline-block bg-neon-blue text-gamer-dark px-6 py-2 rounded-xl font-bold text-sm tracking-wide hover:scale-105 transition shadow-neon-blue">
            Explorar Productos
        </router-link>
    </div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Lista de Items -->
        <div class="col-span-1 lg:col-span-2 space-y-4">
            <div class="bg-gamer-card border border-gray-800 rounded-2xl divide-y divide-gray-800/50 overflow-hidden">
                <div v-for="(item, index) in cartItems" :key="item.id" class="p-4 flex items-center gap-4 hover:bg-white/[0.02] transition">
                    <!-- Image -->
                    <div class="h-20 w-20 bg-black/20 rounded-xl overflow-hidden border border-gray-800 flex items-center justify-center flex-shrink-0">
                        <img v-if="item.image_url" :src="item.image_url" :alt="item.name" class="w-full h-full object-cover">
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>

                    <!-- Details -->
                    <div class="flex-grow">
                        <h3 class="font-bold text-sm text-gray-200 line-clamp-1 mb-1">{{ item.name }}</h3>
                        <p class="text-xs text-gray-500 mb-2">Precio: ${{ item.price }}</p>

                        <!-- Quantity Stepper -->
                        <div class="flex items-center bg-gray-900 border border-gray-800 rounded-lg px-1.5 py-0.5 w-max">
                            <button @click="decreaseQuantity(index)" class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-white text-xs">-</button>
                            <span class="w-6 text-center text-xs font-bold">{{ item.quantity }}</span>
                            <button @click="increaseQuantity(index)" class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-white text-xs">+</button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-right">
                        <p class="font-black text-neon-green text-sm">${{ (item.price * item.quantity).toFixed(2) }}</p>
                        <button @click="removeFromCart(index)" class="text-[10px] text-red-400 hover:text-red-300 hover:underline mt-2">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumen De Orden -->
        <div class="col-span-1">
            <div class="bg-gamer-card border border-gray-800 rounded-2xl p-6 sticky top-24">
                <div class="flex items-center gap-2 mb-6 pb-2 border-b border-gray-800">
                    <div class="w-1 h-3 bg-neon-purple"></div>
                    <h3 class="font-bold text-sm uppercase tracking-wider text-gray-200">Resumen de Orden</h3>
                </div>

                <div class="space-y-3 text-xs text-gray-400 mb-6">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="text-white font-bold">${{ cartTotal.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Envío</span>
                        <span class="text-neon-green font-bold">Gratis</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Impuestos incluidos</span>
                        <span>$0.00</span>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-4 mb-6">
                    <div class="flex justify-between items-baseline">
                        <span class="font-bold text-sm text-gray-200">TOTAL</span>
                        <span class="font-black text-xl text-neon-green shadow-neon-green/10">${{ cartTotal.toFixed(2) }}</span>
                    </div>
                </div>
                
                <button @click="proceedToCheckout" class="w-full bg-gradient-to-r from-neon-purple to-neon-blue hover:from-neon-purple/90 hover:to-neon-blue/90 text-white py-3 rounded-xl font-black text-sm uppercase tracking-wider transition duration-300 shadow-neon-purple/20 flex items-center justify-center gap-2">
                    Proceder al Pago
                </button>
            </div>
        </div>
    </div>
  </div>
</template>
