<?php if (isset($component)) { $__componentOriginalfa92fd5562a0c82e62f2e625d459a2d3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfa92fd5562a0c82e62f2e625d459a2d3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.store-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('store-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-4xl font-black text-white mb-8">📦 Finalizar Pedido</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Columna izquierda: Dirección y Tarjeta -->
                <div class="space-y-6">
                    <!-- Selección de dirección -->
                    <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                        <h2 class="text-2xl font-bold text-white mb-6">1. Dirección de entrega</h2>

                        <?php if($addresses->count() > 0): ?>
                            <div class="space-y-4 mb-6" id="address-list">
                                <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="block address-item">
                                        <input type="radio" name="address_id" value="<?php echo e($address->id); ?>" 
                                               data-province="<?php echo e($address->state); ?>"
                                               <?php echo e($address->is_default ? 'checked' : ''); ?> required
                                               class="hidden peer address-radio">
                                        <div class="border border-gray-800 rounded-lg p-4 cursor-pointer peer-checked:border-neon-purple peer-checked:bg-neon-purple/10 transition">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <h3 class="text-white font-bold"><?php echo e($address->name); ?></h3>
                                                    <p class="text-gray-400 text-sm"><?php echo e($address->street); ?>, <?php echo e($address->number); ?></p>
                                                    <?php if($address->complement): ?>
                                                        <p class="text-gray-400 text-sm"><?php echo e($address->complement); ?></p>
                                                    <?php endif; ?>
                                                    <p class="text-gray-400 text-sm"><?php echo e($address->city); ?> - <?php echo e($address->state); ?></p>
                                                    <p class="text-gray-400 text-sm">CP: <?php echo e($address->zipcode); ?></p>
                                                    <p class="text-gray-400 text-sm">Tel: <?php echo e($address->phone); ?></p>
                                                </div>
                                                <?php if($address->is_default): ?>
                                                    <span class="text-neon-blue text-xs font-bold">PREDETERMINADA</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div class="text-right mb-4">
                                <a href="<?php echo e(route('addresses.create')); ?>" class="text-neon-blue hover:text-neon-purple transition text-sm">
                                    + Añadir nueva dirección
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-8">
                                <p class="text-gray-400 mb-4">Necesitas una dirección para continuar</p>
                                <a href="<?php echo e(route('addresses.create')); ?>" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                    Añadir dirección
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Formulario de tarjeta de crédito -->
                    <?php if($addresses->count() > 0): ?>
                    <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6">
                        <h2 class="text-2xl font-bold text-white mb-6">2. Datos de pago</h2>
                        
                        <div class="space-y-4">
                            <!-- Número de tarjeta -->
                            <div>
                                <label class="block text-gray-300 mb-2 text-sm">Número de tarjeta</label>
                                <div class="relative">
                                    <input type="text" 
                                           id="card-number"
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19"
                                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple font-mono"
                                           x-model="cardNumber"
                                           @input="formatCardNumber">
                                    <div class="absolute right-3 top-3 flex space-x-1">
                                        <span class="w-8 h-8 bg-gradient-to-br from-red-500 to-orange-500 rounded"></span>
                                        <span class="w-8 h-8 bg-gradient-to-br from-blue-500 to-cyan-500 rounded"></span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Solo para fines de demostración</p>
                            </div>
                            
                            <!-- Fila: Fecha de caducidad y CVV -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-300 mb-2 text-sm">Fecha caducidad</label>
                                    <input type="text" 
                                           id="expiry-date"
                                           placeholder="MM/AA"
                                           maxlength="5"
                                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"
                                           x-model="expiryDate"
                                           @input="formatExpiryDate">
                                </div>
                                <div>
                                    <label class="block text-gray-300 mb-2 text-sm">CVV</label>
                                    <input type="text" 
                                           id="cvv"
                                           placeholder="123"
                                           maxlength="3"
                                           class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"
                                           x-model="cvv"
                                           @input="formatCVV">
                                </div>
                            </div>
                            
                            <!-- Nombre del titular (opcional) -->
                            <div>
                                <label class="block text-gray-300 mb-2 text-sm">Titular de la tarjeta (opcional)</label>
                                <input type="text" 
                                       id="cardholder"
                                       placeholder="Como aparece en la tarjeta"
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"
                                       x-model="cardholder">
                            </div>
                            
                            <div class="bg-blue-900/20 border border-neon-blue/30 rounded-lg p-3 mt-4">
                                <p class="text-xs text-gray-300 flex items-start gap-2">
                                    <svg class="w-4 h-4 text-neon-blue mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span>🔒 Los datos de tu tarjeta son seguros. No guardamos esta información.</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Columna derecha: Resumen y botón final -->
                <div class="space-y-6">
                    <!-- Resumen del pedido con impuestos dinámicos -->
                    <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6 sticky top-24">
                        <h2 class="text-2xl font-bold text-white mb-6">3. Resumen del pedido</h2>
                        
                        <div class="space-y-3 mb-6" id="cart-items">
                            <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $itemTotal = $item['price'] * $item['quantity'];
                                ?>
                                <div class="flex justify-between text-sm cart-item">
                                    <span class="text-gray-400"><?php echo e($item['name']); ?> x<?php echo e($item['quantity']); ?></span>
                                    <span class="text-white item-subtotal"><?php echo e(number_format($itemTotal, 2)); ?>€</span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>

                        <!-- Desglose de impuestos -->
                        <div id="tax-summary" class="bg-gray-800/50 rounded-lg p-4 mb-4">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-400">Subtotal:</span>
                                <span class="text-white subtotal-amount"><?php echo e(number_format($subtotal, 2)); ?>€</span>
                            </div>
                            <div id="tax-details">
                                <div class="flex justify-between text-sm mb-2 tax-line">
                                    <span class="text-gray-400 tax-name">Selecciona una dirección</span>
                                    <span class="text-white tax-amount">-</span>
                                </div>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-2 border-t border-gray-700">
                                <span class="text-white">Total:</span>
                                <span class="text-neon-blue total-amount"><?php echo e(number_format($subtotal, 2)); ?>€</span>
                            </div>
                        </div>

                        <!-- Botón de confirmación final (incluye dirección + tarjeta) -->
                        <form action="<?php echo e(route('cart.checkout')); ?>" method="POST" id="checkout-form" @submit.prevent="validateAndSubmit">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="address_id" id="selected-address-id" x-model="selectedAddressId">
                            
                            <!-- Campos ocultos para la tarjeta (solo para demostración) -->
                            <input type="hidden" name="card_last_four" x-model="cardLastFour">
                            <input type="hidden" name="card_brand" x-model="cardBrand">
                            
                            <button type="submit" 
                                    class="w-full mt-4 px-6 py-4 bg-gradient-to-r from-neon-purple to-neon-blue text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)] disabled:opacity-50 disabled:cursor-not-allowed"
                                    :disabled="!isFormValid">
                                💳 Pagar ahora
                            </button>
                        </form>

                        <div class="mt-4 text-center">
                            <a href="<?php echo e(route('cart.index')); ?>" class="text-sm text-gray-400 hover:text-neon-blue transition">
                                ← Volver al carrito
                            </a>
                        </div>
                    </div>

                    <!-- Ayuda -->
                    <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                        <h2 class="text-xl font-bold text-white mb-4">Información de pago</h2>
                        <ul class="space-y-3 text-sm text-gray-400">
                            <li class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span><strong>Península:</strong> IVA 21%</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-green-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span><strong>Canarias (GC/TF):</strong> IGIC 7%</span>
                            </li>
                            <li class="flex items-start space-x-2">
                                <svg class="w-5 h-5 text-neon-blue mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                                <span>Pago 100% seguro</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const addressRadios = document.querySelectorAll('.address-radio');
        const subtotal = <?php echo e($subtotal); ?>;
        
        // Variables para Alpine.js
        window.checkoutData = {
            cardNumber: '',
            expiryDate: '',
            cvv: '',
            cardholder: '',
            selectedAddressId: null,
            
            get cardLastFour() {
                const numbers = this.cardNumber.replace(/\s/g, '');
                return numbers.slice(-4);
            },
            
            get cardBrand() {
                const firstDigit = this.cardNumber.replace(/\s/g, '')[0];
                if (firstDigit === '4') return 'Visa';
                if (firstDigit === '5') return 'Mastercard';
                if (firstDigit === '3') return 'American Express';
                return 'Desconocida';
            },
            
            get isFormValid() {
                // Validación básica para demostración
                const cardNumbers = this.cardNumber.replace(/\s/g, '');
                const isValidCard = cardNumbers.length >= 15 && cardNumbers.length <= 16;
                const isValidExpiry = this.expiryDate.length === 5 && this.expiryDate.includes('/');
                const isValidCVV = this.cvv.length >= 3;
                const hasAddress = this.selectedAddressId !== null;
                
                return isValidCard && isValidExpiry && isValidCVV && hasAddress;
            },
            
            formatCardNumber() {
                let value = this.cardNumber.replace(/\s/g, '').replace(/\D/g, '');
                let formatted = '';
                for (let i = 0; i < value.length; i++) {
                    if (i > 0 && i % 4 === 0) formatted += ' ';
                    formatted += value[i];
                }
                this.cardNumber = formatted;
            },
            
            formatExpiryDate() {
                let value = this.expiryDate.replace(/\D/g, '');
                if (value.length >= 2) {
                    this.expiryDate = value.slice(0, 2) + '/' + value.slice(2, 4);
                } else {
                    this.expiryDate = value;
                }
            },
            
            formatCVV() {
                this.cvv = this.cvv.replace(/\D/g, '').slice(0, 3);
            },
            
            validateAndSubmit() {
                if (!this.isFormValid) {
                    alert('Por favor, completa todos los datos correctamente');
                    return;
                }
                
                // Simulación de pago exitoso
                alert('✅ Pago procesado correctamente (simulación)');
                
                // Enviar el formulario
                document.getElementById('checkout-form').submit();
            }
        };
        
        function calculateTax(province) {
            const provinceUpper = province ? province.toUpperCase() : '';
            let isCanarias = provinceUpper.includes('GC') || provinceUpper.includes('TF');
            
            const taxRate = isCanarias ? 7 : 21;
            const taxName = isCanarias ? 'IGIC 7%' : 'IVA 21%';
            const taxAmount = subtotal * (taxRate / 100);
            const total = subtotal + taxAmount;
            
            return { taxRate, taxName, taxAmount, total, isCanarias };
        }
        
        function updateTaxSummary(province) {
            const taxSummary = calculateTax(province);
            const taxDetails = document.getElementById('tax-details');
            
            let taxLine = document.querySelector('.tax-line');
            if (!taxLine) {
                taxLine = document.createElement('div');
                taxLine.className = 'flex justify-between text-sm mb-2 tax-line';
                taxDetails.appendChild(taxLine);
            }
            
            taxLine.innerHTML = `
                <span class="text-gray-400 tax-name">${taxSummary.taxName}:</span>
                <span class="text-white tax-amount">${taxSummary.taxAmount.toFixed(2)}€</span>
            `;
            
            document.querySelector('.total-amount').textContent = taxSummary.total.toFixed(2) + '€';
        }
        
        // Escuchar cambios en las direcciones
        addressRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const province = this.dataset.province;
                updateTaxSummary(province);
                
                // Actualizar el ID de dirección seleccionada
                if (window.checkoutData) {
                    window.checkoutData.selectedAddressId = this.value;
                    document.getElementById('selected-address-id').value = this.value;
                }
            });
            
            // Si es la seleccionada por defecto, actualizar
            if (radio.checked) {
                updateTaxSummary(radio.dataset.province);
                if (window.checkoutData) {
                    window.checkoutData.selectedAddressId = radio.value;
                    document.getElementById('selected-address-id').value = radio.value;
                }
            }
        });
    });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfa92fd5562a0c82e62f2e625d459a2d3)): ?>
<?php $attributes = $__attributesOriginalfa92fd5562a0c82e62f2e625d459a2d3; ?>
<?php unset($__attributesOriginalfa92fd5562a0c82e62f2e625d459a2d3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfa92fd5562a0c82e62f2e625d459a2d3)): ?>
<?php $component = $__componentOriginalfa92fd5562a0c82e62f2e625d459a2d3; ?>
<?php unset($__componentOriginalfa92fd5562a0c82e62f2e625d459a2d3); ?>
<?php endif; ?>
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/cart/checkout.blade.php ENDPATH**/ ?>