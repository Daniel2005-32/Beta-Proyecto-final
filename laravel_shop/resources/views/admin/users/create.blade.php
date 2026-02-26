<x-store-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabecera -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-2">
                    <span class="text-neon-blue">➕ Crear Nuevo Usuario</span>
                </h1>
                <p class="text-gray-400">Añade un nuevo usuario al sistema</p>
            </div>

            <!-- Formulario -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Nombre</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition @error('name') border-neon-red @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition @error('email') border-neon-red @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Contraseña</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition @error('password') border-neon-red @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-neon-red">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition">
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="is_admin" id="is_admin" value="1"
                               class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                        <label for="is_admin" class="text-gray-300">Usuario administrador</label>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="submit" 
                                class="px-8 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                            Crear Usuario
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
