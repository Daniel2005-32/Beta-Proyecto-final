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
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-2">
                    <span class="text-neon-purple">👤 Mi Perfil</span>
                </h1>
                <p class="text-gray-400">Bienvenido de nuevo, <?php echo e(Auth::user()->name); ?></p>
            </div>

            <?php if(session('success')): ?>
                <div class="bg-green-900/50 border border-green-500 text-green-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="bg-red-900/50 border border-neon-red text-red-200 px-4 py-3 rounded-lg mb-6">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <!-- Información personal - Fondo azul oscuro -->
            <div class="bg-gradient-to-br from-blue-900/30 to-blue-950/50 rounded-2xl border border-neon-blue/30 p-8 mb-8 backdrop-blur-sm">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-neon-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Información personal
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Nombre</label>
                        <p class="text-white text-lg font-bold"><?php echo e(Auth::user()->name); ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Email</label>
                        <p class="text-white text-lg"><?php echo e(Auth::user()->email); ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Miembro desde</label>
                        <p class="text-white"><?php echo e(Auth::user()->created_at->format('d/m/Y')); ?></p>
                    </div>
                    
                    <div>
                        <label class="block text-gray-400 text-sm mb-1">Rol</label>
                        <?php if(Auth::user()->isSuperAdmin()): ?>
                            <p class="text-neon-red font-bold">Super Administrador</p>
                        <?php elseif(Auth::user()->is_admin): ?>
                            <p class="text-neon-blue font-bold">Administrador</p>
                        <?php else: ?>
                            <p class="text-gray-300">Usuario</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="<?php echo e(route('profile.edit')); ?>" class="px-6 py-2 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition inline-flex items-center gap-2 shadow-[0_0_15px_rgba(0,210,255,0.3)]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                        Editar perfil
                    </a>
                </div>
            </div>
            

            <!-- Subastas ganadas - Fondo morado oscuro -->
            <div class="bg-gradient-to-br from-purple-900/30 to-purple-950/50 rounded-2xl border border-neon-purple/30 p-8 mb-8 backdrop-blur-sm">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Subastas ganadas
                </h2>
                
                <?php
                    $wonAuctions = Auth::user()->wonAuctions()
                        ->where('auction_claimed', false)
                        ->orderBy('auction_end_time', 'desc')
                        ->get();
                ?>
                
                <?php if($wonAuctions->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $wonAuctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-800/50 rounded-lg p-4 flex justify-between items-center border border-gray-700 hover:border-neon-purple/50 transition">
                                <div class="flex items-center space-x-4">
                                    <img src="<?php echo e($auction->image); ?>" alt="<?php echo e($auction->name); ?>" class="w-16 h-16 object-cover rounded-lg">
                                    <div>
                                        <h3 class="text-white font-bold"><?php echo e($auction->name); ?></h3>
                                        <p class="text-gray-400 text-sm">
                                            Ganada el <?php echo e($auction->auction_end_time ? $auction->auction_end_time->format('d/m/Y') : 'Fecha no disponible'); ?>

                                        </p>
                                        <p class="text-neon-purple font-bold"><?php echo e(number_format($auction->auction_final_price ?? $auction->price, 2)); ?>€</p>
                                    </div>
                                </div>
                                <a href="<?php echo e(route('products.show', $auction->slug)); ?>" 
                                   class="px-4 py-2 bg-neon-purple text-white rounded-lg hover:bg-neon-purple/80 transition text-sm shadow-[0_0_15px_rgba(157,0,255,0.3)]">
                                    Ver producto
                                </a>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 bg-gray-800/30 rounded-lg">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-400">No has ganado ninguna subasta aún</p>
                        <a href="<?php echo e(route('auctions.index')); ?>" class="inline-block mt-3 text-neon-purple hover:underline">
                            Ver subastas activas
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sorteos ganados - Fondo rojo oscuro -->
            <div class="bg-gradient-to-br from-red-900/30 to-red-950/50 rounded-2xl border border-neon-red/30 p-8 mb-8 backdrop-blur-sm">
                <h2 class="text-2xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-neon-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 11.5v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 9v6m0 0l-3-3m3 3l3-3M3 3h18v6H3V3z"></path>
                    </svg>
                    Sorteos ganados
                </h2>
                
                <?php
                    $wonRaffles = Auth::user()->wonRaffles()
                        ->orderBy('draw_date', 'desc')
                        ->get();
                ?>
                
                <?php if($wonRaffles->count() > 0): ?>
                    <div class="space-y-4">
                        <?php $__currentLoopData = $wonRaffles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $raffle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $product = $raffle->getProduct();
                            ?>
                            <div class="bg-gray-800/50 rounded-lg p-4 flex justify-between items-center border border-gray-700 hover:border-neon-red/50 transition">
                                <div class="flex items-center space-x-4">
                                    <?php if($product): ?>
                                        <img src="<?php echo e($product->image); ?>" alt="<?php echo e($product->name); ?>" class="w-16 h-16 object-cover rounded-lg">
                                    <?php else: ?>
                                        <div class="w-16 h-16 bg-gray-700 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h3 class="text-white font-bold"><?php echo e($raffle->title); ?></h3>
                                        <p class="text-gray-400 text-sm">Sorteado el <?php echo e($raffle->draw_date->format('d/m/Y')); ?></p>
                                        <?php if($product): ?>
                                            <p class="text-neon-red font-bold"><?php echo e($product->name); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php if($product): ?>
                                    <a href="<?php echo e(route('products.show', $product->slug)); ?>" 
                                       class="px-4 py-2 bg-neon-red text-white rounded-lg hover:bg-neon-red/80 transition text-sm shadow-[0_0_15px_rgba(255,0,85,0.3)]">
                                        Ver premio
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 bg-gray-800/30 rounded-lg">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 11.5v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 9v6m0 0l-3-3m3 3l3-3M3 3h18v6H3V3z"></path>
                        </svg>
                        <p class="text-gray-400">No has ganado ningún sorteo aún</p>
                        <a href="<?php echo e(route('raffles.index')); ?>" class="inline-block mt-3 text-neon-red hover:underline">
                            Ver sorteos activos
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Historial de pedidos - Fondo verde oscuro con ACORDEONES -->
            <div id="pedidos" class="bg-gradient-to-br from-green-900/30 to-green-950/50 rounded-2xl border border-green-500/30 p-8 mb-8 backdrop-blur-sm scroll-mt-24">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        📦 Mis pedidos
                    </h2>
                    <?php
                        $ordersCount = Auth::user()->orders()->count();
                    ?>
                    <span class="text-gray-400 text-sm">Total: <?php echo e($ordersCount); ?> pedidos</span>
                </div>
                
                <?php
                    $orders = Auth::user()->orders()
                        ->with('items.product')
                        ->orderBy('created_at', 'desc')
                        ->get();
                ?>
                
                <?php if($orders->count() > 0): ?>
                    <div class="space-y-3" x-data="{ openOrder: null }">
                        <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-800/50 rounded-lg border border-gray-700 overflow-hidden">
                                <!-- CABECERA DEL PEDIDO (SIEMPRE VISIBLE) -->
                                <div class="p-4 flex justify-between items-center cursor-pointer hover:bg-gray-700/50 transition"
                                     @click="openOrder = openOrder === <?php echo e($index); ?> ? null : <?php echo e($index); ?>">
                                    <div class="flex items-center gap-4 flex-wrap">
                                        <span class="text-green-500 font-bold">Pedido #<?php echo e($order->id); ?></span>
                                        <span class="text-gray-400 text-sm"><?php echo e($order->created_at->format('d/m/Y')); ?></span>
                                        <?php if($order->status == 'pending'): ?>
                                            <span class="px-2 py-0.5 bg-yellow-600/20 text-yellow-400 rounded-full text-xs">Pendiente</span>
                                        <?php elseif($order->status == 'completed'): ?>
                                            <span class="px-2 py-0.5 bg-green-600/20 text-green-400 rounded-full text-xs">Completado</span>
                                        <?php else: ?>
                                            <span class="px-2 py-0.5 bg-red-600/20 text-red-400 rounded-full text-xs">Cancelado</span>
                                        <?php endif; ?>
                                        <span class="text-white font-bold"><?php echo e(number_format($order->total, 2)); ?>€</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <a href="<?php echo e(route('orders.show', $order)); ?>" 
                                           class="text-sm text-green-500 hover:text-green-400 transition">
                                            Detalles
                                        </a>
                                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                             :class="{ 'rotate-180': openOrder === <?php echo e($index); ?> }"
                                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>
                                
                                <!-- DETALLE DEL PEDIDO (DESPLEGABLE) -->
                                <div x-show="openOrder === <?php echo e($index); ?>"
                                     x-transition:enter="transition ease-out duration-300"
                                     x-transition:enter-start="opacity-0 -translate-y-2"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-200"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-2"
                                     class="border-t border-gray-700 p-4 bg-gray-800/80">
                                    
                                    <!-- Productos del pedido -->
                                    <div class="space-y-3 mb-4">
                                        <h4 class="text-sm font-bold text-gray-300 mb-2">Productos:</h4>
                                        <?php $__currentLoopData = $order->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <div class="flex items-center gap-3 text-sm">
                                                <?php if($item->product): ?>
                                                    <img src="<?php echo e($item->product->image); ?>" alt="<?php echo e($item->product->name); ?>" class="w-12 h-12 object-cover rounded">
                                                    <div class="flex-1">
                                                        <span class="text-white"><?php echo e($item->product->name); ?></span>
                                                        <span class="text-gray-400 text-xs block">Cantidad: <?php echo e($item->quantity); ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="w-12 h-12 bg-gray-700 rounded flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                        </svg>
                                                    </div>
                                                <?php endif; ?>
                                                <span class="text-green-500 font-bold"><?php echo e(number_format($item->price * $item->quantity, 2)); ?>€</span>
                                            </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                    
                                    <!-- Información adicional del pedido -->
                                    <div class="grid grid-cols-2 gap-4 text-sm pt-3 border-t border-gray-700">
                                        <div>
                                            <span class="text-gray-400 block">Fecha completa:</span>
                                            <span class="text-white"><?php echo e($order->created_at->format('d/m/Y H:i')); ?></span>
                                        </div>
                                        <?php if($order->address): ?>
                                        <div>
                                            <span class="text-gray-400 block">Dirección:</span>
                                            <span class="text-white"><?php echo e($order->address->name); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 bg-gray-800/30 rounded-lg">
                        <svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                        <p class="text-gray-400">No has realizado ningún pedido aún</p>
                        <a href="<?php echo e(route('products.index')); ?>" class="inline-block mt-3 text-green-500 hover:underline">
                            Comprar ahora
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Direcciones - Fondo neutro (gris) -->
            <div class="bg-gradient-to-br from-gray-800/80 to-gray-900/90 rounded-2xl border border-gray-700 p-8 backdrop-blur-sm">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Direcciones
                    </h2>
                    <a href="<?php echo e(route('addresses.create')); ?>" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-600 transition text-sm">
                        + Nueva dirección
                    </a>
                </div>
                
                <?php
                    $addresses = App\Models\Address::where('user_id', Auth::id())->get();
                ?>
                
                <?php if($addresses->count() > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php $__currentLoopData = $addresses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $address): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 <?php echo e($address->is_default ? 'border-gray-500' : ''); ?>">
                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="text-white font-bold"><?php echo e($address->name); ?></h3>
                                    <?php if($address->is_default): ?>
                                        <span class="text-gray-400 text-xs font-bold">PREDETERMINADA</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-gray-400 text-sm"><?php echo e($address->street); ?>, <?php echo e($address->number); ?></p>
                                <?php if($address->complement): ?>
                                    <p class="text-gray-400 text-sm"><?php echo e($address->complement); ?></p>
                                <?php endif; ?>
                                <p class="text-gray-400 text-sm"><?php echo e($address->city); ?>, <?php echo e($address->state); ?></p>
                                <p class="text-gray-400 text-sm">CP: <?php echo e($address->zipcode); ?></p>
                                <p class="text-gray-400 text-sm">Tel: <?php echo e($address->phone); ?></p>
                                
                                <div class="mt-3 flex space-x-2">
                                    <a href="<?php echo e(route('addresses.edit', $address)); ?>" class="text-gray-400 hover:text-white text-sm">
                                        Editar
                                    </a>
                                    <?php if(!$address->is_default): ?>
                                        <form action="<?php echo e(route('addresses.set-default', $address)); ?>" method="POST" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="text-gray-500 hover:text-gray-300 text-sm">
                                                Establecer como predeterminada
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 bg-gray-800/30 rounded-lg">
                        <p class="text-gray-400">No tienes direcciones guardadas</p>
                    </div>
                <?php endif; ?>
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
<?php endif; ?><?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/profile/index.blade.php ENDPATH**/ ?>