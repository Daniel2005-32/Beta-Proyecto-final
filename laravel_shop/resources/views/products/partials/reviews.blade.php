<div class="mt-8" x-data="reviews()" x-init="init({{ $product->id }})">
    <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
        <svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
        </svg>
        Valoraciones
    </h2>

    <!-- Resumen de valoraciones -->
    <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6 mb-6">
        <div class="flex items-center gap-8">
            <div class="text-center">
                <span class="text-5xl font-bold text-neon-purple" x-text="averageRating.toFixed(1)"></span>
                <span class="text-gray-400 text-sm block">sobre 5</span>
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm text-gray-400 w-12">5⭐</span>
                    <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500" :style="'width: ' + (ratingCounts[5] / totalReviews * 100) + '%'"></div>
                    </div>
                    <span class="text-sm text-gray-400 w-8" x-text="ratingCounts[5]"></span>
                </div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm text-gray-400 w-12">4⭐</span>
                    <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500" :style="'width: ' + (ratingCounts[4] / totalReviews * 100) + '%'"></div>
                    </div>
                    <span class="text-sm text-gray-400 w-8" x-text="ratingCounts[4]"></span>
                </div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm text-gray-400 w-12">3⭐</span>
                    <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-500" :style="'width: ' + (ratingCounts[3] / totalReviews * 100) + '%'"></div>
                    </div>
                    <span class="text-sm text-gray-400 w-8" x-text="ratingCounts[3]"></span>
                </div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm text-gray-400 w-12">2⭐</span>
                    <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-orange-500" :style="'width: ' + (ratingCounts[2] / totalReviews * 100) + '%'"></div>
                    </div>
                    <span class="text-sm text-gray-400 w-8" x-text="ratingCounts[2]"></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-400 w-12">1⭐</span>
                    <div class="flex-1 h-2 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-red-500" :style="'width: ' + (ratingCounts[1] / totalReviews * 100) + '%'"></div>
                    </div>
                    <span class="text-sm text-gray-400 w-8" x-text="ratingCounts[1]"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de valoración (solo para usuarios autenticados no baneados) -->
    @auth
        @if(!auth()->user()->isBanned())
            <div x-show="!hasUserReviewed" class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6 mb-6">
                <h3 class="text-lg font-bold text-white mb-4">¿Qué te parece este producto?</h3>
                
                <form @submit.prevent="submitReview">
                    <div class="flex items-center gap-2 mb-4">
                        <template x-for="star in 5" :key="star">
                            <button type="button" @click="userRating = star" class="focus:outline-none">
                                <svg class="w-8 h-8 transition" :class="star <= userRating ? 'text-yellow-500' : 'text-gray-600'" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                        </template>
                    </div>
                    
                    <textarea x-model="userComment" 
                              placeholder="Escribe un comentario (opcional)..."
                              rows="3"
                              class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-neon-purple mb-4"></textarea>
                    
                    <button type="submit"
                            :disabled="userRating === 0"
                            class="px-6 py-2 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition disabled:opacity-50">
                        Enviar valoración
                    </button>
                </form>
            </div>
        @endif
    @else
        <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6 mb-6 text-center">
            <p class="text-gray-400 mb-2">¿Quieres valorar este producto?</p>
            <a href="{{ route('login') }}" class="text-neon-purple hover:underline">Inicia sesión</a>
        </div>
    @endauth

    <!-- Lista de valoraciones -->
    <div class="space-y-4" x-show="approvedReviews.length > 0">
        <template x-for="review in approvedReviews" :key="review.id">
            <div class="bg-gamer-card rounded-2xl border border-gray-800 p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple flex items-center justify-center text-white font-bold text-sm">
                            <span x-text="review.user.name.charAt(0).toUpperCase()"></span>
                        </div>
                        <div>
                            <span class="text-white font-bold text-sm" x-text="review.user.name"></span>
                            <div class="flex items-center gap-1">
                                <template x-for="star in 5" :key="star">
                                    <svg class="w-4 h-4" :class="star <= review.rating ? 'text-yellow-500' : 'text-gray-600'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </template>
                            </div>
                        </div>
                    </div>
                    <span class="text-xs text-gray-500" x-text="new Date(review.created_at).toLocaleDateString()"></span>
                </div>
                <p class="text-gray-300 text-sm" x-show="review.comment" x-text="review.comment"></p>
                <p class="text-gray-500 text-sm italic" x-show="!review.comment">Sin comentario</p>
            </div>
        </template>
    </div>

    <div x-show="approvedReviews.length === 0" class="text-center py-8 text-gray-500">
        <p>Aún no hay valoraciones para este producto</p>
    </div>
</div>

<script>
function reviews() {
    return {
        productId: null,
        approvedReviews: [],
        averageRating: 0,
        totalReviews: 0,
        ratingCounts: {1:0, 2:0, 3:0, 4:0, 5:0},
        hasUserReviewed: {{ auth()->check() ? ($product->reviewedByUser(auth()->id()) ? 'true' : 'false') : 'false' }},
        userRating: 0,
        userComment: '',
        
        init(id) {
            this.productId = id;
            this.loadReviews();
        },
        
        loadReviews() {
            fetch(`/products/${this.productId}/reviews`)
                .then(response => response.json())
                .then(data => {
                    this.approvedReviews = data.reviews;
                    this.calculateStats();
                });
        },
        
        calculateStats() {
            this.totalReviews = this.approvedReviews.length;
            if (this.totalReviews === 0) return;
            
            let sum = 0;
            this.ratingCounts = {1:0, 2:0, 3:0, 4:0, 5:0};
            
            this.approvedReviews.forEach(review => {
                sum += review.rating;
                this.ratingCounts[review.rating]++;
            });
            
            this.averageRating = sum / this.totalReviews;
        },
        
        submitReview() {
            if (this.userRating === 0) return;
            
            fetch(`/products/${this.productId}/reviews`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rating: this.userRating,
                    comment: this.userComment
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.hasUserReviewed = true;
                    this.userRating = 0;
                    this.userComment = '';
                    alert(data.message);
                } else {
                    alert(data.error);
                }
            });
        }
    }
}
</script>
