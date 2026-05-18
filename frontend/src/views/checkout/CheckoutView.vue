<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';
import LoadingState from '../../components/LoadingState.vue';
import { store } from '../../utils/store';
import { translateLaravelErrors } from '../../utils/errorTranslator';


const router = useRouter();
import { apiBase } from '@/utils/api';
const cart = ref([]);
const addresses = ref([]);
const selectedAddressId = ref(null);
const loading = ref(true);
const processing = ref(false);
const error = ref(null);

onMounted(async () => {
    const token = localStorage.getItem('token');
    if (!store.token) {
        router.push('/register');
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

const selectedAddress = computed(() => {
    return addresses.value.find(a => a.id === selectedAddressId.value);
});

const taxRate = computed(() => {
    if (!selectedAddress.value) return 0.21; // Default
    const state = selectedAddress.value.state ? selectedAddress.value.state.toUpperCase().trim() : '';
    const canaryIslands = ['GC', 'TF', 'LP', 'LZ', 'FV', 'EH'];
    return canaryIslands.includes(state) ? 0.07 : 0.21;
});

const discountPercentage = computed(() => (store.appliedPoints * 0.1).toFixed(1));
const pointsDiscountAmount = computed(() => subtotal.value * (parseFloat(discountPercentage.value) / 100));
const couponDiscountAmount = computed(() => store.appliedCoupon ? store.appliedCoupon.discount : 0);

const discountedSubtotal = computed(() => Math.max(0, subtotal.value - pointsDiscountAmount.value - couponDiscountAmount.value));
const taxAmount = computed(() => discountedSubtotal.value * taxRate.value);

const total = computed(() => discountedSubtotal.value + taxAmount.value);

const cardForm = ref({
    number: '',
    expiry: '',
    cvv: ''
});

const formatCardNumber = (e) => {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let parts = [];
    for (let i = 0; i < value.length; i += 4) {
        parts.push(value.substring(i, i + 4));
    }
    cardForm.value.number = parts.join(' ');
};

const formatExpiry = (e) => {
    let value = e.target.value.replace(/[^0-9]/g, '');
    let month = value.substring(0, 2);
    let year = value.substring(2, 4);

    if (month.length === 2) {
        if (parseInt(month) > 12) month = '12';
        if (parseInt(month) === 0) month = '01';
    }

    if (year.length === 2) {
        let yrVal = parseInt(year);
        if (yrVal < 26) year = '26';
        if (yrVal > 99) year = '99';
    }

    if (value.length > 2) {
        cardForm.value.expiry = month + '/' + year;
    } else {
        cardForm.value.expiry = month;
    }
};

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
            points_used: store.appliedPoints,
            coupon_code: store.appliedCoupon?.code,
            cart: cart.value.map(item => ({
                id: item.id,
                quantity: item.quantity
            })),
            payment: cardForm.value // Enviar datos de tarjeta por si acaso
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });

        // Clear cart on success
        store.updateCart([]);
        store.clearCoupon();

        // Redirect to profile immediately
        router.push('/profile');
        store.notify("¡Pedido realizado con éxito!");

    } catch (err) {
        error.value = translateLaravelErrors(err.response?.data?.error || err.response?.data?.message || "Ocurrió un error al procesar tu pedido.");
    } finally {
        processing.value = false;
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-5xl text-white">
    <h1 class="text-3xl font-black uppercase italic tracking-tighter border-l-4 border-neon-blue pl-4 mb-8">Finalizar <span class="text-neon-blue">Compra</span></h1>

    <LoadingState v-if="loading" />
    
    <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Details Forms -->
        <div class="lg:col-span-2 space-y-6">
            
            <div v-if="error" class="bg-red-900/50 border border-red-500 text-white p-4 rounded-xl text-xs">{{ error }}</div>

            <!-- Addresses -->
            <div class="bg-gamer-card p-6 rounded-2xl border border-gray-800 shadow-xl">
                <div class="flex justify-between items-center mb-4 pb-2 border-b border-gray-800">
                     <h2 class="text-xl font-bold text-white">Dirección de Envío</h2>
                     <router-link :to="{ path: '/profile/addresses', query: { redirect: '/checkout' } }" class="text-xs text-neon-blue hover:underline">Añadir nueva</router-link>
                </div>

                <div v-if="addresses.length === 0" class="text-gray-500 italic p-4 bg-gray-900/50 rounded-xl border border-gray-800">
                    No tienes ninguna dirección registrada.
                </div>
                
                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label v-for="address in addresses" :key="address.id" 
                           class="border border-gray-800 rounded-xl p-4 cursor-pointer relative bg-gray-900/30 hover:bg-gray-800/50 transition"
                           :class="{'border-neon-blue bg-neon-blue/10': selectedAddressId === address.id}">
                        
                        <input type="radio" v-model="selectedAddressId" :value="address.id" class="absolute top-4 right-4 text-neon-blue accent-neon-blue">
                        <p class="font-bold text-gray-200">{{ address.name }}</p>
                        <p class="text-sm text-gray-400">{{ address.street }} {{ address.number }}</p>
                        <p class="text-sm text-gray-400">{{ address.city }}, {{ address.state }}</p>
                    </label>
                </div>
            </div>

            <!-- Payment Method (Card Details) -->
            <div class="bg-gamer-card p-6 rounded-2xl border border-gray-800 shadow-xl border-l-4 border-neon-blue">
                <h2 class="text-xl font-bold mb-4 text-white">Método de Pago (Tarjeta)</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Número de Tarjeta (16 dígitos)</label>
                        <input v-model="cardForm.number" @input="formatCardNumber" type="text" maxlength="19" placeholder="0000 0000 0000 0000" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-white text-sm focus:border-neon-blue focus:outline-none transition">
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs uppercase text-gray-400 mb-1">Caducidad</label>
                            <input v-model="cardForm.expiry" @input="formatExpiry" type="text" maxlength="5" placeholder="MM/AA" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-white text-sm focus:border-neon-blue focus:outline-none transition">
                        </div>
                        <div>
                            <label class="block text-xs uppercase text-gray-400 mb-1">CVV</label>
                            <input v-model="cardForm.cvv" @input="cardForm.cvv = cardForm.cvv.replace(/[^0-9]/g, '')" type="text" maxlength="3" placeholder="123" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-white text-sm focus:border-neon-blue focus:outline-none transition">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Order Summary -->
        <div class="bg-gamer-card p-6 rounded-2xl border border-gray-800 shadow-xl h-fit sticky top-4">
            <h2 class="text-xl font-bold mb-4 pb-2 border-b border-gray-800 text-white">Resumen del Pedido</h2>
            
            <div class="space-y-4 mb-6 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                <div v-for="item in cart" :key="item.id" class="flex justify-between items-center border-b border-gray-800/50 pb-2">
                    <div class="flex items-center gap-2">
                        <div class="relative w-12 h-12 bg-gray-900 border border-gray-800 rounded-lg overflow-hidden flex items-center justify-center">
                            <img v-if="item.image_url" :src="item.image_url" loading="lazy" class="max-w-full max-h-full object-cover transition duration-300" :class="{'blur-sm scale-110': item.is_censored && (!store.user || !store.user.show_censored_content)}">
                            <div v-if="item.is_censored && (!store.user || !store.user.show_censored_content)" class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="text-[7px] font-black text-red-500 uppercase tracking-tighter">Censurado</span>
                            </div>
                            <span v-else-if="!item.image_url" class="text-[8px] text-gray-700">Sin Img</span>
                        </div>
                        <div>
                            <p class="font-bold text-sm text-gray-200 line-clamp-1 w-32" :title="item.full_name || item.name">{{ item.full_name || item.name }}</p>
                            <p class="text-xs text-gray-500">Cant: {{ item.quantity }}</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-200">{{(item.price * item.quantity).toFixed(2)}}€</span>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-4">
                <div class="flex justify-between mb-2 text-sm">
                    <span class="text-gray-400">Subtotal</span>
                    <span class="font-bold text-white">{{subtotal.toFixed(2)}}€</span>
                </div>
                <div class="flex justify-between mb-2 text-sm text-neon-green" v-if="store.appliedPoints > 0">
                    <span>Descuento Soul Points ({{ discountPercentage }}%)</span>
                    <span class="font-bold">-{{pointsDiscountAmount.toFixed(2)}}€</span>
                </div>
                <div class="flex justify-between mb-2 text-sm text-yellow-400" v-if="store.appliedCoupon">
                    <span>Cupón: {{ store.appliedCoupon.code }}</span>
                    <span class="font-bold">-{{couponDiscountAmount.toFixed(2)}}€</span>
                </div>
                <div class="flex justify-between mb-2 text-sm">
                    <span class="text-gray-400">Impuestos ({{ Math.round(taxRate * 100) }}% {{ taxRate === 0.07 ? 'IGIC' : 'IVA' }})</span>
                    <span class="font-bold text-white">{{taxAmount.toFixed(2)}}€</span>
                </div>
                <div class="flex justify-between mb-2 text-sm">
                    <span class="text-gray-400">Envío</span>
                    <span class="text-white font-bold">Gratis</span>
                </div>
                <div class="flex justify-between mt-4 mb-6">
                    <span class="text-xl font-black text-white">Total</span>
                    <span class="text-xl font-black text-white">{{total.toFixed(2)}}€</span>
                </div>

                <button @click="processCheckout" 
                        :disabled="processing || !selectedAddressId"
                        class="w-full bg-neon-blue text-gamer-dark font-black py-3 px-4 rounded-xl hover:bg-white hover:shadow-neon-blue transition duration-300 disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wider text-sm">
                    {{ processing ? 'Procesando...' : 'Confirmar Pedido' }}
                </button>
            </div>
        </div>
    </div>
  </div>
</template>
