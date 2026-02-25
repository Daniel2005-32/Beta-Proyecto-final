<x-store-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver al panel
                </a>
            </div>

            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Crear Nuevo Producto</h1>

                <form action="{{ route('admin.products.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-300 mb-2">Nombre</label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Slug</label>
                            <input type="text" name="slug" value="{{ old('slug') }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Categoría</label>
                            <select name="category_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Precio (€)</label>
                            <input type="number" step="0.01" name="price" value="{{ old('price') }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Stock</label>
                            <input type="number" name="stock" value="{{ old('stock', 1) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">URL de la imagen</label>
                            <input type="url" name="image" value="{{ old('image') }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2">Descripción</label>
                        <textarea name="description" rows="4" required
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="featured" value="1"
                                   class="rounded bg-gray-800 border-gray-700 text-neon-blue">
                            <span class="text-gray-300">Destacado</span>
                        </label>

                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="trending" value="1"
                                   class="rounded bg-gray-800 border-gray-700 text-neon-purple">
                            <span class="text-gray-300">Tendencia</span>
                        </label>

                        <label class="flex items-center space-x-3">
                            <input type="checkbox" name="is_exclusive" value="1"
                                   class="rounded bg-gray-800 border-gray-700 text-neon-red">
                            <span class="text-gray-300">Exclusivo 🔥</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full px-6 py-4 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                        Crear Producto
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-store-layout>
