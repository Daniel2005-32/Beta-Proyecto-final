<x-store-layout>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="md:flex">
            <div class="md:w-1/2">
                <img src="{{ $product->image ?? 'https://via.placeholder.com/600' }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
            </div>
            <div class="md:w-1/2 p-8">
                <div class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</div>
                <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
                <p class="text-gray-600 mb-6">{{ $product->description }}</p>
                <div class="text-4xl font-bold text-indigo-600 mb-6">{{ $product->price }}€</div>
                
                <div class="flex space-x-4 mb-8">
                    @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-bold">
                                Añadir al Carrito
                            </button>
                        </form>
                        <div class="text-green-600 font-bold self-center">Stock disponible: {{ $product->stock }}</div>
                    @else
                        <div class="bg-red-100 text-red-600 px-8 py-3 rounded-lg font-bold">
                            Agotado
                        </div>
                    @endif
                </div>

                @if($product->is_exclusive && $product->stock <= 1 && $product->stock > 0)
                    <div class="bg-yellow-100 p-4 rounded-lg border border-yellow-300">
                        <p class="font-bold text-yellow-800">¡Última unidad exclusiva! Podría subastarse pronto.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($related->count() > 0)
        <h2 class="text-2xl font-bold mt-12 mb-6">Productos Relacionados</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($related as $item)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <img src="{{ $item->image ?? 'https://via.placeholder.com/300' }}" alt="{{ $item->name }}" class="w-full h-32 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold text-md mb-2 truncate">{{ $item->name }}</h3>
                        <div class="flex justify-between items-center">
                            <span class="font-bold">{{ $item->price }}€</span>
                            <a href="{{ route('products.show', $item->slug) }}" class="text-indigo-600 hover:text-indigo-800 text-sm">Ver</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-store-layout>
