<x-store-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabecera -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-2">
                    <span class="text-neon-blue">✏️ Editar Usuario</span>
                </h1>
                <p class="text-gray-400">Modifica los datos del usuario</p>
            </div>

            <!-- Formulario -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Nombre</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition @error('name') border-neon-red @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition @error('email') border-neon-red @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border-t border-gray-800 pt-4">
                        <h3 class="text-lg font-bold text-white mb-4">Cambiar contraseña (opcional)</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-300 mb-2 font-bold">Nueva contraseña</label>
                                <input type="password" name="password" minlength="8"
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition">
                            </div>

                            <div>
                                <label class="block text-gray-300 mb-2 font-bold">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation"
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="is_admin" id="is_admin" value="1"
                               {{ $user->is_admin ? 'checked' : '' }}
                               class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                        <label for="is_admin" class="text-gray-300">Usuario administrador</label>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="submit" 
                                class="px-8 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                            Actualizar Usuario
                        </button>
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-8 py-3 bg-gray-800 text-gray-300 font-bold rounded-lg hover:bg-gray-700 transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-store-layout>
