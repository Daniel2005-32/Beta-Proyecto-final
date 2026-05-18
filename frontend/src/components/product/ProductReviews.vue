<script setup>
import { store } from '../../utils/store';

const props = defineProps({
    product: Object,
    reviewForm: Object,
    submittingReview: Boolean,
    reviewError: String
});

const emit = defineEmits(['submit-review']);

</script>

<template>
    <section class="mb-12 border-t border-gray-800 pt-8">
        <h2 class="text-xl font-bold uppercase tracking-tight text-white mb-6">Valoraciones de la comunidad</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Formulario -->
            <div class="col-span-1 bg-gamer-card border border-gray-800 rounded-2xl p-6 h-max">
                <h3 class="font-bold text-sm text-gray-200 mb-4">Dejar una valoración</h3>
                <div v-if="!store.token" class="text-xs text-gray-400">
                    <router-link to="/register" class="text-neon-blue hover:underline">Regístrate</router-link> para valorar este producto.
                </div>
                <form v-else @submit.prevent="emit('submit-review')" class="space-y-4">
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Puntuación</label>
                        <div class="flex gap-1 text-neon-blue text-xl cursor-pointer">
                            <span v-for="star in 5" :key="star" @click="reviewForm.rating = star">
                                {{ star <= reviewForm.rating ? '★' : '☆' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Comentario</label>
                        <textarea v-model="reviewForm.comment" rows="3" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-neon-blue" placeholder="¿Qué te pareció el producto?"></textarea>
                    </div>
                    <button type="submit" :disabled="submittingReview" class="w-full bg-neon-blue text-gamer-dark font-black py-3 rounded-xl text-xs uppercase hover:bg-white transition shadow-neon-blue/20">
                        <i class="fas fa-paper-plane mr-2"></i>
                        {{ submittingReview ? 'Enviando...' : 'Enviar Valoración' }}
                    </button>
                </form>
                <p v-if="reviewError" class="text-red-400 text-xs mt-2">{{ reviewError }}</p>
            </div>

            <!-- Lista de Comentarios -->
            <div class="col-span-1 md:col-span-2 space-y-4">
                <div v-if="!product.approved_reviews || product.approved_reviews.length === 0" class="text-gray-500 text-sm">
                    No hay valoraciones aprobadas aún. ¡Sé el primero!
                </div>
                <div v-else v-for="review in product.approved_reviews" :key="review.id" class="bg-gamer-card border border-gray-800 rounded-xl p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-neon-purple text-sm">{{ review.user?.name || 'Usuario' }}</span>
                        <div class="flex text-neon-blue text-xs">
                            <span v-for="i in 5" :key="i">{{ i <= review.rating ? '★' : '☆' }}</span>
                        </div>
                    </div>
                    <p class="text-gray-300 text-sm">{{ review.comment }}</p>
                    <span class="text-[10px] text-gray-600 block mt-2">{{ new Date(review.created_at).toLocaleDateString() }}</span>
                </div>
            </div>
        </div>
    </section>
</template>
