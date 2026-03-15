<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
const cart = ref([]);
const addresses = ref([]);
const selectedAddressId = ref(null);
const loading = ref(true);
const processing = ref(false);
const error = ref(null);

onMounted(async () => {
    const token = localStorage.getItem('token');
    if (!token) {
        router.push('/login');
        return;
    }

    // Load cart from LocalStorage
    const storedCart = JSON.parse(localStorage.getItem('cart') || '[]');
    if (storedCart.length === 0) {
        router.push('/cart');
        return;
    }
    cart.value = storedCart;

    // Load user addresses
    try {
        const res = await axios.get(`${apiBase}/addresses`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        addresses.value = res.data.addresses || [];
        
        // Auto-select default address
        const defaultAddr = addresses.value.find(a => a.is_default);
        if (defaultAddr) selectedAddressId.value = defaultAddr.id;

    } catch (err) {
        error.value = "No se pudieron cargar tus direcciones.";
    } finally {
        loading.value = false;
    }
});

const subtotal = computed(() => {
    return cart.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const processCheckout = async () => {
    if (!selectedAddressId.value) {
        error.value = "Por favor selecciona una dirección de envío.";
        return;
    }

    processing.value = true;
    error.value = null;

    try {
        const token = localStorage.getItem('token');
        const res = await axios.post(`${apiBase}/checkout`, {
            address_id: selectedAddressId.value,
            cart: cart.value.map(item => ({
                id: item.id,
                quantity: item.quantity
            }))
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });

        // Clear cart on success
        localStorage.removeItem('cart');
        // Dispatch event to update navbar standardizing cart icon
        window.dispatchEvent(new Event('cart-updated'));
        
        // Redirect to success or profile orders
        alert("¡Pedido realizado con éxito!");
        router.push('/profile');

    } catch (err) {
        error.value = err.response?.data?.error || "Ocurrió un error al procesar tu pedido.";
    } finally {
        processing.value = false;
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-5xl">
    <h1 class="text-3xl font-bold mb-8">Finalizar Compra</h1>

    <div v-if="loading" class="text-center py-12 text-gray-500">Cargando información...</div>
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Details Forms -->
        <div class="lg:col-span-2 space-y-6">
            
            <div v-if="error" class="bg-red-100 text-red-800 p-4 rounded">{{ error }}</div>

            <!-- Addresses -->
            <div class="bg-white p-6 rounded shadow">
                <div class="flex justify-between items-center mb-4">
                     <h2 class="text-xl font-bold">Dirección de Envío</h2>
                     <router-link to="/profile/addresses" class="text-sm text-indigo-600 hover:underline">Añadir nueva</router-link>
                </div>

                <div v-if="addresses.length === 0" class="text-gray-500 italic p-4 bg-gray-50 rounded">
                    No tienes ninguna dirección registrada.
                </div>
                
                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label v-for="address in addresses" :key="address.id" 
                           class="border rounded p-4 cursor-pointer relative"
                           :class="{'border-emerald-500 bg-emerald-50': selectedAddressId === address.id}">
                        
                        <input type="radio" v-model="selectedAddressId" :value="address.id" class="absolute top-4 right-4 text-emerald-600">
                        <p class="font-bold text-gray-800">{{ address.name }}</p>
                        <p class="text-sm text-gray-600">{{ address.street }} {{ address.number }}</p>
                        <p class="text-sm text-gray-600">{{ address.city }}, {{ address.state }}</p>
                    </label>
                </div>
            </div>

            <!-- Payment Method (Mock) -->
            <div class="bg-white p-6 rounded shadow border-l-4 border-emerald-500">
                <h2 class="text-xl font-bold mb-4">Método de Pago</h2>
                <div class="p-4 border rounded bg-gray-50 flex items-center">
                    <input type="radio" checked class="mr-3" disabled>
                    <div>
                        <p class="font-bold text-gray-800">Pago a Contra Entrega</p>
                        <p class="text-sm text-gray-500">Pagas en efectivo o tarjeta al recibir tu paquete.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="bg-white p-6 rounded shadow h-fit sticky top-4">
            <h2 class="text-xl font-bold mb-4 pb-2 border-b">Resumen del Pedido</h2>
            
            <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2">
                <div v-for="item in cart" :key="item.id" class="flex justify-between items-center border-b pb-2">
                    <div class="flex items-center gap-2">
                        <img v-if="item.image_url" :src="item.image_url" class="w-12 h-12 object-cover rounded">
                        <div v-else class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center text-xs text-gray-400">Sin Img</div>
                        <div>
                            <p class="font-bold text-sm text-gray-800 line-clamp-1 w-32" :title="item.name">{{ item.name }}</p>
                            <p class="text-xs text-gray-500">Cant: {{ item.quantity }}</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-800">${{ (item.price * item.quantity).toFixed(2) }}</span>
                </div>
            </div>

            <div class="border-t pt-4">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Subtotal</span>
                    <span class="font-bold">${{ subtotal.toFixed(2) }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-600">Envío</span>
                    <span class="text-emerald-600 font-bold">Gratis</span>
                </div>
                <div class="flex justify-between mt-4 mb-6">
                    <span class="text-xl font-black">Total</span>
                    <span class="text-xl font-black text-emerald-600">${{ subtotal.toFixed(2) }}</span>
                </div>

                <button @click="processCheckout" 
                        :disabled="processing || !selectedAddressId"
                        class="w-full bg-gray-900 text-white font-bold py-3 px-4 rounded hover:bg-emerald-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    {{ processing ? 'Procesando...' : 'Confirmar Pedido' }}
                </button>
            </div>
        </div>
    </div>
  </div>
</template>
