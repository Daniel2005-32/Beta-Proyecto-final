<div x-data="floatingChat()" 
     x-init="init()"
     class="fixed bottom-4 right-4 z-50">
    
    <!-- Botón flotante del chat -->
    <button @click="toggleChat()" 
            class="w-14 h-14 rounded-full bg-gradient-to-r from-neon-blue to-neon-purple shadow-[0_0_20px_rgba(0,210,255,0.5)] hover:scale-110 transition-all flex items-center justify-center relative">
        <svg x-show="!isOpen" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <svg x-show="isOpen" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        
        <!-- Indicador de mensajes no leídos -->
        <span x-show="unreadCount > 0 && !isOpen" 
              class="absolute -top-1 -right-1 bg-neon-red text-white text-xs font-bold px-2 py-1 rounded-full">
            <span x-text="unreadCount"></span>
        </span>
    </button>

    <!-- Ventana del chat -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="absolute bottom-16 right-0 w-80 bg-gamer-card border border-neon-blue/20 rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Cabecera del chat -->
        <div class="bg-gradient-to-r from-neon-blue to-neon-purple p-4 flex justify-between items-center">
            <div>
                <h3 class="text-white font-bold">Chat comunitario</h3>
                <p class="text-xs text-white/70" x-show="!isAuthenticated">Modo solo lectura</p>
            </div>
            <div class="flex space-x-2">
                <button @click="minimizeChat()" class="text-white/80 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                    </svg>
                </button>
                <button @click="toggleChat()" class="text-white/80 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Área de mensajes -->
        <div x-ref="messagesContainer" class="h-64 overflow-y-auto p-4 space-y-3" style="scroll-behavior: smooth;">
            <template x-for="msg in messages" :key="msg.id">
                <div :class="{'flex justify-end': msg.user_id === currentUserId}" class="flex items-start space-x-2">
                    <template x-if="msg.user_id !== currentUserId">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                             x-text="msg.user_name?.charAt(0).toUpperCase() || '?'">
                        </div>
                    </template>
                    <div :class="msg.user_id === currentUserId ? 'bg-neon-blue/20' : 'bg-gray-800/50'"
                         class="rounded-lg px-3 py-2 max-w-[200px]">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="text-xs font-bold text-neon-blue" x-text="msg.user_name"></span>
                            <span class="text-xs text-gray-500" x-text="msg.time"></span>
                        </div>
                        <p class="text-sm text-white break-words" x-text="msg.message"></p>
                    </div>
                    <template x-if="msg.user_id === currentUserId">
                        <div class="w-6 h-6 rounded-full bg-gradient-to-br from-neon-blue to-neon-purple flex-shrink-0 flex items-center justify-center text-white text-xs font-bold"
                             x-text="msg.user_name?.charAt(0).toUpperCase() || '?'">
                        </div>
                    </template>
                </div>
            </template>
            
            <!-- Mensaje cuando no hay mensajes -->
            <template x-if="messages.length === 0">
                <div class="text-center py-8">
                    <div class="text-5xl mb-3">💬</div>
                    <p class="text-gray-400 text-sm mb-2">No hay mensajes aún</p>
                    <p class="text-xs text-gray-500">¡Sé el primero en hablar!</p>
                </div>
            </template>
        </div>

        <!-- Formulario de mensaje -->
        <div class="border-t border-gray-800 p-3">
            <!-- USUARIOS AUTENTICADOS: pueden escribir -->
            <form x-show="isAuthenticated" @submit.prevent="sendMessage">
                <div class="flex space-x-2">
                    <input type="text" 
                           x-model="newMessage"
                           @keyup.enter="sendMessage"
                           placeholder="Escribe un mensaje..."
                           class="flex-1 bg-gray-800 border border-gray-700 rounded-lg px-3 py-2 text-sm text-white focus:outline-none focus:border-neon-blue">
                    <button type="submit"
                            :disabled="!newMessage.trim()"
                            class="px-4 py-2 bg-neon-blue text-gamer-dark text-sm font-bold rounded-lg hover:scale-105 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Enviar
                    </button>
                </div>
                <div class="mt-2 text-xs text-center text-gray-500">
                    <span class="text-neon-blue">✓</span> Conectado como <span x-text="userName" class="text-neon-blue font-bold"></span>
                </div>
            </form>

            <!-- USUARIOS NO AUTENTICADOS: solo lectura con invitación -->
            <div x-show="!isAuthenticated" 
                 class="p-4 bg-gradient-to-r from-neon-purple/20 to-neon-blue/20 border border-neon-purple/30 rounded-lg text-center">
                <div class="flex items-center justify-center space-x-2 mb-3">
                    <svg class="w-5 h-5 text-neon-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span class="text-sm font-bold text-white">🔒 Chat privado</span>
                </div>
                <p class="text-xs text-gray-300 mb-3">
                    Los mensajes son solo para miembros registrados
                </p>
                <div class="flex space-x-2 justify-center mb-2">
                    <a href="{{ route('login') }}" 
                       class="px-3 py-1.5 bg-neon-blue text-gamer-dark text-xs font-bold rounded hover:scale-105 transition">
                        Iniciar sesión
                    </a>
                    <a href="{{ route('register') }}" 
                       class="px-3 py-1.5 bg-neon-purple text-white text-xs font-bold rounded hover:scale-105 transition">
                        Registrarse
                    </a>
                </div>
                <p class="text-xs text-gray-500">
                    Puedes leer los mensajes, pero necesitas una cuenta para escribir
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Variable global para acceder a la instancia del chat
let chatInstance = null;

function floatingChat() {
    return {
        isOpen: false,
        messages: [],
        newMessage: '',
        currentUserId: {{ auth()->id() ?? 'null' }},
        isAuthenticated: {{ auth()->check() ? 'true' : 'false' }},
        userName: '{{ auth()->user()->name ?? '' }}',
        unreadCount: 0,
        
        init() {
            // Guardar la instancia globalmente
            chatInstance = this;
            this.loadMessages();
            // Actualizar cada 3 segundos
            setInterval(() => this.loadMessages(), 3000);
        },
        
        toggleChat() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.unreadCount = 0;
                setTimeout(() => this.scrollToBottom(), 100);
            }
        },
        
        minimizeChat() {
            this.isOpen = false;
        },
        
        loadMessages() {
            fetch('{{ route("chat.refresh") }}')
                .then(response => response.json())
                .then(data => {
                    this.messages = data;
                    if (this.isOpen) {
                        this.scrollToBottom();
                    }
                    // Incrementar contador de no leídos si hay nuevos mensajes y el chat está cerrado
                    if (!this.isOpen && data.length > this.messages.length) {
                        this.unreadCount++;
                    }
                });
        },
        
        sendMessage() {
            if (!this.isAuthenticated || !this.newMessage.trim()) return;
            
            const messageText = this.newMessage;
            this.newMessage = '';
            
            fetch('{{ route("chat.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message: messageText })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.loadMessages();
                }
            });
        },
        
        scrollToBottom() {
            setTimeout(() => {
                const container = this.$refs.messagesContainer;
                container.scrollTop = container.scrollHeight;
            }, 50);
        }
    }
}

// Función global para refrescar el chat desde cualquier parte
window.refreshChat = function() {
    if (chatInstance && typeof chatInstance.loadMessages === 'function') {
        chatInstance.loadMessages();
        console.log('Chat refrescado');
    }
};
</script>