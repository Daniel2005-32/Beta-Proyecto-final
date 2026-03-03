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
            <h1 class="text-4xl font-black text-white mb-8">🛒 Tu Carrito</h1>
            
            <?php
                $cart = session('cart', []);
                $subtotal = 0;
                foreach($cart as $id => $item) {
                    $subtotal += $item['price'] * $item['quantity'];
                }
            ?>

            <?php if(empty($cart)): ?>
                <!-- Carrito vacío -->
                <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-12 text-center">
                    <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-white mb-2">Tu carrito está vacío</h2>
                    <p class="text-gray-400 mb-6">¡Explora nuestros productos y encuentra algo increíble!</p>
                    <a href="<?php echo e(route('products.index')); ?>" class="inline-block px-8 py-4 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                        Ver Productos
                    </a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Lista de productos -->
                    <div class="lg:col-span-2">
                        <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
                            <table class="w-full">
                                <thead class="bg-gray-800 border-b border-neon-blue/20">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-neon-blue">Producto</th>
                                        <th class="px-6 py-4 text-left text-neon-blue">Precio</th>
                                        <th class="px-6 py-4 text-left text-neon-blue">Cantidad</th>
                                        <th class="px-6 py-4 text-left text-neon-blue">Total</th>
                                        <th class="px-6 py-4 text-left text-neon-blue">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $product = \App\Models\Product::find($id);
                                            $maxStock = $product ? $product->stock : 0;
                                            $subtotalProducto = $item['price'] * $item['quantity'];
                                        ?>
                                        <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-3">
                                                    <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>" class="w-12 h-12 object-cover rounded">
                                                    <span class="text-white font-medium"><?php echo e($item['name']); ?></span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-300"><?php echo e(number_format($item['price'], 2)); ?>€</td>
                                            <td class="px-6 py-4">
                                                <form action="<?php echo e(route('cart.update', $id)); ?>" method="POST" class="flex items-center space-x-2">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="number" 
                                                           name="quantity" 
                                                           value="<?php echo e($item['quantity']); ?>" 
                                                           min="1" 
                                                           max="<?php echo e($maxStock); ?>"
                                                           class="w-16 bg-gray-800 border border-gray-700 rounded px-2 py-1 text-white focus:outline-none focus:border-neon-blue">
                                                    <button type="submit" class="text-neon-blue hover:text-neon-blue/80 transition" title="Actualizar">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 text-white font-bold"><?php echo e(number_format($subtotalProducto, 2)); ?>€</td>
                                            <td class="px-6 py-4">
                                                <form action="<?php echo e(route('cart.remove', $id)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <button type="submit" class="text-neon-red hover:text-neon-red/80 transition" title="Eliminar">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Resumen del pedido (sin IVA) -->
                    <div class="lg:col-span-1">
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-6 sticky top-24">
                            <h2 class="text-2xl font-bold text-white mb-6">Resumen del pedido</h2>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between text-gray-300">
                                    <span>Subtotal:</span>
                                    <span><?php echo e(number_format($subtotal, 2)); ?>€</span>
                                </div>
                                <div class="flex justify-between text-gray-300">
                                    <span>Envío:</span>
                                    <span class="text-green-500">Gratis</span>
                                </div>
                                <div class="border-t border-gray-800 my-4"></div>
                                <div class="flex justify-between text-xl font-bold text-white">
                                    <span>Total (sin impuestos):</span>
                                    <span class="text-neon-blue"><?php echo e(number_format($subtotal, 2)); ?>€</span>
                                </div>
                                <p class="text-xs text-gray-500 text-right">
                                    * Los impuestos se calcularán al seleccionar tu dirección
                                </p>
                            </div>

                            <?php if(auth()->guard()->check()): ?>
                                <a href="<?php echo e(route('cart.checkout.form')); ?>" 
                                   class="block w-full mt-6 px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition text-center shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                    ✅ Proceder al pago
                                </a>
                            <?php else: ?>
                                <div class="mt-6 bg-gray-800/50 border border-neon-red/30 rounded-lg p-4 text-center">
                                    <p class="text-gray-300 mb-3">🔒 Debes iniciar sesión para comprar</p>
                                    <div class="flex gap-3">
                                        <a href="<?php echo e(route('login')); ?>" class="flex-1 px-4 py-2 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition">
                                            Iniciar sesión
                                        </a>
                                        <a href="<?php echo e(route('register')); ?>" class="flex-1 px-4 py-2 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition">
                                            Registrarse
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-4 text-center">
                                <a href="<?php echo e(route('products.index')); ?>" class="text-sm text-gray-400 hover:text-neon-blue transition">
                                    ← Seguir comprando
                                </a>
                            </div>
                        </div>
                    </div>
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
<?php /**PATH /home/ctk/Documentos/Proyecto_Daniel/Proyecto-final-main/laravel_shop/resources/views/cart/index.blade.php ENDPATH**/ ?>