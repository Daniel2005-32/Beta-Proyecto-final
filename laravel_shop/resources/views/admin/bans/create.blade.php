<x-store-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón volver -->
            <div class="mb-6">
                <a href="{{ route('admin.bans.index') }}" class="text-gray-400 hover:text-neon-blue transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a la lista de baneos
                </a>
            </div>

            <!-- Formulario de baneo -->
            <div class="bg-gamer-card rounded-2xl border border-neon-red/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6 flex items-center gap-2">
                    <svg class="w-8 h-8 text-neon-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                    Nuevo Baneo
                </h1>

                <form action="{{ route('admin.bans.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Selección de usuario -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Usuario a banear</label>
                        <select name="user_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-red">
                            <option value="">Selecciona un usuario</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Motivo del baneo -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Motivo del baneo</label>
                        <textarea name="reason" rows="3" required
                                  placeholder="Ej: Comportamiento inapropiado, insultos, spam..."
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-red"></textarea>
                    </div>

                    <!-- Duración del baneo -->
                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Duración del baneo</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <select name="duration" id="duration" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-red">
                                    <option value="1">1 hora</option>
                                    <option value="24">24 horas (1 día)</option>
                                    <option value="48">48 horas (2 días)</option>
                                    <option value="168">168 horas (7 días)</option>
                                    <option value="720">720 horas (30 días)</option>
                                    <option value="permanent">Permanente</option>
                                </select>
                            </div>
                            <div id="duration_unit_container">
                                <select name="duration_unit" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-red">
                                    <option value="hours">Horas</option>
                                    <option value="days">Días</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="flex space-x-4 pt-4">
                        <button type="submit" 
                                class="flex-1 px-6 py-4 bg-neon-red text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(255,0,85,0.4)]">
                            Banear usuario
                        </button>
                        <a href="{{ route('admin.bans.index') }}" 
                           class="flex-1 px-6 py-4 bg-gray-800 text-gray-300 font-bold rounded-lg hover:bg-gray-700 transition text-center">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>

            <!-- Información adicional -->
            <div class="mt-6 bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                <h2 class="text-xl font-bold text-white mb-4">📋 Información sobre baneos</h2>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li class="flex items-start gap-2">
                        <span class="text-neon-red">•</span>
                        Los baneos temporales expiran automáticamente después del tiempo seleccionado.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-neon-red">•</span>
                        Los baneos permanentes no expiran y deben ser removidos manualmente.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-neon-red">•</span>
                        El usuario baneado no podrá acceder a ninguna función de la tienda.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-neon-red">•</span>
                        Puedes desbanear a un usuario en cualquier momento desde la lista de baneos.
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar selector de unidad según la duración
        document.getElementById('duration').addEventListener('change', function() {
            const container = document.getElementById('duration_unit_container');
            if (this.value === 'permanent') {
                container.style.display = 'none';
            } else {
                container.style.display = 'block';
            }
        });
    </script>
</x-store-layout>
