<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';

const raffle = ref(null);
const loading = ref(true);
const error = ref(null);
const quantity = ref(1);
const submittionLoading = ref(false);

const isAuthenticated = computed(() => !!localStorage.getItem('token'));

const fetchRaffle = async () => {
    loading.value = true;
    error.value = null;
    try {
        const token = localStorage.getItem('token');
        const headers = token ? { Authorization: `Bearer ${token}` } : {};
        
        const response = await axios.get(`${apiBase}/raffles/${route.params.id}`, { headers });
        raffle.value = response.data.raffle;
    } catch (err) {
        error.value = "Sorteo no encontrado o error al cargar.";
    } finally {
        loading.value = false;
    }
};

const buyTickets = async () => {
    if (!isAuthenticated.value) {
        alert("Debes iniciar sesión para comprar boletos.");
        router.push('/login');
        return;
    }

    submittionLoading.value = true;
    try {
        const token = localStorage.getItem('token');
        await axios.post(`${apiBase}/raffles/${route.params.id}/enter`, {
            quantity: quantity.value
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });

        alert("¡Has comprado tus boletos con éxito!");
        fetchRaffle(); // Reload stats
    } catch (err) {
        const msg = err.response?.data?.message || "Hubo un error al comprar los boletos.";
        alert(msg);
    } finally {
        submittionLoading.value = false;
    }
};

