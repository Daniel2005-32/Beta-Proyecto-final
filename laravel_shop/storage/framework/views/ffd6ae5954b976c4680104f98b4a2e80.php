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
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="<?php echo e(route('addresses.index')); ?>" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver a mis direcciones
                </a>
            </div>

            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-8">
                <h1 class="text-3xl font-black text-white mb-6">Nueva Dirección</h1>

                <form action="<?php echo e(route('addresses.store')); ?>" method="POST" class="space-y-6">
                    <?php echo csrf_field(); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre completo -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2">Nombre completo</label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>" required
                                   placeholder="Ej: María García López"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label class="block text-gray-300 mb-2">Teléfono</label>
                            <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" required
                                   placeholder="Ej: 612345678"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <!-- Código Postal -->
                        <div>
                            <label class="block text-gray-300 mb-2">Código Postal</label>
                            <input type="text" name="zipcode" value="<?php echo e(old('zipcode')); ?>" required
                                   placeholder="Ej: 28001"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <!-- Calle -->
                        <div class="md:col-span-2">
                            <label class="block text-gray-300 mb-2">Calle</label>
                            <input type="text" name="street" value="<?php echo e(old('street')); ?>" required
                                   placeholder="Ej: Calle Mayor"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <!-- Número -->
                        <div>
                            <label class="block text-gray-300 mb-2">Número</label>
                            <input type="text" name="number" value="<?php echo e(old('number')); ?>" required
                                   placeholder="Ej: 23"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <!-- Puerta / Piso / Escalera -->
                        <div>
                            <label class="block text-gray-300 mb-2">Piso / Puerta / Escalera</label>
                            <input type="text" name="complement" value="<?php echo e(old('complement')); ?>"
                                   placeholder="Ej: 3º 2ª, Escalera A"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <!-- Provincia (con siglas) -->
                        <div>
                            <label class="block text-gray-300 mb-2">Provincia (siglas)</label>
                            <input type="text" name="state" value="<?php echo e(old('state')); ?>" required
                                   placeholder="Ej: GC (Las Palmas), M (Madrid), B (Barcelona)"
                                   maxlength="2"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                            <p class="text-xs text-gray-500 mt-1">Usa las siglas de 2 letras (GC, M, B, etc.)</p>
                        </div>

                        <!-- Ciudad / Localidad -->
                        <div>
                            <label class="block text-gray-300 mb-2">Ciudad / Localidad</label>
                            <input type="text" name="city" value="<?php echo e(old('city')); ?>" required
                                   placeholder="Ej: Las Palmas"
                                   class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue">
                        </div>

                        <!-- Campo oculto para neighborhood -->
                        <input type="hidden" name="neighborhood" value="">

                        <!-- Marcar como predeterminada -->
                        <div class="md:col-span-2 flex items-center space-x-3">
                            <input type="checkbox" name="is_default" id="is_default" value="1"
                                   class="w-5 h-5 rounded bg-gray-800 border-gray-700 text-neon-purple focus:ring-neon-purple">
                            <label for="is_default" class="text-gray-300">Establecer como dirección predeterminada</label>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full px-6 py-4 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                            Guardar dirección
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/addresses/create.blade.php ENDPATH**/ ?>