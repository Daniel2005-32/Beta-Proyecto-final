<div class="group bg-gamer-card rounded-2xl overflow-hidden border border-gray-800 hover:border-neon-blue/50 transition duration-300 shadow-xl relative">
    
    <!-- BADGE EXCLUSIVO -->
    @if($product->is_exclusive)
        <div class="absolute top-4 left-4 z-10">
            <span class="bg-neon-red text-white text-xs font-black px-3 py-1.5 rounded-full shadow-[0_0_15px_rgba(255,0,85,0.4)] flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1.5 1.5 0 001.5 1.002l3.404 2.44c.857.614.4 1.898-.642 1.898h-3.854a1.5 1.5 0 00-1.5 1.002l-1.07 3.292c-.3.921-1.603.921-1.902 0l-1.07-3.292a1.5 1.5 0 00-1.5-1.002L2.95 9.56c-.857-.614-.4-1.898.642-1.898h3.854a1.5 1.5 0 001.5-1.002l1.07-3.292z"/>
                </svg>
                EXCLUSIVO
            </span>
        </div>
    @endif
    
    <!-- Imagen -->
    <a href="{{ route('products.show', $product->slug) }}" class="block relative overflow-hidden aspect-square">
        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
        
        <!-- BADGE OFERTA -->
        @if($product->original_price && $product->original_price > $product->price)
            <div class="absolute top-4 right-4 bg-neon-blue text-gamer-dark text-xs font-black px-3 py-1.5 rounded-full shadow-[0_0_15px_rgba(0,210,255,0.4)]">
                -{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%
            </div>
        @endif
        
        <!-- BADGE SUBASTA ACTIVA -->
        @if($product->isAuctionActive())
            <div class="absolute bottom-4 left-4 bg-neon-purple text-white text-xs font-black px-3 py-1.5 rounded-full shadow-[0_0_15px_rgba(157,0,255,0.4)]">
                ⚡ SUBASTA ACTIVA
            </div>
        @elseif($product->is_exclusive && $product->stock == 1)
            <div class="absolute bottom-4 left-4 bg-neon-red text-white text-xs font-black px-3 py-1.5 rounded-full shadow-[0_0_15px_rgba(255,0,85,0.4)]">
                ⏳ ÚLTIMA UNIDAD
            </div>
        @endif
    </a>
    
    <!-- Información -->
    <div class="p-6">
        <a href="{{ route('products.show', $product->slug) }}" class="hover:text-neon-blue transition">
            <h3 class="font-bold text-lg text-white mb-1 truncate">{{ $product->name }}</h3>
        </a>
        <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $product->description }}</p>
        
        <!-- Precios y botón -->
        <div class="flex justify-between items-center">
            <div>
                @if($product->isAuctionActive())
                    <span class="text-2xl font-black text-neon-purple italic">{{ number_format($product->price, 2) }}€</span>
                    <span class="text-sm text-gray-500 line-through ml-2">{{ number_format($product->auction_start_price ?? $product->price, 2) }}€</span>
                @elseif($product->original_price && $product->original_price > $product->price)
                    <span class="text-2xl font-black text-neon-red italic">{{ number_format($product->price, 2) }}€</span>
                    <span class="text-sm text-gray-500 line-through ml-2">{{ number_format($product->original_price, 2) }}€</span>
                @else
                    <span class="text-2xl font-black text-white italic">{{ number_format($product->price, 2) }}€</span>
                @endif
            </div>
            
            @auth
                @if(Auth::user()->isBanned())
                    <!-- Usuario baneado - no puede hacer nada -->
                    <div class="p-2 bg-gray-700 rounded-lg cursor-not-allowed opacity-50" title="No puedes realizar acciones mientras estás baneado">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                @elseif($product->isAuctionActive())
                    <a href="{{ route('auctions.show', $product->id) }}" class="p-2 bg-neon-purple rounded-lg hover:bg-neon-purple/80 transition shadow-lg" title="Ver subasta">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                @elseif($product->is_exclusive && $product->stock == 1)
                    <a href="{{ route('auctions.confirm', $product->id) }}" class="p-2 bg-neon-red rounded-lg hover:bg-neon-red/80 transition shadow-lg" title="Iniciar subasta">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('products.show', $product->slug) }}" class="p-2 bg-gray-800 rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition shadow-lg" title="Ver detalles">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
                @endif
            @else
                <a href="{{ route('products.show', $product->slug) }}" class="p-2 bg-gray-800 rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition shadow-lg" title="Ver detalles">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </a>
            @endauth
        </div>
    </div>
</div>
