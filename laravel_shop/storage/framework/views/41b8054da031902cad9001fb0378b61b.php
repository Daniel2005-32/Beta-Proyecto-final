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
            <div class="mb-6">
                <a href="<?php echo e(route('auctions.index')); ?>" class="text-gray-400 hover:text-neon-purple transition inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver a subastas
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 overflow-hidden">
                        <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-96 object-cover">
                        
                        <div class="p-8">
                            <h1 class="text-3xl font-black text-white mb-2"><?php echo e($product->name); ?></h1>
                            <p class="text-gray-400 mb-6"><?php echo e($product->description); ?></p>
                            
                            <?php if($product->isAuctionActive()): ?>
                                <div class="bg-gray-800/50 rounded-lg p-4 mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-400">Tiempo restante:</span>
                                        <span class="text-2xl font-bold text-neon-purple"><?php echo e($product->auctionTimeLeft()); ?></span>
                                    </div>
                                    <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden">
                                        <div class="h-full bg-neon-purple transition-all duration-1000" 
                                             style="width: <?php echo e($product->getAuctionPercentage()); ?>%"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div class="mt-8">
                                <h2 class="text-xl font-bold text-white mb-4">Puja actual</h2>
                                <div class="bg-gray-800/50 rounded-lg p-6 text-center">
                                    <p class="text-4xl font-black text-neon-purple"><?php echo e(number_format($product->price, 2)); ?>€</p>
                                    <p class="text-gray-400 mt-2">Mejor postor: <?php echo e($product->auctionWinner->name ?? 'Nadie aún'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="lg:col-span-1">
                    <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 p-6 sticky top-24">
                        <h2 class="text-2xl font-bold text-white mb-6">💰 Pujar ahora</h2>
                        
                        <?php if($product->isAuctionActive()): ?>
                            <?php if(auth()->guard()->check()): ?>
                                <form action="<?php echo e(route('auctions.bid', $product->id)); ?>" method="POST" class="space-y-4">
                                    <?php echo csrf_field(); ?>
                                    
                                    <div>
                                        <label class="block text-gray-300 mb-2">Tu puja (€)</label>
                                        <input type="number" 
                                               name="amount" 
                                               step="0.01" 
                                               min="<?php echo e($product->price + 0.01); ?>" 
                                               value="<?php echo e($product->price + 1); ?>"
                                               class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-purple"
                                               required>
                                        <p class="text-sm text-gray-400 mt-2">
                                            Puja actual: <?php echo e(number_format($product->price, 2)); ?>€
                                        </p>
                                    </div>
                                    
                                    <button type="submit" 
                                            class="w-full px-6 py-4 bg-neon-purple text-white font-bold rounded-lg hover:bg-neon-purple/80 transition transform hover:scale-105">
                                        ✅ Confirmar puja
                                    </button>
                                </form>
                            <?php else: ?>
                                <div class="text-center py-8 bg-gray-800/30 rounded-lg">
                                    <p class="text-gray-400 mb-4">Debes iniciar sesión para pujar</p>
                                    <a href="<?php echo e(route('login')); ?>" class="block w-full px-4 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                        Iniciar sesión
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php elseif($product->isAuctionEnded()): ?>
                            <div class="text-center py-8">
                                <?php if($product->auction_winner_id == Auth::id()): ?>
                                    <div class="bg-green-900/30 border border-green-500 rounded-lg p-6">
                                        <p class="text-green-400 text-lg font-bold mb-2">🎉 ¡FELICIDADES!</p>
                                        <p class="text-white mb-4">Has ganado esta subasta</p>
                                    </div>
                                <?php elseif($product->auction_winner_id): ?>
                                    <div class="bg-gray-800/50 rounded-lg p-6">
                                        <p class="text-gray-400">Subasta finalizada</p>
                                        <p class="text-neon-purple font-bold mt-2">Ganador: <?php echo e($product->auctionWinner->name); ?></p>
                                    </div>
                                <?php else: ?>
                                    <div class="bg-gray-800/50 rounded-lg p-6">
                                        <p class="text-gray-400">Subasta finalizada sin pujas</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- PANEL DE ADMINISTRACIÓN PARA SUBASTAS -->
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(Auth::user()->is_admin && $product->isAuctionActive()): ?>
                                <div class="mt-6 pt-6 border-t border-gray-800">
                                    <h3 class="text-lg font-bold text-white mb-4">⚙️ Panel de Admin - Subastas</h3>
                                    
                                    <div class="space-y-3">
                                        <!-- Extender tiempo -->
                                        <form action="<?php echo e(route('auctions.extend', $product->id)); ?>" method="POST" class="flex space-x-2">
                                            <?php echo csrf_field(); ?>
                                            <input type="number" 
                                                   name="hours" 
                                                   min="1" 
                                                   max="72" 
                                                   value="1" 
                                                   class="w-20 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-white text-center">
                                            <button type="submit" 
                                                    class="flex-1 px-3 py-1 bg-neon-blue/10 text-neon-blue rounded hover:bg-neon-blue hover:text-gamer-dark transition text-sm">
                                                Extender
                                            </button>
                                        </form>
                                        
                                        <!-- Reducir tiempo -->
                                        <form action="<?php echo e(route('auctions.reduce', $product->id)); ?>" method="POST" class="flex space-x-2">
                                            <?php echo csrf_field(); ?>
                                            <input type="number" 
                                                   name="hours" 
                                                   min="1" 
                                                   max="24" 
                                                   value="1" 
                                                   class="w-20 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-white text-center">
                                            <button type="submit" 
                                                    class="flex-1 px-3 py-1 bg-neon-purple/10 text-neon-purple rounded hover:bg-neon-purple hover:text-white transition text-sm">
                                                Reducir
                                            </button>
                                        </form>
                                        
                                        <!-- Reiniciar a 24h -->
                                        <form action="<?php echo e(route('auctions.reset', $product->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" 
                                                    class="w-full px-3 py-1 bg-yellow-600/10 text-yellow-500 rounded hover:bg-yellow-600 hover:text-white transition text-sm">
                                                Reiniciar a 24h
                                            </button>
                                        </form>
                                        
                                        <!-- Finalizar ahora -->
                                        <form action="<?php echo e(route('auctions.force-end', $product->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" 
                                                    class="w-full px-3 py-1 bg-neon-red/10 text-neon-red rounded hover:bg-neon-red hover:text-white transition text-sm"
                                                    onclick="return confirm('¿Estás seguro de finalizar esta subasta ahora?')">
                                                Finalizar ahora
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/auctions/show.blade.php ENDPATH**/ ?>