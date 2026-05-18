<script setup>
defineProps({
    relatedProducts: Array
});
</script>

<template>
    <section v-if="relatedProducts.length > 0">
        <div class="flex items-center justify-between mb-6 pb-2 border-b border-gray-800">
            <div class="flex items-center gap-2">
                <div class="w-1 h-3 bg-neon-purple"></div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-200">Completa tu escuadrón</h2>
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <router-link :to="`/products/${rel.slug}`" v-for="rel in relatedProducts" :key="rel.id" class="group bg-gamer-card border border-gray-800/80 rounded-2xl overflow-hidden hover:border-neon-purple/30 hover:scale-[1.01] transition duration-300 flex flex-col h-full relative">
                <div class="h-48 flex items-center justify-center overflow-hidden relative bg-black/5">
                    <img v-if="rel.image_url" :src="rel.image_url" :alt="rel.name" loading="lazy" class="w-full h-full object-contain group-hover:scale-105 transition duration-500">
                    <span v-else class="text-gray-600 text-xs">Sin foto</span>
                </div>

                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="font-bold text-xs text-gray-200 line-clamp-1 mb-1 group-hover:text-neon-purple transition">{{ rel.name }}</h3>
                    <p class="mt-auto text-white font-black text-sm">{{rel.price}}€</p>
                </div>
            </router-link>
        </div>
    </section>
</template>
