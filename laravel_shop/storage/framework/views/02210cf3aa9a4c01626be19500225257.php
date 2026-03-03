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
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-neon-purple">🎲 Gestión de Sorteos</span>
                    </h1>
                    <p class="text-gray-400">Administra los sorteos mensuales</p>
                </div>
                <a href="<?php echo e(route('admin.raffles.create')); ?>" class="px-6 py-3 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition">
                    + Nuevo Sorteo
                </a>
            </div>

            <?php if(session('success')): ?>
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="bg-red-900/50 border border-neon-red text-red-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-800 border-b border-neon-purple/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-neon-purple">ID</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Título</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Producto</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Precio/Entrada</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Fecha fin</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Estado</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Ganador</th>
                            <th class="px-6 py-4 text-left text-neon-purple">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $raffles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $raffle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $extra = $raffle->getExtraData();
                                $product = $raffle->getProduct();
                                $cleanDesc = $raffle->getCleanDescription();
                            ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-300"><?php echo e($raffle->id); ?></td>
                                <td class="px-6 py-4">
                                    <div class="text-white font-medium"><?php echo e($raffle->title); ?></div>
                                    <div class="text-gray-500 text-xs"><?php echo e(Str::limit($cleanDesc, 50)); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if($product): ?>
                                        <div class="flex items-center space-x-2">
                                            <img src="<?php echo e($product->image); ?>" alt="" class="w-8 h-8 object-cover rounded">
                                            <span class="text-gray-300"><?php echo e($product->name); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-500">Producto no disponible</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-neon-purple"><?php echo e($extra['ticket_price'] ?? 20); ?>€</span>
                                </td>
                                <td class="px-6 py-4 text-gray-300">
                                    <?php echo e(isset($extra['end_date']) ? \Carbon\Carbon::parse($extra['end_date'])->format('d/m/Y H:i') : ($raffle->draw_date ? $raffle->draw_date->format('d/m/Y H:i') : 'No definida')); ?>

                                </td>
                                <td class="px-6 py-4">
                                    <?php if($raffle->status == 'pending'): ?>
                                        <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-xs">Activo</span>
                                    <?php elseif($raffle->status == 'completed'): ?>
                                        <span class="px-3 py-1 bg-gray-600/20 text-gray-400 rounded-full text-xs">Finalizado</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-yellow-600/20 text-yellow-400 rounded-full text-xs"><?php echo e($raffle->status); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if($raffle->winner): ?>
                                        <span class="text-neon-blue"><?php echo e($raffle->winner->name); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-500">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="<?php echo e(route('admin.raffles.edit', $raffle)); ?>" 
                                           class="px-3 py-1 bg-neon-purple/10 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition text-sm">
                                            Editar
                                        </a>
                                        
                                        <?php if($raffle->status == 'pending'): ?>
                                            <?php if(!$raffle->winner): ?>
                                                <form action="<?php echo e(route('admin.raffles.draw', $raffle)); ?>" method="POST" class="inline">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" 
                                                            class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm"
                                                            onclick="return confirm('¿Sortear ganador ahora? Esto finalizará el sorteo.')">
                                                        Sortear
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <?php if($raffle->status == 'pending' && $raffle->draw_date < now()): ?>
                                            <form action="<?php echo e(route('admin.raffles.activate', $raffle)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-green-600/10 text-green-400 rounded-lg hover:bg-green-600 hover:text-white transition text-sm">
                                                    Activar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form action="<?php echo e(route('admin.raffles.destroy', $raffle)); ?>" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Eliminar este sorteo?')"
                                              class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition text-sm">
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <?php echo e($raffles->links()); ?>

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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/raffles/index.blade.php ENDPATH**/ ?>