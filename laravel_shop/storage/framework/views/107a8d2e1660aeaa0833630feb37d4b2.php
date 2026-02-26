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
            <!-- Cabecera con botones -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-neon-blue">👥 Gestión de Usuarios</span>
                    </h1>
                    <p class="text-gray-400">Administra todos los usuarios del sistema</p>
                </div>
                <div class="flex space-x-3">
                    <!-- Botón para limpiar chat -->
                    <a href="<?php echo e(route('admin.clean.messages')); ?>" 
                       class="px-6 py-3 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]"
                       onclick="return confirm('¿Eliminar todos los mensajes del chat de más de 1 hora?')">
                        🗑️ Limpiar Chat
                    </a>
                    <!-- Botón para nuevo usuario -->
                    <a href="<?php echo e(route('admin.users.create')); ?>" 
                       class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                        + Nuevo Usuario
                    </a>
                </div>
            </div>

            <!-- Mensajes -->
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

            <!-- Tabla de usuarios -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-800 border-b border-neon-blue/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-neon-blue">ID</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Nombre</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Email</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Rol</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Registro</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-300"><?php echo e($user->id); ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple flex items-center justify-center text-white font-bold text-sm">
                                            <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                                        </div>
                                        <span class="text-white font-medium"><?php echo e($user->name); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-300"><?php echo e($user->email); ?></td>
                                <td class="px-6 py-4">
                                    <?php if($user->is_admin): ?>
                                        <span class="px-3 py-1 bg-neon-purple/20 text-neon-purple rounded-full text-xs font-bold">ADMIN</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-xs">Usuario</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-400 text-sm"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="<?php echo e(route('admin.users.edit', $user)); ?>" 
                                           class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                            Editar
                                        </a>
                                        
                                        <?php if($user->id !== Auth::id()): ?>
                                            <form action="<?php echo e(route('admin.users.toggle-admin', $user)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <button type="submit" 
                                                        class="px-3 py-1 <?php echo e($user->is_admin ? 'bg-neon-purple/10 text-neon-purple hover:bg-neon-purple hover:text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600'); ?> rounded-lg transition text-sm">
                                                    <?php echo e($user->is_admin ? 'Quitar Admin' : 'Hacer Admin'); ?>

                                                </button>
                                            </form>
                                            
                                            <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" 
                                                  method="POST" 
                                                  onsubmit="return confirm('¿Eliminar usuario <?php echo e($user->name); ?>? Esta acción no se puede deshacer.')"
                                                  class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" 
                                                        class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition text-sm">
                                                    Eliminar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="mt-6">
                <?php echo e($users->links()); ?>

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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/users/index.blade.php ENDPATH**/ ?>