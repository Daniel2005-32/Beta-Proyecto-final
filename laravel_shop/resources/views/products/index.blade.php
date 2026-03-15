<x-store-layout>
    <div class="py-12">
        <!-- Mismo ancho que la página principal -->
        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Cabecera con contadores -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">Catálogo de Productos</h1>
                <p class="text-gray-400">{{ $products->total() }} productos disponibles</p>
            </div>

            <!-- Filtros rápidos por categoría -->
            <div class="mb-8">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">Filtrar por categoría:</h2>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('products.index') }}" 
                       class="px-4 py-2 rounded-full text-sm font-bold transition 
                       {{ !request('category') && !request('exclusive') ? 'bg-neon-blue text-gamer-dark' : 'bg-gamer-card text-gray-400 hover:text-white' }}">
                        Todos ({{ $categories->sum('products_count') }})
                    </a>
                    
                    @foreach($categories as $category)
                        <a href="{{ route('products.index') }}?category={{ $category->slug }}" 
                           class="px-4 py-2 rounded-full text-sm font-bold transition
                           {{ request('category') == $category->slug ? 'bg-neon-blue text-gamer-dark' : 'bg-gamer-card text-gray-400 hover:text-white' }}">
                            {{ $category->name }} ({{ $category->products_count }})
                        </a>
                    @endforeach
                    
                    <a href="{{ route('products.exclusivos') }}" 
                       class="px-4 py-2 rounded-full text-sm font-bold transition bg-gamer-card text-neon-red hover:bg-neon-red hover:text-white border border-neon-red/30">
                        🔥 Exclusivos ({{ App\Models\Product::where('is_exclusive', true)->count() }})
                    </a>
                </div>
            </div>

            <!-- Grid de productos - 5 columnas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @forelse($products as $product)
                    @include('products.partials.product-card', ['product' => $product])
                @empty
                    <div class="col-span-full text-center py-12 bg-gamer-card rounded-2xl border border-gray-800">
                        <p class="text-gray-400">No hay productos disponibles</p>
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