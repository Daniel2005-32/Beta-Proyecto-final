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
                    Bienvenido a <span class="text-neon-blue neon-text-blue">Gamer</span> <span class="text-neon-purple neon-text-purple">Guild</span>
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
                <div class="group bg-gamer-card rounded-2xl overflow-hidden border border-gray-800 hover:border-neon-blue/50 transition duration-300 shadow-xl">
                    <div class="relative overflow-hidden aspect-square">
                        <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/400x400/161b22/00d2ff?text=' . urlencode($product->name)); ?>" 
                             alt="<?php echo e($product->name); ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-gamer-dark to-transparent opacity-0 group-hover:opacity-60 transition duration-300"></div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-white mb-1 group-hover:text-neon-blue transition truncate"><?php echo e($product->name); ?></h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2"><?php echo e($product->description); ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-black text-white italic"><?php echo e(number_format($product->price, 2)); ?>€</span>
                            <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="p-2 bg-gray-800 rounded-lg group-hover:bg-neon-blue group-hover:text-gamer-dark transition shadow-lg">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
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
                <div class="group bg-gamer-card rounded-2xl overflow-hidden border border-gray-800 hover:border-neon-purple/50 transition duration-300">
                    <div class="relative overflow-hidden aspect-[4/3]">
                        <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/400x300/161b22/9d00ff?text=' . urlencode($product->name)); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                    </div>
                    <div class="p-5">
                        <h3 class="font-bold text-white mb-3 group-hover:text-neon-purple transition truncate"><?php echo e($product->name); ?></h3>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-black text-white italic"><?php echo e(number_format($product->price, 2)); ?>€</span>
                            <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="text-xs font-black uppercase tracking-widest text-neon-purple hover:underline">Detalles</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    
    <!-- Productos Exclusivos -->
    <div class="mb-16">
        <h2 class="text-3xl font-black uppercase italic tracking-tighter text-white border-l-4 border-neon-red pl-4 mb-8">
            Artículos <span class="text-neon-red">Exclusivos</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php $__currentLoopData = $exclusive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="group bg-gamer-card rounded-2xl overflow-hidden border border-neon-red/30 hover:border-neon-red transition duration-300 shadow-[0_0_20px_rgba(255,0,85,0.1)]">
                    <div class="relative overflow-hidden aspect-square">
                        <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/400x400/161b22/ff0055?text=' . urlencode($product->name)); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4 bg-neon-red text-white text-[10px] font-black uppercase tracking-tighter px-2 py-1 rounded shadow-lg">
                            Solo 1 disponible
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-lg text-white mb-4 truncate"><?php echo e($product->name); ?></h3>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-black text-neon-red italic"><?php echo e(number_format($product->price, 2)); ?>€</span>
                            <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="px-4 py-2 bg-neon-red text-white text-xs font-black uppercase tracking-widest rounded hover:scale-105 transition shadow-[0_0_15px_rgba(255,0,85,0.4)]">
                                Comprar Ya
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php endif; ?><?php /**PATH C:\Users\Daniel\Documents\Prueba\Proyecto_final\laravel_shop\resources\views/home.blade.php ENDPATH**/ ?>