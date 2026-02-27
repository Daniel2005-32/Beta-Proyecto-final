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
            <!-- Cabecera -->
            <div class="mb-8">
                <h1 class="text-4xl font-black text-white mb-2">
                    <span class="text-neon-blue">👤 Mi Perfil</span>
                </h1>
                <p class="text-gray-400">Gestiona tu información personal</p>
            </div>

            <!-- Mensajes de éxito/error -->
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

            <!-- Grid de información -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Columna izquierda - Avatar -->
                <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                    <div class="flex flex-col items-center">
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple mb-4 flex items-center justify-center overflow-hidden">
                            <?php if($user->avatar): ?>
                                <img src="<?php echo e(asset('avatars/' . $user->avatar)); ?>" alt="<?php echo e($user->name); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <span class="text-5xl text-white font-black"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></span>
                            <?php endif; ?>
                        </div>
                        <h2 class="text-xl font-bold text-white mb-1"><?php echo e($user->name); ?></h2>
                        <p class="text-gray-400 text-sm mb-4"><?php echo e($user->email); ?></p>
                        
                        <!-- Formulario para subir avatar -->
                        <form action="<?php echo e(route('profile.avatar')); ?>" method="POST" enctype="multipart/form-data" class="w-full">
                            <?php echo csrf_field(); ?>
                            <label class="block mb-2 text-sm text-gray-400">Cambiar avatar</label>
                            <input type="file" name="avatar" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-neon-blue file:text-gamer-dark hover:file:bg-neon-blue/80">
                            <button type="submit" class="w-full mt-3 px-4 py-2 bg-neon-purple/20 text-neon-purple text-sm font-bold rounded-lg hover:bg-neon-purple hover:text-white transition">
                                Subir avatar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Columna derecha - Información y acciones -->
                <div class="md:col-span-2 space-y-6">
                    <!-- Información personal -->
                    <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                        <h3 class="text-xl font-bold text-white mb-4">Información personal</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-3 border-b border-gray-800">
                                <span class="text-gray-400">Nombre</span>
                                <span class="text-white font-medium"><?php echo e($user->name); ?></span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-gray-800">
                                <span class="text-gray-400">Email</span>
                                <span class="text-white font-medium"><?php echo e($user->email); ?></span>
                            </div>
                            <div class="flex items-center justify-between pb-3 border-b border-gray-800">
                                <span class="text-gray-400">Miembro desde</span>
                                <span class="text-white font-medium"><?php echo e($user->created_at->format('d/m/Y')); ?></span>
                            </div>
                            <?php if($user->is_admin): ?>
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-400">Rol</span>
                                    <span class="text-neon-purple font-bold">👑 Administrador</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-6">
                            <a href="<?php echo e(route('profile.edit')); ?>" class="inline-block px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                Editar perfil
                            </a>
                        </div>
                    </div>

                    <!-- SUBASTAS GANADAS -->
                    <?php
                        $wonAuctions = App\Models\Product::where('auction_winner_id', $user->id)
                            ->where('auction_claimed', false)
                            ->get();
                        $claimedAuctions = App\Models\Product::where('auction_winner_id', $user->id)
                            ->where('auction_claimed', true)
                            ->get();
                    ?>

                    <?php if($wonAuctions->count() > 0): ?>
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/30 p-6">
                            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                🏆 Subastas ganadas pendientes
                            </h3>
                            
                            <div class="space-y-4">
                                <?php $__currentLoopData = $wonAuctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-gray-800/50 rounded-lg p-4 flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <img src="<?php echo e($auction->image); ?>" alt="<?php echo e($auction->name); ?>" class="w-12 h-12 object-cover rounded">
                                            <div>
                                                <h4 class="text-white font-bold"><?php echo e($auction->name); ?></h4>
                                                <p class="text-sm text-gray-400">Precio final: <?php echo e(number_format($auction->price, 2)); ?>€</p>
                                            </div>
                                        </div>
                                        <form action="<?php echo e(route('auctions.claim', $auction->id)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="px-4 py-2 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition">
                                                Reclamar premio
                                            </button>
                                        </form>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if($claimedAuctions->count() > 0): ?>
                        <div class="bg-gamer-card rounded-2xl border border-green-500/30 p-6">
                            <h3 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                ✅ Premios reclamados
                            </h3>
                            
                            <div class="space-y-2">
                                <?php $__currentLoopData = $claimedAuctions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="bg-gray-800/30 rounded-lg p-3 flex items-center space-x-3">
                                        <img src="<?php echo e($auction->image); ?>" alt="<?php echo e($auction->name); ?>" class="w-8 h-8 object-cover rounded">
                                        <span class="text-gray-300"><?php echo e($auction->name); ?></span>
                                        <span class="text-green-500 text-sm ml-auto"><?php echo e(number_format($auction->price, 2)); ?>€</span>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Estadísticas -->
                    <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 p-6">
                        <h3 class="text-xl font-bold text-white mb-4">Estadísticas</h3>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-4 bg-gray-800/50 rounded-lg">
                                <div class="text-3xl font-bold text-neon-blue"><?php echo e(App\Models\Order::where('user_id', $user->id)->count()); ?></div>
                                <div class="text-sm text-gray-400">Pedidos realizados</div>
                            </div>
                            <div class="text-center p-4 bg-gray-800/50 rounded-lg">
                                <div class="text-3xl font-bold text-neon-purple"><?php echo e($wonAuctions->count() + $claimedAuctions->count()); ?></div>
                                <div class="text-sm text-gray-400">Subastas ganadas</div>
                            </div>
                        </div>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/profile/index.blade.php ENDPATH**/ ?>