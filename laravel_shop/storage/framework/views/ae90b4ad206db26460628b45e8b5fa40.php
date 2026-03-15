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
            <!-- Cabecera -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">
                    <span class="text-neon-red">🔥 Ofertas Especiales</span>
                </h1>
                <p class="text-gray-400">Los mejores productos con descuentos increíbles</p>
            </div>

            <!-- Grid de productos en oferta -->
            <?php if(isset($offers) && $offers->count() > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <?php $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="group bg-gamer-card rounded-2xl overflow-hidden border border-neon-red/30 hover:border-neon-red transition duration-300 shadow-[0_0_20px_rgba(255,0,85,0.1)]">
                            <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="block">
                                <div class="relative overflow-hidden aspect-square">
                                    <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                                    
                                    <!-- Badge de descuento -->
                                    <?php if($product->original_price && $product->original_price > $product->price): ?>
                                        <?php
                                            $discount = round((($product->original_price - $product->price) / $product->original_price) * 100);
                                        ?>
                                        <div class="absolute top-4 right-4 bg-neon-red text-white text-xs font-black px-3 py-1.5 rounded-full shadow-[0_0_15px_rgba(255,0,85,0.4)]">
                                            -<?php echo e($discount); ?>%
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                            
                            <div class="p-6">
                                <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="hover:text-neon-red transition">
                                    <h3 class="font-bold text-lg text-white mb-2 truncate"><?php echo e($product->name); ?></h3>
                                </a>
                                
                                <!-- Precios SIN IVA -->
                                <div class="flex items-center justify-between mt-4">
                                    <div>
                                        <?php if($product->original_price && $product->original_price > $product->price): ?>
                                            <span class="text-2xl font-black text-neon-red italic"><?php echo e(number_format($product->price, 2)); ?>€</span>
                                            <span class="text-sm text-gray-500 line-through ml-2"><?php echo e(number_format($product->original_price, 2)); ?>€</span>
                                        <?php else: ?>
                                            <span class="text-2xl font-black text-white italic"><?php echo e(number_format($product->price, 2)); ?>€</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="px-4 py-2 bg-neon-red text-white text-xs font-black uppercase tracking-widest rounded hover:scale-105 transition shadow-[0_0_15px_rgba(255,0,85,0.4)]">
                                        Ver oferta
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Botón para ver todos los productos -->
                <div class="text-center mt-12">
                    <a href="<?php echo e(route('products.index')); ?>" class="inline-block px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                        Ver catálogo completo
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-gamer-card rounded-2xl border border-neon-red/20 p-12 text-center">
                    <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 12H4M12 4v16"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-white mb-2">No hay ofertas disponibles</h2>
                    <p class="text-gray-400 mb-6">Pronto tendremos nuevas ofertas para ti</p>
                    <a href="<?php echo e(route('products.index')); ?>" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                        Ver todos los productos
                    </a>
                </div>
            <?php endif; ?>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/offers.blade.php ENDPATH**/ ?>