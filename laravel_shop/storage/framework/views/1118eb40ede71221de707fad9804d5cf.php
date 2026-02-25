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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4"><?php echo e($category->name); ?></h1>
                <p class="text-gray-400"><?php echo e($category->description); ?></p>
                <p class="text-gray-500 mt-2"><?php echo e($products->count()); ?> productos disponibles</p>
            </div>
            
            <?php if($products->count() > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="group bg-gamer-card rounded-2xl overflow-hidden border border-gray-800 hover:border-neon-blue/50 transition">
                            <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-48 object-cover">
                            <div class="p-4">
                                <span class="text-xs text-neon-blue uppercase"><?php echo e($product->category->name); ?></span>
                                <h3 class="text-white font-bold text-lg mt-1"><?php echo e($product->name); ?></h3>
                                <p class="text-gray-400 text-sm mt-2 line-clamp-2"><?php echo e($product->description); ?></p>
                                <div class="flex items-center justify-between mt-4">
                                    <span class="text-2xl font-bold text-neon-blue"><?php echo e(number_format($product->price, 2)); ?>€</span>
                                    <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="px-4 py-2 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition">
                                        Ver
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-gamer-card rounded-2xl border border-gray-800">
                    <p class="text-gray-400">No hay productos en esta categoría</p>
                </div>
            <?php endif; ?>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/products/category.blade.php ENDPATH**/ ?>