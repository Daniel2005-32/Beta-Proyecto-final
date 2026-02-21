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
    <h1 class="text-3xl font-bold mb-8">Tu Carrito de Compras</h1>

    <?php if(session('cart')): ?>
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
                    <?php $total = 0 ?>
                    <?php $__currentLoopData = session('cart'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $total += $details['price'] * $details['quantity'] ?>
                        <tr class="border-t">
                            <td class="py-4 flex items-center">
                                <img src="<?php echo e($details['image'] ?? 'https://via.placeholder.com/50'); ?>" class="w-12 h-12 rounded mr-4">
                                <?php echo e($details['name']); ?>

                            </td>
                            <td class="py-4"><?php echo e($details['price']); ?>€</td>
                            <td class="py-4"><?php echo e($details['quantity']); ?></td>
                            <td class="py-4"><?php echo e($details['price'] * $details['quantity']); ?>€</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            
            <div class="flex justify-between items-center mt-8 border-t pt-4">
                <div class="text-2xl font-bold">Total: <?php echo e($total); ?>€</div>
                <form action="<?php echo e(route('cart.checkout')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-indigo-700">
                        Finalizar Compra
                    </button>
                </form>
            </div>
            
            <?php if($total >= 20): ?>
                <div class="mt-4 bg-green-100 text-green-800 p-3 rounded">
                    ¡Genial! Tu compra supera los 20€, entrarás automáticamente en el sorteo mensual.
                </div>
            <?php else: ?>
                <div class="mt-4 bg-gray-100 text-gray-600 p-3 rounded">
                    Añade <?php echo e(20 - $total); ?>€ más para entrar en el sorteo mensual.
                </div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-12">
            <p class="text-gray-500 mb-4">Tu carrito está vacío.</p>
            <a href="<?php echo e(route('products.index')); ?>" class="text-indigo-600 font-bold hover:underline">Ir a comprar</a>
        </div>
    <?php endif; ?>
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
<?php /**PATH C:\Users\Daniel\Documents\Prueba\Proyecto_final\laravel_shop\resources\views/cart/index.blade.php ENDPATH**/ ?>