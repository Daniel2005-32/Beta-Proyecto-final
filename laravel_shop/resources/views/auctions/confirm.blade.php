<x-store-layout>
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 p-8 text-center">
                <!-- Icono -->
                <div class="text-6xl mb-4">⚡</div>
                
                <h1 class="text-3xl font-black text-white mb-4">
                    ¿Quieres iniciar una subasta?
                </h1>
                
                <p class="text-gray-400 mb-6">
                    Este producto exclusivo tiene solo 1 unidad disponible.
                    Puedes iniciar una subasta de 24 horas con un <span class="text-neon-purple font-bold">20% de descuento</span>
                    sobre el precio original.
                </p>
                
                <div class="bg-gray-800/50 rounded-lg p-4 mb-8">
                    @php
                        $originalPrice = $product->price;
                        $startingPrice = $originalPrice * 0.8; // 20% de descuento
                    @endphp
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-400">Precio original:</span>
                        <span class="text-white line-through">{{ number_format($originalPrice, 2) }}€</span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span class="text-gray-300">Precio de salida:</span>
                        <span class="text-neon-purple">{{ number_format($startingPrice, 2) }}€</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        * Los usuarios podrán pujar durante 24 horas
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <form action="{{ route('auctions.start', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full sm:w-auto px-8 py-4 bg-neon-purple text-white font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                            ✅ Sí, iniciar subasta
                        </button>
                    </form>
                    
                    <form action="{{ route('auctions.cancel', $product->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full sm:w-auto px-8 py-4 bg-gray-800 text-gray-300 font-black rounded-full hover:bg-gray-700 transition">
                            ❌ No, volver al inicio
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-store-layout>
