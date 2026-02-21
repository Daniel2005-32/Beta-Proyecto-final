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
    <div class="flex">
        <!-- Sidebar Filters -->
        <div class="w-1/4 pr-8">
            <h3 class="font-bold mb-4">Categorías</h3>
            <ul class="space-y-2">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li>
                        <a href="<?php echo e(route('products.index', ['category' => $category->slug])); ?>" class="text-gray-600 hover:text-indigo-600 <?php echo e(request('category') == $category->slug ? 'font-bold text-indigo-600' : ''); ?>">
                            <?php echo e($category->name); ?>

                        </a>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>

        <!-- Product Grid -->
        <div class="w-3/4">
            <h2 class="text-2xl font-bold mb-6">Catálogo</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <img src="<?php echo e($product->image ?? 'https://via.placeholder.com/300'); ?>" alt="<?php echo e($product->name); ?>" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-lg mb-2"><?php echo e($product->name); ?></h3>
                            <p class="text-gray-500 text-sm mb-2"><?php echo e($product->category->name); ?></p>
                            <div class="flex justify-between items-center mt-4">
                                <span class="text-xl font-bold"><?php echo e($product->price); ?>€</span>
                                <a href="<?php echo e(route('products.show', $product->slug)); ?>" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Ver</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-8">
                <?php echo e($products->links()); ?>

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
<?php /**PATH C:\Users\Daniel\Documents\Prueba\Proyecto_final\laravel_shop\resources\views/products/index.blade.php ENDPATH**/ ?>