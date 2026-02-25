<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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

            <!-- FILTROS POR CATEGORÍA -->
            @if($categories->count() > 0)
                <div class="mb-8 flex flex-wrap gap-3">
                    <a href="{{ route('products.exclusivos') }}" 
                       class="px-4 py-2 rounded-full text-sm font-bold transition 
                       {{ !request('category') ? 'bg-neon-red text-white' : 'bg-gamer-card text-gray-400 hover:text-white' }}">
                        Todos ({{ $products->total() }})
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

            <!-- PRODUCTOS -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($products as $product)
                        <div class="group bg-gamer-card rounded-2xl overflow-hidden border border-gray-800 hover:border-neon-red/50 transition duration-300 shadow-xl relative">
                            <div class="absolute top-4 left-4 z-10">
                                <span class="bg-neon-red text-white text-xs font-black px-3 py-1.5 rounded-full shadow-[0_0_15px_rgba(255,0,85,0.4)]">
                                    🔥 EXCLUSIVO
                                </span>
                            </div>
                            
                            <div class="relative overflow-hidden aspect-square">
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                            </div>
                            
                            <div class="p-6">
                                <h3 class="font-bold text-lg text-white mb-1 truncate">{{ $product->name }}</h3>
                                <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $product->description }}</p>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-2xl font-black text-neon-red italic">{{ number_format($product->price, 2) }}€</span>
                                    <a href="{{ route('products.show', $product->slug) }}" class="p-2 bg-gray-800 rounded-lg hover:bg-neon-red hover:text-white transition">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- PAGINACIÓN -->
                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-12 bg-gamer-card rounded-2xl border border-neon-red/20">
                    <svg class="w-16 h-16 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-white mb-2">No hay exclusivos</h3>
                    <p class="text-gray-400 mb-6">
                        @if(request('category'))
                            No hay productos exclusivos en esta categoría
                        @else
                            Próximamente llegarán más artículos exclusivos
                        @endif
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                        Ver catálogo general
                    </a>
                </div>
            @endif
            
            <!-- Botón inferior para volver (por si acaso) -->
            <div class="mt-8 text-center">
                <a href="{{ route('products.index') }}" class="inline-flex items-center text-gray-400 hover:text-neon-blue transition group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Volver al catálogo general</span>
                </a>
            </div>
        </div>
    </div>
</x-store-layout>
