<script setup>
defineProps({
    items: {
        type: Array,
        required: true,
        // Item structure: { label: String, path: String, active: Boolean }
    }
});
</script>

<template>
    <nav class="flex mb-6 overflow-x-auto no-scrollbar py-2" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3 whitespace-nowrap">
            <li class="inline-flex items-center">
                <router-link to="/" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-neon-blue transition-colors">
                    <i class="fas fa-home mr-2 text-[12px]"></i>
                    Inicio
                </router-link>
            </li>
            <li v-for="(item, index) in items" :key="index">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-[8px] text-gray-600 mx-2"></i>
                    <router-link v-if="!item.active" :to="item.path" class="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-neon-blue transition-colors">
                        {{ item.label }}
                    </router-link>
                    <span v-else class="text-[10px] font-black uppercase tracking-widest text-neon-purple cursor-default">
                        {{ item.label }}
                    </span>
                </div>
            </li>
        </ol>
    </nav>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>

