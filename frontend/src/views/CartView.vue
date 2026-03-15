<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const cartItems = ref(JSON.parse(localStorage.getItem('cart') || '[]'));
const apiBase = 'http://localhost:8000/api';
const loading = ref(false);

const cartTotal = computed(() => {
    return cartItems.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const removeFromCart = (index) => {
    cartItems.value.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cartItems.value));
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
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Carrito de Compras</h1>
        <router-link to="/products" class="text-emerald-600 hover:underline">Continuar Comprando</router-link>
    </div>

    <div v-if="cartItems.length === 0" class="text-center py-12 text-gray-500">
        <p class="mb-4">Tu carrito está vacío.</p>
        <router-link to="/products" class="bg-emerald-600 text-white px-6 py-2 rounded font-bold hover:bg-emerald-700 transition">
            Ver Productos
        </router-link>
    </div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="col-span-1 lg:col-span-2">
            <div class="bg-white rounded shadow divide-y">
                <div v-for="(item, index) in cartItems" :key="item.id" class="p-4 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg">{{ item.name }}</h3>
                        <p class="text-gray-500">Cantidad: {{ item.quantity }} x ${{ item.price }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <p class="font-bold text-emerald-600">${{ item.price * item.quantity }}</p>
                        <button @click="removeFromCart(index)" class="text-red-500 hover:text-red-700">Eliminar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-1">
            <div class="bg-gray-50 p-6 rounded shadow">
                <h3 class="font-bold text-xl mb-4 border-b pb-2">Resumen</h3>
                <div class="flex justify-between mb-2">
                    <span>Subtotal</span>
                    <span>${{ cartTotal }}</span>
                </div>
                <div class="flex justify-between border-t pt-2 mt-4">
                    <span class="font-bold">Total</span>
                    <span class="font-bold text-xl text-emerald-600">${{ cartTotal }}</span>
                </div>
                
                <button @click="proceedToCheckout" class="w-full mt-6 bg-emerald-600 text-white py-3 rounded font-bold hover:bg-emerald-700 transition">
                    Proceder al Pago
                </button>
            </div>
        </div>
    </div>
  </div>
</template>
