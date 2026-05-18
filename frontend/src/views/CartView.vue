<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';

import axios from 'axios';
import { store } from '../utils/store';
import Breadcrumbs from '@/components/Breadcrumbs.vue';

const router = useRouter();
const cartItems = computed(() => store.cart);
import { apiBase } from '@/utils/api';
const loading = ref(false);

const cartTotal = computed(() => {
    return cartItems.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const increaseQuantity = (index) => {
    const items = [...store.cart];
    if (items[index].quantity < items[index].stock) {
        items[index].quantity += 1;
        store.updateCart(items);
    }
};

const decreaseQuantity = (index) => {
    const items = [...store.cart];
    if (items[index].quantity > 1) {
        items[index].quantity -= 1;
    }
    store.updateCart(items);
};

const removeFromCart = (index) => {
    const items = [...store.cart];
    items.splice(index, 1);
    store.updateCart(items);
};

const proceedToCheckout = async () => {
    if (!store.token) {
        router.push({ path: '/register', query: { message: 'Primero debes registrarte para finalizar la compra.' } });
        return;
    }
    router.push('/checkout');
};

const userPoints = computed(() => store.user?.points || 0);
const maxRedeemablePoints = computed(() => {
    // Máximo 150 puntos por pedido (sincronizado con el backend)
    return 150;
});

const pointsToApply = ref(store.appliedPoints || 0);

const couponCode = ref('');
const couponDiscount = computed(() => store.appliedCoupon ? store.appliedCoupon.discount : 0);

const discountPercentage = computed(() => (pointsToApply.value * 0.1).toFixed(1));
const pointsDiscountAmount = computed(() => cartTotal.value * (parseFloat(discountPercentage.value) / 100));
const finalTotal = computed(() => cartTotal.value - pointsDiscountAmount.value - couponDiscount.value);

const updateAppliedPoints = () => {
    if (pointsToApply.value > userPoints.value) pointsToApply.value = userPoints.value;
    if (pointsToApply.value > maxRedeemablePoints.value) pointsToApply.value = maxRedeemablePoints.value;
    store.setAppliedPoints(pointsToApply.value);
};

const applyCoupon = async () => {
    if (!couponCode.value) return;
    loading.value = true;
    await store.validateCoupon(couponCode.value, cartTotal.value);
    loading.value = false;
};

const removeCoupon = () => {
    store.clearCoupon();
    couponCode.value = '';
};

</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    <Breadcrumbs :items="[{ label: 'Carrito', path: '', active: true }]" />
    
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
                    <div class="h-20 w-20 bg-black/20 rounded-xl overflow-hidden border border-gray-800 flex items-center justify-center flex-shrink-0 relative">
                        <img v-if="item.image_url" :src="item.image_url" :alt="item.name" loading="lazy" class="w-full h-full object-cover" :class="{'blur-md scale-110': item.is_censored && (!store.user || !store.user.show_censored_content)}">
                        <div v-if="item.is_censored && (!store.user || !store.user.show_censored_content)" class="absolute inset-0 bg-black/40 flex items-center justify-center">
                           <span class="text-[8px] font-black text-red-500 uppercase tracking-tighter text-center">Sensible</span>
                        </div>
                        <svg v-else-if="!item.image_url" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>

                    <!-- Details -->
                    <div class="flex-grow">
                        <h3 class="font-bold text-sm text-gray-200 line-clamp-1 mb-1">{{ item.full_name || item.name }}</h3>
                        <p class="text-xs text-gray-500 mb-2">Precio: {{item.price}}€</p>

                        <!-- Quantity Stepper -->
                        <div class="flex items-center bg-gray-900 border border-gray-800 rounded-lg px-1.5 py-0.5 w-max">
                            <button @click="decreaseQuantity(index)" class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-white text-xs">-</button>
                            <span class="w-6 text-center text-xs font-bold">{{ item.quantity }}</span>
                            <button @click="increaseQuantity(index)" :disabled="item.quantity >= item.stock" :class="{'opacity-50 cursor-not-allowed': item.quantity >= item.stock}" class="w-5 h-5 flex items-center justify-center text-gray-400 hover:text-white text-xs">+</button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-right">
                        <p class="font-black text-white text-sm">{{(item.price * item.quantity).toFixed(2)}}€</p>
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
                        <span class="text-white font-bold">{{cartTotal.toFixed(2)}}€</span>
                    </div>
                    <div v-if="pointsToApply > 0" class="flex justify-between text-neon-green">
                        <span>Descuento Soul Points ({{ discountPercentage }}%)</span>
                        <span class="font-bold">-{{pointsDiscountAmount.toFixed(2)}}€</span>
                    </div>
                    <div v-if="store.appliedCoupon" class="flex justify-between text-yellow-400">
                        <span>Cupón: {{ store.appliedCoupon.code }} ({{ store.appliedCoupon.type === 'percentage' ? store.appliedCoupon.value + '%' : 'Fijo' }})</span>
                        <span class="font-bold">-{{couponDiscount.toFixed(2)}}€</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Envío</span>
                        <span class="text-white font-bold">Gratis</span>
                    </div>
                </div>

                <!-- Points Logic -->
                <div v-if="store.token && userPoints > 0" class="mb-4 bg-black/30 border border-gray-800 rounded-xl p-4">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-[10px] font-black uppercase text-gray-500 tracking-wider">Usar Soul Points</span>
                        <span class="text-[10px] font-bold text-neon-blue">{{ pointsToApply }} / {{ Math.min(userPoints, maxRedeemablePoints) }}</span>
                    </div>
                    <input type="range" 
                           v-model.number="pointsToApply" 
                           :min="0" 
                           :max="Math.min(userPoints, maxRedeemablePoints)" 
                           step="10"
                           @input="updateAppliedPoints"
                           class="w-full h-1.5 bg-gray-800 rounded-lg appearance-none cursor-pointer accent-neon-blue mb-2">
                    <p class="text-[9px] text-gray-600 leading-tight">1 punto = 0.1% de ahorro. Límite de 150 pts por producto.</p>
                </div>

                <!-- Coupon Logic -->
                <div v-if="store.token" class="mb-6">
                    <div class="flex gap-2">
                        <input type="text" 
                               v-model="couponCode" 
                               placeholder="Código de cupón" 
                               class="flex-grow bg-black/30 border border-gray-800 rounded-xl px-3 py-2 text-xs focus:ring-1 focus:ring-neon-blue outline-none transition"
                               :disabled="store.appliedCoupon">
                        <button v-if="!store.appliedCoupon" 
                                @click="applyCoupon" 
                                :disabled="loading || !couponCode"
                                class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition disabled:opacity-50">
                            Aplicar
                        </button>
                        <button v-else 
                                @click="removeCoupon" 
                                class="bg-red-900/30 hover:bg-red-900/50 text-red-400 px-4 py-2 rounded-xl text-xs font-bold transition border border-red-900/50">
                            Quitar
                        </button>
                    </div>
                </div>

                <div class="border-t border-gray-800 pt-4 mb-6">
                    <div class="flex justify-between items-baseline">
                        <span class="font-bold text-sm text-gray-200">TOTAL</span>
                        <span class="font-black text-xl text-white shadow-white/10">{{finalTotal.toFixed(2)}}€</span>
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
