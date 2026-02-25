<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón para volver a subastas -->
            <div class="mb-6">
                <a href="{{ route('auctions.index') }}" class="inline-flex items-center text-gray-400 hover:text-neon-purple transition group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Volver a subastas</span>
                </a>
            </div>

            @auth
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Columna izquierda: Información del producto y subasta -->
                    <div class="lg:col-span-2">
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 overflow-hidden">
                            <!-- Imagen del producto -->
                            <div class="relative h-96">
                                <img src="{{ $auction->product->image }}" alt="{{ $auction->product->name }}" class="w-full h-full object-cover">
                                
                                <!-- Badges de estado -->
                                <div class="absolute top-4 left-4 flex gap-2">
                                    <span class="bg-neon-red text-white px-4 py-2 rounded-full text-sm font-bold shadow-[0_0_15px_rgba(255,0,85,0.4)]">
                                        -20% DESCUENTO BASE
                                    </span>
                                    @if($auction->isActive())
                                        <span class="bg-green-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                            ⚡ ACTIVA
                                        </span>
                                    @else
                                        <span class="bg-gray-600 text-white px-4 py-2 rounded-full text-sm font-bold">
                                            🏁 FINALIZADA
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Temporizador -->
                                <div class="absolute top-4 right-4 bg-neon-purple text-white px-4 py-2 rounded-full text-sm font-bold">
                                    ⏱️ {{ $auction->timeLeft() }}
                                </div>
                            </div>
                            
                            <!-- Detalles del producto -->
                            <div class="p-8">
                                <h1 class="text-3xl font-black text-white mb-2">{{ $auction->product->name }}</h1>
                                <p class="text-gray-400 mb-6">{{ $auction->product->description }}</p>
                                
                                <!-- Información de la subasta -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                                    <div class="bg-gray-800/50 rounded-lg p-4 text-center">
                                        <div class="text-sm text-gray-400 mb-1">Precio original</div>
                                        <div class="text-xl font-bold text-gray-300 line-through">{{ number_format($auction->product->price, 2) }}€</div>
                                    </div>
                                    <div class="bg-gray-800/50 rounded-lg p-4 text-center">
                                        <div class="text-sm text-gray-400 mb-1">Precio base</div>
                                        <div class="text-xl font-bold text-neon-blue">{{ number_format($auction->starting_price, 2) }}€</div>
                                    </div>
                                    <div class="bg-gray-800/50 rounded-lg p-4 text-center">
                                        <div class="text-sm text-gray-400 mb-1">Puja actual</div>
                                        <div class="text-xl font-bold text-neon-purple">{{ number_format($auction->current_price, 2) }}€</div>
                                    </div>
                                    <div class="bg-gray-800/50 rounded-lg p-4 text-center">
                                        <div class="text-sm text-gray-400 mb-1">Próxima puja</div>
                                        <div class="text-xl font-bold text-neon-red">{{ number_format($auction->nextBidAmount(), 2) }}€+</div>
                                    </div>
                                </div>
                                
                                <!-- Historial de pujas -->
                                <h2 class="text-2xl font-bold text-white mb-4">📊 Historial de pujas</h2>
                                
                                @if($auction->bids->count() > 0)
                                    <div class="bg-gray-800/30 rounded-lg overflow-hidden">
                                        <table class="w-full">
                                            <thead class="bg-gray-800 border-b border-gray-700">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-sm font-bold text-neon-purple">Usuario</th>
                                                    <th class="px-6 py-3 text-left text-sm font-bold text-neon-purple">Cantidad</th>
                                                    <th class="px-6 py-3 text-left text-sm font-bold text-neon-purple">Fecha</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($auction->bids as $bid)
                                                    <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                                        <td class="px-6 py-4 text-white">{{ $bid->user->name }}</td>
                                                        <td class="px-6 py-4">
                                                            <span class="font-bold text-neon-purple">{{ number_format($bid->amount, 2) }}€</span>
                                                        </td>
                                                        <td class="px-6 py-4 text-gray-400 text-sm">{{ $bid->created_at->diffForHumans() }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8 bg-gray-800/30 rounded-lg">
                                        <p class="text-gray-400">Aún no hay pujas. ¡Sé el primero!</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Columna derecha: Formulario de puja -->
                    <div class="lg:col-span-1">
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 p-6 sticky top-24">
                            <h2 class="text-2xl font-bold text-white mb-6">💰 Pujar ahora</h2>
                            
                            @if($auction->isActive())
                                <form action="{{ route('auctions.bid', $auction->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    
                                    <div>
                                        <label class="block text-gray-300 mb-2">Tu puja (€)</label>
                                        <input type="number" 
                                               name="amount" 
                                               step="0.01" 
                                               min="{{ $auction->nextBidAmount() }}" 
                                               value="{{ $auction->nextBidAmount() }}"
                                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple transition"
                                               required>
                                        <p class="text-sm text-gray-400 mt-2">Mínimo: {{ number_format($auction->nextBidAmount(), 2) }}€</p>
                                    </div>
                                    
                                    <button type="submit" 
                                            class="w-full px-6 py-4 bg-neon-purple text-white font-bold rounded-lg hover:bg-neon-purple/80 transition transform hover:scale-105 shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                                        ✅ Confirmar puja
                                    </button>
                                </form>
                                
                                <div class="mt-4 p-4 bg-gray-800/50 rounded-lg">
                                    <h3 class="text-sm font-bold text-gray-300 mb-2">⚡ Información:</h3>
                                    <ul class="text-xs text-gray-400 space-y-1">
                                        <li>• Las pujas son vinculantes</li>
                                        <li>• Si ganas, se te notificará por email</li>
                                        <li>• El pago se realizará al finalizar</li>
                                        <li>• Stock limitado: ¡solo 1 unidad!</li>
                                    </ul>
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-800/30 rounded-lg">
                                    <p class="text-gray-400 mb-4">Esta subasta ha finalizado</p>
                                    @if($auction->currentWinner)
                                        <div class="p-4 bg-neon-purple/20 rounded-lg">
                                            <p class="text-sm text-gray-300">Ganador:</p>
                                            <p class="text-lg font-bold text-neon-purple">{{ $auction->currentWinner->name }}</p>
                                            <p class="text-2xl font-black mt-2">{{ number_format($auction->current_price, 2) }}€</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Contador de pujas -->
                            <div class="mt-6 pt-6 border-t border-gray-800">
                                <div class="flex justify-between text-gray-400">
                                    <span>Total pujas:</span>
                                    <span class="text-white font-bold">{{ $auction->total_bids }}</span>
                                </div>
                                <div class="flex justify-between text-gray-400 mt-2">
                                    <span>Finaliza:</span>
                                    <span class="text-white">{{ $auction->end_time->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Redirigir al login si no está autenticado -->
                <script>window.location.href = "{{ route('login') }}";</script>
            @endauth
        </div>
    </div>
</x-store-layout>
