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
        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-5xl font-black text-white mb-8">📦 Finalizar Pedido</h1>

            <form action="<?php echo e(route('cart.checkout')); ?>" method="POST" id="checkout-form">
                <?php echo csrf_field(); ?>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Columna izquierda -->
                    <div class="space-y-6">
                        <!-- Dirección de entrega -->
                        <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                            <h2 class="text-3xl font-bold text-white mb-6">1. Dirección de entrega</h2>

                            <?php if($addresses->count() > 0): ?>
                                <div class="space-y-4 mb-6">
                                    <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <label class="block">
                                            <input type="radio" name="address_id" value="<?php echo e($address->id); ?>" 
                                                   data-province="<?php echo e($address->state); ?>"
                                                   <?php echo e($address->is_default ? 'checked' : ''); ?> 
                                                   required
                                                   class="hidden peer address-radio">
                                            <div class="border border-gray-800 rounded-xl p-6 cursor-pointer peer-checked:border-neon-purple peer-checked:bg-neon-purple/10 transition">
                                                <div class="flex justify-between items-start">
                                                    <div class="space-y-2 text-lg">
                                                        <h3 class="text-white font-bold text-2xl"><?php echo e($address->name); ?></h3>
                                                        <p class="text-gray-400"><?php echo e($address->street); ?>, <?php echo e($address->number); ?></p>
                                                        <?php if($address->complement): ?>
                                                            <p class="text-gray-400"><?php echo e($address->complement); ?></p>
                                                        <?php endif; ?>
                                                        <p class="text-gray-400"><?php echo e($address->city); ?> - <?php echo e($address->state); ?></p>
                                                        <p class="text-gray-400">CP: <?php echo e($address->zipcode); ?></p>
                                                        <p class="text-gray-400">Tel: <?php echo e($address->phone); ?></p>
                                                    </div>
                                                    <?php if($address->is_default): ?>
                                                        <span class="text-neon-blue text-base font-bold">PREDETERMINADA</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </label>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                <div class="text-right mb-4">
                                    <a href="<?php echo e(route('addresses.create')); ?>" class="text-neon-blue hover:text-neon-purple transition text-lg">
                                        + Añadir nueva dirección
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-8">
                                    <p class="text-gray-400 text-xl mb-4">Necesitas una dirección para continuar</p>
                                    <a href="<?php echo e(route('addresses.create')); ?>" class="inline-block px-8 py-4 bg-neon-blue text-gamer-dark text-lg font-bold rounded-lg hover:scale-105 transition">
                                        Añadir dirección
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Datos de pago -->
                        <?php if($addresses->count() > 0): ?>
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8">
                            <h2 class="text-3xl font-bold text-white mb-6">2. Datos de pago</h2>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-gray-300 mb-2 text-xl">Número de tarjeta</label>
                                    <input type="text" 
                                           id="card_number_display"
                                           placeholder="1234 5678 9012 3456"
                                           maxlength="19"
                                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-6 py-4 text-white text-xl focus:outline-none focus:border-neon-purple font-mono"
                                           oninput="formatCardNumber(this)">
                                    <input type="hidden" name="card_last_four" id="card_last_four">
                                    <input type="hidden" name="card_brand" id="card_brand">
                                </div>
                                
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-gray-300 mb-2 text-xl">Caducidad</label>
                                        <input type="text" 
                                               id="expiry_date"
                                               placeholder="MM/AA"
                                               maxlength="5"
                                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-6 py-4 text-white text-xl focus:outline-none focus:border-neon-purple"
                                               oninput="formatExpiryDate(this)">
                                    </div>
                                    <div>
                                        <label class="block text-gray-300 mb-2 text-xl">CVV</label>
                                        <input type="text" 
                                               id="cvv"
                                               placeholder="123"
                                               maxlength="3"
                                               class="w-full bg-gray-800 border border-gray-700 rounded-xl px-6 py-4 text-white text-xl focus:outline-none focus:border-neon-purple"
                                               oninput="formatCVV(this)">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-gray-300 mb-2 text-xl">Titular</label>
                                    <input type="text" 
                                           name="cardholder"
                                           placeholder="Como aparece en la tarjeta"
                                           class="w-full bg-gray-800 border border-gray-700 rounded-xl px-6 py-4 text-white text-xl focus:outline-none focus:border-neon-purple">
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Columna derecha - Resumen -->
                    <div class="space-y-6">
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8 sticky top-24">
                            <h2 class="text-3xl font-bold text-white mb-6">3. Resumen del pedido</h2>
                            
                            <div class="space-y-4 mb-6">
                                <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $itemTotal = $item['price'] * $item['quantity'];
                                    ?>
                                    <div class="flex justify-between text-xl">
                                        <span class="text-gray-400"><?php echo e($item['name']); ?> x<?php echo e($item['quantity']); ?></span>
                                        <span class="text-white"><?php echo e(number_format($itemTotal, 2)); ?>€</span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>

                            <div id="tax-summary" class="bg-gray-800/50 rounded-xl p-6 mb-6">
                                <div class="flex justify-between text-xl mb-3">
                                    <span class="text-gray-400">Subtotal:</span>
                                    <span class="text-white"><?php echo e(number_format($subtotal, 2)); ?>€</span>
                                </div>
                                <div id="tax-details">
                                    <div class="flex justify-between text-xl mb-3 tax-line">
                                        <span class="text-gray-400 tax-name">Selecciona dirección</span>
                                        <span class="text-white tax-amount">-</span>
                                    </div>
                                </div>
                                <div class="flex justify-between text-3xl font-bold pt-4 border-t border-gray-700">
                                    <span class="text-white">Total:</span>
                                    <span class="text-neon-blue total-amount"><?php echo e(number_format($subtotal, 2)); ?>€</span>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="w-full mt-4 px-8 py-5 bg-gradient-to-r from-neon-purple to-neon-blue text-white text-xl font-bold rounded-xl hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]"
                                    onclick="return validateForm()">
                                💳 Pagar ahora
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    function formatCardNumber(input) {
        let value = input.value.replace(/\s/g, '').replace(/\D/g, '');
        let formatted = '';
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) formatted += ' ';
            formatted += value[i];
        }
        input.value = formatted;
        
        const numbers = value;
        document.getElementById('card_last_four').value = numbers.slice(-4);
        
        const firstDigit = numbers.charAt(0);
        let brand = 'Desconocida';
        if (firstDigit === '4') brand = 'Visa';
        if (firstDigit === '5') brand = 'Mastercard';
        if (firstDigit === '3') brand = 'American Express';
        document.getElementById('card_brand').value = brand;
    }
    
    function formatExpiryDate(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length >= 2) {
            input.value = value.slice(0, 2) + '/' + value.slice(2, 4);
        } else {
            input.value = value;
        }
    }
    
    function formatCVV(input) {
        input.value = input.value.replace(/\D/g, '').slice(0, 3);
    }
    
    function validateForm() {
        const addressSelected = document.querySelector('input[name="address_id"]:checked');
        if (!addressSelected) {
            alert('Selecciona una dirección');
            return false;
        }
        
        const cardNumber = document.getElementById('card_number_display').value.replace(/\s/g, '');
        if (cardNumber.length < 15) {
            alert('Número de tarjeta inválido');
            return false;
        }
        
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
            const taxAmount = <?php echo e($subtotal); ?> * (taxRate / 100);
            const total = <?php echo e($subtotal); ?> + taxAmount;
            
            document.querySelector('.tax-line').innerHTML = `
                <span class="text-gray-400 tax-name">${taxName}:</span>
                <span class="text-white tax-amount">${taxAmount.toFixed(2)}€</span>
            `;
            
            document.querySelector('.total-amount').textContent = total.toFixed(2) + '€';
        });
        
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
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
<?php endif; ?><?php /**PATH C:\Users\Daniel\Documents\Prueba\Proyecto_final\laravel_shop\resources\views/cart/checkout.blade.php ENDPATH**/ ?>