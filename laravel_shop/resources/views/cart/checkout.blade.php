<x-store-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-black text-white mb-8">📦 Finalizar Pedido</h1>

            <form action="{{ route('cart.checkout') }}" method="POST" id="checkout-form">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Columna izquierda: Dirección y Tarjeta -->
                    <div class="space-y-6">
                        <!-- Selección de dirección -->
                        <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                            <h2 class="text-2xl font-bold text-white mb-6">1. Dirección de entrega</h2>

                            @if($addresses->count() > 0)
                                <div class="space-y-4 mb-6">
                                    @foreach($addresses as $address)
                                        <label class="block">
                                            <input type="radio" name="address_id" value="{{ $address->id }}" 
                                                   data-province="{{ $address->state }}"
                                                   {{ $address->is_default ? 'checked' : '' }} 
                                                   required
                                                   class="hidden peer address-radio">
                                            <div class="border border-gray-800 rounded-lg p-4 cursor-pointer peer-checked:border-neon-purple peer-checked:bg-neon-purple/10 transition">
                                                <div class="flex justify-between items-start">
                                                    <div>
                                                        <h3 class="text-white font-bold">{{ $address->name }}</h3>
                                                        <p class="text-gray-400 text-sm">{{ $address->street }}, {{ $address->number }}</p>
                                                        @if($address->complement)
                                                            <p class="text-gray-400 text-sm">{{ $address->complement }}</p>
                                                        @endif
                                                        <p class="text-gray-400 text-sm">{{ $address->city }} - {{ $address->state }}</p>
                                                        <p class="text-gray-400 text-sm">CP: {{ $address->zipcode }}</p>
                                                        <p class="text-gray-400 text-sm">Tel: {{ $address->phone }}</p>
                                                    </div>
                                                    @if($address->is_default)
                                                        <span class="text-neon-blue text-xs font-bold">PREDETERMINADA</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="text-right mb-4">
                                    <a href="{{ route('addresses.create') }}" class="text-neon-blue hover:text-neon-purple transition text-sm">
                                        + Añadir nueva dirección
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <p class="text-gray-400 mb-4">Necesitas una dirección para continuar</p>
                                    <a href="{{ route('addresses.create') }}" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                        Añadir dirección
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Formulario de tarjeta de crédito -->
                        @if($addresses->count() > 0)
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6">
                            <h2 class="text-2xl font-bold text-white mb-6">2. Datos de pago</h2>
                            
                            <div class="space-y-4">
                                <!-- Número de tarjeta -->
                                <div>
                                    <label class="block text-gray-300 mb-2 text-sm">Número de tarjeta</label>
                                    <input type="text" 
                                           name="card_number_display"
                                           id="card_number_display"
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19"
                                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple font-mono"
                                           oninput="formatCardNumber(this)">
                                    <input type="hidden" name="card_last_four" id="card_last_four">
                                    <input type="hidden" name="card_brand" id="card_brand">
                                </div>
                                
                                <!-- Fila: Fecha de caducidad y CVV -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-gray-300 mb-2 text-sm">Fecha caducidad</label>
                                        <input type="text" 
                                               name="expiry_date"
                                               id="expiry_date"
                                               placeholder="MM/AA"
                                               maxlength="5"
                                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"
                                               oninput="formatExpiryDate(this)">
                                    </div>
                                    <div>
                                        <label class="block text-gray-300 mb-2 text-sm">CVV</label>
                                        <input type="text" 
                                               name="cvv"
                                               id="cvv"
                                               placeholder="123"
                                               maxlength="3"
                                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"
                                               oninput="formatCVV(this)">
                                    </div>
                                </div>
                                
                                <!-- Nombre del titular -->
                                <div>
                                    <label class="block text-gray-300 mb-2 text-sm">Titular de la tarjeta</label>
                                    <input type="text" 
                                           name="cardholder"
                                           placeholder="Como aparece en la tarjeta"
                                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Columna derecha: Resumen y botón final -->
                    <div class="space-y-6">
                        <!-- Resumen del pedido -->
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6 sticky top-24">
                            <h2 class="text-2xl font-bold text-white mb-6">3. Resumen del pedido</h2>
                            
                            <div class="space-y-3 mb-6">
                                @foreach($cart as $id => $item)
                                    @php
                                        $itemTotal = $item['price'] * $item['quantity'];
                                    @endphp
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-400">{{ $item['name'] }} x{{ $item['quantity'] }}</span>
                                        <span class="text-white">{{ number_format($itemTotal, 2) }}€</span>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Desglose de impuestos -->
                            <div id="tax-summary" class="bg-gray-800/50 rounded-lg p-4 mb-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-400">Subtotal:</span>
                                    <span class="text-white">{{ number_format($subtotal, 2) }}€</span>
                                </div>
                                <div id="tax-details">
                                    <div class="flex justify-between text-sm mb-2 tax-line">
                                        <span class="text-gray-400 tax-name">Selecciona una dirección</span>
                                        <span class="text-white tax-amount">-</span>
                                    </div>
                                </div>
                                <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-700">
                                    <span class="text-white">Total:</span>
                                    <span class="text-neon-blue total-amount">{{ number_format($subtotal, 2) }}€</span>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full mt-4 px-6 py-4 bg-gradient-to-r from-neon-purple to-neon-blue text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]"
                                    onclick="return validateForm()">
                                💳 Pagar ahora
                            </button>

                            <div class="mt-4 text-center">
                                <a href="{{ route('cart.index') }}" class="text-sm text-gray-400 hover:text-neon-blue transition">
                                    ← Volver al carrito
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Variables globales
    let subtotal = {{ $subtotal }};
    
    // Formatear número de tarjeta
    function formatCardNumber(input) {
        let value = input.value.replace(/\s/g, '').replace(/\D/g, '');
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += value[i];
        }
        input.value = formatted;
        
        // Actualizar campos ocultos
        const numbers = value;
        document.getElementById('card_last_four').value = numbers.slice(-4);
        
        // Detectar marca
        const firstDigit = numbers.charAt(0);
        let brand = 'Desconocida';
        if (firstDigit === '4') brand = 'Visa';
        if (firstDigit === '5') brand = 'Mastercard';
        if (firstDigit === '3') brand = 'American Express';
        document.getElementById('card_brand').value = brand;
    }
    
    // Formatear fecha de caducidad
    function formatExpiryDate(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length >= 2) {
            input.value = value.slice(0, 2) + '/' + value.slice(2, 4);
        } else {
            input.value = value;
        }
    }
    
    // Formatear CVV
    function formatCVV(input) {
        input.value = input.value.replace(/\D/g, '').slice(0, 3);
    }
    
    // Validar formulario antes de enviar
    function validateForm() {
        // Validar dirección seleccionada
        const addressSelected = document.querySelector('input[name="address_id"]:checked');
        if (!addressSelected) {
            alert('Por favor, selecciona una dirección de envío');
            return false;
        }
        
        // Validar número de tarjeta
        const cardNumber = document.getElementById('card_number_display').value.replace(/\s/g, '');
        if (cardNumber.length < 15 || cardNumber.length > 16) {
            alert('Por favor, introduce un número de tarjeta válido');
            return false;
        }
        
        // Validar fecha de caducidad
        const expiryDate = document.getElementById('expiry_date').value;
        if (expiryDate.length !== 5 || !expiryDate.includes('/')) {
            alert('Por favor, introduce una fecha de caducidad válida (MM/AA)');
            return false;
        }
        
        // Validar CVV
        const cvv = document.getElementById('cvv').value;
        if (cvv.length < 3) {
            alert('Por favor, introduce un CVV válido');
            return false;
        }
        
        // Validar que los campos ocultos tengan valores
        if (!document.getElementById('card_last_four').value) {
            alert('Error con los datos de la tarjeta');
            return false;
        }
        
        // Simular pago
        alert('✅ Procesando pago...');
        return true;
    }
    
    // Calcular impuestos según provincia
    document.querySelectorAll('.address-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const province = this.dataset.province;
            const provinceUpper = province ? province.toUpperCase() : '';
            const isCanarias = provinceUpper.includes('GC') || provinceUpper.includes('TF');
            
            const taxRate = isCanarias ? 7 : 21;
            const taxName = isCanarias ? 'IGIC 7%' : 'IVA 21%';
            const taxAmount = subtotal * (taxRate / 100);
            const total = subtotal + taxAmount;
            
            const taxLine = document.querySelector('.tax-line');
            taxLine.innerHTML = `
                <span class="text-gray-400 tax-name">${taxName}:</span>
                <span class="text-white tax-amount">${taxAmount.toFixed(2)}€</span>
            `;
            
            document.querySelector('.total-amount').textContent = total.toFixed(2) + '€';
        });
        
        // Inicializar con la dirección seleccionada por defecto
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });
    </script>
</x-store-layout>
