<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-black text-white mb-8">🔥 Ofertas Especiales</h1>
            
            @if(isset($offers) && count($offers) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($offers as $product)
                        <div class="group bg-gamer-card rounded-2xl overflow-hidden border border-neon-red/30 hover:border-neon-red transition">
                            <div class="relative">
                                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                <div class="absolute top-4 left-4 bg-neon-red text-white text-xs font-bold px-2 py-1 rounded-full">
                                    -{{ round((($product->original_price - $product->price) / $product->original_price) * 100) }}%
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="text-white font-bold">{{ $product->name }}</h3>
                                <div class="flex items-center mt-2">
                                    <span class="text-2xl font-bold text-neon-red">{{ number_format($product->price, 2) }}€</span>
                                    <span class="ml-2 text-sm text-gray-500 line-through">{{ number_format($product->original_price, 2) }}€</span>
                                </div>
                                <a href="{{ route('products.show', $product->slug) }}" class="mt-4 block text-center px-4 py-2 bg-neon-red/20 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition">
                                    Ver oferta
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gamer-card rounded-2xl border border-neon-red/20 p-12 text-center">
                    <p class="text-gray-300 text-xl">Próximamente encontrarás las mejores ofertas aquí</p>
                    <p class="text-neon-red mt-4">¡Vuelve pronto para no perdértelas!</p>
                </div>
            @endif
        </div>
    </div>
</x-store-layout>
