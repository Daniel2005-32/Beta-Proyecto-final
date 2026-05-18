<script setup>
defineProps({
    product: Object,
    shouldBlur: Boolean
});
</script>

<template>
    <div class="space-y-4">
        <div v-if="product.image_url" class="relative bg-gamer-card/40 backdrop-blur-sm rounded-3xl p-6 border border-white/5 aspect-square flex items-center justify-center overflow-hidden">
            <img :src="product.image_url" :alt="product.name" loading="lazy" class="max-w-full max-h-full object-contain drop-shadow-2xl transition-none" :class="{'blur-3xl scale-110': shouldBlur}">
            
            <!-- Censorship Overlay -->
            <div v-if="shouldBlur" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-black/60 backdrop-blur-md">
                <div class="bg-red-600 text-white text-xs font-black px-6 py-2 rounded-full shadow-2xl border border-red-500 animate-pulse uppercase tracking-widest">
                    Contenido Sensible
                </div>
                <p class="text-[10px] text-gray-400 mt-4 font-bold uppercase tracking-widest px-8 text-center max-w-xs leading-relaxed">
                    Esta imagen contiene contenido marcado como sensible. Puedes desactivar la censura en tu <router-link to="/profile" class="text-neon-blue hover:underline">perfil</router-link>.
                </p>
            </div>

            <div v-if="product.stock === 0" class="absolute inset-0 bg-black/60 flex items-center justify-center text-white font-black text-2xl uppercase tracking-tighter rotate-12 border-4 border-white/20">Agotado</div>
            
            <!-- Collector's Edition Badge -->
            <span v-if="product.featured" class="absolute top-4 left-4 z-30 bg-neon-purple/20 border border-neon-purple/50 text-neon-purple text-[10px] font-black uppercase px-2.5 py-1 rounded-md shadow-neon-purple/30 backdrop-blur-md">EDICIÓN COLECCIONISTA</span>
        </div>
        <span v-else class="aspect-square bg-gamer-card border border-gray-800 rounded-3xl overflow-hidden shadow-2xl flex items-center justify-center relative group text-gray-500">Sin Imagen</span>
    </div>
</template>
