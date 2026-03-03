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
            <!-- Botón para volver -->
            <div class="mb-6">
                <a href="<?php echo e(route('products.index')); ?>" class="text-gray-400 hover:text-neon-blue transition">
                    ← Seguir comprando
                </a>
            </div>

            <!-- Cabecera -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-black text-white mb-2">Pedido #<?php echo e($order->id); ?></h1>
                        <p class="text-gray-400">Realizado el <?php echo e($order->created_at->format('d/m/Y H:i')); ?></p>
                    </div>
                    <div>
                        <?php if($order->status == 'pending'): ?>
                            <span class="bg-yellow-600/20 text-yellow-400 px-4 py-2 rounded-full text-sm font-bold border border-yellow-600/30">
                                Pendiente
                            </span>
                        <?php elseif($order->status == 'completed'): ?>
                            <span class="bg-green-600/20 text-green-400 px-4 py-2 rounded-full text-sm font-bold border border-green-600/30">
                                Completado
                            </span>
                        <?php else: ?>
                            <span class="bg-red-600/20 text-red-400 px-4 py-2 rounded-full text-sm font-bold border border-red-600/30">
                                Cancelado
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Productos del pedido -->
            <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8 mb-6">
                <h2 class="text-2xl font-bold text-white mb-6">Productos</h2>
                
                <div class="space-y-4">
                    <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center justify-between py-4 border-b border-gray-800 last:border-0">
                            <div class="flex items-center space-x-4">
                                <img src="<?php echo e($item->product->image); ?>" alt="<?php echo e($item->product->name); ?>" class="w-16 h-16 object-cover rounded-lg">
                                <div>
                                    <h3 class="text-white font-bold"><?php echo e($item->product->name); ?></h3>
                                    <p class="text-gray-400 text-sm">Cantidad: <?php echo e($item->quantity); ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-neon-blue font-bold"><?php echo e(number_format($item->price * $item->quantity, 2)); ?>€</p>
                                <p class="text-gray-500 text-sm"><?php echo e(number_format($item->price, 2)); ?>€ x <?php echo e($item->quantity); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Total base (sin impuestos) -->
                <div class="mt-6 pt-6 border-t border-gray-800">
                    <div class="flex justify-between items-center">
                        <span class="text-xl text-white font-bold">Total del pedido</span>
                        <span class="text-2xl text-neon-purple font-black"><?php echo e(number_format($order->total, 2)); ?>€</span>
                    </div>
                    
                    <!-- Información de impuestos -->
                    <?php if($order->address): ?>
                        <?php
                            $province = $order->address->state;
                            $taxRate = App\Helpers\PriceHelper::getTaxRate($province);
                            $taxName = $taxRate == 7 ? 'IGIC' : 'IVA';
                        ?>
                        <p class="text-xs text-gray-500 text-right mt-2">
                            * <?php echo e($taxName); ?> <?php echo e($taxRate); ?>% aplicado (según dirección de envío)
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Información de pago -->
            <?php if($order->card_last_four): ?>
            <div class="bg-gamer-card rounded-2xl border border-neon-green/20 p-8 mb-6">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-neon-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                    💳 Información de pago
                </h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Tarjeta</p>
                        <p class="text-white font-bold"><?php echo e($order->card_brand ?? 'Desconocida'); ?> terminada en <?php echo e($order->card_last_four); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Estado del pago</p>
                        <p class="text-green-400 font-bold">✅ Pagado</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Dirección de entrega -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8 mb-6">
                <h2 class="text-2xl font-bold text-white mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-neon-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    📍 Dirección de entrega
                </h2>
                
                <?php if($order->address): ?>
                    <div class="space-y-2 text-gray-300">
                        <p class="font-bold text-white text-lg"><?php echo e($order->address->name); ?></p>
                        <p class="flex items-start gap-2">
                            <span class="text-neon-blue min-w-[80px]">Calle:</span>
                            <?php echo e($order->address->street); ?>, <?php echo e($order->address->number); ?>

                        </p>
                        <?php if($order->address->complement): ?>
                            <p class="flex items-start gap-2">
                                <span class="text-neon-blue min-w-[80px]">Complemento:</span>
                                <?php echo e($order->address->complement); ?>

                            </p>
                        <?php endif; ?>
                        <p class="flex items-start gap-2">
                            <span class="text-neon-blue min-w-[80px]">Ciudad:</span>
                            <?php echo e($order->address->city); ?> - <?php echo e($order->address->state); ?>

                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-neon-blue min-w-[80px]">Código Postal:</span>
                            <?php echo e($order->address->zipcode); ?>

                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-neon-blue min-w-[80px]">Teléfono:</span>
                            <?php echo e($order->address->phone); ?>

                        </p>
                    </div>
                <?php else: ?>
                    <p class="text-gray-400">No hay dirección asociada a este pedido</p>
                <?php endif; ?>
            </div>

            <!-- Información adicional -->
            <div class="bg-gamer-card rounded-2xl border border-neon-red/20 p-8">
                <h2 class="text-2xl font-bold text-white mb-4">📋 Información del pedido</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-400 text-sm">Estado</p>
                        <p class="text-white font-bold"><?php echo e(ucfirst($order->status)); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Fecha</p>
                        <p class="text-white font-bold"><?php echo e($order->created_at->format('d/m/Y')); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">Total productos</p>
                        <p class="text-white font-bold"><?php echo e($order->items->count()); ?></p>
                    </div>
                    <div>
                        <p class="text-gray-400 text-sm">ID de pedido</p>
                        <p class="text-white font-bold">#<?php echo e($order->id); ?></p>
                    </div>
                </div>
            </div>

            <!-- Botón para volver a comprar -->
            <div class="mt-8 text-center">
                <a href="<?php echo e(route('products.index')); ?>" class="inline-flex items-center text-gray-400 hover:text-neon-blue transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Seguir comprando
                </a>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/orders/show.blade.php ENDPATH**/ ?>