<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import ProductCard from './ProductCard.vue';

const props = defineProps({
  products: {
    type: Array,
    required: true
  },
  title: {
    type: String,
    default: ''
  },
  accentColor: {
    type: String,
    default: 'neon-blue'
  },
  viewAllLink: {
    type: String,
    default: '/products'
  }
});

const scrollContainer = ref(null);
let autoPlayInterval = null;

// Drag state
const isDown = ref(false);
const isDragging = ref(false);
const startX = ref(0);
const scrollLeftStart = ref(0);

const startAutoPlay = () => {
    autoPlayInterval = setInterval(() => {
        if (scrollContainer.value && !isDown.value) {
            const container = scrollContainer.value;
            const maxScroll = container.scrollWidth - container.clientWidth;
            
            if (container.scrollLeft >= maxScroll - 5) {
                container.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
                container.scrollBy({ left: 240, behavior: 'smooth' });
            }
        }
    }, 4500);
};

const stopAutoPlay = () => {
    if (autoPlayInterval) {
        clearInterval(autoPlayInterval);
    }
};

const handleMouseDown = (e) => {
    isDown.value = true;
    isDragging.value = false; // Reset drag state on each click
    startX.value = e.pageX - scrollContainer.value.offsetLeft;
    scrollLeftStart.value = scrollContainer.value.scrollLeft;
    // Deshabilitar scroll-behavior smooth durante el drag para respuesta inmediata
    scrollContainer.value.style.scrollBehavior = 'auto';
};

const handleMouseLeave = () => {
    isDown.value = false;
    isDragging.value = false;
    if (scrollContainer.value) {
        scrollContainer.value.style.scrollBehavior = 'smooth';
    }
};

const handleMouseUp = () => {
    // Si no hubo arrastre significativo, es un click
    isDown.value = false;
    // Necesitamos un pequeño delay para que el click del router-link se procese antes de falsear isDragging
    setTimeout(() => {
        isDragging.value = false;
    }, 50);

    if (scrollContainer.value) {
        scrollContainer.value.style.scrollBehavior = 'smooth';
    }
};

const handleMouseMove = (e) => {
    if (!isDown.value) return;
    
    const x = e.pageX - scrollContainer.value.offsetLeft;
    const walk = (x - startX.value) * 2; // Multiplicador de velocidad
    
    // Solo marcamos como "drag" si el movimiento es superior a 5 píxeles
    if (Math.abs(x - startX.value) > 5) {
        isDragging.value = true;
    }

    if (isDragging.value) {
        e.preventDefault();
        scrollContainer.value.scrollLeft = scrollLeftStart.value - walk;
    }
};

const scroll = (direction) => {
    if (scrollContainer.value) {
        scrollContainer.value.style.scrollBehavior = 'smooth';
        const amount = direction === 'left' ? -300 : 300;
        scrollContainer.value.scrollBy({ left: amount, behavior: 'smooth' });
    }
};

onMounted(() => {
    startAutoPlay();
});

onUnmounted(() => {
    stopAutoPlay();
});
</script>

<template>
  <section class="mb-16 relative group" @mouseenter="stopAutoPlay" @mouseleave="startAutoPlay">
    <!-- Encabezado del Carrusel -->
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 pl-4"
            :class="`border-${accentColor}`">
            <template v-if="title.includes(' ')">
                {{ title.split(' ')[0] }} <span :class="`text-${accentColor}`">{{ title.split(' ').slice(1).join(' ') }}</span>
            </template>
            <template v-else>
                <span :class="`text-${accentColor}`">{{ title }}</span>
            </template>
        </h2>
        <router-link :to="viewAllLink" :class="`text-sm font-bold text-gray-500 hover:text-${accentColor} transition uppercase tracking-widest`">
            Ver todo
        </router-link>
    </div>

    <!-- Contenedor del Carrusel -->
    <div class="relative">
        <!-- Botones de navegación (solo visibles en hover) -->
        <button @click="scroll('left')" 
                class="absolute -left-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/50 border border-white/10 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-0 -translate-x-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
            </svg>
        </button>

        <div ref="scrollContainer" 
             @mousedown="handleMouseDown"
             @mouseleave="handleMouseLeave"
             @mouseup="handleMouseUp"
             @mousemove="handleMouseMove"
             class="flex overflow-x-auto gap-4 pb-4 scroll-smooth hide-scrollbar snap-x snap-mandatory px-1 select-none cursor-grab active:cursor-grabbing">
            <div v-for="product in products" 
                 :key="product.id" 
                 :class="[
                   'w-[70%] sm:w-[45%] lg:w-[30%] xl:w-[22%] flex-shrink-0 snap-start',
                   isDragging ? 'pointer-events-none' : 'pointer-events-auto'
                 ]">
                <ProductCard :product="product" />
            </div>
        </div>

        <button @click="scroll('right')" 
                class="absolute -right-4 top-1/2 -translate-y-1/2 z-20 w-10 h-10 bg-black/50 border border-white/10 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-all opacity-0 group-hover:opacity-100 group-hover:translate-x-0 translate-x-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
            </svg>
        </button>
    </div>
  </section>
</template>

<style scoped>
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
