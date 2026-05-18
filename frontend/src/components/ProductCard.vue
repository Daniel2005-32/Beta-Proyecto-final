<script setup>
import { computed } from 'vue';
import { store } from '../utils/store';

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
});

const isAuctionActive = computed(() => {
    return props.product.is_in_auction && props.product.auction_end_time && new Date(props.product.auction_end_time) > new Date();
});

const shouldBlur = computed(() => {
    return props.product.is_censored && (!store.user || !store.user.show_censored_content);
});
</script>

<template>
  <div class="relative group h-full">
    <!-- Glow Aura Backdrop on Hover -->
    <div class="absolute inset-x-0 inset-y-0 bg-gradient-to-br from-neon-blue via-neon-purple to-neon-red opacity-0 group-hover:opacity-20 blur-xl transition-all duration-500 rounded-2xl"></div>

    <div class="bg-gamer-card/90 backdrop-blur-md rounded-2xl overflow-hidden border border-white/5 hover:border-neon-blue/40 transition duration-300 shadow-xl relative flex flex-col h-full z-10 card-hover">
        <!-- Wishlist Button - Moved Inside for better hover behavior -->
        <button 
            @click.stop.prevent="store.toggleWishlist(product.id)"
            class="absolute top-4 left-4 z-30 p-2.5 rounded-xl backdrop-blur-md border border-white/10 transition-all duration-300 group/wishlist hover:scale-110 active:scale-95"
            :class="store.wishlist.includes(product.id) ? 'bg-red-500/20 text-red-500 border-red-500/50 shadow-neon-red/20' : 'bg-black/40 text-gray-400 hover:text-white'"
        >
            <i :class="store.wishlist.includes(product.id) ? 'fas fa-heart' : 'far fa-heart'" class="text-sm"></i>
            
            <!-- Tooltip -->
            <span class="absolute left-full ml-3 px-2 py-1 bg-black/90 text-[8px] text-white uppercase font-black rounded opacity-0 group-hover/wishlist:opacity-100 transition-opacity pointer-events-none whitespace-nowrap border border-gray-800">
                {{ store.wishlist.includes(product.id) ? 'Quitar de favoritos' : 'Añadir a favoritos' }}
            </span>
        </button>

        <!-- Imagen Section -->
        <router-link :to="`/products/${product.slug}`" class="block relative overflow-hidden aspect-square" :class="{'opacity-50 grayscale': product.stock === 0}">
            <img v-if="product.image_url" :src="product.image_url" :alt="product.name" loading="lazy" class="w-full h-full object-contain p-4 group-hover:scale-105 transition duration-500 z-10" :class="{'blur-2xl scale-110': shouldBlur}">
            <div v-else class="w-full h-full bg-gray-900 flex items-center justify-center text-gray-600 text-xs">Sin imagen</div>
            
            <!-- Overlay de Censura -->
            <div v-if="shouldBlur" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-black/40 backdrop-blur-sm">
                <div class="bg-red-600 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-lg border border-red-400 animate-pulse uppercase tracking-tighter">
                    Contenido Sensible
                </div>
                <p class="text-[8px] text-gray-300 mt-2 font-bold uppercase tracking-widest px-4 text-center">Ver en detalle para desbloquear</p>
            </div>
            
            <!-- Badge Descuento -->
            <div v-if="product.original_price && parseFloat(product.original_price) > parseFloat(product.price)" class="absolute top-4 right-4 bg-gradient-to-r from-neon-blue via-red-600 to-black text-white text-xs md:text-sm font-black px-4 py-2.5 rounded-xl shadow-[0_0_25px_rgba(157,0,255,0.4)] animate-pulse tracking-wider z-20">
                -{{ Math.round((1 - (product.price / product.original_price)) * 100) }}%
            </div>

            <!-- Franchise Badges -->
            <div class="absolute bottom-4 right-4 flex flex-col gap-1 z-20 items-end">
                <span v-if="product.is_anime" class="px-2 py-0.5 bg-neon-cyan/20 border border-neon-cyan/50 text-neon-cyan text-[8px] font-black uppercase rounded shadow-[0_0_10px_rgba(0,243,255,0.2)]">Anime</span>
                <span v-if="product.is_marvel" class="px-2 py-0.5 bg-neon-red/20 border border-neon-red/50 text-neon-red text-[8px] font-black uppercase rounded shadow-[0_0_10px_rgba(255,0,0,0.2)]">Marvel</span>
                <span v-if="product.is_star_wars" class="px-2 py-0.5 bg-neon-blue/20 border border-neon-blue/50 text-neon-blue text-[8px] font-black uppercase rounded shadow-[0_0_10px_rgba(0,119,255,0.2)]">Star Wars</span>
                <span v-if="product.is_dc" class="px-2 py-0.5 bg-neon-purple/20 border border-neon-purple/50 text-neon-purple text-[8px] font-black uppercase rounded shadow-[0_0_10px_rgba(157,0,255,0.2)]">DC</span>
            </div>
        </router-link>

        <!-- Información -->
        <div class="p-6 flex flex-col flex-grow">
            <router-link :to="`/products/${product.slug}`" class="hover:text-neon-blue transition">
                <h3 class="font-bold text-base text-white mb-1 truncate">{{ product.full_name || product.name }}</h3>
            </router-link>

            <p class="text-gray-400 text-xs mb-4 line-clamp-2">{{ product.description }}</p>

            <!-- Precios -->
            <div class="mt-auto flex justify-between items-center">
                <div>
                    <span v-if="product.original_price && parseFloat(product.original_price) > parseFloat(product.price)" class="text-2xl font-black text-[#FF0000] drop-shadow-[0_0_8px_rgba(255,0,0,0.3)] italic text-glow">{{ parseFloat(product.price).toFixed(2) }}€</span>
                    <span v-else class="text-2xl font-black text-white italic text-glow">{{ parseFloat(product.price).toFixed(2) }}€</span>
                    
                    <span v-if="product.original_price && parseFloat(product.original_price) > parseFloat(product.price)" class="text-sm text-gray-500 line-through ml-2">
                        {{ parseFloat(product.original_price).toFixed(2) }}€
                    </span>
                </div>
                
                <!-- Botón de Acción -->
                <router-link :to="isAuctionActive ? `/auctions/${product.id}` : `/products/${product.slug}`" class="p-2 bg-gray-800 rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-gray-400 hover:scale-105 shadow-lg" :title="isAuctionActive ? 'Ir a subasta' : 'Ver detalles'">
                    <svg v-if="isAuctionActive" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </router-link>
            </div>
        </div>
    </div>
  </div>
</template>

