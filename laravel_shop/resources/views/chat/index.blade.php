<x-store-layout>
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
                    @forelse($messages as $msg)
                        <div class="flex items-start space-x-3 {{ $msg->user_id == Auth::id() ? 'flex-row-reverse space-x-reverse' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple flex items-center justify-center text-white font-bold text-sm">
                                    {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-1 max-w-md">
                                <div class="flex items-center space-x-2 mb-1 {{ $msg->user_id == Auth::id() ? 'justify-end' : '' }}">
                                    <span class="text-sm font-bold text-neon-blue">{{ $msg->user->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $msg->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="rounded-lg p-3 {{ $msg->user_id == Auth::id() ? 'bg-neon-blue/20 text-white' : 'bg-gray-800/50 text-gray-300' }}">
                                    {{ $msg->message }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-400">No hay mensajes aún. ¡Sé el primero en escribir!</p>
                        </div>
                    @endforelse
                </div>

                <!-- Formulario de mensaje -->
                <div class="border-t border-gray-800 p-4">
                    <form id="chat-form" action="{{ route('chat.store') }}" method="POST" class="flex space-x-3">
                        @csrf
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
</x-store-layout>
