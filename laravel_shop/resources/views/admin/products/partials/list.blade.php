<div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 border-b border-neon-blue/20">
            <tr>
                <th class="px-6 py-4 text-left text-neon-blue">ID</th>
                <th class="px-6 py-4 text-left text-neon-blue">Imagen</th>
                <th class="px-6 py-4 text-left text-neon-blue">Nombre</th>
                <th class="px-6 py-4 text-left text-neon-blue">Categoría</th>
                <th class="px-6 py-4 text-left text-neon-blue">Badges</th>
                <th class="px-6 py-4 text-left text-neon-blue">Precio</th>
                <th class="px-6 py-4 text-left text-neon-blue">Stock</th>
                <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $product)
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-gray-300">{{ $product->id }}</td>
                    <td class="px-6 py-4">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-white font-medium">{{ $product->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $product->category->name }}</td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            @if($product->is_exclusive)
                                <span class="px-2 py-0.5 bg-neon-red/20 text-neon-red text-xs rounded-full">EXCLUSIVO</span>
                            @endif
                            @if($product->featured)
                                <span class="px-2 py-0.5 bg-neon-blue/20 text-neon-blue text-xs rounded-full">DESTACADO</span>
                            @endif
                            @if($product->trending)
                                <span class="px-2 py-0.5 bg-neon-purple/20 text-neon-purple text-xs rounded-full">TENDENCIA</span>
                            @endif
                            @if($product->original_price && $product->original_price > $product->price)
                                <span class="px-2 py-0.5 bg-green-600/20 text-green-400 text-xs rounded-full">OFERTA</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($product->original_price)
                            <div>
                                <span class="text-neon-red font-bold">{{ number_format($product->price, 2) }}€</span>
                                <span class="text-gray-500 line-through text-sm ml-1">{{ number_format($product->original_price, 2) }}€</span>
                            </div>
                        @else
                            <span class="text-white">{{ number_format($product->price, 2) }}€</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($product->stock > 0)
                            <span class="text-green-500">{{ $product->stock }}</span>
                        @else
                            <span class="text-red-500">Agotado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.products.edit', $product) }}" 
                               class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                Editar
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Eliminar este producto?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition text-sm">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <p>No hay productos para mostrar</p>
                        <a href="{{ route('admin.products.create') }}" class="inline-block mt-4 px-4 py-2 bg-neon-blue text-gamer-dark rounded-lg">
                            Crear primer producto
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $products->links() }}
</div>
