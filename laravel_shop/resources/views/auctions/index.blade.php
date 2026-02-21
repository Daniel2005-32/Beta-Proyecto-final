<x-store-layout>
    <h1 class="text-3xl font-bold mb-8 text-yellow-600">Subastas Activas</h1>
    
    @if($auctions->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($auctions as $auction)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-yellow-200">
                    <img src="{{ $auction->product->image ?? 'https://via.placeholder.com/300' }}" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="font-bold text-xl mb-2">{{ $auction->product->name }}</h3>
                        <div class="flex justify-between mb-4">
                            <span class="text-gray-500">Precio Actual:</span>
                            <span class="font-bold text-2xl text-indigo-600">{{ $auction->current_price }}€</span>
                        </div>
                        <div class="text-sm text-gray-500 mb-6">
                            Termina en: {{ $auction->end_time->diffForHumans() }}
                        </div>
                        <a href="{{ route('auctions.show', $auction->id) }}" class="block w-full bg-yellow-500 text-white text-center py-2 rounded hover:bg-yellow-600 font-bold">
                            Pujar Ahora
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded shadow">
            <p class="text-gray-500">No hay subastas activas en este momento.</p>
        </div>
    @endif
</x-store-layout>
