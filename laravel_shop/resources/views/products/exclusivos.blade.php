<x-store-layout>
    <div class="py-12">
        <!-- Mismo ancho que la página principal -->
        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón para volver al catálogo normal -->
            <div class="mb-6">
                <a href="{{ route('products.index') }}" class="inline-flex items-center text-gray-400 hover:text-neon-blue transition group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Volver al catálogo general</span>
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">
                    <span class="text-neon-red">🔥 Artículos Exclusivos</span>
                </h1>
                <p class="text-gray-400">Productos únicos y ediciones limitadas</p>
                <p class="text-gray-500 mt-2">{{ $products->total() }} productos exclusivos disponibles</p>
            </div>

            <!-- FILTRO POR CATEGORÍA -->
            @if($categories->count() > 0)
                <div class="mb-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.exclusivos') }}" 
                       class="px-4 py-2 rounded-full text-sm font-bold transition 
                       {{ !request('category') ? 'bg-neon-red text-white' : 'bg-gamer-card text-gray-400 hover:text-white' }}">
                        Todos los exclusivos
                    </a>
                    
                    @foreach($categories as $cat)
                        <a href="{{ route('products.exclusivos') }}?category={{ $cat->slug }}" 
                           class="px-4 py-2 rounded-full text-sm font-bold transition
                           {{ request('category') == $cat->slug ? 'bg-neon-red text-white' : 'bg-gamer-card text-gray-400 hover:text-white' }}">
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </a>
                    @endforeach
                </div>
            @endif

            <!-- Grid de productos - 5 columnas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @forelse($products as $product)
                    @include('products.partials.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full text-center py-12 bg-gamer-card rounded-2xl border border-neon-red/20">
                        <p class="text-gray-400">No hay productos exclusivos disponibles</p>
                    </div>
                @endforelse
            </div>

            <!-- Paginación -->
            <div class="mt-8">
                {{ $products->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-store-layout>