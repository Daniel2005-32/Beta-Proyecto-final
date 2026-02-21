<x-store-layout>
    <div class="flex">
        <!-- Sidebar Filters -->
        <div class="w-1/4 pr-8">
            <h3 class="font-bold mb-4">Categorías</h3>
            <ul class="space-y-2">
                @foreach($categories as $category)
                    <li>
                        <a href="{{ route('products.index', ['category' => $category->slug]) }}" class="text-gray-600 hover:text-indigo-600 {{ request('category') == $category->slug ? 'font-bold text-indigo-600' : '' }}">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <!-- Product Grid -->
        <div class="w-3/4">
            <h2 class="text-2xl font-bold mb-6">Catálogo</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <img src="{{ $product->image ?? 'https://via.placeholder.com/300' }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-500 text-sm mb-2">{{ $product->category->name }}</p>
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-xl font-bold">{{ $product->price }}€</span>
                                <a href="{{ route('products.show', $product->slug) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Ver</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-store-layout>
