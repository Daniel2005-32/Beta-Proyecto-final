<div class="bg-gamer-card rounded-2xl border border-neon-green/20 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 border-b border-neon-green/20">
            <tr>
                <th class="px-6 py-4 text-left text-neon-green">ID</th>
                <th class="px-6 py-4 text-left text-neon-green">Cliente</th>
                <th class="px-6 py-4 text-left text-neon-green">Productos</th>
                <th class="px-6 py-4 text-left text-neon-green">Total</th>
                <th class="px-6 py-4 text-left text-neon-green">Fecha</th>
                <th class="px-6 py-4 text-left text-neon-green">Estado</th>
                <th class="px-6 py-4 text-left text-neon-green">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-gray-300">#<?php echo e($order->id); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple flex items-center justify-center text-white font-bold text-sm">
                                <?php echo e(strtoupper(substr($order->user->name, 0, 1))); ?>

                            </div>
                            <div>
                                <span class="text-white font-medium"><?php echo e($order->user->name); ?></span>
                                <p class="text-gray-500 text-xs"><?php echo e($order->user->email); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="max-w-[200px] space-y-1">
                            <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="text-[10px] leading-tight">
                                    <span class="text-neon-blue font-black"><?php echo e($item->quantity); ?>x</span>
                                    <span class="text-gray-400 uppercase tracking-tighter"><?php echo e($item->product->name ?? 'Producto eliminado'); ?></span>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-neon-green font-bold"><?php echo e(number_format($order->total, 2)); ?>€</span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-sm"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></td>
                    <td class="px-6 py-4">
                        <?php if($order->status == 'pending'): ?>
                            <span class="px-3 py-1 bg-yellow-600/20 text-yellow-400 rounded-full text-xs">Pendiente</span>
                        <?php elseif($order->status == 'completed'): ?>
                            <span class="px-3 py-1 bg-green-600/20 text-green-400 rounded-full text-xs">Completado</span>
                        <?php else: ?>
                            <span class="px-3 py-1 bg-red-600/20 text-red-400 rounded-full text-xs">Cancelado</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="<?php echo e(route('admin.orders.show', $order)); ?>" 
                               class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                Ver detalles
                            </a>
                            
                            <?php if($order->status == 'pending'): ?>
                                <form action="<?php echo e(route('admin.orders.update-status', $order)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="px-3 py-1 bg-green-600/20 text-green-400 rounded-lg hover:bg-green-600 hover:text-white transition text-xs font-bold uppercase">
                                        Completar
                                    </button>
                                </form>
                                <form action="<?php echo e(route('admin.orders.update-status', $order)); ?>" method="POST" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="status" value="cancelled">
                                    <button type="submit" class="px-3 py-1 bg-red-600/20 text-red-400 rounded-lg hover:bg-red-600 hover:text-white transition text-xs font-bold uppercase">
                                        Cancelar
                                    </button>
                                </form>
                            <?php endif; ?>

                            <form action="<?php echo e(route('admin.orders.destroy', $order)); ?>" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Eliminar este pedido?')"
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
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <p>No hay pedidos para mostrar</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($orders->links()); ?>

</div>
<?php /**PATH /app/resources/views/admin/orders/partials/list.blade.php ENDPATH**/ ?>