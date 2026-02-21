<x-store-layout>
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="md:flex">
            <div class="md:w-1/2 bg-gray-100 flex items-center justify-center">
                <img src="{{ $auction->product->image ?? 'https://via.placeholder.com/600' }}" class="max-w-full max-h-96">
            </div>
            <div class="md:w-1/2 p-8">
                <h1 class="text-3xl font-bold mb-2">{{ $auction->product->name }}</h1>
                <p class="text-gray-600 mb-6">{{ $auction->product->description }}</p>
                
                <div class="bg-yellow-50 p-6 rounded-lg border border-yellow-200 mb-8">
                    <div class="text-sm text-gray-500 mb-1">Oferta actual</div>
                    <div class="text-4xl font-bold text-indigo-600 mb-4">{{ $auction->current_price }}€</div>
                    <div class="text-sm text-red-500 font-bold mb-4">
                        Cierra: {{ $auction->end_time->format('d M Y, H:i') }} ({{ $auction->end_time->diffForHumans() }})
                    </div>
                    
                    @auth
                        <form action="{{ route('auctions.bid', $auction->id) }}" method="POST" class="flex gap-4">
                            @csrf
                            <input type="number" name="amount" min="{{ $auction->current_price + 1 }}" step="0.01" class="flex-1 rounded border-gray-300" placeholder="Tu oferta..." required>
                            <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded font-bold hover:bg-indigo-700">
                                Pujar
                            </button>
                        </form>
                    @else
                        <div class="text-center p-4 bg-gray-200 rounded">
                            <a href="{{ route('login') }}" class="text-indigo-600 font-bold">Inicia sesión</a> para pujar.
                        </div>
                    @endauth
                </div>

                <h3 class="font-bold mb-4">Historial de Pujas</h3>
                <ul class="space-y-2">
                    @foreach($auction->bids()->latest()->take(5)->get() as $bid)
                        <li class="flex justify-between text-sm border-b pb-2">
                            <span>{{ $bid->user->name }}</span>
                            <span class="font-mono font-bold">{{ $bid->amount }}€</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</x-store-layout>
