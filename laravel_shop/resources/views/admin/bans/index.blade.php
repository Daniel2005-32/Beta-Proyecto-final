<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-neon-red">🚫 Gestión de Baneos</span>
                    </h1>
                    <p class="text-gray-400">Administra los usuarios baneados</p>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-neon-purple/10 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        Ver Usuarios
                    </a>
                    <a href="{{ route('admin.bans.create') }}" class="px-6 py-2 bg-neon-red text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(255,0,85,0.4)]">
                        + Nuevo Baneo
                    </a>
                </div>
            </div>

            <!-- Resto del contenido igual... -->
            @include('admin.bans.partials.list')
        </div>
    </div>
</x-store-layout>
