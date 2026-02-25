<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón para volver al catálogo -->
            <div class="mb-6">
                <a href="{{ route('products.index') }}" class="inline-flex items-center text-gray-400 hover:text-neon-blue transition group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Volver al catálogo</span>
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">{{ $category->name }}</h1>
                <p class="text-gray-400">{{ $category->description }}</p>
                <p class="text-gray-500 mt-2">{{ $products->total() }} productos disponibles en {{ $category->name }}</p>
            </div>
            
            <!-- Filtros rápidos -->
            <div class="mb-8 flex flex-wrap gap-3">
                <a href="{{ route('products.exclusivos') }}?category={{ $category->slug }}" 
                   class="px-4 py-2 rounded-full text-sm font-bold bg-gamer-card text-neon-red hover:bg-neon-red hover:text-white transition border border-neon-red/30">
                    🔥 Exclusivos en {{ $category->name }}
                </a>
            </div>
            
            <!-- Grid de productos -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @forelse($products as $product)
                    @include('products.partials.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full text-center py-12 bg-gamer-card rounded-2xl border border-gray-800">
                        <p class="text-gray-400">No hay productos en esta categoría</p>
                        <a href="{{ route('products.index') }}" class="inline-block mt-4 px-6 py-2 bg-neon-blue text-gamer-dark rounded-lg hover:scale-105 transition">
                            Ver todos los productos
                        </a>
                    </div>
                @endforelse
            </div>
            
            <!-- Paginación -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-store-layout>
