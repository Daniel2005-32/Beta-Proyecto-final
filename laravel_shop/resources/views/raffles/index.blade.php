<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">
                    <span class="text-neon-purple">🎲 Sorteos Mensuales</span>
                </h1>
                <p class="text-gray-400">¡Participa en nuestros sorteos! Cada compra de 20€ te da una entrada.</p>
            </div>

            @php
                $activeRaffles = App\Models\Raffle::where('status', 'pending')
                    ->where('draw_date', '>', now())
                    ->get();
                $endedRaffles = App\Models\Raffle::where('status', 'completed')
                    ->orderBy('draw_date', 'desc')
                    ->take(5)
                    ->get();
            @endphp

            @if($activeRaffles->count() > 0)
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-white mb-6">🎯 Sorteos Activos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($activeRaffles as $raffle)
                            @php
                                $extra = $raffle->getExtraData();
                                $product = $raffle->getProduct();
                                $userEntries = Auth::check() ? $raffle->getUserEntries(Auth::id()) : 0;
                                $totalEntries = $raffle->entries()->sum('quantity');
                            @endphp
                            <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 overflow-hidden hover:border-neon-purple transition">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-xl font-bold text-white">{{ $raffle->title }}</h3>
                                        <span class="bg-neon-purple/20 text-neon-purple px-3 py-1 rounded-full text-xs font-bold">ACTIVO</span>
                                    </div>
                                    
                                    @if($product)
                                        <div class="mb-4">
                                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-40 object-cover rounded-lg">
                                        </div>
                                        <p class="text-gray-400 text-sm mb-2">{{ $raffle->getCleanDescription() }}</p>
                                        <p class="text-neon-blue font-bold text-lg mb-2">{{ $product->name }}</p>
                                    @endif
                                    
                                    <div class="space-y-2 mb-4 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Precio del producto:</span>
                                            <span class="text-white">{{ number_format($product->price ?? 0, 2) }}€</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Cada entrada:</span>
                                            <span class="text-neon-purple">{{ $extra['ticket_price'] ?? 20 }}€ en compras</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Total entradas:</span>
                                            <span class="text-white">{{ $totalEntries }}</span>
                                        </div>
                                        @auth
                                            <div class="flex justify-between">
                                                <span class="text-gray-400">Tus entradas:</span>
                                                <span class="text-neon-blue font-bold">{{ $userEntries }}</span>
                                            </div>
                                            @if($totalEntries > 0)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Tu probabilidad:</span>
                                                    <span class="text-neon-purple">{{ $raffle->getUserChance(Auth::id()) }}%</span>
                                                </div>
                                            @endif
                                        @endauth
                                        <div class="flex justify-between pt-2 border-t border-gray-800">
                                            <span class="text-gray-400">Fecha del sorteo:</span>
                                            <span class="text-neon-purple">{{ Carbon\Carbon::parse($extra['end_date'] ?? $raffle->draw_date)->format('d/m/Y H:i') }}</span>
                                        </div>
                                    </div>
                                    
                                    @auth
                                        <div class="mt-4 text-center text-sm text-gray-400">
                                            @if($userEntries > 0)
                                                <p class="text-green-500">¡Tienes {{ $userEntries }} entrada(s)!</p>
                                            @else
                                                <p>Compra productos para obtener entradas</p>
                                            @endif
                                        </div>
                                    @else
                                        <div class="mt-4 text-center">
                                            <a href="{{ route('login') }}" class="text-neon-purple hover:underline text-sm">
                                                Inicia sesión para participar
                                            </a>
                                        </div>
                                    @endauth
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center py-12 bg-gamer-card rounded-2xl border border-gray-800 mb-12">
                    <p class="text-gray-400">No hay sorteos activos en este momento</p>
                </div>
            @endif

            @if($endedRaffles->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold text-white mb-6">🏁 Últimos Sorteos Finalizados</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-60">
                        @foreach($endedRaffles as $raffle)
                            @php
                                $product = $raffle->getProduct();
                            @endphp
                            <div class="bg-gamer-card rounded-2xl border border-gray-800 overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-white mb-2">{{ $raffle->title }}</h3>
                                    @if($raffle->winner)
                                        <p class="text-green-500 text-sm">Ganador: {{ $raffle->winner->name }}</p>
                                    @else
                                        <p class="text-gray-500 text-sm">Sorteo sin ganador</p>
                                    @endif
                                    @if($product)
                                        <p class="text-gray-400 text-xs mt-2">Premio: {{ $product->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mt-12 bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                <h2 class="text-xl font-bold text-white mb-4">📊 Cómo funcionan los sorteos</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl mb-2">🛒</div>
                        <h3 class="text-neon-blue font-bold mb-2">Compra</h3>
                        <p class="text-sm text-gray-400">Cada compra de 20€ te da 1 entrada</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl mb-2">🎟️</div>
                        <h3 class="text-neon-purple font-bold mb-2">Acumula</h3>
                        <p class="text-sm text-gray-400">Cuantas más entradas, más probabilidades</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl mb-2">🏆</div>
                        <h3 class="text-neon-red font-bold mb-2">Gana</h3>
                        <p class="text-sm text-gray-400">Al finalizar, un ganador se lleva el premio</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
