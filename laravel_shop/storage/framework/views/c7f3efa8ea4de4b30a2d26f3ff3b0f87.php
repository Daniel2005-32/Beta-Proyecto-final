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
        <div class="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-5xl font-black text-white mb-8">🛒 Tu Carrito</h1> <!-- Texto más grande -->
            
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
                    <svg class="w-32 h-32 text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"> <!-- Icono más grande -->
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h2 class="text-3xl font-bold text-white mb-2">Tu carrito está vacío</h2> <!-- Texto más grande -->
                    <p class="text-gray-400 text-lg mb-6">¡Explora nuestros productos y encuentra algo increíble!</p> <!-- Texto más grande -->
                    <a href="<?php echo e(route('products.index')); ?>" class="inline-block px-10 py-5 bg-neon-blue text-gamer-dark font-black rounded-full hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)] text-lg">
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
                                        <th class="px-6 py-4 text-left text-neon-blue text-lg">Producto</th> <!-- Texto más grande -->
                                        <th class="px-6 py-4 text-left text-neon-blue text-lg">Precio</th>
                                        <th class="px-6 py-4 text-left text-neon-blue text-lg">Cantidad</th>
                                        <th class="px-6 py-4 text-left text-neon-blue text-lg">Total</th>
                                        <th class="px-6 py-4 text-left text-neon-blue text-lg">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $cart; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $product = \App\Models\Product::find($id);
                                            $maxStock = $product ? $product->stock : 0;
                                            $itemTotal = $item['price'] * $item['quantity'];
                                        ?>
                                        <tr class="border-b border-gray-800 hover:bg-gray-800/50 transition">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-3">
                                                    <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>" class="w-16 h-16 object-cover rounded"> <!-- Imagen más grande -->
                                                    <span class="text-white font-medium text-lg"><?php echo e($item['name']); ?></span> <!-- Texto más grande -->
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-300 text-lg"><?php echo e(number_format($item['price'], 2)); ?>€</td> <!-- Texto más grande -->
                                            <td class="px-6 py-4">
                                                <div class="flex items-center space-x-2">
                                                    <input type="number" 
                                                           value="<?php echo e($item['quantity']); ?>" 
                                                           min="1" 
                                                           max="<?php echo e($maxStock); ?>"
                                                           class="w-20 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-white text-center text-lg"> <!-- Input más grande -->
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-white font-bold text-xl"><?php echo e(number_format($itemTotal, 2)); ?>€</td> <!-- Texto más grande -->
                                            <td class="px-6 py-4">
                                                <button class="text-neon-red hover:text-neon-red/80 transition text-lg">Eliminar</button> <!-- Botón más grande -->
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Resumen del pedido - MÁS GRANDE -->
                    <div class="lg:col-span-1">
                        <div class="bg-gamer-card rounded-2xl border border-neon-purple/20 p-8 sticky top-24"> <!-- Padding más grande -->
                            <h2 class="text-3xl font-bold text-white mb-6">Resumen del pedido</h2> <!-- Texto más grande -->
                            
                            <div class="space-y-4">
                                <div class="flex justify-between text-gray-300 text-xl"> <!-- Texto más grande -->
                                    <span>Subtotal:</span>
                                    <span><?php echo e(number_format($subtotal, 2)); ?>€</span>
                                </div>
                                <div class="flex justify-between text-gray-300 text-xl">
                                    <span>Envío:</span>
                                    <span class="text-green-500">Gratis</span>
                                </div>
                                <div class="border-t border-gray-800 my-6"></div>
                                <div class="flex justify-between text-2xl font-bold text-white"> <!-- Texto más grande -->
                                    <span>Total:</span>
                                    <span class="text-neon-blue"><?php echo e(number_format($subtotal, 2)); ?>€</span>
                                </div>
                                <p class="text-base text-gray-500 text-right"> <!-- Texto más grande -->
                                    * Los impuestos se calcularán al seleccionar tu dirección
                                </p>
                            </div>

                            <?php if(auth()->guard()->check()): ?>
                                <a href="<?php echo e(route('cart.checkout.form')); ?>" 
                                   class="block w-full mt-6 px-8 py-4 bg-neon-blue text-gamer-dark text-xl font-bold rounded-lg hover:scale-105 transition text-center shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                                    ✅ Proceder al pago
                                </a>
                            <?php else: ?>
                                <div class="mt-6 bg-gray-800/50 border border-neon-red/30 rounded-lg p-6 text-center">
                                    <p class="text-gray-300 text-lg mb-3">🔒 Debes iniciar sesión para comprar</p>
                                    <div class="flex gap-3">
                                        <a href="<?php echo e(route('login')); ?>" class="flex-1 px-5 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition text-lg">
                                            Iniciar sesión
                                        </a>
                                        <a href="<?php echo e(route('register')); ?>" class="flex-1 px-5 py-3 bg-neon-purple text-white font-bold rounded-lg hover:scale-105 transition text-lg">
                                            Registrarse
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-4 text-center">
                                <a href="<?php echo e(route('products.index')); ?>" class="text-lg text-gray-400 hover:text-neon-blue transition">
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
<?php /**PATH C:\Users\Daniel\Documents\Prueba\Proyecto_final\laravel_shop\resources\views/cart/index.blade.php ENDPATH**/ ?>