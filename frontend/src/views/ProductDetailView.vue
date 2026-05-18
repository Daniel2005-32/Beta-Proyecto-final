<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import LoadingState from '../components/LoadingState.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { store } from '../utils/store';
import { apiBase } from '@/utils/api';

// Nuevos Componentes Modulares
import ProductGallery from '../components/product/ProductGallery.vue';
import ProductInfo from '../components/product/ProductInfo.vue';
import ProductReviews from '../components/product/ProductReviews.vue';
import ProductRelated from '../components/product/ProductRelated.vue';

const route = useRoute();
const router = useRouter();

const product = ref(null);
const relatedProducts = ref([]);
const loading = ref(true);
const error = ref(null);
const quantity = ref(1);

// Estado para reseñas
const reviewForm = ref({
    rating: 5,
    comment: ''
});
const submittingReview = ref(false);
const reviewError = ref(null);

// Estado para variantes/tomos
const selectedVariant = ref(null);

// El producto "Raíz" de la serie (el padre)
const seriesParent = computed(() => {
    if (!product.value) return null;
    return product.value.parent || product.value;
});

// El título limpio de la serie (sin "Vol. 1", etc)
const seriesTitle = computed(() => {
    if (!seriesParent.value) return '';
    return seriesParent.value.name.replace(/\s*(?:[Vv]ol\.?|[Vv]olumen|[Tt]omo)\s*\d+.*$/i, '').trim();
});

// Lista completa de tomos de la serie
const allVolumes = computed(() => {
    if (!seriesParent.value) return [];
    const children = seriesParent.value.children || [];
    const list = [seriesParent.value, ...children];
    const unique = Array.from(new Map(list.map(p => [p.id, p])).values());
    return unique.sort((a, b) => {
        return a.name.localeCompare(b.name, undefined, { numeric: true, sensitivity: 'base' });
    });
});

const displayProduct = computed(() => {
    return selectedVariant.value || product.value;
});

const shouldBlur = computed(() => {
    if (!displayProduct.value) return false;
    return displayProduct.value.is_censored && (!store.user || !store.user.show_censored_content);
});

const updateMetaTags = () => {
    if (!product.value) return;
    const title = `${product.value.name} | Soul Guild`;
    document.title = title;
    
    // OG Tags update
    document.querySelector('meta[property="og:site_name"]')?.setAttribute('content', 'Soul Guild');
    const ogTitle = document.querySelector('meta[property="og:title"]');
    if (ogTitle) ogTitle.setAttribute('content', title);
    
    const ogDesc = document.querySelector('meta[property="og:description"]');
    if (ogDesc) ogDesc.setAttribute('content', product.value.description?.substring(0, 150) + '...');
    
    const ogImg = document.querySelector('meta[property="og:image"]');
    if (ogImg) ogImg.setAttribute('content', product.value.image_url);
};

const fetchProduct = async () => {
    loading.value = true;
    error.value = null;
    try {
        const response = await axios.get(`${apiBase}/products/${route.params.id}`); 
        product.value = response.data.product;
        
        if (product.value) {
            selectedVariant.value = product.value;
            updateMetaTags();

            // Cargar relacionados por categoría
            try {
                const listResp = await axios.get(`${apiBase}/products`, { params: { category: product.value.category_id } });
                const allProducts = listResp.data.products.data || [];
                const filtered = allProducts.filter(p => p.id !== product.value.id && p.parent_id !== product.value.id);
                relatedProducts.value = filtered.sort(() => 0.5 - Math.random()).slice(0, 4);
            } catch (e) { /* Error silencioso para relacionados */ }
        } else {
            error.value = "Producto no encontrado.";
        }
    } catch (err) {
        error.value = "Producto no encontrado o error en el servidor.";
    } finally {
        loading.value = false;
    }
};

