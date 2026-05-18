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
                <h1 class="text-3xl font-black text-white mb-6">Editar Sorteo</h1>

                <form action="<?php echo e(route('admin.raffles.update', $raffle)); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2 font-bold">Título del sorteo</label>
                            <input type="text" name="title" value="<?php echo e(old('title', $raffle->title)); ?>" required
                                   placeholder="Ej: Sorteo Mensual de Marzo"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2 font-bold">Descripción</label>
                            <textarea name="description" rows="3"
                                      placeholder="Describe el sorteo..."
                                      class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"><?php echo e(old('description', $raffle->getCleanDescription())); ?></textarea>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Producto a sortear</label>
                            <select name="product_id" required class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                                <option value="">Selecciona un producto</option>
                                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($product->id); ?>" <?php echo e((old('product_id', $extraData['product_id'] ?? '') == $product->id) ? 'selected' : ''); ?>>
                                        <?php echo e($product->name); ?> (<?php echo e(number_format($product->price, 2)); ?>€)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Precio por entrada (€)</label>
                            <input type="number" step="0.01" name="ticket_price" 
                                   value="<?php echo e(old('ticket_price', $extraData['ticket_price'] ?? 20)); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                            <p class="text-xs text-gray-500 mt-1">Cada compra de este importe da 1 entrada</p>
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Fecha de inicio</label>
                            <input type="datetime-local" name="start_date" 
                                   value="<?php echo e(old('start_date', isset($extraData['start_date']) ? \Carbon\Carbon::parse($extraData['start_date'])->format('Y-m-d\TH:i') : '')); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Fecha de fin</label>
                            <input type="datetime-local" name="end_date" 
                                   value="<?php echo e(old('end_date', isset($extraData['end_date']) ? \Carbon\Carbon::parse($extraData['end_date'])->format('Y-m-d\TH:i') : '')); ?>" required
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                        </div>

                        <div>
                            <label class="block text-gray-300 mb-2 font-bold">Límite de entradas</label>
                            <input type="number" name="max_entries" 
                                   value="<?php echo e(old('max_entries', $extraData['max_entries'] ?? '')); ?>"
                                   placeholder="Sin límite"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple">
                            <p class="text-xs text-gray-500 mt-1">Déjalo vacío para entradas ilimitadas</p>
                        </div>
                    </div>

                    <div class="pt-4 flex gap-4">
                        <button type="submit" class="flex-1 px-6 py-4 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                            Actualizar Sorteo
                        </button>
                        <a href="<?php echo e(route('admin.raffles.index')); ?>" 
                           class="px-6 py-4 bg-gray-800 text-gray-300 font-bold rounded-lg hover:bg-gray-700 transition">
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
<?php /**PATH /app/resources/views/admin/raffles/edit.blade.php ENDPATH**/ ?>