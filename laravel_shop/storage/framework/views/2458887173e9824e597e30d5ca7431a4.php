<div class="bg-gamer-card rounded-2xl border border-neon-purple/20 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 border-b border-neon-purple/20">
            <tr>
                <th class="px-6 py-4 text-left text-neon-purple">ID</th>
                <th class="px-6 py-4 text-left text-neon-purple">Usuario</th>
                <th class="px-6 py-4 text-left text-neon-purple">Email</th>
                <th class="px-6 py-4 text-left text-neon-purple">Registro</th>
                <th class="px-6 py-4 text-left text-neon-purple">Admin</th>
                <th class="px-6 py-4 text-left text-neon-purple">Estado</th>
                <th class="px-6 py-4 text-left text-neon-purple">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-gray-300"><?php echo e($user->id); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-purple to-neon-blue flex items-center justify-center text-white font-bold text-sm">
                                <?php echo e(strtoupper(substr($user->name, 0, 1))); ?>

                            </div>
                            <span class="text-white font-medium"><?php echo e($user->name); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-400"><?php echo e($user->email); ?></td>
                    <td class="px-6 py-4 text-gray-400"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                    <td class="px-6 py-4">
                        <?php if($user->is_admin): ?>
                            <span class="text-neon-purple">✓</span>
                        <?php else: ?>
                            <span class="text-gray-600">✗</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($user->isBanned()): ?>
                            <span class="px-3 py-1 bg-neon-red/20 text-neon-red rounded-full text-xs">Baneado</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-xs">Activo</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <?php if(!$user->is_admin): ?>
                                <form action="<?php echo e(route('admin.users.toggle-admin', $user)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-3 py-1 bg-neon-purple/10 text-neon-purple rounded-lg hover:bg-neon-purple hover:text-white transition text-sm">
                                        Hacer Admin
                                    </button>
                                </form>
                            <?php endif; ?>
                            <?php if(!$user->isBanned()): ?>
                                <a href="<?php echo e(route('admin.bans.create', ['user_id' => $user->id])); ?>" 
                                   class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition text-sm">
                                    Banear
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <p>No hay usuarios para mostrar</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($users->links()); ?>

</div>
<?php /**PATH /app/resources/views/admin/users/partials/list.blade.php ENDPATH**/ ?>