const startAutomaticAuction = async () => {
    if (!store.token) {
        router.push({ path: '/register', query: { message: 'Primero debes registrarte para iniciar una subasta.' } });
        return;
    }
    try {
        const token = localStorage.getItem('token');
        const res = await axios.post(`${apiBase}/auctions/${product.value.id}/start`, {}, {
            headers: { Authorization: `Bearer ${token}` }
        });
        store.notify("¡Subasta iniciada con éxito! Redirigiendo...");
        if (res.data.auction_id) {
            router.push(`/auctions/${res.data.auction_id}`);
        } else {
            router.push('/auctions');
        }
    } catch (err) {
        store.notify("Error al iniciar subasta: " + (err.response?.data?.error || err.message), 'error');
    }
};

const addToCart = () => {
    if (!store.token) {
        router.push({ path: '/register', query: { message: 'Primero debes registrarte para añadir al carrito.' } });
        return;
    }
    if (!displayProduct.value) return;
    
    for (let i = 0; i < quantity.value; i++) {
        store.addToCart(displayProduct.value);
    }
    store.notify('Producto añadido al carrito');
};

const isWishlisted = computed(() => {
    if (!displayProduct.value) return false;
    return store.wishlist.includes(displayProduct.value.id);
});

const toggleWishlist = () => {
    if (!displayProduct.value) return;
    store.toggleWishlist(displayProduct.value.id);
};

const submitReview = async () => {
    if (!store.token) {
        store.notify("Debes iniciar sesión para dejar una valoración.", 'error');
        return;
    }
    submittingReview.value = true;
    reviewSuccess.value = null;
    reviewError.value = null;
    try {
        await axios.post(`${apiBase}/products/${product.value.id}/reviews`, reviewForm.value);
        store.notify("Valoración enviada correctamente. Pendiente de aprobación.");
        reviewForm.value.comment = '';
        reviewForm.value.rating = 5;
    } catch (err) {
        reviewError.value = err.response?.data?.error || "Hubo un error al enviar la valoración.";
    } finally {
        submittingReview.value = false;
    }
};

onMounted(() => {
    fetchProduct();
});

watch(() => route.params.id, (newId) => {
    if (newId) {
        quantity.value = 1;
        fetchProduct();
    }
});

</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    <Breadcrumbs v-if="product" :items="[
        { label: 'Catálogo', path: '/products' },
        { label: seriesTitle, path: '', active: true }
    ]" />

    <div class="container mx-auto px-4 py-4 max-w-7xl text-white relative z-10">
        <LoadingState v-if="loading" />
        <div v-else-if="error" class="text-center py-16 text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl">{{ error }}</div>
        
        <div v-else-if="product">
            <!-- Back Button -->
            <div class="mb-10 mt-4 flex items-center">
                <button @click="router.push('/products')" class="flex items-center gap-2 text-gray-400 hover:text-neon-blue transition-all group/back bg-gray-900/50 px-4 py-2 rounded-xl border border-gray-800">
                    <i class="fas fa-arrow-left transition-transform group-hover/back:-translate-x-1"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">Volver al Catálogo</span>
                </button>
            </div>

            <!-- Main Layout -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
                <!-- Left: Gallery -->
                <ProductGallery :product="displayProduct" :shouldBlur="shouldBlur" />

                <!-- Right: Information & Actions -->
                <ProductInfo 
                    :product="displayProduct"
                    :seriesTitle="seriesTitle"
                    :seriesParent="seriesParent"
                    :allVolumes="allVolumes"
                    :isWishlisted="isWishlisted"
                    v-model:selectedVariant="selectedVariant"
                    v-model:quantity="quantity"
                    @add-to-cart="addToCart"
                    @toggle-wishlist="toggleWishlist"
                    @start-auction="startAutomaticAuction"
                />
            </div>

            <!-- Reviews Section -->
            <ProductReviews 
                :product="seriesParent"
                :reviewForm="reviewForm"
                :submittingReview="submittingReview"
                :reviewError="reviewError"
                @submit-review="submitReview"
            />

            <!-- Related Products Section -->
            <ProductRelated :relatedProducts="relatedProducts" />
        </div>
    </div>
  </div>
</template>
