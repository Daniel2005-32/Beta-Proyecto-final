<script setup>
import { ref, watch, onUnmounted } from 'vue';
import axios from 'axios';

const isOpen = ref(false);
const messages = ref([]);
const newMessage = ref('');
const loading = ref(false);
let interval = null;

const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';

const fetchMessages = async () => {
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get(`${apiBase}/chat`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        messages.value = res.data;
    } catch (err) {
        console.error("Error fetching chat", err);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim()) return;
    try {
        const token = localStorage.getItem('token');
        await axios.post(`${apiBase}/chat`, { message: newMessage.value }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        newMessage.value = '';
        fetchMessages();
    } catch (err) {
        alert("Error al enviar mensaje");
    }
};

watch(isOpen, (newValue) => {
    if (newValue) {
        fetchMessages();
        interval = setInterval(fetchMessages, 3000);
    } else {
        if (interval) {
            clearInterval(interval);
            interval = null;
        }
    }
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});
</script>

<template>
  <div class="fixed bottom-6 right-6 z-50">
    <!-- Floating Button -->
    <button @click="isOpen = !isOpen" class="w-14 h-14 bg-gradient-to-r from-neon-purple to-neon-blue rounded-full flex items-center justify-center shadow-lg shadow-neon-purple/30 hover:scale-110 active:scale-95 transition cursor-pointer">
        <i v-if="!isOpen" class="fas fa-comment-dots text-2xl text-white"></i>
        <i v-else class="fas fa-times text-2xl text-white"></i>
    </button>

    <!-- Chat Window popup -->
    <div v-if="isOpen" class="absolute bottom-16 right-0 w-80 h-[450px] bg-gamer-card border border-gray-800 rounded-2xl flex flex-col shadow-2xl shadow-neon-blue/20 backdrop-blur-md overflow-hidden animate-slideUp">
        
        <!-- Header -->
        <div class="p-4 border-b border-gray-800 bg-gray-900 flex justify-between items-center">
            <div>
                <h4 class="text-xs font-black uppercase text-neon-blue">Chat Global Soporte</h4>
                <p class="text-[9px] text-gray-500">Los mensajes se borran cada hora.</p>
            </div>
            <span class="h-2 w-2 bg-neon-green rounded-full shadow-neon-green"></span>
        </div>

        <!-- Messages Feed -->
        <div class="flex-1 p-4 overflow-y-auto space-y-3 custom-scrollbar">
            <div v-for="msg in messages" :key="msg.id" class="flex flex-col">
                <div class="flex items-center gap-1">
                    <span class="font-bold text-[11px] text-neon-purple">{{ msg.user_name }}</span>
                    <span class="text-[8px] text-gray-600">{{ msg.time }}</span>
                </div>
                <p class="text-xs text-gray-200 bg-gray-900/50 border border-gray-800/80 px-2.5 py-1.5 rounded-r-xl rounded-bl-xl mt-0.5 inline-block max-w-[80%] break-words">{{ msg.message }}</p>
            </div>
            <div v-if="messages.length === 0" class="text-center py-20 text-gray-600 text-xs">
                No hay actividad reciente.
            </div>
        </div>

        <!-- Input Row -->
        <form @submit.prevent="sendMessage" class="p-3 border-t border-gray-800 bg-gray-950 flex gap-2">
            <input v-model="newMessage" type="text" placeholder="Escribe un mensaje..." class="flex-1 bg-gray-900 border border-gray-800 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:border-neon-purple transition text-white">
            <button type="submit" class="bg-neon-purple text-white px-3 py-1.5 rounded-xl text-xs font-bold hover:bg-purple-600 transition">Enviar</button>
        </form>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #374151;
  border-radius: 10px;
}
.animate-slideUp {
    animation: slideUp 0.3s ease-out forwards;
}
@keyframes slideUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>
