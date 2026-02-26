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
            <!-- Cabecera -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-2">
                    <span class="text-neon-blue">➕ Crear Nuevo Usuario</span>
                </h1>
                <p class="text-gray-400">Añade un nuevo usuario al sistema</p>
            </div>

            <!-- Formulario -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Nombre</label>
                        <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-neon-red <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-neon-red"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Email</label>
                        <input type="email" name="email" value="<?php echo e(old('email')); ?>" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-neon-red <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-neon-red"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Contraseña</label>
                        <input type="password" name="password" required minlength="8"
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-neon-red <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-neon-red"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-xs text-gray-500 mt-1">Mínimo 8 caracteres</p>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2 font-bold">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition">
                    </div>

                    <div class="flex items-center space-x-3">
                        <input type="checkbox" name="is_admin" id="is_admin" value="1"
                               class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                        <label for="is_admin" class="text-gray-300">Usuario administrador</label>
                    </div>

                    <div class="flex space-x-4 pt-4">
                        <button type="submit" 
                                class="px-8 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                            Crear Usuario
                        </button>
                        <a href="<?php echo e(route('admin.users.index')); ?>" 
                           class="px-8 py-3 bg-gray-800 text-gray-300 font-bold rounded-lg hover:bg-gray-700 transition">
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/users/create.blade.php ENDPATH**/ ?>