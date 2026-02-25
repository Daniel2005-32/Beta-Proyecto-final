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
    <!-- Sección de Bienvenida (Intro) -->
    <div class="relative rounded-3xl overflow-hidden mb-12 border border-neon-blue/20 bg-gamer-card shadow-[0_0_30px_rgba(0,210,255,0.1)]">
        <div class="absolute inset-0 bg-gradient-to-r from-gamer-dark via-transparent to-gamer-dark opacity-60"></div>
        <!-- Simulación de Banner -->
        <div class="h-[400px] bg-gradient-to-br from-neon-blue/10 via-neon-purple/10 to-neon-red/10 flex items-center px-8 md:px-16 relative z-10">
            <div class="max-w-2xl">
                <h1 class="text-5xl md:text-6xl font-black text-white leading-tight mb-4 tracking-tighter uppercase italic">
                    Bienvenidos a <span class="text-neon-blue neon-text-blue">Gamer</span> <span class="text-neon-purple neon-text-purple">Guild</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-300 mb-8 leading-relaxed font-medium">
                    Tu santuario definitivo para la cultura gamer y otaku. En Gamer Guild nos apasiona ofrecerte lo último en videojuegos, manga de colección, las figuras más detalladas y el mejor cosplay para tus eventos. ¡Únete a nuestra hermandad!
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="<?php echo e(route('products.index')); ?>" class="px-8 py-4 bg-neon-blue text-gamer-dark font-black uppercase tracking-widest rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                        Explorar Catálogo
                    </a>
                    <a href="<?php echo e(route('auctions.index')); ?>" class="px-8 py-4 border-2 border-neon-purple text-neon-purple font-black uppercase tracking-widest rounded-full hover:bg-neon-purple hover:text-white transition shadow-[0_0_20px_rgba(157,0,255,0.2)]">
                        Ver Subastas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos Destacados -->
    <div class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 border-neon-blue pl-4">
                Productos <span class="text-neon-blue">Destacados</span>
            </h2>
            <a href="<?php echo e(route('products.index')); ?>" class="text-sm font-bold text-gray-500 hover:text-neon-blue transition uppercase tracking-widest">Ver todo</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php $__currentLoopData = $featured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('products.partials.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Tendencias -->
    <div class="mb-16">
        <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 border-neon-purple pl-4 mb-8">
            En <span class="text-neon-purple">Tendencia</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php $__currentLoopData = $trending; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('products.partials.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    
    <!-- Artículos Exclusivos -->
    <div class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 border-neon-red pl-4">
                Artículos <span class="text-neon-red">Exclusivos</span>
            </h2>
            <a href="<?php echo e(route('products.exclusivos')); ?>" class="text-sm font-bold text-gray-500 hover:text-neon-red transition uppercase tracking-widest">Ver todos</a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php $__empty_1 = true; $__currentLoopData = $exclusive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php echo $__env->make('products.partials.product-card', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-center py-12 bg-gamer-card rounded-2xl border border-neon-red/20">
                    <p class="text-gray-400">No hay artículos exclusivos disponibles</p>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/home.blade.php ENDPATH**/ ?>