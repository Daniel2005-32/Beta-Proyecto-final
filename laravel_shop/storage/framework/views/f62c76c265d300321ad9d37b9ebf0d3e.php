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
            <div class="mb-4">
                <a href="<?php echo e(url()->previous()); ?>" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver
                </a>
            </div>
            
            <div class="bg-gamer-card rounded-2xl overflow-hidden border border-neon-blue/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                    <!-- Imagen del producto -->
                    <div>
                        <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full rounded-lg">
                    </div>
                    
                    <!-- Detalles del producto -->
                    <div>
                        <span class="text-neon-blue text-sm uppercase"><?php echo e($product->category->name); ?></span>
                        <h1 class="text-4xl font-black text-white mt-2"><?php echo e($product->name); ?></h1>
                        <p class="text-gray-400 mt-4"><?php echo e($product->description); ?></p>
                        
                        <!-- Precio y oferta -->
                        <div class="mt-6">
                            <?php if($product->original_price && $product->original_price > $product->price): ?>
                                <span class="text-3xl font-black text-neon-red"><?php echo e(number_format($product->price, 2)); ?>€</span>
                                <span class="ml-2 text-gray-500 line-through text-lg"><?php echo e(number_format($product->original_price, 2)); ?>€</span>
                            <?php else: ?>
                                <span class="text-3xl font-black text-neon-blue"><?php echo e(number_format($product->price, 2)); ?>€</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Stock -->
                        <div class="mt-4">
                            <?php if($product->stock > 0): ?>
                                <span class="text-green-500">✅ Stock disponible: <?php echo e($product->stock); ?> unidades</span>
                            <?php else: ?>
                                <span class="text-red-500">❌ Agotado</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="mt-8 flex gap-4">
                            <form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                    Añadir al carrito
                                </button>
                            </form>
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