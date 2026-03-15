<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">
                    <span class="text-neon-purple">⚡ Subastas en Vivo</span>
                </h1>
                <p class="text-gray-400">Puja por productos únicos cuando solo queda 1 unidad</p>
            </div>

            <!-- Subastas Activas -->
            <div class="mb-16">
                <h2 class="text-2xl font-bold text-white mb-6">Activas ⏳</h2>
                
                @if($activeAuctions->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($activeAuctions as $product)
                            <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 overflow-hidden hover:border-neon-purple transition group">
                                <div class="relative">
                                    <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover">
                                    
                                    <!-- Contador regresivo -->
                                    <div class="absolute top-4 right-4 bg-neon-purple text-white px-3 py-1 rounded-full text-sm font-bold">
                                        ⏱️ {{ $product->auctionTimeLeft() }}
                                    </div>
                                    
                                    <!-- Barra de progreso -->
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                                        <div class="h-full bg-neon-purple transition-all duration-1000" 
                                             style="width: {{ $product->getAuctionPercentage() }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="p-6">
                                    <h3 class="text-xl font-bold text-white mb-2">{{ $product->name }}</h3>
                                    
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400">Precio inicial:</span>
                                            <span class="text-gray-300">{{ number_format($product->auction_start_price ?? $product->price, 2) }}€</span>
                                        </div>
                                        <div class="flex justify-between text-lg font-bold">
                                            <span class="text-gray-300">Puja actual:</span>
                                            <span class="text-neon-purple">{{ number_format($product->price, 2) }}€</span>
                                        </div>
                                        @if($product->auction_winner_id)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-400">Mejor postor:</span>
                                                <span class="text-neon-blue">{{ $product->auctionWinner->name ?? 'Anónimo' }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <a href="{{ route('auctions.show', $product->id) }}" 
                                       class="block w-full text-center px-4 py-3 bg-neon-purple text-white font-bold rounded-lg hover:bg-neon-purple/80 transition">
                                        Ver Subasta
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
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-store-layout>
