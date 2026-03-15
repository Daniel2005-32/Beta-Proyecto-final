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
            
            <!-- Cabecera con contadores -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">Catálogo de Productos</h1>
                <p class="text-gray-400"><?php echo e($products->total()); ?> productos disponibles</p>
            </div>

            <!-- Filtros rápidos por categoría -->
            <div class="mb-8">
                <h2 class="text-sm font-bold uppercase tracking-wider text-gray-500 mb-3">Filtrar por categoría:</h2>
                <div class="flex flex-wrap gap-3">
                    <a href="<?php echo e(route('products.index')); ?>" 
                       class="px-4 py-2 rounded-full text-sm font-bold transition 
                       <?php echo e(!request('category') && !request('exclusive') ? 'bg-neon-blue text-gamer-dark' : 'bg-gamer-card text-gray-400 hover:text-white'); ?>">
                        Todos (<?php echo e($categories->sum('products_count')); ?>)
                    </a>
                    
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('products.index')); ?>?category=<?php echo e($category->slug); ?>" 
                           class="px-4 py-2 rounded-full text-sm font-bold transition
                           <?php echo e(request('category') == $category->slug ? 'bg-neon-blue text-gamer-dark' : 'bg-gamer-card text-gray-400 hover:text-white'); ?>">
                            <?php echo e($category->name); ?> (<?php echo e($category->products_count); ?>)
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    
                    <a href="<?php echo e(route('products.exclusivos')); ?>" 
                       class="px-4 py-2 rounded-full text-sm font-bold transition bg-gamer-card text-neon-red hover:bg-neon-red hover:text-white border border-neon-red/30">
                        🔥 Exclusivos (<?php echo e(App\Models\Product::where('is_exclusive', true)->count()); ?>)
                    </a>
                </div>
            </div>

            <!-- Grid de productos - 5 columnas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('products.partials.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-12 bg-gamer-card rounded-2xl border border-gray-800">
                        <p class="text-gray-400">No hay productos disponibles</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Paginación -->
            <div class="mt-8">
                <?php echo e($products->links()); ?>

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
<?php endif; ?><?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/products/index.blade.php ENDPATH**/ ?>