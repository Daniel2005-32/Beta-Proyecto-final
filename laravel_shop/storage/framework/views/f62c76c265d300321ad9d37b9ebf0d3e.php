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
            <div class="bg-gamer-card rounded-2xl overflow-hidden border border-neon-blue/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                    <div>
                        <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full rounded-lg">
                    </div>
                    <div>
                        <span class="text-neon-blue text-sm uppercase"><?php echo e($product->category); ?></span>
                        <h1 class="text-4xl font-black text-white mt-2"><?php echo e($product->name); ?></h1>
                        <p class="text-gray-400 mt-4"><?php echo e($product->description); ?></p>
                        <div class="mt-6">
                            <span class="text-3xl font-black text-neon-blue"><?php echo e(number_format($product->price, 2)); ?>€</span>
                        </div>
                        <div class="mt-6">
                            <span class="text-gray-400">Stock: <?php echo e($product->stock); ?> unidades</span>
                        </div>
                        <div class="mt-8 flex gap-4">
                            <button class="px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition">
                                Añadir al carrito
                            </button>
                            <a href="<?php echo e(route('products.index')); ?>" class="px-8 py-4 border border-neon-purple text-neon-purple font-black rounded-full hover:bg-neon-purple hover:text-white transition">
                                Volver
                            </a>
                        </div>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/products/show.blade.php ENDPATH**/ ?>