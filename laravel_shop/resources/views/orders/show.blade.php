<x-store-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón para volver -->
            <div class="mb-6">
                <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-neon-blue transition">
                    ← Seguir comprando
                </a>
            </div>

            <!-- Cabecera -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-black text-white mb-2">Pedido #{{ $order->id }}</h1>
                        <p class="text-gray-400">Realizado el {{ $order->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        @if($order->status == 'pending')
                            <span class="bg-yellow-600/20 text-yellow-400 px-4 py-2 rounded-full text-sm font-bold border border-yellow-600/30">
                                Pendiente
                            </span>
                        @elseif($order->status == 'completed')
                            <span class="bg-green-600/20 text-green-400 px-4 py-2 rounded-full text-sm font-bold border border-green-600/30">
                                Completado
                            </span>
                        @else
                            <span class="bg-red-600/20 text-red-400 px-4 py-2 rounded-full text-sm font-bold border border-red-600/30">
                                Cancelado
                            </span>
                        @endif
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

                <!-- Total -->
                <div class="mt-6 pt-6 border-t border-gray-800 flex justify-between items-center">
                    <span class="text-xl text-white font-bold">Total</span>
                    <span class="text-2xl text-neon-purple font-black">{{ number_format($order->total, 2) }}€</span>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="bg-gamer-card rounded-2xl border border-neon-red/20 p-8">
                <h2 class="text-2xl font-bold text-white mb-4">Información del pedido</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Estado</p>
                        <p class="text-white font-bold">{{ ucfirst($order->status) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Fecha</p>
                        <p class="text-white font-bold">{{ $order->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Total productos</p>
                        <p class="text-white font-bold">{{ $order->items->count() }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">ID de pedido</p>
                        <p class="text-white font-bold">#{{ $order->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
