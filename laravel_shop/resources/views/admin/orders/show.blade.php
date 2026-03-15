<x-store-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver a la lista de pedidos
                </a>
            </div>

            <!-- Cabecera -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-black text-white mb-2">Pedido #{{ $order->id }}</h1>
                        <p class="text-gray-400">Realizado el {{ $order->created_at->format('d/m/Y H:i') }}</p>
                        <p class="text-gray-400 mt-1">Cliente: <span class="text-white">{{ $order->user->name }}</span> ({{ $order->user->email }})</p>
                    </div>
                    <div>
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="flex space-x-2">
                            @csrf
                            <select name="status" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white">
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completado</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                Actualizar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Productos del pedido -->
            <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8 mb-6">
                <h2 class="text-2xl font-bold text-white mb-6">Productos</h2>
                
                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex items-center justify-between py-4 border-b border-gray-800 last:border-0">
                            <div class="flex items-center space-x-4">
                                <img src="{{ $item->product->image }}" alt="{{ $item->product->name }}" class="w-16 h-16 object-cover rounded-lg">
                                <div>
                                    <h3 class="text-white font-bold">{{ $item->product->name }}</h3>
                                    <p class="text-gray-400 text-sm">Cantidad: {{ $item->quantity }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-neon-blue font-bold">{{ number_format($item->price * $item->quantity, 2) }}€</p>
                                <p class="text-gray-500 text-sm">{{ number_format($item->price, 2) }}€ x {{ $item->quantity }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Totales base (sin impuestos) -->
                <div class="mt-6 pt-6 border-t border-gray-800">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Subtotal:</span>
                            <span class="text-white">{{ number_format($order->total, 2) }}€</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-800">
                            <span class="text-white">Total del pedido:</span>
                            <span class="text-neon-purple">{{ number_format($order->total, 2) }}€</span>
                        </div>
                        
                        <!-- Información de impuestos (solo texto) -->
                        @if($order->address)
                            @php
                                $province = $order->address->state;
                                $taxRate = App\Helpers\PriceHelper::getTaxRate($province);
                                $taxName = $taxRate == 7 ? 'IGIC' : 'IVA';
                            @endphp
                            <p class="text-xs text-gray-500 text-right mt-2">
                                * {{ $taxName }} {{ $taxRate }}% aplicado (cliente de {{ $province }})
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Dirección de entrega -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <h2 class="text-2xl font-bold text-white mb-4">📍 Dirección de entrega</h2>
                
                @if($order->address)
                    <div class="space-y-2 text-gray-300">
                        <p class="font-bold text-white text-lg">{{ $order->address->name }}</p>
                        <p>{{ $order->address->street }}, {{ $order->address->number }}</p>
                        @if($order->address->complement)
                            <p>{{ $order->address->complement }}</p>
                        @endif
                        <p>{{ $order->address->city }} - {{ $order->address->state }}</p>
                        <p>CP: {{ $order->address->zipcode }}</p>
                        <p>Tel: {{ $order->address->phone }}</p>
                    </div>
                @else
                    <p class="text-gray-400">No hay dirección asociada a este pedido</p>
                @endif
            </div>
        </div>
    </div>
</x-store-layout>
