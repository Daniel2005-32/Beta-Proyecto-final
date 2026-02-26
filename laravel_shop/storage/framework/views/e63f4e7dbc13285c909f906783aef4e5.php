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
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Botón volver -->
            <div class="mb-6">
                <a href="<?php echo e(route('admin.products.index')); ?>" class="text-gray-400 hover:text-neon-blue transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al panel
                </a>
            </div>

            <!-- Formulario de edición -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Editar Producto</h1>

                <form action="<?php echo e(route('admin.products.update', $product)); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-300 mb-2">Nombre</label>
                            <input type="text" name="name" value="<?php echo e(old('name', $product->name)); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Slug</label>
                            <input type="text" name="slug" value="<?php echo e(old('slug', $product->slug)); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Categoría</label>
                            <select name="category_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                                <?php $__currentLoopData = \App\Models\Category::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php echo e($product->category_id == $category->id ? 'selected' : ''); ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Precio (€)</label>
                            <input type="number" step="0.01" name="price" value="<?php echo e(old('price', $product->price)); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Stock</label>
                            <input type="number" name="stock" value="<?php echo e(old('stock', $product->stock)); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">URL de la imagen</label>
                            <input type="url" name="image" value="<?php echo e(old('image', $product->image)); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-300 mb-2">Descripción</label>
                        <textarea name="description" rows="4" required
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue"><?php echo e(old('description', $product->description)); ?></textarea>
                    </div>

                    <!-- CARACTERÍSTICAS - CORREGIDO -->
                    <div class="grid grid-cols-3 gap-4">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="featured" 
                                   value="1" 
                                   <?php echo e($product->featured ? 'checked' : ''); ?>

                                   class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-blue focus:ring-neon-blue">
                            <span class="text-gray-300">Producto destacado</span>
                        </label>

                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="trending" 
                                   value="1" 
                                   <?php echo e($product->trending ? 'checked' : ''); ?>

                                   class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                            <span class="text-gray-300">Producto en tendencia</span>
                        </label>

                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="is_exclusive" 
                                   value="1" 
                                   <?php echo e($product->is_exclusive ? 'checked' : ''); ?>

                                   class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-red focus:ring-neon-red">
                            <span class="text-gray-300">Producto exclusivo 🔥</span>
                        </label>
                    </div>

                    <?php if($product->original_price): ?>
                        <div class="bg-gray-800/50 p-4 rounded-lg">
                            <label class="block text-gray-300 mb-2">Precio original (para ofertas)</label>
                            <input type="number" step="0.01" name="original_price" value="<?php echo e(old('original_price', $product->original_price)); ?>"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>
                    <?php endif; ?>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-6 py-4 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                            Actualizar Producto
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/products/edit.blade.php ENDPATH**/ ?>