<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón para volver -->
            <div class="mb-6">
                <a href="{{ route('auctions.index') }}" class="text-gray-400 hover:text-neon-purple transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a subastas
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Imagen y detalles -->
                <div class="lg:col-span-2">
                    <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 overflow-hidden">
                        <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-96 object-cover">
                        
                        <div class="p-8">
                            <h1 class="text-3xl font-black text-white mb-2">{{ $product->name }}</h1>
                            <p class="text-gray-400 mb-6">{{ $product->description }}</p>
                            
                            @if($product->isAuctionActive())
                                <div class="bg-gray-800/50 rounded-lg p-4 mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-400">Tiempo restante:</span>
                                        <span class="text-2xl font-bold text-neon-purple">{{ $product->auctionTimeLeft() }}</span>
                                    </div>
                                    <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden">
                                        <div class="h-full bg-neon-purple transition-all duration-1000" 
                                             style="width: {{ $product->getAuctionPercentage() }}%"></div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mt-8">
                                <h2 class="text-xl font-bold text-white mb-4">📊 Puja actual</h2>
                                <div class="bg-gray-800/50 rounded-lg p-6 text-center">
                                    @if($product->auction_final_price)
                                        <p class="text-4xl font-black text-neon-purple">{{ number_format($product->auction_final_price, 2) }}€</p>
                                        <p class="text-gray-400 text-sm mt-1">Precio final de subasta</p>
                                    @else
                                        <p class="text-4xl font-black text-neon-purple">{{ number_format($product->price, 2) }}€</p>
                                    @endif
                                    <p class="text-gray-400 mt-2">Mejor postor: {{ $product->auctionWinner->name ?? 'Nadie aún' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Panel de pujas -->
                <div class="lg:col-span-1">
                    <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 p-6 sticky top-24">
                        <h2 class="text-2xl font-bold text-white mb-6">💰 Pujar ahora</h2>
                        
                        @if($product->isAuctionActive())
                            @auth
                                @if(Auth::user()->isBanned())
                                    <div class="text-center py-8 bg-neon-red/10 border border-neon-red/30 rounded-lg">
                                        <svg class="w-12 h-12 text-neon-red mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                        </svg>
                                        <p class="text-neon-red font-bold mb-2">⛔ ACCESO DENEGADO</p>
                                        <p class="text-gray-400 text-sm">No puedes participar en subastas mientras estás baneado.</p>
                                    </div>
                                @else
                                    <form action="{{ route('auctions.bid', $product->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        
                                        <div>
                                            <label class="block text-gray-300 mb-2">Tu puja (€)</label>
                                            <input type="number" 
                                                name="amount" 
                                                step="0.01" 
                                                min="{{ $product->price + 0.01 }}" 
                                                value="{{ $product->price + 1 }}"
                                                class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"
                                                required>
                                            <p class="text-sm text-gray-400 mt-2">
                                                Puja actual: {{ number_format($product->price, 2) }}€
                                            </p>
                                        </div>
                                        
                                        <button type="submit" 
                                                class="w-full px-6 py-4 bg-neon-purple text-white font-bold rounded-lg hover:bg-neon-purple/80 transition transform hover:scale-105">
                                            ✅ Confirmar puja
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="text-center py-8 bg-gray-800/30 rounded-lg">
                                    <p class="text-gray-400 mb-4">Debes iniciar sesión para pujar</p>
                                    <a href="{{ route('login') }}" class="block w-full px-4 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                        Iniciar sesión
                                    </a>
                                </div>
                            @endauth
                        @elseif($product->isAuctionEnded() || $product->auction_final_price)
                            <div class="text-center py-8">
                                @if($product->auction_winner_id == Auth::id())
                                    <div class="bg-green-900/30 border border-green-500 rounded-lg p-6">
                                        <p class="text-green-400 text-lg font-bold mb-2">🎉 ¡FELICIDADES!</p>
                                        <p class="text-white mb-4">Has ganado esta subasta</p>
                                        <p class="text-neon-purple text-2xl font-bold">{{ number_format($product->auction_final_price ?? $product->price, 2) }}€</p>
                                    </div>
                                @elseif($product->auction_winner_id)
                                    <div class="bg-gray-800/50 rounded-lg p-6">
                                        <p class="text-gray-400">Subasta finalizada</p>
                                        <p class="text-neon-purple font-bold mt-2">Ganador: {{ $product->auctionWinner->name }}</p>
                                        <p class="text-neon-purple mt-2">{{ number_format($product->auction_final_price ?? $product->price, 2) }}€</p>
                                    </div>
                                @else
                                    <div class="bg-gray-800/50 rounded-lg p-6">
                                        <p class="text-gray-400">Subasta finalizada sin pujas</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                        
                        <!-- PANEL DE ADMINISTRACIÓN PARA SUBASTAS -->
                        @auth
                            @if(Auth::user()->is_admin && $product->isAuctionActive())
                                <div class="mt-6 pt-6 border-t border-gray-800">
                                    <h3 class="text-lg font-bold text-white mb-4">⚙️ Panel de Admin - Subastas</h3>
                                    
                                    <div class="space-y-3">
                                        <!-- Extender tiempo -->
                                        <form action="{{ route('auctions.extend', $product->id) }}" method="POST" class="flex space-x-2">
                                            @csrf
                                            <input type="number" 
                                                name="hours" 
                                                min="1" 
                                                max="72" 
                                                value="1" 
                                                class="w-20 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-white text-center">
                                            <button type="submit" 
                                                    class="flex-1 px-3 py-1 bg-neon-blue/10 text-neon-blue rounded hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                                Extender
                                            </button>
                                        </form>
                                        
                                        <!-- Reducir tiempo -->
                                        <form action="{{ route('auctions.reduce', $product->id) }}" method="POST" class="flex space-x-2">
                                            @csrf
                                            <input type="number" 
                                                name="hours" 
                                                min="1" 
                                                max="24" 
                                                value="1" 
                                                class="w-20 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-white text-center">
                                            <button type="submit" 
                                                    class="flex-1 px-3 py-1 bg-neon-purple/10 text-neon-purple rounded hover:bg-neon-purple hover:text-white transition text-sm">
                                                Reducir
                                            </button>
                                        </form>
                                        
                                        <!-- Reiniciar a 24h -->
                                        <form action="{{ route('auctions.reset', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full px-3 py-1 bg-yellow-600/10 text-yellow-500 rounded hover:bg-yellow-600 hover:text-white transition text-sm">
                                                Reiniciar a 24h
                                            </button>
                                        </form>
                                        
                                        <!-- Finalizar ahora -->
                                        <form action="{{ route('auctions.force-end', $product->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="w-full px-3 py-1 bg-neon-red/10 text-neon-red rounded hover:bg-neon-red hover:text-white transition text-sm"
                                                    onclick="return confirm('¿Estás seguro de finalizar esta subasta ahora?')">
                                                Finalizar ahora
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
