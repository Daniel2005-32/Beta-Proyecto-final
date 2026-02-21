<x-store-layout>
    <h1 class="text-3xl font-bold mb-8">Tu Carrito de Compras</h1>

    @if(session('cart'))
        <div class="bg-white rounded-lg shadow overflow-hidden p-6">
            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="py-2">Producto</th>
                        <th class="py-2">Precio</th>
                        <th class="py-2">Cantidad</th>
                        <th class="py-2">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = 0 @endphp
                    @foreach(session('cart') as $id => $details)
                        @php $total += $details['price'] * $details['quantity'] @endphp
                        <tr class="border-t">
                            <td class="py-4 flex items-center">
                                <img src="{{ $details['image'] ?? 'https://via.placeholder.com/50' }}" class="w-12 h-12 rounded mr-4">
                                {{ $details['name'] }}
                            </td>
                            <td class="py-4">{{ $details['price'] }}€</td>
                            <td class="py-4">{{ $details['quantity'] }}</td>
                            <td class="py-4">{{ $details['price'] * $details['quantity'] }}€</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            
            <div class="flex justify-between items-center mt-8 border-t pt-4">
                <div class="text-2xl font-bold">Total: {{ $total }}€</div>
                <form action="{{ route('cart.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700">
                        Finalizar Compra
                    </button>
                </form>
            </div>
            
            @if($total >= 20)
                <div class="mt-4 bg-green-100 text-green-800 p-3 rounded">
                    ¡Genial! Tu compra supera los 20€, entrarás automáticamente en el sorteo mensual.
                </div>
            @else
                <div class="mt-4 bg-gray-100 text-gray-600 p-3 rounded">
                    Añade {{ 20 - $total }}€ más para entrar en el sorteo mensual.
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">Tu carrito está vacío.</p>
            <a href="{{ route('products.index') }}" class="text-indigo-600 font-bold hover:underline">Ir a comprar</a>
        </div>
    @endif
</x-store-layout>
