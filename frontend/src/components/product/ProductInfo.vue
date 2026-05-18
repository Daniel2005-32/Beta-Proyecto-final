<script setup>
const props = defineProps({
    product: Object, // displayProduct
    seriesTitle: String,
    seriesParent: Object,
    allVolumes: Array,
    selectedVariant: Object,
    quantity: Number,
    isWishlisted: Boolean
});

const emit = defineEmits([
    'update:selectedVariant', 
    'update:quantity', 
    'add-to-cart', 
    'toggle-wishlist', 
    'start-auction'
]);

const updateQuantity = (val) => {
    if (val < 1) return;
    if (val > props.product.stock) return;
    emit('update:quantity', val);
};
</script>

<template>
    <div class="flex flex-col">
        <div class="mb-6">
            <span class="text-xs font-bold text-neon-blue uppercase tracking-widest">{{ product.category_id === 1 ? 'Consolas' : 'Producto' }}</span>
            <h1 class="text-3xl md:text-5xl font-black mt-2 mb-2 tracking-tight leading-tight text-white">{{ seriesTitle }}</h1>
            
            <!-- Franchise Badges -->
            <div class="flex flex-wrap gap-2 mb-4">
                <span v-if="product.is_anime" class="px-2 py-0.5 bg-neon-cyan/20 border border-neon-cyan/50 text-neon-cyan text-[10px] font-black uppercase rounded shadow-[0_0_15px_rgba(0,243,255,0.1)]">Anime</span>
                <span v-if="product.is_marvel" class="px-2 py-0.5 bg-neon-red/20 border border-neon-red/50 text-neon-red text-[10px] font-black uppercase rounded shadow-[0_0_15px_rgba(255,0,0,0.1)]">Marvel</span>
                <span v-if="product.is_star_wars" class="px-2 py-0.5 bg-neon-blue/20 border border-neon-blue/50 text-neon-blue text-[10px] font-black uppercase rounded shadow-[0_0_15px_rgba(0,119,255,0.1)]">Star Wars</span>
                <span v-if="product.is_dc" class="px-2 py-0.5 bg-neon-purple/20 border border-neon-purple/50 text-neon-purple text-[10px] font-black uppercase rounded shadow-[0_0_15px_rgba(157,0,255,0.1)]">DC</span>
            </div>

            <p v-if="selectedVariant && selectedVariant.id !== seriesParent.id" class="text-xl font-bold text-neon-purple italic mb-4">Actual: {{ selectedVariant.name }}</p>
            <p v-else-if="selectedVariant && selectedVariant.id === seriesParent.id" class="text-xl font-bold text-neon-purple italic mb-4">Actual: Tomo 1</p>
            
            <!-- Stars Aesthetic -->
            <div class="flex items-center gap-1 text-neon-blue mb-4 text-sm">
                <span v-for="i in 5" :key="i">
                    {{ i <= Math.round(seriesParent.average_rating || 0) ? '★' : '☆' }}
                </span>
                <span class="text-xs text-gray-500 ml-2">({{ seriesParent.approved_reviews?.length || 0 }} valoraciones)</span>
            </div>

            <p class="text-gray-400 text-sm md:text-base leading-relaxed mb-6">{{ product.description }}</p>
        </div>

        <!-- Price and Buy Layout -->
        <div class="bg-gamer-card border border-gray-800 rounded-2xl p-6 mb-8 shadow-xl shadow-neon-blue/5">
            
            <!-- Selector de Variantes (Tomos) -->
            <div v-if="allVolumes.length > 1" class="mb-8 border-b border-gray-800 pb-6">
                <h3 class="text-xs font-black text-gray-300 uppercase mb-4 tracking-widest">Selecciona tu Ejemplar:</h3>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                    <button v-for="variant in allVolumes" 
                            :key="variant.id" 
                            @click="emit('update:selectedVariant', variant)"
                            :class="[
                                'px-2 py-2 text-[10px] font-bold border rounded-lg transition-all',
                                selectedVariant?.id === variant.id 
                                    ? 'bg-neon-blue text-gamer-dark border-neon-blue shadow-[0_0_10px_rgba(0,243,255,0.3)]' 
                                    : 'bg-black/40 border-gray-800 text-gray-400 hover:border-gray-600'
                            ]">
                        {{ variant.id === seriesParent.id && variant.name.includes('Vol. 1') ? 'Vol. 1' : variant.name.replace('Vol.', '').trim() }}
                    </button>
                </div>
            </div>

            <div class="flex justify-between items-baseline mb-4 border-b border-gray-800 pb-4">
                <span class="text-xs font-bold text-gray-400">PRECIO</span>
                <p class="text-3xl font-black text-white">{{product.price}}€</p>
            </div>

            <!-- Quantity counter selector -->
            <div class="flex items-center gap-4 mb-6">
                <span class="text-xs font-bold text-gray-400 uppercase">Cantidad:</span>
                <div class="flex items-center bg-gray-900 border border-gray-800 rounded-xl px-2 py-1">
                    <button @click="updateQuantity(quantity - 1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white" :disabled="product.stock <= 0">-</button>
                    <span class="w-8 text-center text-sm font-bold">{{ product.stock > 0 ? quantity : 0 }}</span>
                    <button @click="updateQuantity(quantity + 1)" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white" :disabled="product.stock <= 0">+</button>
                </div>
                <span v-if="product.stock > 0" class="text-white text-xs italic">({{ product.stock }} disponibles)</span>
                <span v-else class="text-xs text-red-500">Sin stock</span>
            </div>

            <!-- Iniciar Subasta Button (Para Productos Exclusivos con Stock 1) -->
            <div v-if="product.is_exclusive && parseFloat(product.stock) === 1 && !product.is_in_auction" class="mb-4">
                <p class="text-[11px] text-gray-400 mb-2 italic">⚠️ Si inicias la subasta, el precio base de puja tendrá un <strong>20% de descuento</strong>.</p>
                <button @click="emit('start-auction')" 
                        class="w-full bg-gradient-to-r from-neon-purple to-neon-purple/70 hover:from-neon-purple/90 hover:to-neon-purple/80 text-white py-3 rounded-xl font-black text-sm uppercase tracking-wider transition duration-300 shadow-neon-purple/20 flex items-center justify-center gap-2">
                    <i class="fas fa-gavel text-xs"></i>
                    Iniciar Subasta
                </button>
            </div>

            <!-- Buy actions -->
            <div class="flex flex-wrap gap-3 items-center">
                <button v-if="!(product.is_exclusive && parseFloat(product.stock) === 1 && !product.is_in_auction)" @click="emit('add-to-cart')" :disabled="product.stock <= 0" :class="product.stock <= 0 ? 'opacity-50 cursor-not-allowed' : ''" class="flex-1 py-3 bg-gradient-to-r from-neon-purple to-neon-blue hover:from-neon-purple/90 hover:to-neon-blue/90 text-white rounded-xl font-black text-sm uppercase tracking-wider transition duration-300 flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
                    {{ product.stock > 0 ? 'Añadir al Carrito' : 'Agotado' }}
                </button>

                <!-- Botón Favoritos -->
                <button 
                    @click="emit('toggle-wishlist')"
                    class="p-3 rounded-xl backdrop-blur-md border border-white/10 transition-all duration-300 group/wish hover:scale-110 active:scale-95 flex items-center justify-center"
                    :class="isWishlisted ? 'bg-red-500/20 text-red-500 border-red-500/50 shadow-neon-red/20' : 'bg-black/40 text-gray-400 hover:text-white'"
                    title="Guardar en favoritos"
                >
                    <i :class="isWishlisted ? 'fas fa-heart' : 'far fa-heart'" class="text-lg"></i>
                </button>
            </div>
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
</template>
