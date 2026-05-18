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
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-black text-white mb-2">
                        <span class="text-neon-blue">🚫 Gestión de Palabras Censuradas</span>
                    </h1>
                    <p class="text-gray-400">Administra las palabras prohibidas en el chat</p>
                </div>
                <a href="<?php echo e(route('admin.censored-words.create')); ?>" 
                   class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                    + Añadir Palabra
                </a>
            </div>

            <?php if(session('success')): ?>
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-800 border-b border-neon-blue/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-neon-blue">ID</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Palabra</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Severidad</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Estado</th>
                            <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $words; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $word): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                <td class="px-6 py-4 text-gray-300"><?php echo e($word->id); ?></td>
                                <td class="px-6 py-4 text-white font-bold"><?php echo e($word->word); ?></td>
                                <td class="px-6 py-4">
                                    <?php if($word->severity == 'high'): ?>
                                        <span class="px-3 py-1 bg-neon-red/20 text-neon-red rounded-full text-xs">Alta</span>
                                    <?php elseif($word->severity == 'medium'): ?>
                                        <span class="px-3 py-1 bg-yellow-600/20 text-yellow-500 rounded-full text-xs">Media</span>
                                    <?php else: ?>
                                        <span class="px-3 py-1 bg-neon-blue/20 text-neon-blue rounded-full text-xs">Baja</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php if($word->active): ?>
                                        <span class="text-green-500">Activo</span>
                                    <?php else: ?>
                                        <span class="text-gray-500">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-2">
                                        <a href="<?php echo e(route('admin.censored-words.edit', $word)); ?>" 
                                           class="px-3 py-1 bg-neon-blue/10 text-neon-blue rounded-lg hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                            Editar
                                        </a>
                                        <form action="<?php echo e(route('admin.censored-words.toggle', $word)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" 
                                                    class="px-3 py-1 <?php echo e($word->active ? 'bg-yellow-600/10 text-yellow-500 hover:bg-yellow-600 hover:text-white' : 'bg-green-600/10 text-green-500 hover:bg-green-600 hover:text-white'); ?> rounded-lg transition text-sm">
                                                <?php echo e($word->active ? 'Desactivar' : 'Activar'); ?>

                                            </button>
                                        </form>
                                        <form action="<?php echo e(route('admin.censored-words.destroy', $word)); ?>" 
                                              method="POST" 
                                              onsubmit="return confirm('¿Eliminar esta palabra?')"
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
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <?php echo e($words->links()); ?>

            </div>

            <!-- Comandos útiles -->
            <div class="mt-8 bg-gamer-card rounded-2xl border border-neon-purple/20 p-6">
                <h2 class="text-xl font-bold text-white mb-4">🛠️ Comandos útiles</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-800/50 p-4 rounded-lg">
                        <code class="text-neon-blue">php artisan badwords:find --stats</code>
                        <p class="text-sm text-gray-400 mt-2">Ver estadísticas de censura</p>
                    </div>
                    <div class="bg-gray-800/50 p-4 rounded-lg">
                        <code class="text-neon-blue">php artisan badwords:find --check</code>
                        <p class="text-sm text-gray-400 mt-2">Analizar mensajes existentes</p>
                    </div>
                    <div class="bg-gray-800/50 p-4 rounded-lg">
                        <code class="text-neon-blue">php artisan badwords:find --add="palabra"</code>
                        <p class="text-sm text-gray-400 mt-2">Añadir palabra desde terminal</p>
                    </div>
                </div>
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
<?php /**PATH /app/resources/views/admin/censored-words/index.blade.php ENDPATH**/ ?>