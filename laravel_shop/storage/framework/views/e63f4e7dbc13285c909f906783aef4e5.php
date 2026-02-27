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
            <div class="mb-6">
                <a href="<?php echo e(route('admin.products.index')); ?>" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver al panel
                </a>
            </div>

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
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>" <?php echo e($product->category_id == $category->id ? 'selected' : ''); ?>>
                                        <?php echo e($category->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Stock</label>
                            <input type="number" name="stock" value="<?php echo e(old('stock', $product->stock)); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div class="md:col-span-2">
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

                    <!-- SECCIÓN DE PRECIOS Y OFERTAS -->
                    <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700">
                        <h2 class="text-xl font-bold text-white mb-4">💰 Precio y Ofertas</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-300 mb-2">Precio (€)</label>
                                <input type="number" step="0.01" name="price" value="<?php echo e(old('price', $product->price)); ?>" required
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                            </div>

                            <div class="flex items-center space-x-3 pt-7">
                                <?php
                                    $onSale = $product->original_price && $product->original_price > $product->price;
                                ?>
                                <input type="checkbox" name="on_sale" id="on_sale" value="1" 
                                       <?php echo e($onSale ? 'checked' : ''); ?>

                                       class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-blue focus:ring-neon-blue">
                                <label for="on_sale" class="text-gray-300 font-bold">Activar oferta (descuento)</label>
                            </div>
                        </div>

                        <div id="original_price_container" class="mt-4 <?php echo e($onSale ? '' : 'hidden'); ?>">
                            <label class="block text-gray-300 mb-2">Precio original (antes del descuento)</label>
                            <input type="number" step="0.01" name="original_price" value="<?php echo e(old('original_price', $product->original_price)); ?>"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                            <p class="text-sm text-gray-500 mt-1">El precio de oferta es el que pusiste arriba</p>
                        </div>
                    </div>

                    <!-- CARACTERÍSTICAS -->
                    <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700">
                        <h2 class="text-xl font-bold text-white mb-4">⭐ Características</h2>
                        <div class="grid grid-cols-3 gap-4">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="featured" value="1" <?php echo e($product->featured ? 'checked' : ''); ?>

                                       class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-blue focus:ring-neon-blue">
                                <span class="text-gray-300">Destacado</span>
                            </label>

                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="trending" value="1" <?php echo e($product->trending ? 'checked' : ''); ?>

                                       class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                                <span class="text-gray-300">Tendencia</span>
                            </label>

                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="is_exclusive" value="1" <?php echo e($product->is_exclusive ? 'checked' : ''); ?>

                                       class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-red focus:ring-neon-red">
                                <span class="text-gray-300">Exclusivo 🔥</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full px-6 py-4 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                        Actualizar Producto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('on_sale').addEventListener('change', function() {
            const container = document.getElementById('original_price_container');
            if (this.checked) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        });
    </script>
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