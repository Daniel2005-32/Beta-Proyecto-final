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
                <a href="<?php echo e(route('admin.users.index')); ?>" class="text-gray-400 hover:text-neon-purple transition">
                    ← Volver a la lista
                </a>
            </div>

            <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Crear Nuevo Usuario</h1>

                <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Nombre</label>
                        <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Contraseña</label>
                        <input type="password" name="password" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Confirmar Contraseña</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                    </div>

                    <div class="flex items-center space-x-2">
                        <input type="checkbox" name="is_admin" id="is_admin" value="1" class="rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                        <label for="is_admin" class="text-gray-300">¿Es administrador?</label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-6 py-4 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                            Crear Usuario
                        </button>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/users/create.blade.php ENDPATH**/ ?>