onMounted(() => {
    fetchRaffle();
});
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    <div v-if="loading" class="text-center py-16 text-gray-500">Cargando detalles del sorteo...</div>
    <div v-else-if="error" class="text-center py-16 text-red-400 bg-red-500/10 border border-red-500/20 rounded-xl">{{ error }}</div>
    
    <div v-else-if="raffle" class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-16">
        
        <!-- Left: Image frame layout -->
        <div class="space-y-4">
            <div class="aspect-square bg-gamer-card border border-gray-800 rounded-3xl overflow-hidden shadow-2xl flex items-center justify-center relative group">
                <img v-if="raffle.product?.image_url" :src="raffle.product.image_url" :alt="raffle.title" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                <div v-else class="flex flex-col items-center text-gray-600 text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 opacity-10 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 009.5 3H9a2 2 0 00-2 2v1h2m3 2h1a2 2 0 012 2v1h2m-5-3a3 3 0 01-3 3H7m5-3a3 3 0 00-3 3H5a2 2 0 00-2 2v5a2 2 0 002 2h2m10-7h2a2 2 0 012 2v5a2 2 0 01-2 2h-2"/></svg>
                    <span>Sin imagen de producto</span>
                </div>
                
                <span v-if="raffle.is_active" class="absolute top-4 left-4 bg-neon-purple/20 border border-neon-purple/50 text-neon-purple text-xs font-black uppercase px-2.5 py-1 rounded-md shadow-neon-purple/30">SORTEO EN VIVO</span>
            </div>
        </div>

        <!-- Right: Detail interaction column -->
        <div class="flex flex-col">
            <div class="mb-6">
                <span class="text-xs font-bold text-neon-purple uppercase tracking-widest">Sorteo Especial de Colección</span>
                <h1 class="text-3xl md:text-5xl font-black mt-2 mb-4 tracking-tight leading-tight text-white">{{ raffle.title }}</h1>
                
                <p class="text-gray-400 text-sm md:text-base leading-relaxed mb-6">{{ raffle.description }}</p>
                
                <div v-if="raffle.product" class="p-3 bg-black/20 border border-gray-800 rounded-xl inline-flex items-center gap-2 mb-4">
                    <span class="text-xs text-gray-500">Producto asociado:</span>
                    <span class="text-xs text-neon-blue font-bold">{{ raffle.product.name }}</span>
                </div>
            </div>

            <!-- Ticket Pricing Summary frame -->
            <div class="bg-gamer-card border border-gray-800 rounded-2xl p-6 mb-8 shadow-xl shadow-neon-purple/5">
                <div class="flex justify-between items-baseline mb-4 border-b border-gray-800 pb-4">
                    <span class="text-xs font-bold text-gray-400">PRECIO POR TICKET</span>
                    <p class="text-3xl font-black text-neon-green">${{ raffle.ticket_price }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6 text-xs border-b border-gray-800 pb-4 text-gray-400">
                    <div>
                        <span class="block text-gray-600 mb-1">Tickets Vendidos:</span>
                        <span class="text-white font-black">{{ raffle.total_entries }} <span v-if="raffle.max_entries">/ {{ raffle.max_entries }}</span></span>
                    </div>
                    <div>
                        <span class="block text-gray-600 mb-1">Cierra en:</span>
                        <span class="text-neon-blue font-bold">{{ raffle.time_left }}</span>
                    </div>
                </div>

                <!-- Personal user analytics stats context -->
                <div v-if="isAuthenticated && raffle.user_entries !== undefined" class="bg-gradient-to-r from-neon-purple/10 to-transparent border border-neon-purple/20 rounded-xl p-4 mb-6">
                    <h4 class="text-xs font-bold text-gray-300 uppercase mb-2">Tus Estadísticas</h4>
                    <div class="flex justify-between items-center text-xs">
                        <div>
                            <span class="text-gray-500">Tus Entradas:</span>
                            <span class="text-neon-purple font-black ml-1">{{ raffle.user_entries }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Probabilidad de ganar:</span>
                            <span class="text-neon-purple font-black ml-1">{{ raffle.user_chance }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Buy controls if active -->
                <div v-if="raffle.is_active" class="flex flex-col gap-4">
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-gray-400 uppercase">Cantidad:</span>
                        <div class="flex items-center bg-gray-900 border border-gray-800 rounded-xl px-2 py-1">
                            <button @click="quantity > 1 ? quantity-- : null" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white">-</button>
                            <span class="w-8 text-center text-sm font-bold">{{ quantity }}</span>
                            <button @click="quantity++" class="w-6 h-6 flex items-center justify-center text-gray-400 hover:text-white">+</button>
                        </div>
                        <span class="text-xs text-gray-500 ml-auto">Total: <span class="text-neon-green font-bold">${{ raffle.ticket_price * quantity }}</span></span>
                    </div>

                    <button @click="buyTickets" :disabled="submittionLoading" class="w-full bg-gradient-to-r from-neon-purple to-neon-blue hover:from-neon-purple/90 hover:to-neon-blue/90 text-white py-3 rounded-xl font-black text-sm uppercase tracking-wider transition duration-300 shadow-neon-purple/20 flex items-center justify-center gap-2 disabled:opacity-50">
                        <span v-if="submittionLoading">Procesando...</span>
                        <span v-else class="flex items-center gap-2">
                             Adquirir Tickets
                        </span>
                    </button>
                </div>
                <div v-else class="text-center py-2 bg-gray-900/50 border border-gray-800 rounded-xl text-red-400 text-sm font-bold">
                    Sorteo Finalizado
                </div>
            </div>

            <!-- Rules checklist frame -->
            <div class="border border-gray-800 rounded-2xl p-6 bg-gamer-card/30">
                <h4 class="text-xs font-black text-gray-300 uppercase mb-3">Términos y Condiciones:</h4>
                <ul class="space-y-1.5 text-xs text-gray-500">
                    <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-purple rounded-full"></div> El sorteo se realiza automáticamente en la fecha indicada.</li>
                    <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-purple rounded-full"></div> Puedes comprar múltiples tickets para aumentar tus probabilidades.</li>
                    <li class="flex items-center gap-2"><div class="w-1 h-1 bg-neon-purple rounded-full"></div> Transacción no reembolsable una vez emitidos los boletos.</li>
                </ul>
            </div>
        </div>
    </div>
  </div>
</template>
