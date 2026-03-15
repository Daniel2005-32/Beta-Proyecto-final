<div class="bg-gamer-card rounded-2xl border border-neon-red/20 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 border-b border-neon-red/20">
            <tr>
                <th class="px-6 py-4 text-left text-neon-red">ID</th>
                <th class="px-6 py-4 text-left text-neon-red">Usuario</th>
                <th class="px-6 py-4 text-left text-neon-red">Baneado por</th>
                <th class="px-6 py-4 text-left text-neon-red">Razón</th>
                <th class="px-6 py-4 text-left text-neon-red">Duración</th>
                <th class="px-6 py-4 text-left text-neon-red">Fecha</th>
                <th class="px-6 py-4 text-left text-neon-red">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bans as $ban)
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-gray-300">{{ $ban->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-red to-neon-purple flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($ban->user->name, 0, 1)) }}
                            </div>
                            <span class="text-white font-medium">{{ $ban->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $ban->bannedBy->name ?? 'Sistema' }}</td>
                    <td class="px-6 py-4 text-gray-400 max-w-xs truncate">{{ $ban->reason }}</td>
                    <td class="px-6 py-4">
                        @if($ban->is_permanent)
                            <span class="text-neon-red">Permanente</span>
                        @else
                            <span class="text-yellow-500">{{ $ban->banned_until->diffForHumans() }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-sm">{{ $ban->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.bans.unban', $ban->user) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-green-600/10 text-green-400 rounded-lg hover:bg-green-600 hover:text-white transition text-sm"
                                    onclick="return confirm('¿Quitar el baneo a este usuario?')">
                                Desbanear
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <p>No hay baneos activos</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $bans->links() }}
</div>
