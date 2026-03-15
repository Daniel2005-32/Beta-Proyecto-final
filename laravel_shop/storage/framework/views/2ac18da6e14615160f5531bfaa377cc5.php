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
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-4">
                    <span class="text-neon-purple">🎲 Sorteos Mensuales</span>
                </h1>
                <p class="text-gray-400">¡Participa en nuestros sorteos! Cada compra de 20€ te da una entrada.</p>
            </div>

            <?php
                $activeRaffles = App\Models\Raffle::where('status', 'pending')
                    ->where('draw_date', '>', now())
                    ->get();
                $endedRaffles = App\Models\Raffle::where('status', 'completed')
                    ->orderBy('draw_date', 'desc')
                    ->take(5)
                    ->get();
            ?>

            <?php if($activeRaffles->count() > 0): ?>
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-white mb-6">🎯 Sorteos Activos</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php $__currentLoopData = $activeRaffles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $raffle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $extra = $raffle->getExtraData();
                                $product = $raffle->getProduct();
                                $userEntries = Auth::check() ? $raffle->getUserEntries(Auth::id()) : 0;
                                $totalEntries = $raffle->entries()->sum('quantity');
                            ?>
                            <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 overflow-hidden hover:border-neon-purple transition">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <h3 class="text-xl font-bold text-white"><?php echo e($raffle->title); ?></h3>
                                        <span class="bg-neon-purple/20 text-neon-purple px-3 py-1 rounded-full text-xs font-bold">ACTIVO</span>
                                    </div>
                                    
                                    <?php if($product): ?>
                                        <div class="mb-4">
                                            <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-40 object-cover rounded-lg">
                                        </div>
                                        <p class="text-gray-400 text-sm mb-2"><?php echo e($raffle->getCleanDescription()); ?></p>
                                        <p class="text-neon-blue font-bold text-lg mb-2"><?php echo e($product->name); ?></p>
                                    <?php endif; ?>
                                    
                                    <div class="space-y-2 mb-4 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Precio del producto:</span>
                                            <span class="text-white"><?php echo e(number_format($product->price ?? 0, 2)); ?>€</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Cada entrada:</span>
                                            <span class="text-neon-purple"><?php echo e($extra['ticket_price'] ?? 20); ?>€ en compras</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Total entradas:</span>
                                            <span class="text-white"><?php echo e($totalEntries); ?></span>
                                        </div>
                                        <?php if(auth()->guard()->check()): ?>
                                            <div class="flex justify-between">
                                                <span class="text-gray-400">Tus entradas:</span>
                                                <span class="text-neon-blue font-bold"><?php echo e($userEntries); ?></span>
                                            </div>
                                            <?php if($totalEntries > 0): ?>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-400">Tu probabilidad:</span>
                                                    <span class="text-neon-purple"><?php echo e($raffle->getUserChance(Auth::id())); ?>%</span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        <div class="flex justify-between pt-2 border-t border-gray-800">
                                            <span class="text-gray-400">Fecha del sorteo:</span>
                                            <span class="text-neon-purple"><?php echo e(Carbon\Carbon::parse($extra['end_date'] ?? $raffle->draw_date)->format('d/m/Y H:i')); ?></span>
                                        </div>
                                    </div>
                                    
                                    <?php if(auth()->guard()->check()): ?>
                                        <div class="mt-4 text-center text-sm text-gray-400">
                                            <?php if($userEntries > 0): ?>
                                                <p class="text-green-500">¡Tienes <?php echo e($userEntries); ?> entrada(s)!</p>
                                            <?php else: ?>
                                                <p>Compra productos para obtener entradas</p>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="mt-4 text-center">
                                            <a href="<?php echo e(route('login')); ?>" class="text-neon-purple hover:underline text-sm">
                                                Inicia sesión para participar
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-gamer-card rounded-2xl border border-gray-800 mb-12">
                    <p class="text-gray-400">No hay sorteos activos en este momento</p>
                </div>
            <?php endif; ?>

            <?php if($endedRaffles->count() > 0): ?>
                <div>
                    <h2 class="text-2xl font-bold text-white mb-6">🏁 Últimos Sorteos Finalizados</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 opacity-60">
                        <?php $__currentLoopData = $endedRaffles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $raffle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $product = $raffle->getProduct();
                            ?>
                            <div class="bg-gamer-card rounded-2xl border border-gray-800 overflow-hidden">
                                <div class="p-6">
                                    <h3 class="text-lg font-bold text-white mb-2"><?php echo e($raffle->title); ?></h3>
                                    <?php if($raffle->winner): ?>
                                        <p class="text-green-500 text-sm">Ganador: <?php echo e($raffle->winner->name); ?></p>
                                    <?php else: ?>
                                        <p class="text-gray-500 text-sm">Sorteo sin ganador</p>
                                    <?php endif; ?>
                                    <?php if($product): ?>
                                        <p class="text-gray-400 text-xs mt-2">Premio: <?php echo e($product->name); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="mt-12 bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                <h2 class="text-xl font-bold text-white mb-4">📊 Cómo funcionan los sorteos</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="text-3xl mb-2">🛒</div>
                        <h3 class="text-neon-blue font-bold mb-2">Compra</h3>
                        <p class="text-sm text-gray-400">Cada compra de 20€ te da 1 entrada</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl mb-2">🎟️</div>
                        <h3 class="text-neon-purple font-bold mb-2">Acumula</h3>
                        <p class="text-sm text-gray-400">Cuantas más entradas, más probabilidades</p>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl mb-2">🏆</div>
                        <h3 class="text-neon-red font-bold mb-2">Gana</h3>
                        <p class="text-sm text-gray-400">Al finalizar, un ganador se lleva el premio</p>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/raffles/index.blade.php ENDPATH**/ ?>