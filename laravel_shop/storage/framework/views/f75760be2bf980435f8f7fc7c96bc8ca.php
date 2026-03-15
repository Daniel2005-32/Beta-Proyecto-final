<div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-800 border-b border-neon-blue/20">
            <tr>
                <th class="px-6 py-4 text-left text-neon-blue">ID</th>
                <th class="px-6 py-4 text-left text-neon-blue">Imagen</th>
                <th class="px-6 py-4 text-left text-neon-blue">Nombre</th>
                <th class="px-6 py-4 text-left text-neon-blue">Categoría</th>
                <th class="px-6 py-4 text-left text-neon-blue">Badges</th>
                <th class="px-6 py-4 text-left text-neon-blue">Precio</th>
                <th class="px-6 py-4 text-left text-neon-blue">Stock</th>
                <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                    <td class="px-6 py-4 text-gray-300"><?php echo e($product->id); ?></td>
                    <td class="px-6 py-4">
                        <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-12 h-12 object-cover rounded">
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-white font-medium"><?php echo e($product->name); ?></span>
                    </td>
                    <td class="px-6 py-4 text-gray-400"><?php echo e($product->category->name); ?></td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1">
                            <?php if($product->is_exclusive): ?>
                                <span class="px-2 py-0.5 bg-neon-red/20 text-neon-red text-xs rounded-full">EXCLUSIVO</span>
                            <?php endif; ?>
                            <?php if($product->featured): ?>
                                <span class="px-2 py-0.5 bg-neon-blue/20 text-neon-blue text-xs rounded-full">DESTACADO</span>
                            <?php endif; ?>
                            <?php if($product->trending): ?>
                                <span class="px-2 py-0.5 bg-neon-purple/20 text-neon-purple text-xs rounded-full">TENDENCIA</span>
                            <?php endif; ?>
                            <?php if($product->original_price && $product->original_price > $product->price): ?>
                                <span class="px-2 py-0.5 bg-green-600/20 text-green-400 text-xs rounded-full">OFERTA</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($product->original_price): ?>
                            <div>
                                <span class="text-neon-red font-bold"><?php echo e(number_format($product->price, 2)); ?>€</span>
                                <span class="text-gray-500 line-through text-sm ml-1"><?php echo e(number_format($product->original_price, 2)); ?>€</span>
                            </div>
                        <?php else: ?>
                            <span class="text-white"><?php echo e(number_format($product->price, 2)); ?>€</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php if($product->stock > 0): ?>
                            <span class="text-green-500"><?php echo e($product->stock); ?></span>
                        <?php else: ?>
                            <span class="text-red-500">Agotado</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <a href="<?php echo e(route('admin.products.edit', $product)); ?>" 
                               class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                Editar
                            </a>
                            <form action="<?php echo e(route('admin.products.destroy', $product)); ?>" 
                                  method="POST" 
                                  onsubmit="return confirm('¿Eliminar este producto?')"
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
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <p>No hay productos para mostrar</p>
                        <a href="<?php echo e(route('admin.products.create')); ?>" class="inline-block mt-4 px-4 py-2 bg-neon-blue text-gamer-dark rounded-lg">
                            Crear primer producto
                        </a>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($products->links()); ?>

</div>
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/products/partials/list.blade.php ENDPATH**/ ?>