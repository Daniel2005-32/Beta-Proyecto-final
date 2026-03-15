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

                <!-- Total base (sin impuestos) -->
                <div class="mt-6 pt-6 border-t border-gray-800">
                    <div class="flex justify-between items-center">
                        <span class="text-xl text-white font-bold">Total del pedido</span>
                        <span class="text-2xl text-neon-purple font-black">{{ number_format($order->total, 2) }}€</span>
                    </div>
                    
                    <!-- Información de impuestos -->
                    @if($order->address)
                        @php
                            $province = $order->address->state;
                            $taxRate = App\Helpers\PriceHelper::getTaxRate($province);
                            $taxName = $taxRate == 7 ? 'IGIC' : 'IVA';
                        @endphp
                        <p class="text-xs text-gray-500 text-right mt-2">
                            * {{ $taxName }} {{ $taxRate }}% aplicado (según dirección de envío)
                        </p>
                    @endif
                </div>
            </div>

            <!-- Información de pago -->
            @if($order->card_last_four)
            <div class="bg-gamer-card rounded-2xl border border-neon-green/20 p-8 mb-6">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-neon-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    💳 Información de pago
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Tarjeta</p>
                        <p class="text-white font-bold">{{ $order->card_brand ?? 'Desconocida' }} terminada en {{ $order->card_last_four }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Estado del pago</p>
                        <p class="text-green-400 font-bold">✅ Pagado</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Dirección de entrega -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8 mb-6">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-neon-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    📍 Dirección de entrega
                </h2>
                
                @if($order->address)
                    <div class="space-y-2 text-gray-300">
                        <p class="font-bold text-white text-lg">{{ $order->address->name }}</p>
                        <p class="flex items-start gap-2">
                            <span class="text-neon-blue min-w-[80px]">Calle:</span>
                            {{ $order->address->street }}, {{ $order->address->number }}
                        </p>
                        @if($order->address->complement)
                            <p class="flex items-start gap-2">
                                <span class="text-neon-blue min-w-[80px]">Complemento:</span>
                                {{ $order->address->complement }}
                            </p>
                        @endif
                        <p class="flex items-start gap-2">
                            <span class="text-neon-blue min-w-[80px]">Ciudad:</span>
                            {{ $order->address->city }} - {{ $order->address->state }}
                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-neon-blue min-w-[80px]">Código Postal:</span>
                            {{ $order->address->zipcode }}
                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-neon-blue min-w-[80px]">Teléfono:</span>
                            {{ $order->address->phone }}
                        </p>
                    </div>
                @else
                    <p class="text-gray-400">No hay dirección asociada a este pedido</p>
                @endif
            </div>

            <!-- Información adicional -->
            <div class="bg-gamer-card rounded-2xl border border-neon-red/20 p-8">
                <h2 class="text-2xl font-bold text-white mb-4">📋 Información del pedido</h2>
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

            <!-- Botón para volver a comprar -->
            <div class="mt-8 text-center">
                <a href="{{ route('products.index') }}" class="inline-flex items-center text-gray-400 hover:text-neon-blue transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Seguir comprando
                </a>
            </div>
        </div>
    </div>
</x-store-layout>
