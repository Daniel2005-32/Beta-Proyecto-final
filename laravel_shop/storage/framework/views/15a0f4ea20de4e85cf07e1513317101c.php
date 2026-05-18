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
                <h1 class="text-4xl font-black text-white mb-2">
                    <span class="text-neon-purple">💬 Chat de la Comunidad</span>
                </h1>
                <p class="text-gray-400">Habla con otros miembros de Soul Guild</p>
            </div>

            <!-- Contenedor del chat -->
            <div class="bg-gamer-card rounded-2xl border border-neon-blue/20 overflow-hidden">
                <!-- Área de mensajes -->
                <div id="chat-messages" class="h-96 overflow-y-auto p-6 space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-start space-x-3 <?php echo e($msg->user_id == Auth::id() ? 'flex-row-reverse space-x-reverse' : ''); ?>">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple flex items-center justify-center text-white font-bold text-sm">
                                    <?php echo e(strtoupper(substr($msg->user->name, 0, 1))); ?>

                                </div>
                            </div>
                            <div class="flex-1 max-w-md">
                                <div class="flex items-center space-x-2 mb-1 <?php echo e($msg->user_id == Auth::id() ? 'justify-end' : ''); ?>">
                                    <span class="text-sm font-bold text-neon-blue"><?php echo e($msg->user->name); ?></span>
                                    <span class="text-xs text-gray-500"><?php echo e($msg->created_at->diffForHumans()); ?></span>
                                </div>
                                <div class="rounded-lg p-3 <?php echo e($msg->user_id == Auth::id() ? 'bg-neon-blue/20 text-white' : 'bg-gray-800/50 text-gray-300'); ?>">
                                    <?php echo e($msg->message); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center py-12">
                            <p class="text-gray-400">No hay mensajes aún. ¡Sé el primero en escribir!</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulario de mensaje -->
                <div class="border-t border-gray-800 p-4">
                    <form id="chat-form" action="<?php echo e(route('chat.store')); ?>" method="POST" class="flex space-x-3">
                        <?php echo csrf_field(); ?>
                        <input type="text" 
                               name="message" 
                               placeholder="Escribe tu mensaje..." 
                               required
                               maxlength="500"
                               class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-neon-blue transition">
                        <button type="submit" 
                                class="px-6 py-3 bg-neon-blue text-gamer-dark font-bold rounded-lg hover:scale-105 transition shadow-[0_0_20px_rgba(0,210,255,0.4)]">
                            Enviar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll al último mensaje
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Envío AJAX del formulario
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            submitBtn.textContent = 'Enviando...';
            submitBtn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    form.reset();
                    location.reload();
                }
            })
            .catch(error => console.error('Error:', error))
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>
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
<?php /**PATH /app/resources/views/chat/index.blade.php ENDPATH**/ ?>