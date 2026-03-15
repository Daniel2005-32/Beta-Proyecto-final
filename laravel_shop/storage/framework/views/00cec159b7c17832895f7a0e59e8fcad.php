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
                <h1 class="text-3xl font-black text-white mb-6">Crear Nuevo Producto</h1>

                <form action="<?php echo e(route('admin.products.store')); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <!-- Información básica -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-gray-300 mb-2">Nombre</label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Slug</label>
                            <input type="text" name="slug" value="<?php echo e(old('slug')); ?>" required
                                   placeholder="ej: playstation-5"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Categoría</label>
                            <select name="category_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                                <option value="">Selecciona una categoría</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2">Stock</label>
                            <input type="number" name="stock" value="<?php echo e(old('stock', 1)); ?>" required
                                   min="0"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2">URL de la imagen</label>
                            <input type="url" name="image" value="<?php echo e(old('image')); ?>" required
                                   placeholder="https://ejemplo.com/imagen.jpg"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div>
                        <label class="block text-gray-300 mb-2">Descripción</label>
                        <textarea name="description" rows="4" required
                                  class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue"><?php echo e(old('description')); ?></textarea>
                    </div>

                    <!-- SECCIÓN DE PRECIOS Y OFERTAS -->
                    <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700">
                        <h2 class="text-xl font-bold text-white mb-4">💰 Precio y Ofertas</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-gray-300 mb-2">Precio (€)</label>
                                <input type="number" step="0.01" name="price" id="price" value="<?php echo e(old('price')); ?>" required
                                       class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                            </div>

                            <div class="flex items-center space-x-3 pt-7">
                                <input type="checkbox" name="on_sale" id="on_sale" value="1" 
                                       <?php echo e(old('on_sale') ? 'checked' : ''); ?>

                                       class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-blue focus:ring-neon-blue">
                                <label for="on_sale" class="text-gray-300 font-bold">Activar oferta (descuento)</label>
                            </div>
                        </div>

                        <!-- Campo de precio original (se muestra solo si oferta activada) -->
                        <div id="original_price_container" class="mt-4 <?php echo e(old('on_sale') ? '' : 'hidden'); ?>">
                            <label class="block text-gray-300 mb-2">Precio original (antes del descuento)</label>
                            <input type="number" step="0.01" name="original_price" id="original_price" value="<?php echo e(old('original_price')); ?>"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                            <p class="text-sm text-gray-500 mt-1">El precio de oferta será el que pusiste arriba</p>
                            
                            <!-- Vista previa del descuento -->
                            <div id="discount_preview" class="mt-2 text-sm text-gray-400 hidden">
                                Descuento: <span id="discount_percentage" class="text-neon-blue font-bold"></span>
                            </div>
                        </div>
                    </div>

                    <!-- CARACTERÍSTICAS -->
                    <div class="bg-gray-800/30 p-4 rounded-lg border border-gray-700">
                        <h2 class="text-xl font-bold text-white mb-4">⭐ Características</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="featured" value="1" <?php echo e(old('featured') ? 'checked' : ''); ?>

                                       class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-blue focus:ring-neon-blue">
                                <span class="text-gray-300">Destacado</span>
                            </label>

                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="trending" value="1" <?php echo e(old('trending') ? 'checked' : ''); ?>

                                       class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                                <span class="text-gray-300">Tendencia</span>
                            </label>

                            <label class="flex items-center space-x-3 cursor-pointer">
                                <input type="checkbox" name="is_exclusive" value="1" <?php echo e(old('is_exclusive') ? 'checked' : ''); ?>

                                       class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-red focus:ring-neon-red">
                                <span class="text-gray-300">Exclusivo 🔥</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="w-full px-6 py-4 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                        Crear Producto
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Mostrar/ocultar campo de precio original cuando se activa oferta
        document.getElementById('on_sale').addEventListener('change', function() {
            const container = document.getElementById('original_price_container');
            if (this.checked) {
                container.classList.remove('hidden');
                calcularDescuento();
            } else {
                container.classList.add('hidden');
                document.getElementById('discount_preview').classList.add('hidden');
            }
        });

        // Calcular y mostrar el porcentaje de descuento en tiempo real
        function calcularDescuento() {
            const price = parseFloat(document.getElementById('price').value) || 0;
            const originalPrice = parseFloat(document.getElementById('original_price').value) || 0;
            const preview = document.getElementById('discount_preview');
            const percentageSpan = document.getElementById('discount_percentage');
            
            if (originalPrice > price && price > 0) {
                const discount = ((originalPrice - price) / originalPrice * 100).toFixed(1);
                percentageSpan.textContent = discount + '%';
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        document.getElementById('price').addEventListener('input', calcularDescuento);
        document.getElementById('original_price').addEventListener('input', calcularDescuento);
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/products/create.blade.php ENDPATH**/ ?>