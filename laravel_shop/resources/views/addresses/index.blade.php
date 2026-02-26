<x-store-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-black text-white">
                    <span class="text-neon-blue">📍 Mis Direcciones</span>
                </h1>
                <a href="{{ route('addresses.create') }}" class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                    + Nueva Dirección
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($addresses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($addresses as $address)
                        <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6 relative">
                            @if($address->is_default)
                                <span class="absolute top-4 right-4 bg-neon-blue text-gamer-dark text-xs font-bold px-2 py-1 rounded-full">
                                    PREDETERMINADA
                                </span>
                            @endif
                            
                            <h3 class="text-xl font-bold text-white mb-2">{{ $address->name }}</h3>
                            <p class="text-gray-400 text-sm mb-1">{{ $address->street }}, {{ $address->number }}</p>
                            @if($address->complement)
                                <p class="text-gray-400 text-sm mb-1">{{ $address->complement }}</p>
                            @endif
                            <p class="text-gray-400 text-sm mb-1">{{ $address->city }} - {{ $address->state }}</p>
                            <p class="text-gray-400 text-sm mb-1">CP: {{ $address->zipcode }}</p>
                            <p class="text-gray-400 text-sm mb-3">Tel: {{ $address->phone }}</p>
                            
                            <div class="flex space-x-2 mt-4">
                                <a href="{{ route('addresses.edit', $address) }}" class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                    Editar
                                </a>
                                @if(!$address->is_default)
                                    <a href="{{ route('addresses.set-default', $address) }}" class="px-3 py-1 bg-neon-purple/10 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition text-sm">
                                        Establecer como predeterminada
                                    </a>
                                    <form action="{{ route('addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('¿Eliminar esta dirección?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition text-sm">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gamer-card rounded-2xl border border-gray-800 p-12 text-center">
                    <div class="text-5xl mb-4">📍</div>
                    <h2 class="text-2xl font-bold text-white mb-2">No tienes direcciones guardadas</h2>
                    <p class="text-gray-400 mb-6">Añade una dirección para poder realizar tus pedidos</p>
                    <a href="{{ route('addresses.create') }}" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                        Añadir primera dirección
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-store-layout>
