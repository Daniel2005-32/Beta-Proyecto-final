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
            <!-- Cabecera con botón de crear -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-black text-white">Panel de Administración</h1>
                <a href="<?php echo e(route('admin.products.create')); ?>" class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                    + Nuevo Producto
                </a>
            </div>

            <!-- Mensajes de éxito -->
            <?php if(session('success')): ?>
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <!-- Tabla de productos -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-800 border-b border-neon-blue/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-neon-blue">ID</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Imagen</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Producto</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Categoría</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Precio</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Stock</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-300"><?php echo e($product->id); ?></td>
                                <td class="px-6 py-4">
                                    <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-12 h-12 object-cover rounded-lg">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-white font-bold"><?php echo e($product->name); ?></div>
                                    <?php if($product->is_exclusive): ?>
                                        <span class="text-neon-red text-xs">🔥 Exclusivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-300"><?php echo e($product->category->name); ?></td>
                                <td class="px-6 py-4 text-neon-blue font-bold"><?php echo e(number_format($product->price, 2)); ?>€</td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-sm 
                                        <?php echo e($product->stock > 5 ? 'bg-green-900/50 text-green-300' : 
                                           ($product->stock > 0 ? 'bg-yellow-900/50 text-yellow-300' : 'bg-red-900/50 text-red-300')); ?>">
                                        <?php echo e($product->stock); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="<?php echo e(route('admin.products.edit', $product)); ?>" 
                                           class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition">
                                            Editar
                                        </a>
                                        <form action="<?php echo e(route('admin.products.destroy', $product)); ?>" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Estás seguro de eliminar este producto? Esta acción no se puede deshacer.')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-neon-red/10 text-neon-red rounded-lg hover:bg-neon-red hover:text-white transition">
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

            <!-- Paginación -->
            <div class="mt-6">
                <?php echo e($products->links()); ?>

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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/products/index.blade.php ENDPATH**/ ?>