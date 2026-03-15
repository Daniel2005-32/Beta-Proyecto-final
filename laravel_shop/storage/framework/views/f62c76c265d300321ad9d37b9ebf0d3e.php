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
            <div class="mb-4">
                <a href="<?php echo e(url()->previous()); ?>" class="text-gray-400 hover:text-neon-blue transition">
                    ← Volver
                </a>
            </div>
            
            <div class="bg-gamer-card rounded-2xl overflow-hidden border border-neon-blue/20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                    <!-- Imagen del producto -->
                    <div>
                        <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full rounded-lg">
                    </div>

                    <!-- Detalles del producto -->
                    <div>
                        <span class="text-neon-blue text-sm uppercase"><?php echo e($product->category->name); ?></span>
                        <h1 class="text-4xl font-black text-white mt-2"><?php echo e($product->name); ?></h1>
                        
                        <?php if($product->is_exclusive): ?>
                            <span class="inline-block mt-2 px-3 py-1 bg-neon-red/20 text-neon-red rounded-full text-xs font-bold">
                                🔥 EXCLUSIVO
                            </span>
                        <?php endif; ?>
                        
                        <p class="text-gray-400 mt-4"><?php echo e($product->description); ?></p>
                        
                        <!-- Precio SIN IVA -->
                        <div class="mt-6">
                            <?php if($product->isAuctionActive()): ?>
                                <span class="text-3xl font-black text-neon-purple"><?php echo e(number_format($product->price, 2)); ?>€</span>
                                <span class="ml-2 text-gray-500 line-through text-lg"><?php echo e(number_format($product->auction_start_price ?? $product->price, 2)); ?>€</span>
                                <p class="text-sm text-gray-400 mt-1">Subasta activa - <?php echo e($product->auctionTimeLeft()); ?> restantes</p>
                            <?php elseif($product->original_price && $product->original_price > $product->price): ?>
                                <span class="text-3xl font-black text-neon-red"><?php echo e(number_format($product->price, 2)); ?>€</span>
                                <span class="ml-2 text-gray-500 line-through text-lg"><?php echo e(number_format($product->original_price, 2)); ?>€</span>
                            <?php else: ?>
                                <span class="text-3xl font-black text-neon-blue"><?php echo e(number_format($product->price, 2)); ?>€</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Stock -->
                        <div class="mt-4">
                            <?php if($product->stock > 0): ?>
                                <span class="text-green-500">✅ Stock disponible: <?php echo e($product->stock); ?> unidades</span>
                                <?php if($product->is_exclusive && $product->stock == 1): ?>
                                    <p class="text-neon-red text-sm mt-1">⚠️ Última unidad - Inicia una subasta para obtener 20% de descuento</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-red-500">❌ Agotado</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Botones de acción -->
                        <div class="mt-8 flex gap-4">
                            <?php if($product->isAuctionActive()): ?>
                                <a href="<?php echo e(route('auctions.show', $product->id)); ?>" 
                                   class="px-8 py-4 bg-neon-purple text-white font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(157,0,255,0.4)]">
                                    Ver Subasta
                                </a>
                            <?php elseif($product->is_exclusive && $product->stock == 1): ?>
                                <a href="<?php echo e(route('auctions.confirm', $product->id)); ?>" 
                                   class="px-8 py-4 bg-neon-red text-white font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(255,0,85,0.4)]">
                                    Iniciar Subasta
                                </a>
                            <?php elseif($product->stock > 0): ?>
                                <form action="<?php echo e(route('cart.add', $product->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                        Añadir al carrito
                                    </button>
                                </form>
                            <?php endif; ?>
                            
                            <a href="<?php echo e(route('products.index')); ?>" 
                               class="px-8 py-4 border-2 border-gray-700 text-gray-300 font-black rounded-full hover:bg-gray-800 hover:text-white transition">
                                Seguir comprando
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- SECCIÓN DE VALORACIONES - AQUÍ VA TODO -->
            <!-- ============================================ -->
            <div class="mt-12">
                <?php echo $__env->make('products.partials.reviews', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
            <!-- ============================================ -->

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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/products/show.blade.php ENDPATH**/ ?>