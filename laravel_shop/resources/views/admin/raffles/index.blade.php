<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-neon-purple">🎲 Gestión de Sorteos</span>
                    </h1>
                    <p class="text-gray-400">Administra los sorteos mensuales</p>
                </div>
                <a href="{{ route('admin.raffles.create') }}" class="px-6 py-3 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition">
                    + Nuevo Sorteo
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-900/50 border border-neon-red text-red-200 px-4 py-3 rounded-lg mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-800 border-b border-neon-purple/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-neon-purple">ID</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Título</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Producto</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Precio/Entrada</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Fecha fin</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Estado</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Ganador</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($raffles as $raffle)
                            @php
                                $extra = $raffle->getExtraData();
                                $product = $raffle->getProduct();
                                $cleanDesc = $raffle->getCleanDescription();
                            @endphp
                            <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-300">{{ $raffle->id }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium">{{ $raffle->title }}</div>
                                    <div class="text-gray-500 text-xs">{{ Str::limit($cleanDesc, 50) }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($product)
                                        <div class="flex items-center space-x-2">
                                            <img src="{{ $product->image }}" alt="" class="w-8 h-8 object-cover rounded">
                                            <span class="text-gray-300">{{ $product->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-500">Producto no disponible</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-neon-purple">{{ $extra['ticket_price'] ?? 20 }}€</span>
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    {{ isset($extra['end_date']) ? \Carbon\Carbon::parse($extra['end_date'])->format('d/m/Y H:i') : ($raffle->draw_date ? $raffle->draw_date->format('d/m/Y H:i') : 'No definida') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($raffle->status == 'pending')
                                        <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-xs">Activo</span>
                                    @elseif($raffle->status == 'completed')
                                        <span class="px-3 py-1 bg-gray-600/20 text-gray-400 rounded-full text-xs">Finalizado</span>
                                    @else
                                        <span class="px-3 py-1 bg-yellow-600/20 text-yellow-400 rounded-full text-xs">{{ $raffle->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($raffle->winner)
                                        <span class="text-neon-blue">{{ $raffle->winner->name }}</span>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.raffles.edit', $raffle) }}" 
                                           class="px-3 py-1 bg-neon-purple/10 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition text-sm">
                                            Editar
                                        </a>
                                        
                                        @if($raffle->status == 'pending')
                                            @if(!$raffle->winner)
                                                <form action="{{ route('admin.raffles.draw', $raffle) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm"
                                                            onclick="return confirm('¿Sortear ganador ahora? Esto finalizará el sorteo.')">
                                                        Sortear
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                        
                                        @if($raffle->status == 'pending' && $raffle->draw_date < now())
                                            <form action="{{ route('admin.raffles.activate', $raffle) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-green-600/10 text-green-400 rounded-lg hover:bg-green-600 hover:text-white transition text-sm">
                                                    Activar
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.raffles.destroy', $raffle) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Eliminar este sorteo?')"
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
                {{ $raffles->links() }}
            </div>
        </div>
    </div>
</x-store-layout>
