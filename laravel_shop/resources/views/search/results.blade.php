<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabecera -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-2">
                    <span class="text-neon-blue">🔍 Resultados de búsqueda</span>
                </h1>
                <p class="text-gray-400">
                    @if($products->total() > 0)
                        Se encontraron {{ $products->total() }} productos para "{{ $query }}"
                    @else
                        No se encontraron productos para "{{ $query }}"
                    @endif
                </p>
            </div>

            <!-- Grid de productos -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($products as $product)
                        @include('products.partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-gamer-card rounded-2xl border border-gray-800 p-12 text-center">
                    <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-white mb-2">No hay resultados</h2>
                    <p class="text-gray-400 mb-6">No encontramos productos que coincidan con "{{ $query }}"</p>
                    <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                        Ver todos los productos
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-store-layout>
