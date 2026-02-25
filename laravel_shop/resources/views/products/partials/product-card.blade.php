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
    <div class="relative overflow-hidden aspect-square">
        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
        
        <!-- BADGE OFERTA -->
        @if($product->original_price && $product->original_price > $product->price)
            <div class="absolute top-4 right-4 bg-neon-blue text-gamer-dark text-xs font-black px-3 py-1.5 rounded-full shadow-[0_0_15px_rgba(0,210,255,0.4)]">
                -{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%
            </div>
        @endif
        
        <!-- BADGE STOCK BAJO -->
        @if($product->stock <= 2 && $product->stock > 0)
            <div class="absolute bottom-4 left-4 bg-yellow-600 text-white text-xs font-black px-3 py-1.5 rounded-full shadow-[0_0_15px_rgba(255,255,0,0.4)]">
                ⚡ Últimas {{ $product->stock }}
            </div>
        @endif
    </div>
    
    <!-- Información -->
    <div class="p-6">
        <h3 class="font-bold text-lg text-white mb-1 group-hover:text-neon-blue transition truncate">{{ $product->name }}</h3>
        <p class="text-gray-500 text-sm mb-4 line-clamp-2">{{ $product->description }}</p>
        
        <!-- Precios -->
        <div class="flex justify-between items-center">
            <div>
                @if($product->original_price && $product->original_price > $product->price)
                    <span class="text-2xl font-black text-neon-red italic">{{ number_format($product->price, 2) }}€</span>
                    <span class="text-sm text-gray-500 line-through ml-2">{{ number_format($product->original_price, 2) }}€</span>
                @else
                    <span class="text-2xl font-black text-white italic">{{ number_format($product->price, 2) }}€</span>
                @endif
            </div>
            
            <a href="{{ route('products.show', $product->slug) }}" class="p-2 bg-gray-800 rounded-lg group-hover:bg-neon-blue group-hover:text-gamer-dark transition shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
            </a>
        </div>
    </div>
</div>
