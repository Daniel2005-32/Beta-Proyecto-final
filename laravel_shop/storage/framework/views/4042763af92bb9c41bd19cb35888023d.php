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
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-black text-white">
                    <span class="text-neon-blue">📍 Mis Direcciones</span>
                </h1>
                <a href="<?php echo e(route('addresses.create')); ?>" class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                    + Nueva Dirección
                </a>
            </div>

            <?php if(session('success')): ?>
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if($addresses->count() > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6 relative">
                            <?php if($address->is_default): ?>
                                <span class="absolute top-4 right-4 bg-neon-blue text-gamer-dark text-xs font-bold px-2 py-1 rounded-full">
                                    PREDETERMINADA
                                </span>
                            <?php endif; ?>
                            
                            <h3 class="text-xl font-bold text-white mb-2"><?php echo e($address->name); ?></h3>
                            <p class="text-gray-400 text-sm mb-1"><?php echo e($address->street); ?>, <?php echo e($address->number); ?></p>
                            <?php if($address->complement): ?>
                                <p class="text-gray-400 text-sm mb-1"><?php echo e($address->complement); ?></p>
                            <?php endif; ?>
                            <p class="text-gray-400 text-sm mb-1"><?php echo e($address->city); ?> - <?php echo e($address->state); ?></p>
                            <p class="text-gray-400 text-sm mb-1">CP: <?php echo e($address->zipcode); ?></p>
                            <p class="text-gray-400 text-sm mb-3">Tel: <?php echo e($address->phone); ?></p>
                            
                            <div class="flex space-x-2 mt-4">
                                <a href="<?php echo e(route('addresses.edit', $address)); ?>" class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                    Editar
                                </a>
                                <?php if(!$address->is_default): ?>
                                    <a href="<?php echo e(route('addresses.set-default', $address)); ?>" class="px-3 py-1 bg-neon-purple/10 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition text-sm">
                                        Establecer como predeterminada
                                    </a>
                                    <form action="<?php echo e(route('addresses.destroy', $address)); ?>" method="POST" onsubmit="return confirm('¿Eliminar esta dirección?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition text-sm">
                                            Eliminar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php else: ?>
                <div class="bg-gamer-card rounded-2xl border border-gray-800 p-12 text-center">
                    <div class="text-5xl mb-4">📍</div>
                    <h2 class="text-2xl font-bold text-white mb-2">No tienes direcciones guardadas</h2>
                    <p class="text-gray-400 mb-6">Añade una dirección para poder realizar tus pedidos</p>
                    <a href="<?php echo e(route('addresses.create')); ?>" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                        Añadir primera dirección
                    </a>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/addresses/index.blade.php ENDPATH**/ ?>