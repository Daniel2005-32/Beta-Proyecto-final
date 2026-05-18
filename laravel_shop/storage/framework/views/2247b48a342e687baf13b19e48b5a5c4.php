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
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="<?php echo e(route('admin.bans.index')); ?>" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver a la lista de baneos
                </a>
            </div>

            <div class="bg-gamer-card rounded-2xl border border-neon-red/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Editar Baneo</h1>

                <form action="<?php echo e(route('admin.bans.update', $ban)); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Usuario</label>
                        <input type="text" value="<?php echo e($ban->user->name); ?> (<?php echo e($ban->user->email); ?>)" disabled
                               class="w-full bg-gray-800/50 border border-gray-700 rounded-lg px-4 py-3 text-gray-400 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Motivo del baneo</label>
                        <textarea name="reason" rows="3" required
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-red"><?php echo e(old('reason', $ban->reason)); ?></textarea>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="submit" 
                                class="flex-1 px-6 py-4 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                            Actualizar baneo
                        </button>
                        <a href="<?php echo e(route('admin.bans.index')); ?>" 
                           class="flex-1 px-6 py-4 bg-gray-800 text-gray-300 font-bold rounded-lg hover:bg-gray-700 transition text-center">
                            Cancelar
                        </a>
                    </div>
                </form>
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
<?php /**PATH /app/resources/views/admin/bans/edit.blade.php ENDPATH**/ ?>