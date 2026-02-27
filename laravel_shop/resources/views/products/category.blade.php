<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">{{ $category->name }}</h1>
                <p class="text-gray-400">{{ $category->description }}</p>
                <p class="text-gray-500 mt-2">{{ $products->total() }} productos disponibles en {{ $category->name }}</p>
            </div>
            
            <!-- Filtros rápidos -->
            <div class="mb-8 flex flex-wrap gap-3">
                <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-full text-sm font-bold bg-gamer-card text-gray-400 hover:text-white transition">
                    ← Ver todas las categorías
                </a>
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
