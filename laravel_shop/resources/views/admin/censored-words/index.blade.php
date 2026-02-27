<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-neon-blue">🚫 Gestión de Palabras Censuradas</span>
                    </h1>
                    <p class="text-gray-400">Administra las palabras prohibidas en el chat</p>
                </div>
                <a href="{{ route('admin.censored-words.create') }}" 
                   class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                    + Añadir Palabra
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-800 border-b border-neon-blue/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-neon-blue">ID</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Palabra</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Severidad</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Estado</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($words as $word)
                            <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-300">{{ $word->id }}</td>
                                <td class="px-6 py-4 text-white font-bold">{{ $word->word }}</td>
                                <td class="px-6 py-4">
                                    @if($word->severity == 'high')
                                        <span class="px-3 py-1 bg-neon-red/20 text-neon-red rounded-full text-xs">Alta</span>
                                    @elseif($word->severity == 'medium')
                                        <span class="px-3 py-1 bg-yellow-600/20 text-yellow-500 rounded-full text-xs">Media</span>
                                    @else
                                        <span class="px-3 py-1 bg-neon-blue/20 text-neon-blue rounded-full text-xs">Baja</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($word->active)
                                        <span class="text-green-500">Activo</span>
                                    @else
                                        <span class="text-gray-500">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.censored-words.edit', $word) }}" 
                                           class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                            Editar
                                        </a>
                                        <form action="{{ route('admin.censored-words.toggle', $word) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1 {{ $word->active ? 'bg-yellow-600/10 text-yellow-500 hover:bg-yellow-600 hover:text-white' : 'bg-green-600/10 text-green-500 hover:bg-green-600 hover:text-white' }} rounded-lg transition text-sm">
                                                {{ $word->active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.censored-words.destroy', $word) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Eliminar esta palabra?')"
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $words->links() }}
            </div>

            <!-- Comandos útiles -->
            <div class="mt-8 bg-gamer-card rounded-2xl border border-neon-purple/20 p-6">
                <h2 class="text-xl font-bold text-white mb-4">🛠️ Comandos útiles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-800/50 p-4 rounded-lg">
                        <code class="text-neon-blue">php artisan badwords:find --stats</code>
                        <p class="text-sm text-gray-400 mt-2">Ver estadísticas de censura</p>
                    </div>
                    <div class="bg-gray-800/50 p-4 rounded-lg">
                        <code class="text-neon-blue">php artisan badwords:find --check</code>
                        <p class="text-sm text-gray-400 mt-2">Analizar mensajes existentes</p>
                    </div>
                    <div class="bg-gray-800/50 p-4 rounded-lg">
                        <code class="text-neon-blue">php artisan badwords:find --add="palabra"</code>
                        <p class="text-sm text-gray-400 mt-2">Añadir palabra desde terminal</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
