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
                <a href="<?php echo e(route('admin.raffles.index')); ?>" class="text-gray-400 hover:text-neon-purple transition">
                    ← Volver a la lista
                </a>
            </div>

            <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Crear Nuevo Sorteo</h1>

                <form action="<?php echo e(route('admin.raffles.store')); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2 font-bold">Título del sorteo</label>
                            <input type="text" name="title" value="<?php echo e(old('title')); ?>" required
                                   placeholder="Ej: Sorteo Mensual de Marzo"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2 font-bold">Descripción</label>
                            <textarea name="description" rows="3"
                                      placeholder="Describe el sorteo..."
                                      class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"><?php echo e(old('description')); ?></textarea>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Producto a sortear</label>
                            <select name="product_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                                <option value="">Selecciona un producto</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->id); ?>" <?php echo e(old('product_id') == $product->id ? 'selected' : ''); ?>>
                                        <?php echo e($product->name); ?> (<?php echo e($product->price); ?>€)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Precio por entrada (€)</label>
                            <input type="number" step="0.01" name="ticket_price" value="<?php echo e(old('ticket_price', 20)); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                            <p class="text-xs text-gray-500 mt-1">Cada compra de este importe da 1 entrada</p>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Fecha de inicio</label>
                            <input type="datetime-local" name="start_date" value="<?php echo e(old('start_date')); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Fecha de fin</label>
                            <input type="datetime-local" name="end_date" value="<?php echo e(old('end_date')); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Límite de entradas</label>
                            <input type="number" name="max_entries" value="<?php echo e(old('max_entries')); ?>"
                                   placeholder="Sin límite"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                            <p class="text-xs text-gray-500 mt-1">Déjalo vacío para entradas ilimitadas</p>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-6 py-4 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                            Crear Sorteo
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-6 bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                <h2 class="text-xl font-bold text-white mb-4">📋 Información importante</h2>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li class="flex items-start gap-2">
                        <span class="text-neon-purple">•</span>
                        Los usuarios obtendrán 1 entrada por cada <?php echo e(old('ticket_price', 20)); ?>€ gastados.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-neon-purple">•</span>
                        El sorteo se realizará automáticamente en la fecha de fin.
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-neon-purple">•</span>
                        Puedes activar o sortear manualmente desde el panel.
                    </li>
                </ul>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/admin/raffles/create.blade.php ENDPATH**/ ?>