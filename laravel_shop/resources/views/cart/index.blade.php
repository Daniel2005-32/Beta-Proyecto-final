<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-black text-white mb-8">🛒 Tu Carrito</h1>
            
            @php
                $cart = session('cart', []);
                $total = 0;
                foreach($cart as $id => $item) {
                    $total += $item['price'] * $item['quantity'];
                }
            @endphp

            @if(empty($cart))
                <!-- Carrito vacío -->
                <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-12 text-center">
                    <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-white mb-2">Tu carrito está vacío</h2>
                    <p class="text-gray-400 mb-6">¡Explora nuestros productos y encuentra algo increíble!</p>
                    <a href="{{ route('products.index') }}" class="inline-block px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                        Ver Productos
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Lista de productos -->
                    <div class="lg:col-span-2">
                        <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-800 border-b border-neon-blue/20">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-neon-blue">Producto</th>
                                        <th class="px-6 py-4 text-left text-neon-blue">Precio</th>
                                        <th class="px-6 py-4 text-left text-neon-blue">Cantidad</th>
                                        <th class="px-6 py-4 text-left text-neon-blue">Total</th>
                                        <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="cart-items">
                                    @foreach($cart as $id => $item)
                                        @php
                                            $product = \App\Models\Product::find($id);
                                            $maxStock = $product ? $product->stock : 0;
                                        @endphp
                                        <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition" data-id="{{ $id }}" data-price="{{ $item['price'] }}">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-3">
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-12 h-12 object-cover rounded">
                                                    <span class="text-white font-medium">{{ $item['name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-300 price-display">{{ number_format($item['price'], 2) }}€</td>
                                            <td class="px-6 py-4">
                                                <form class="update-cart-form flex items-center space-x-2" data-id="{{ $id }}">
                                                    @csrf
                                                    <input type="number" 
                                                           name="quantity" 
                                                           value="{{ $item['quantity'] }}" 
                                                           min="1" 
                                                           max="{{ $maxStock }}"
                                                           class="quantity-input w-16 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-white focus:outline-none focus:border-neon-blue">
                                                    <button type="submit" class="text-neon-blue hover:text-neon-blue/80 transition" title="Actualizar">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                @if($item['quantity'] > $maxStock)
                                                    <p class="text-neon-red text-xs mt-1">Stock disponible: {{ $maxStock }}</p>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-white font-bold line-total">{{ number_format($item['price'] * $item['quantity'], 2) }}€</td>
                                            <td class="px-6 py-4">
                                                <form action="{{ route('cart.remove', $id) }}" method="POST" class="remove-form">
                                                    @csrf
                                                    <button type="submit" class="text-neon-red hover:text-neon-red/80 transition" title="Eliminar">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Resumen del pedido -->
                    <div class="lg:col-span-1">
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6 sticky top-24">
                            <h2 class="text-2xl font-bold text-white mb-6">Resumen del pedido</h2>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between text-gray-300">
                                    <span>Subtotal:</span>
                                    <span id="cart-subtotal">{{ number_format($total, 2) }}€</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Envío:</span>
                                    <span class="text-green-500">Gratis</span>
                                </div>
                                <div class="border-t border-gray-800 my-4"></div>
                                <div class="flex justify-between text-xl font-bold text-white">
                                    <span>Total:</span>
                                    <span id="cart-total" class="text-neon-blue">{{ number_format($total, 2) }}€</span>
                                </div>
                            </div>

                            @auth
                                <form action="{{ route('cart.checkout') }}" method="POST" class="mt-6">
                                    @csrf
                                    <button type="submit" class="w-full px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                        ✅ Proceder al pago
                                    </button>
                                </form>
                            @else
                                <div class="mt-6 bg-gray-800/50 border border-neon-red/30 rounded-lg p-4 text-center">
                                    <p class="text-gray-300 mb-3">🔒 Debes iniciar sesión para comprar</p>
                                    <div class="flex gap-3">
                                        <a href="{{ route('login') }}" class="flex-1 px-4 py-2 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                            Iniciar sesión
                                        </a>
                                        <a href="{{ route('register') }}" class="flex-1 px-4 py-2 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition">
                                            Registrarse
                                        </a>
                                    </div>
                                </div>
                            @endauth

                            <div class="mt-4 text-center">
                                <a href="{{ route('products.index') }}" class="text-sm text-gray-400 hover:text-neon-blue transition">
                                    ← Seguir comprando
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Actualizar cantidad en tiempo real
        document.querySelectorAll('.update-cart-form').forEach(form => {
            const input = form.querySelector('.quantity-input');
            const row = form.closest('tr');
            const price = parseFloat(row.dataset.price);
            const lineTotal = row.querySelector('.line-total');
            const productId = form.dataset.id;
            
            // Actualizar al cambiar el input
            input.addEventListener('change', function() {
                const quantity = parseInt(this.value);
                if (quantity < 1) this.value = 1;
                
                // Actualizar línea
                const newTotal = price * quantity;
                lineTotal.textContent = newTotal.toFixed(2) + '€';
                
                // Actualizar totales globales
                updateGlobalTotals();
            });
            
            // Envío AJAX del formulario
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('quantity', input.value);
                
                fetch('{{ url("cart/update") }}/' + productId, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar totales con la respuesta del servidor
                        document.getElementById('cart-subtotal').textContent = data.subtotal + '€';
                        document.getElementById('cart-total').textContent = data.total + '€';
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
        
        function updateGlobalTotals() {
            let subtotal = 0;
            document.querySelectorAll('#cart-items tr').forEach(row => {
                const lineTotalText = row.querySelector('.line-total').textContent;
                const lineTotal = parseFloat(lineTotalText.replace('€', ''));
                subtotal += lineTotal;
            });
            
            document.getElementById('cart-subtotal').textContent = subtotal.toFixed(2) + '€';
            document.getElementById('cart-total').textContent = subtotal.toFixed(2) + '€';
        }
        
        // Eliminar producto
        document.querySelectorAll('.remove-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
    </script>
</x-store-layout>
