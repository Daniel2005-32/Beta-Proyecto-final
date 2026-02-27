<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-neon-red">🚫 Gestión de Baneos</span>
                    </h1>
                    <p class="text-gray-400">Administra los usuarios baneados del sistema</p>
                </div>
                <a href="{{ route('admin.bans.create') }}" 
                   class="px-6 py-3 bg-neon-red text-white font-bold rounded-lg hover:scale-105 transition">
                    + Nuevo Baneo
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-gamer-card rounded-2xl border border-neon-red/20 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-800 border-b border-neon-red/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-neon-red">ID</th>
                            <th class="px-6 py-4 text-left text-neon-red">Usuario</th>
                            <th class="px-6 py-4 text-left text-neon-red">Motivo</th>
                            <th class="px-6 py-4 text-left text-neon-red">Duración</th>
                            <th class="px-6 py-4 text-left text-neon-red">Tiempo restante</th>
                            <th class="px-6 py-4 text-left text-neon-red">Baneado por</th>
                            <th class="px-6 py-4 text-left text-neon-red">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bans as $ban)
                            <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-300">{{ $ban->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-neon-red flex items-center justify-center text-white font-bold text-sm">
                                            {{ strtoupper(substr($ban->user->name, 0, 1)) }}
                                        </div>
                                        <span class="text-white font-medium">{{ $ban->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-300">{{ $ban->reason }}</td>
                                <td class="px-6 py-4">
                                    @if($ban->is_permanent)
                                        <span class="px-3 py-1 bg-neon-red/20 text-neon-red rounded-full text-xs">Permanente</span>
                                    @elseif($ban->banned_until)
                                        <span class="px-3 py-1 bg-yellow-600/20 text-yellow-500 rounded-full text-xs">
                                            Hasta {{ $ban->banned_until->format('d/m/Y H:i') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($ban->isActive())
                                        <span class="text-neon-red font-bold">{{ $ban->timeLeft() }}</span>
                                    @else
                                        <span class="text-green-500">Expirado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-300">{{ $ban->banner?->name ?? 'Sistema' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        @if($ban->isActive())
                                            <form action="{{ route('admin.bans.unban', $ban->user) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-600/10 text-green-500 rounded-lg hover:bg-green-600 hover:text-white transition text-sm">
                                                    Desbanear
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.bans.destroy', $ban) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Eliminar este registro de baneo?')"
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
                {{ $bans->links() }}
            </div>
        </div>
    </div>
</x-store-layout>
