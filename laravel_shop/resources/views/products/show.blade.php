<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gamer-card rounded-2xl overflow-hidden border border-neon-blue/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                    <!-- Imagen del producto -->
                    <div>
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full rounded-lg shadow-2xl">
                    </div>
                    
                    <!-- Detalles del producto -->
                    <div>
                        <span class="text-neon-blue text-sm uppercase tracking-wider">{{ $product->category->name }}</span>
                        <h1 class="text-4xl font-black text-white mt-2">{{ $product->name }}</h1>
                        
                        @if($product->description)
                            <p class="text-gray-400 mt-4 text-lg">{{ $product->description }}</p>
                        @endif
                        
                        <!-- Precio y oferta -->
                        <div class="mt-6">
                            @if($product->original_price && $product->original_price > $product->price)
                                <span class="text-3xl font-black text-neon-red">{{ number_format($product->price, 2) }}€</span>
                                <span class="ml-2 text-gray-500 line-through text-lg">{{ number_format($product->original_price, 2) }}€</span>
                                <span class="ml-2 bg-neon-red text-white text-sm font-bold px-2 py-1 rounded-full">
                                    -{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%
                                </span>
                            @else
                                <span class="text-3xl font-black text-neon-blue">{{ number_format($product->price, 2) }}€</span>
                            @endif
                        </div>
                        
                        <!-- Stock -->
                        <div class="mt-4">
                            @if($product->stock > 0)
                                <span class="text-green-500">✅ Stock disponible: {{ $product->stock }} unidades</span>
                            @else
                                <span class="text-red-500">❌ Agotado</span>
                            @endif
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="mt-8 flex gap-4">
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                    Añadir al carrito
                                </button>
                            </form>
                            <a href="{{ route('products.index') }}" class="px-8 py-4 border-2 border-neon-purple text-neon-purple font-black rounded-full hover:bg-neon-purple hover:text-white transition">
                                Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
