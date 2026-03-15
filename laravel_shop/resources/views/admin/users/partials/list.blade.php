<div class="bg-gamer-card rounded-2xl border border-neon-purple/20 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 border-b border-neon-purple/20">
            <tr>
                <th class="px-6 py-4 text-left text-neon-purple">ID</th>
                <th class="px-6 py-4 text-left text-neon-purple">Usuario</th>
                <th class="px-6 py-4 text-left text-neon-purple">Email</th>
                <th class="px-6 py-4 text-left text-neon-purple">Registro</th>
                <th class="px-6 py-4 text-left text-neon-purple">Admin</th>
                <th class="px-6 py-4 text-left text-neon-purple">Estado</th>
                <th class="px-6 py-4 text-left text-neon-purple">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-gray-300">{{ $user->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-purple to-neon-blue flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <span class="text-white font-medium">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400">{{ $user->email }}</td>
                    <td class="px-6 py-4 text-gray-400">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">
                        @if($user->is_admin)
                            <span class="text-neon-purple">✓</span>
                        @else
                            <span class="text-gray-600">✗</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($user->isBanned())
                            <span class="px-3 py-1 bg-neon-red/20 text-neon-red rounded-full text-xs">Baneado</span>
                        @else
                            <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-xs">Activo</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            @if(!$user->is_admin)
                                <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-neon-purple/10 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition text-sm">
                                        Hacer Admin
                                    </button>
                                </form>
                            @endif
                            @if(!$user->isBanned())
                                <a href="{{ route('admin.bans.create', ['user_id' => $user->id]) }}" 
                                   class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition text-sm">
                                    Banear
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <p>No hay usuarios para mostrar</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $users->links() }}
</div>
