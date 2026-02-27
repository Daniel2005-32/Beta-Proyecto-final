<x-store-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.raffles.index') }}" class="text-gray-400 hover:text-neon-purple transition">
                    ← Volver a la lista
                </a>
            </div>

            <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Crear Nuevo Sorteo</h1>

                <form action="{{ route('admin.raffles.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2 font-bold">Título del sorteo</label>
                            <input type="text" name="title" value="{{ old('title') }}" required
                                   placeholder="Ej: Sorteo Mensual de Marzo"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2 font-bold">Descripción</label>
                            <textarea name="description" rows="3"
                                      placeholder="Describe el sorteo..."
                                      class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Producto a sortear</label>
                            <select name="product_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                                <option value="">Selecciona un producto</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} ({{ $product->price }}€)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Precio por entrada (€)</label>
                            <input type="number" step="0.01" name="ticket_price" value="{{ old('ticket_price', 20) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                            <p class="text-xs text-gray-500 mt-1">Cada compra de este importe da 1 entrada</p>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Fecha de inicio</label>
                            <input type="datetime-local" name="start_date" value="{{ old('start_date') }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Fecha de fin</label>
                            <input type="datetime-local" name="end_date" value="{{ old('end_date') }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Límite de entradas</label>
                            <input type="number" name="max_entries" value="{{ old('max_entries') }}"
                                   placeholder="Sin límite"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                            <p class="text-xs text-gray-500 mt-1">Déjalo vacío para entradas ilimitadas</p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-6 py-4 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                            Crear Sorteo
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-6 bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                <h2 class="text-xl font-bold text-white mb-4">📋 Información importante</h2>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li class="flex items-start gap-2">
                        <span class="text-neon-purple">•</span>
                        Los usuarios obtendrán 1 entrada por cada {{ old('ticket_price', 20) }}€ gastados.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-neon-purple">•</span>
                        El sorteo se realizará automáticamente en la fecha de fin.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-neon-purple">•</span>
                        Puedes activar o sortear manualmente desde el panel.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</x-store-layout>
