<x-store-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-black text-white mb-8">🛒 Tu Carrito</h1>
            
            @php
                $cart = session('cart', []);
                $subtotal = 0;
                foreach($cart as $id => $item) {
                    $subtotal += $item['price'] * $item['quantity'];
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
                                            $itemTotal = $item['price'] * $item['quantity'];
                                        @endphp
                                        <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition" data-id="{{ $id }}" data-price="{{ $item['price'] }}">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-3">
                                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-12 h-12 object-cover rounded">
                                                    <span class="text-white font-medium">{{ $item['name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-300 price-cell">{{ number_format($item['price'], 2) }}€</td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-2">
                                                    <button type="button" 
                                                            onclick="updateQuantityFromInput({{ $id }}, 'decrease')" 
                                                            class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-neon-blue/20 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                            {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                    
                                                    <input type="number" 
                                                           value="{{ $item['quantity'] }}" 
                                                           min="1" 
                                                           max="{{ $maxStock }}"
                                                           class="w-16 bg-gray-800 border border-gray-700 rounded-lg px-2 py-1 text-white text-center quantity-input"
                                                           onchange="updateQuantity({{ $id }}, this.value)">
                                                    
                                                    <button type="button" 
                                                            onclick="updateQuantityFromInput({{ $id }}, 'increase')" 
                                                            class="w-8 h-8 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-neon-blue/20 transition disabled:opacity-50 disabled:cursor-not-allowed"
                                                            {{ $item['quantity'] >= $maxStock ? 'disabled' : '' }}>
                                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-white font-bold item-total">{{ number_format($itemTotal, 2) }}€</td>
                                            <td class="px-6 py-4">
                                                <form action="{{ route('cart.remove', $id) }}" method="POST" class="inline">
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
                                    <span id="cart-subtotal">{{ number_format($subtotal, 2) }}€</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Envío:</span>
                                    <span class="text-green-500">Gratis</span>
                                </div>
                                <div class="border-t border-gray-800 my-4"></div>
                                <div class="flex justify-between text-xl font-bold text-white">
                                    <span>Total:</span>
                                    <span id="cart-total">{{ number_format($subtotal, 2) }}€</span>
                                </div>
                                <p class="text-xs text-gray-500 text-right">
                                    * Los impuestos se calcularán al seleccionar tu dirección
                                </p>
                            </div>

                            @auth
                                <a href="{{ route('cart.checkout.form') }}" 
                                   class="block w-full mt-6 px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition text-center shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                    ✅ Proceder al pago
                                </a>
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
    function updateQuantity(productId, newQuantity) {
        console.log('1. Actualizando producto', productId, 'a cantidad', newQuantity);
        
        // Validar cantidad
        newQuantity = parseInt(newQuantity);
        if (isNaN(newQuantity) || newQuantity < 1) {
            console.log('2. Cantidad inválida');
            return;
        }
        
        // Obtener elementos
        const row = document.querySelector(`tr[data-id="${productId}"]`);
        if (!row) {
            console.log('2. Fila no encontrada');
            return;
        }
        
        const maxStock = parseInt(row.querySelector('.quantity-input').getAttribute('max'));
        const price = parseFloat(row.dataset.price);
        
        console.log('2. Stock máximo:', maxStock, 'Precio:', price);
        
        if (newQuantity > maxStock) {
            alert('No hay suficiente stock');
            return;
        }
        
        // Enviar petición
        const token = document.querySelector('meta[name="csrf-token"]').content;
        
        fetch(`/cart/update/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ quantity: newQuantity })
        })
        .then(response => response.json())
        .then(data => {
            console.log('3. Respuesta:', data);
            
            if (data.success) {
                // Actualizar TODO en la interfaz
                
                // 1. Actualizar el input de cantidad
                row.querySelector('.quantity-input').value = newQuantity;
                
                // 2. Actualizar el total del producto
                const newTotal = (price * newQuantity).toFixed(2);
                row.querySelector('.item-total').textContent = newTotal + '€';
                
                // 3. Actualizar subtotal y total global
                document.getElementById('cart-subtotal').textContent = data.subtotal + '€';
                document.getElementById('cart-total').textContent = data.subtotal + '€';
                
                // 4. ACTUALIZAR BOTONES (FORZAR ESTADO)
                const buttons = row.querySelectorAll('button');
                
                // Botón restar (-)
                if (newQuantity <= 1) {
                    buttons[0].setAttribute('disabled', 'disabled');
                    buttons[0].classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    buttons[0].removeAttribute('disabled');
                    buttons[0].classList.remove('opacity-50', 'cursor-not-allowed');
                }
                
                // Botón sumar (+)
                if (newQuantity >= maxStock) {
                    buttons[1].setAttribute('disabled', 'disabled');
                    buttons[1].classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    buttons[1].removeAttribute('disabled');
                    buttons[1].classList.remove('opacity-50', 'cursor-not-allowed');
                }
                
                console.log('4. Botones actualizados');
            } else {
                alert(data.error || 'Error al actualizar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error de conexión');
        });
    }

    function updateQuantityFromInput(productId, action) {
        const row = document.querySelector(`tr[data-id="${productId}"]`);
        if (!row) return;
        
        const input = row.querySelector('.quantity-input');
        let currentValue = parseInt(input.value);
        
        if (action === 'increase') {
            updateQuantity(productId, currentValue + 1);
        } else if (action === 'decrease') {
            updateQuantity(productId, currentValue - 1);
        }
    }
    </script>
</x-store-layout>