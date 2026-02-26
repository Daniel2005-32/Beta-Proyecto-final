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
                <!-- Selección de dirección -->
                <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                    <h2 class="text-2xl font-bold text-white mb-6">1. Dirección de entrega</h2>

                    <?php if($addresses->count() > 0): ?>
                        <form action="<?php echo e(route('cart.checkout')); ?>" method="POST" id="checkout-form">
                            <?php echo csrf_field(); ?>
                            
                            <div class="space-y-4 mb-6">
                                <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="block">
                                        <input type="radio" name="address_id" value="<?php echo e($address->id); ?>" 
                                               <?php echo e($address->is_default ? 'checked' : ''); ?> required
                                               class="hidden peer">
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

                            <!-- Resumen del pedido -->
                            <div class="border-t border-gray-800 pt-6">
                                <h2 class="text-2xl font-bold text-white mb-4">2. Resumen del pedido</h2>
                                
                                <div class="space-y-3 mb-6">
                                    <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-400"><?php echo e($item['name']); ?> x<?php echo e($item['quantity']); ?></span>
                                            <span class="text-white"><?php echo e(number_format($item['price'] * $item['quantity'], 2)); ?>€</span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                <div class="border-t border-gray-800 pt-4">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span class="text-white">Total</span>
                                        <span class="text-neon-blue"><?php echo e(number_format($total, 2)); ?>€</span>
                                    </div>
                                </div>

                                <button type="submit" class="w-full mt-6 px-6 py-4 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                    Confirmar pedido
                                </button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <p class="text-gray-400 mb-4">Necesitas una dirección para continuar</p>
                            <a href="<?php echo e(route('addresses.create')); ?>" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                Añadir dirección
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ayuda -->
                <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6">
                    <h2 class="text-xl font-bold text-white mb-4">Información importante</h2>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-neon-blue mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>El envío es gratuito para todos los pedidos</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-neon-blue mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>El plazo de entrega es de 3 a 7 días hábiles</span>
                        </li>
                        <li class="flex items-start space-x-2">
                            <svg class="w-5 h-5 text-neon-blue mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                            </svg>
                            <span>Pago seguro con tarjeta de crédito/débito</span>
                        </li>
                    </ul>

                    <div class="mt-6 p-4 bg-gray-800/50 rounded-lg">
                        <h3 class="text-white font-bold mb-2">¿Necesitas ayuda?</h3>
                        <p class="text-sm text-gray-400">
                            Contacta con nuestro soporte en <a href="<?php echo e(route('contact')); ?>" class="text-neon-blue hover:underline">Contacto</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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