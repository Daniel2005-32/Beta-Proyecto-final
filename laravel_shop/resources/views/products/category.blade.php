<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">{{ $category->name }}</h1>
                <p class="text-gray-400">{{ $category->description }}</p>
                <p class="text-gray-500 mt-2">{{ $products->count() }} productos disponibles</p>
            </div>
            
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach($products as $product)
                        <div class="group bg-gamer-card rounded-2xl overflow-hidden border border-gray-800 hover:border-neon-blue/50 transition">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <span class="text-xs text-neon-blue uppercase">{{ $product->category->name }}</span>
                                <h3 class="text-white font-bold text-lg mt-1">{{ $product->name }}</h3>
                                <p class="text-gray-400 text-sm mt-2 line-clamp-2">{{ $product->description }}</p>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-2xl font-bold text-neon-blue">{{ number_format($product->price, 2) }}€</span>
                                    <a href="{{ route('products.show', $product->slug) }}" class="px-4 py-2 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition">
                                        Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-gamer-card rounded-2xl border border-gray-800">
                    <p class="text-gray-400">No hay productos en esta categoría</p>
                </div>
            @endif
        </div>
    </div>
</x-store-layout>
