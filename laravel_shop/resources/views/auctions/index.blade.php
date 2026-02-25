<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">
                    <span class="text-neon-purple">⚡ Subastas en Vivo</span>
                </h1>
                <p class="text-gray-400">Cuando solo queda 1 unidad, comienza la subasta con 20% de descuento</p>
            </div>

            @auth
                <!-- Subastas Activas (solo para usuarios autenticados) -->
                <div class="mb-16">
                    <h2 class="text-2xl font-bold text-white mb-6">Activas ⏳</h2>
                    
                    @if($activeAuctions->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($activeAuctions as $auction)
                                <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 overflow-hidden hover:border-neon-purple transition group">
                                    <div class="relative">
                                        <img src="{{ $auction->product->image }}" alt="{{ $auction->product->name }}" class="w-full h-48 object-cover">
                                        <div class="absolute top-4 right-4 bg-neon-purple text-white px-3 py-1 rounded-full text-sm font-bold">
                                            ⏱️ {{ $auction->timeLeft() }}
                                        </div>
                                        <div class="absolute top-4 left-4 bg-neon-red text-white px-3 py-1 rounded-full text-sm font-bold">
                                            -20%
                                        </div>
                                    </div>
                                    
                                    <div class="p-6">
                                        <h3 class="text-xl font-bold text-white mb-2">{{ $auction->product->name }}</h3>
                                        
                                        <div class="space-y-2 mb-4">
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-400">Precio original:</span>
                                                <span class="text-gray-300 line-through">{{ number_format($auction->product->price, 2) }}€</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-400">Precio inicial:</span>
                                                <span class="text-gray-300">{{ number_format($auction->starting_price, 2) }}€</span>
                                            </div>
                                            <div class="flex justify-between text-lg font-bold">
                                                <span class="text-gray-300">Puja actual:</span>
                                                <span class="text-neon-purple">{{ number_format($auction->current_price, 2) }}€</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-400">Próxima puja:</span>
                                                <span class="text-neon-blue">{{ number_format($auction->nextBidAmount(), 2) }}€+</span>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-400">Pujas:</span>
                                                <span class="text-gray-300">{{ $auction->total_bids }}</span>
                                            </div>
                                        </div>
                                        
                                        <a href="{{ route('auctions.show', $auction->id) }}" 
                                           class="block w-full text-center px-4 py-3 bg-neon-purple text-white font-bold rounded-lg hover:bg-neon-purple/80 transition">
                                            Pujar ahora
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6">
                            {{ $activeAuctions->links() }}
                        </div>
                    @else
                        <div class="text-center py-12 bg-gamer-card rounded-2xl border border-gray-800">
                            <p class="text-gray-400">No hay subastas activas en este momento</p>
                            <p class="text-gray-500 text-sm mt-2">Vuelve pronto, cuando los productos tengan stock=1 aparecerán aquí</p>
                        </div>
                    @endif
                </div>

                <!-- Subastas Finalizadas -->
                @if($endedAuctions->count() > 0)
                    <div>
                        <h2 class="text-2xl font-bold text-white mb-6">Finalizadas 🏁</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-75">
                            @foreach($endedAuctions as $auction)
                                <div class="bg-gamer-card rounded-2xl border border-gray-800 overflow-hidden">
                                    <div class="p-6">
                                        <h3 class="text-lg font-bold text-white mb-2">{{ $auction->product->name }}</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-400">Vendido por:</span>
                                                <span class="text-neon-purple">{{ number_format($auction->current_price, 2) }}€</span>
                                            </div>
                                            @if($auction->currentWinner)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Ganador:</span>
                                                    <span class="text-gray-300">{{ $auction->currentWinner->name }}</span>
                                                </div>
                                            @endif
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-400">Total pujas:</span>
                                                <span class="text-gray-300">{{ $auction->total_bids }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <!-- Mensaje para usuarios no autenticados -->
                <div class="text-center py-16 bg-gamer-card rounded-2xl border border-neon-purple/30">
                    <svg class="w-24 h-24 text-gray-600 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    
                    <h2 class="text-3xl font-bold text-white mb-4">🔒 Área de Subastas Privada</h2>
                    <p class="text-gray-400 mb-8 max-w-lg mx-auto">
                        Las subastas son exclusivas para miembros de Gamer Guild. 
                        Inicia sesión o regístrate para participar y conseguir productos únicos con grandes descuentos.
                    </p>
                    
                    <div class="flex gap-4 justify-center">
                        <a href="{{ route('login') }}" class="px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-neon-purple text-white font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                            Crear cuenta
                        </a>
                    </div>
                    
                    <div class="mt-8 text-sm text-gray-500">
                        <p>✨ Beneficios de ser miembro:</p>
                        <div class="flex gap-4 justify-center mt-2">
                            <span>⚡ Participar en subastas</span>
                            <span>💰 Ofertas exclusivas</span>
                            <span>🎁 Productos únicos</span>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</x-store-layout>
