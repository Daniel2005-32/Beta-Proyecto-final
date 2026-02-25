<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabecera con botón de crear -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-black text-white">Panel de Administración</h1>
                <a href="{{ route('admin.products.create') }}" class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                    + Nuevo Producto
                </a>
            </div>

            <!-- Mensajes de éxito -->
            @if(session('success'))
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tabla de productos -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-800 border-b border-neon-blue/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-neon-blue">ID</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Imagen</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Producto</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Categoría</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Precio</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Stock</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-300">{{ $product->id }}</td>
                                <td class="px-6 py-4">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded-lg">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-white font-bold">{{ $product->name }}</div>
                                    @if($product->is_exclusive)
                                        <span class="text-neon-red text-xs">🔥 Exclusivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-300">{{ $product->category->name }}</td>
                                <td class="px-6 py-4 text-neon-blue font-bold">{{ number_format($product->price, 2) }}€</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm 
                                        {{ $product->stock > 5 ? 'bg-green-900/50 text-green-300' : 
                                           ($product->stock > 0 ? 'bg-yellow-900/50 text-yellow-300' : 'bg-red-900/50 text-red-300') }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition">
                                            Editar
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-store-layout>
