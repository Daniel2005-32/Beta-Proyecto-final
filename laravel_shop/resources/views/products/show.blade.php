<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver
                </a>
            </div>
            
            <div class="bg-gamer-card rounded-2xl overflow-hidden border border-neon-blue/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                    <!-- Imagen del producto -->
                    <div>
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                    </div>

                    <!-- Detalles del producto -->
                    <div>
                        <span class="text-neon-blue text-sm uppercase">{{ $product->category->name }}</span>
                        <h1 class="text-4xl font-black text-white mt-2">{{ $product->name }}</h1>
                        
                        @if($product->is_exclusive)
                            <span class="inline-block mt-2 px-3 py-1 bg-neon-red/20 text-neon-red rounded-full text-xs font-bold">
                                🔥 EXCLUSIVO
                            </span>
                        @endif
                        
                        <p class="text-gray-400 mt-4">{{ $product->description }}</p>
                        
                        <!-- Precio SIN IVA -->
                        <div class="mt-6">
                            @if($product->isAuctionActive())
                                <span class="text-3xl font-black text-neon-purple">{{ number_format($product->price, 2) }}€</span>
                                <span class="ml-2 text-gray-500 line-through text-lg">{{ number_format($product->auction_start_price ?? $product->price, 2) }}€</span>
                                <p class="text-sm text-gray-400 mt-1">Subasta activa - {{ $product->auctionTimeLeft() }} restantes</p>
                            @elseif($product->original_price && $product->original_price > $product->price)
                                <span class="text-3xl font-black text-neon-red">{{ number_format($product->price, 2) }}€</span>
                                <span class="ml-2 text-gray-500 line-through text-lg">{{ number_format($product->original_price, 2) }}€</span>
                            @else
                                <span class="text-3xl font-black text-neon-blue">{{ number_format($product->price, 2) }}€</span>
                            @endif
                        </div>
                        
                        <!-- Stock -->
                        <div class="mt-4">
                            @if($product->stock > 0)
                                <span class="text-green-500">✅ Stock disponible: {{ $product->stock }} unidades</span>
                                @if($product->is_exclusive && $product->stock == 1)
                                    <p class="text-neon-red text-sm mt-1">⚠️ Última unidad - Inicia una subasta para obtener 20% de descuento</p>
                                @endif
                            @else
                                <span class="text-red-500">❌ Agotado</span>
                            @endif
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="mt-8 flex gap-4">
                            @if($product->isAuctionActive())
                                <a href="{{ route('auctions.show', $product->id) }}" 
                                   class="px-8 py-4 bg-neon-purple text-white font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                                    Ver Subasta
                                </a>
                            @elseif($product->is_exclusive && $product->stock == 1)
                                <a href="{{ route('auctions.confirm', $product->id) }}" 
                                   class="px-8 py-4 bg-neon-red text-white font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(255,0,85,0.4)]">
                                    Iniciar Subasta
                                </a>
                            @elseif($product->stock > 0)
                                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                        Añadir al carrito
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('products.index') }}" 
                               class="px-8 py-4 border-2 border-gray-700 text-gray-300 font-black rounded-full hover:bg-gray-800 hover:text-white transition">
                                Seguir comprando
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECCIÓN DE VALORACIONES - AQUÍ VA TODO -->
            <!-- ============================================ -->
            <div class="mt-12">
                @include('products.partials.reviews')
            </div>
            <!-- ============================================ -->

        </div>
    </div>
</x-store-layout>
