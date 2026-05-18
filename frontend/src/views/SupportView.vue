<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { apiBase } from '../utils/api';
import { store } from '../utils/store';
import LoadingState from '../components/LoadingState.vue';
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const tickets = ref([]);
const loading = ref(true);
const sending = ref(false);
const subject = ref('');
const message = ref('');

// Custom Confirm Modal State
const showDeleteModal = ref(false);
const ticketToDelete = ref(null);

const fetchTickets = async () => {
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get(`${apiBase}/support/my-tickets`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        tickets.value = res.data.tickets;
    } catch (err) {
        console.error("Error fetching tickets", err);
    } finally {
        loading.value = false;
    }
};

const submitTicket = async () => {
    if (!subject.value || !message.value) return;
    sending.value = true;
    try {
        const token = localStorage.getItem('token');
        await axios.post(`${apiBase}/support`, { 
            subject: subject.value, 
            message: message.value 
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        store.notify("Ticket enviado correctamente", 'success');
        subject.value = '';
        message.value = '';
        fetchTickets();
    } catch (err) {
        console.error("DEBUG SUPPORT ERROR:", err.response || err);
        const errorMsg = err.response?.data?.error || err.response?.data?.message || err.message || "Error al enviar el ticket";
        const status = err.response?.status ? ` [HTTP ${err.response.status}]` : '';
        const detail = err.response?.data?.messages ? JSON.stringify(err.response.data.messages) : '';
        store.notify(`${errorMsg}${status} ${detail}`, 'error');
    } finally {
        sending.value = false;
    }
};

const confirmDelete = (id) => {
    ticketToDelete.value = id;
    showDeleteModal.value = true;
};

const deleteTicket = async () => {
    if (!ticketToDelete.value) return;
    try {
        const token = localStorage.getItem('token');
        await axios.delete(`${apiBase}/support/${ticketToDelete.value}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        store.notify("Petición eliminada correctamente", 'success');
        showDeleteModal.value = false;
        fetchTickets();
    } catch (err) {
        console.error("Error deleting ticket", err);
        store.notify("Error al eliminar la petición", 'error');
    }
};

onMounted(() => {
    if (!localStorage.getItem('token')) {
        router.push('/register');
        return;
    }
    fetchTickets();
});
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-4xl text-white">
    <Breadcrumbs :items="[{ label: 'Soporte', path: '', active: true }]" />
    
    <header class="mb-10 text-center">
        <h1 class="text-5xl font-black uppercase italic tracking-tighter mb-4 text-neon-blue">Soporte Técnico</h1>
        <p class="text-gray-400 text-sm uppercase font-bold tracking-widest">¿Tienes problemas? Cuéntanos y lo solucionaremos.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
        <!-- Ticket Form -->
        <section class="bg-gamer-card border border-white/5 p-8 rounded-3xl shadow-2xl">
            <h2 class="text-xl font-black uppercase italic mb-6 border-l-4 border-neon-blue pl-4">Nuevo Ticket</h2>
            <form @submit.prevent="submitTicket" class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Asunto del Problema</label>
                    <input v-model="subject" type="text" placeholder="Ej: Error en pago, Bug en juego..." 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-neon-blue outline-none transition" required>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2">Mensaje Detallado</label>
                    <textarea v-model="message" rows="5" placeholder="Cuéntanos exactamente qué pasó..." 
                              class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm focus:border-neon-blue outline-none transition resize-none" required></textarea>
                </div>
                <button type="submit" :disabled="sending"
                        class="w-full py-4 bg-neon-blue text-gamer-dark font-black uppercase tracking-widest rounded-xl hover:bg-white transition shadow-neon-blue/20 disabled:opacity-50">
                    {{ sending ? 'Enviando...' : 'Enviar Petición' }}
                </button>
            </form>
        </section>

        <!-- Tickets List -->
        <section>
            <h2 class="text-xl font-black uppercase italic mb-6 border-l-4 border-neon-purple pl-4">Mis Peticiones</h2>
            <LoadingState v-if="loading" />
            <div v-else-if="tickets.length === 0" class="text-center py-10 bg-white/5 rounded-3xl border border-dashed border-white/10">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-widest">No tienes tickets abiertos</p>
            </div>
            <div v-else class="space-y-4">
                <div v-for="ticket in tickets" :key="ticket.id" 
                     class="bg-gamer-card border border-white/5 p-5 rounded-2xl hover:border-white/20 transition group">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="font-black text-sm uppercase tracking-tight">{{ ticket.subject }}</h3>
                        <div class="flex items-center gap-2">
                            <span class="text-[8px] font-black px-2 py-0.5 rounded-full uppercase" :class="{
                                'bg-yellow-500/20 text-yellow-500': ticket.status === 'open',
                                'bg-blue-500/20 text-blue-500': ticket.status === 'pending',
                                'bg-neon-green/20 text-neon-green': ticket.status === 'closed'
                            }">{{ ticket.status }}</span>
                            <button @click="confirmDelete(ticket.id)" 
                                    class="p-2 bg-red-500/10 border border-red-500/20 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/10"
                                    title="Eliminar Petición">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-400 mb-4 line-clamp-2">{{ ticket.message }}</p>
                    
                    <!-- Admin Reply -->
                    <div v-if="ticket.admin_reply" class="mt-4 p-3 bg-neon-blue/5 border-l-2 border-neon-blue rounded-r-xl">
                        <span class="text-[8px] font-black uppercase text-neon-blue block mb-1">Respuesta del Equipo:</span>
                        <p class="text-[10px] text-gray-200 italic">{{ ticket.admin_reply }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Custom Delete Modal -->
        <Teleport to="body">
            <div v-if="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showDeleteModal = false"></div>
                <div class="bg-gamer-card border border-red-500/30 p-8 rounded-3xl max-w-sm w-full relative z-10 shadow-2xl animate-fade-in-up">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                            <i class="fas fa-exclamation-triangle text-2xl"></i>
                        </div>
                        <h3 class="text-xl font-black uppercase italic mb-2 tracking-tighter">¿Eliminar Ticket?</h3>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-6">Esta acción es permanente y no se puede deshacer.</p>
                        
                        <div class="flex gap-3">
                            <button @click="showDeleteModal = false" 
                                    class="flex-grow py-3 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white/10 transition">
                                Cancelar
                            </button>
                            <button @click="deleteTicket" 
                                    class="flex-grow py-3 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-600/20 hover:bg-red-500 transition">
                                Confirmar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
  </div>
</template>
