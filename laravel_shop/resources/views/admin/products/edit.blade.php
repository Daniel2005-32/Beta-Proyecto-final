<x-store-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón volver -->
            <div class="mb-6">
                <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-neon-blue transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al panel
                </a>
            </div>

            <!-- Formulario de edición -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Editar Producto</h1>

                <form action="{{ route('admin.products.update', $product) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-300 mb-2">Nombre</label>
                            <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug', $product->slug) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Categoría</label>
                            <select name="category_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                                @foreach(\App\Models\Category::all() as $category)
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Precio (€)</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price', $product->price) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Stock</label>
                            <input type="number" name="stock" value="{{ old('stock', $product->stock) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">URL de la imagen</label>
                            <input type="url" name="image" value="{{ old('image', $product->image) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2">Descripción</label>
                        <textarea name="description" rows="4" required
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="featured" value="1" {{ $product->featured ? 'checked' : '' }}
                                   class="rounded bg-gray-800 border-gray-700 text-neon-blue focus:ring-neon-blue">
                            <span class="text-gray-300">Producto destacado</span>
                        </label>

                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="trending" value="1" {{ $product->trending ? 'checked' : '' }}
                                   class="rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                            <span class="text-gray-300">Producto en tendencia</span>
                        </label>

                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="is_exclusive" value="1" {{ $product->is_exclusive ? 'checked' : '' }}
                                   class="rounded bg-gray-800 border-gray-700 text-neon-red focus:ring-neon-red">
                            <span class="text-gray-300">Producto exclusivo 🔥</span>
                        </label>
                    </div>

                    @if($product->original_price)
                        <div class="bg-gray-800/50 p-4 rounded-lg">
                            <label class="block text-gray-300 mb-2">Precio original (para ofertas)</label>
                            <input type="number" step="0.01" name="original_price" value="{{ old('original_price', $product->original_price) }}"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>
                    @endif

                    <div class="pt-4">
                        <button type="submit" class="w-full px-6 py-4 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                            Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-store-layout>
