<script setup>
import { ref, watch, onUnmounted, onMounted } from 'vue';
import axios from 'axios';
import { store } from '../utils/store';

const isOpen = ref(false);
const messages = ref([]);
const newMessage = ref('');
const loading = ref(false);
const unreadCount = ref(0);
const lastSeenCount = ref(Number(localStorage.getItem('chat_last_seen') || 0));
let interval = null;

onMounted(() => {
    fetchMessages();
    interval = setInterval(fetchMessages, 10000); // Verifica mensajes en background cada 10s
    
    window.addEventListener('toggle-chat', () => {
        isOpen.value = !isOpen.value;
    });
});

import { apiBase } from '@/utils/api';

const fetchMessages = async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) return;
        const res = await axios.get(`${apiBase}/chat`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        messages.value = res.data;
        
        if (!isOpen.value) {
            const count = messages.value.length - lastSeenCount.value;
            unreadCount.value = count > 0 ? count : 0;
        } else {
            unreadCount.value = 0;
            lastSeenCount.value = messages.value.length;
            localStorage.setItem('chat_last_seen', lastSeenCount.value);
        }
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
        store.notify("Error al enviar mensaje: " + (err.response?.data?.error || err.response?.data?.message || err.message), 'error');
    }
};

watch(isOpen, (newValue) => {
    if (interval) clearInterval(interval);
    if (newValue) {
        unreadCount.value = 0;
        lastSeenCount.value = messages.value.length;
        localStorage.setItem('chat_last_seen', lastSeenCount.value);
        fetchMessages();
        interval = setInterval(fetchMessages, 3000); // 3s si está abierto
    } else {
        interval = setInterval(fetchMessages, 10000); // 10s si está cerrado
    }
});

onUnmounted(() => {
    if (interval) clearInterval(interval);
});

</script>

<template>
  <div class="fixed bottom-24 md:bottom-6 right-4 md:right-6 z-50">
    <!-- Floating Button -->
    <button @click="isOpen = !isOpen" class="w-12 h-12 md:w-14 md:h-14 bg-gradient-to-r from-neon-purple to-neon-blue rounded-full flex items-center justify-center shadow-lg shadow-neon-purple/30 hover:scale-110 active:scale-95 transition cursor-pointer relative">
        <!-- SVG Chat Icon -->
        <svg v-if="!isOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
        </svg>
        <!-- SVG Close Icon -->
        <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:h-6 md:w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        
        <!-- Unread Badge -->
        <span v-if="unreadCount > 0 && !isOpen" class="absolute -top-1 -right-1 bg-red-600 text-white text-[9px] md:text-[10px] h-4 w-4 md:h-5 md:w-5 rounded-full flex items-center justify-center shadow-md shadow-red-600/30">
            {{ unreadCount }}
        </span>
    </button>


    <!-- Chat Window popup -->
    <div v-if="isOpen" class="absolute bottom-16 right-0 w-80 h-[450px] bg-gamer-card border border-gray-800 rounded-2xl flex flex-col shadow-2xl shadow-neon-blue/20 backdrop-blur-md overflow-hidden animate-slideUp">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-neon-blue to-neon-purple p-4 flex justify-between items-center">
            <div>
                <h4 class="text-xs font-black uppercase text-white">Chat Global Soporte</h4>
                <p class="text-[10px] text-white/80">Los mensajes se borran cada hora.</p>
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
