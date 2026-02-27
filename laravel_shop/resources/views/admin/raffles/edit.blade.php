<x-store-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.raffles.index') }}" class="text-gray-400 hover:text-neon-purple transition">
                    ← Volver a la lista
                </a>
            </div>

            <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Editar Sorteo</h1>

                <form action="{{ route('admin.raffles.update', $raffle) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Nombre del sorteo</label>
                        <input type="text" name="name" value="{{ old('name', $raffle->name) }}" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Descripción</label>
                        <textarea name="description" rows="3"
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">{{ old('description', $raffle->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Producto a sortear</label>
                        <select name="product_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                            <option value="">Selecciona un producto</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ (old('product_id', $raffle->product_id) == $product->id) ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->price }}€)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Fecha de inicio</label>
                            <input type="datetime-local" name="start_date" 
                                   value="{{ old('start_date', $raffle->start_date->format('Y-m-d\TH:i')) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Fecha de fin</label>
                            <input type="datetime-local" name="end_date" 
                                   value="{{ old('end_date', $raffle->end_date->format('Y-m-d\TH:i')) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Precio por entrada (€)</label>
                            <input type="number" step="0.01" name="ticket_price" value="{{ old('ticket_price', $raffle->ticket_price) }}" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Límite de entradas</label>
                            <input type="number" name="max_entries" value="{{ old('max_entries', $raffle->max_entries) }}"
                                   placeholder="Sin límite"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>
                    </div>

                    @if($raffle->total_entries > 0)
                        <div class="bg-neon-purple/10 p-4 rounded-lg">
                            <p class="text-white">
                                <span class="font-bold">Total entradas:</span> {{ $raffle->total_entries }}
                            </p>
                            @if($raffle->winner)
                                <p class="text-neon-purple mt-2">
                                    Ganador: {{ $raffle->winner->name }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="pt-4">
                        <button type="submit" class="w-full px-6 py-4 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition">
                            Actualizar Sorteo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-store-layout>
