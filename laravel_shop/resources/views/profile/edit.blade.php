<x-store-layout>
    <div class="py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ url()->previous() }}" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver
                </a>
            </div>

            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Mi Perfil</h1>
                
                @if(session('success'))
                    <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="block text-gray-300 mb-2">Nombre</label>
                        <input type="text" name="name" value="{{ auth()->user()->name }}" 
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        @error('name')
                            <p class="text-neon-red text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-gray-300 mb-2">Email</label>
                        <input type="email" name="email" value="{{ auth()->user()->email }}" 
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        @error('email')
                            <p class="text-neon-red text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                            Actualizar perfil
                        </button>
                    </div>
                </form>
                
                <hr class="border-gray-800 my-8">
                
                <div>
                    <h2 class="text-xl font-bold text-white mb-4">Zona peligrosa</h2>
                    <form method="POST" action="{{ route('profile.destroy') }}" onsubmit="return confirm('¿Estás seguro? Esta acción no se puede deshacer.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 bg-neon-red/20 text-neon-red font-bold rounded-lg hover:bg-neon-red hover:text-white transition">
                            Eliminar cuenta
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
