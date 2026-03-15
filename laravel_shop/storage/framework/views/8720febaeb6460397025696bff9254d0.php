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
        <!-- Mismo ancho que la página principal -->
        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón para volver al catálogo normal -->
            <div class="mb-6">
                <a href="<?php echo e(route('products.index')); ?>" class="inline-flex items-center text-gray-400 hover:text-neon-blue transition group">
                    <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>Volver al catálogo general</span>
                </a>
            </div>

            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">
                    <span class="text-neon-red">🔥 Artículos Exclusivos</span>
                </h1>
                <p class="text-gray-400">Productos únicos y ediciones limitadas</p>
                <p class="text-gray-500 mt-2"><?php echo e($products->total()); ?> productos exclusivos disponibles</p>
            </div>

            <!-- FILTRO POR CATEGORÍA -->
            <?php if($categories->count() > 0): ?>
                <div class="mb-8 flex flex-wrap gap-3">
                    <a href="<?php echo e(route('products.exclusivos')); ?>" 
                       class="px-4 py-2 rounded-full text-sm font-bold transition 
                       <?php echo e(!request('category') ? 'bg-neon-red text-white' : 'bg-gamer-card text-gray-400 hover:text-white'); ?>">
                        Todos los exclusivos
                    </a>
                    
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('products.exclusivos')); ?>?category=<?php echo e($cat->slug); ?>" 
                           class="px-4 py-2 rounded-full text-sm font-bold transition
                           <?php echo e(request('category') == $cat->slug ? 'bg-neon-red text-white' : 'bg-gamer-card text-gray-400 hover:text-white'); ?>">
                            <?php echo e($cat->name); ?> (<?php echo e($cat->products_count); ?>)
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>

            <!-- Grid de productos - 5 columnas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('products.partials.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-12 bg-gamer-card rounded-2xl border border-neon-red/20">
                        <p class="text-gray-400">No hay productos exclusivos disponibles</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <div class="mt-8">
                <?php echo e($products->withQueryString()->links()); ?>

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
<?php endif; ?><?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/products/exclusivos.blade.php ENDPATH**/ ?>