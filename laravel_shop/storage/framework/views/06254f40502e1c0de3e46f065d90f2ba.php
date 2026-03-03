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
            <div class="mb-6">
                <a href="<?php echo e(route('admin.orders.index')); ?>" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver a la lista de pedidos
                </a>
            </div>

            <!-- Cabecera -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-3xl font-black text-white mb-2">Pedido #<?php echo e($order->id); ?></h1>
                        <p class="text-gray-400">Realizado el <?php echo e($order->created_at->format('d/m/Y H:i')); ?></p>
                        <p class="text-gray-400 mt-1">Cliente: <span class="text-white"><?php echo e($order->user->name); ?></span> (<?php echo e($order->user->email); ?>)</p>
                    </div>
                    <div>
                        <form action="<?php echo e(route('admin.orders.update-status', $order)); ?>" method="POST" class="flex space-x-2">
                            <?php echo csrf_field(); ?>
                            <select name="status" class="bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white">
                                <option value="pending" <?php echo e($order->status == 'pending' ? 'selected' : ''); ?>>Pendiente</option>
                                <option value="completed" <?php echo e($order->status == 'completed' ? 'selected' : ''); ?>>Completado</option>
                                <option value="cancelled" <?php echo e($order->status == 'cancelled' ? 'selected' : ''); ?>>Cancelado</option>
                            </select>
                            <button type="submit" class="px-4 py-2 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                Actualizar
                            </button>
                        </form>
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

                <!-- Totales base (sin impuestos) -->
                <div class="mt-6 pt-6 border-t border-gray-800">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Subtotal:</span>
                            <span class="text-white"><?php echo e(number_format($order->total, 2)); ?>€</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-800">
                            <span class="text-white">Total del pedido:</span>
                            <span class="text-neon-purple"><?php echo e(number_format($order->total, 2)); ?>€</span>
                        </div>
                        
                        <!-- Información de impuestos (solo texto) -->
                        <?php if($order->address): ?>
                            <?php
                                $province = $order->address->state;
                                $taxRate = App\Helpers\PriceHelper::getTaxRate($province);
                                $taxName = $taxRate == 7 ? 'IGIC' : 'IVA';
                            ?>
                            <p class="text-xs text-gray-500 text-right mt-2">
                                * <?php echo e($taxName); ?> <?php echo e($taxRate); ?>% aplicado (cliente de <?php echo e($province); ?>)
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Dirección de entrega -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <h2 class="text-2xl font-bold text-white mb-4">📍 Dirección de entrega</h2>
                
                <?php if($order->address): ?>
                    <div class="space-y-2 text-gray-300">
                        <p class="font-bold text-white text-lg"><?php echo e($order->address->name); ?></p>
                        <p><?php echo e($order->address->street); ?>, <?php echo e($order->address->number); ?></p>
                        <?php if($order->address->complement): ?>
                            <p><?php echo e($order->address->complement); ?></p>
                        <?php endif; ?>
                        <p><?php echo e($order->address->city); ?> - <?php echo e($order->address->state); ?></p>
                        <p>CP: <?php echo e($order->address->zipcode); ?></p>
                        <p>Tel: <?php echo e($order->address->phone); ?></p>
                    </div>
                <?php else: ?>
                    <p class="text-gray-400">No hay dirección asociada a este pedido</p>
                <?php endif; ?>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/orders/show.blade.php ENDPATH**/ ?>