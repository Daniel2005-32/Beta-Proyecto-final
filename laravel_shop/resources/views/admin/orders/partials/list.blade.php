<div class="bg-gamer-card rounded-2xl border border-neon-green/20 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 border-b border-neon-green/20">
            <tr>
                <th class="px-6 py-4 text-left text-neon-green">ID</th>
                <th class="px-6 py-4 text-left text-neon-green">Cliente</th>
                <th class="px-6 py-4 text-left text-neon-green">Total</th>
                <th class="px-6 py-4 text-left text-neon-green">Fecha</th>
                <th class="px-6 py-4 text-left text-neon-green">Estado</th>
                <th class="px-6 py-4 text-left text-neon-green">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-gray-300">#{{ $order->id }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($order->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <span class="text-white font-medium">{{ $order->user->name }}</span>
                                <p class="text-gray-500 text-xs">{{ $order->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-neon-green font-bold">{{ number_format($order->total, 2) }}€</span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-sm">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4">
                        @if($order->status == 'pending')
                            <span class="px-3 py-1 bg-yellow-600/20 text-yellow-400 rounded-full text-xs">Pendiente</span>
                        @elseif($order->status == 'completed')
                            <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-xs">Completado</span>
                        @else
                            <span class="px-3 py-1 bg-red-600/20 text-red-400 rounded-full text-xs">Cancelado</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.orders.show', $order) }}" 
                               class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                Ver detalles
                            </a>
                            <form action="{{ route('admin.orders.destroy', $order) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Eliminar este pedido?')"
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
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <p>No hay pedidos para mostrar</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $orders->links() }}
</div>
