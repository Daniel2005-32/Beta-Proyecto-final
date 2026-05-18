<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LoadingState from '../../components/LoadingState.vue';
import { useRouter } from 'vue-router';
import { store } from '../../utils/store';
import { translateLaravelErrors } from '../../utils/errorTranslator';

const router = useRouter();
import { apiBase } from '@/utils/api';
const user = ref({});
const activeTab = ref('info'); // 'info', 'orders', 'auctions', 'raffles', 'addresses'
const loading = ref(true);

const orders = ref([]);
const ordersMeta = ref({ current_page: 1, last_page: 1 });
const raffles = ref([]);
const addresses = ref([]);
const auctions = ref([]);
const coupons = ref([]);

const oldPassword = ref('');
const newPassword = ref('');
const newPasswordConfirm = ref('');

const successMsg = ref('');
const errorMsg = ref('');
const selectedOrder = ref(null);



const fetchOrders = async (page = 1) => {
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get(`${apiBase}/orders?page=${page}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        orders.value = res.data.orders.data;
        ordersMeta.value = {
            current_page: res.data.orders.current_page,
            last_page: res.data.orders.last_page
        };
    } catch (err) {
        console.error("Error fetching orders", err);
    }
};

const fetchData = async () => {

    try {
        const token = localStorage.getItem('token');
        const headers = { Authorization: `Bearer ${token}` };
        
        // Cargar Sorteos, Direcciones, Subastas y Cupones paralelamente
        const [rafRes, addRes, aucRes, cpnRes] = await Promise.all([
            axios.get(`${apiBase}/raffles`, { headers }).catch(() => ({ data: { raffles: [] } })),
            axios.get(`${apiBase}/addresses`, { headers }).catch(() => ({ data: { addresses: [] } })),
            axios.get(`${apiBase}/auctions`, { headers }).catch(() => ({ data: { activeAuctions: { data: [] } } })),
            axios.get(`${apiBase}/my-coupons`, { headers }).catch(() => ({ data: { coupons: [] } }))
        ]);

        fetchOrders(); // Nueva carga paginada
        raffles.value = (rafRes.data.raffles || []).filter(r => r.user_entries > 0);
        addresses.value = addRes.data.addresses || [];
        coupons.value = cpnRes.data.coupons || [];
        
        // Filtrar subastas donde soy el ganador actual (mejor postor)
        const allAuctions = aucRes.data.activeAuctions?.data || [];
        auctions.value = allAuctions.filter(auc => auc.auctionWinner?.id === user.value.id);
    } catch (e) {
        console.error("Error loading parallel profile data", e);
    } finally {
        loading.value = false;
    }
};

onMounted(async () => {
    try {
        const token = localStorage.getItem('token');
        if (!token) {
            router.push('/login');
            return;
        }
        
        const response = await axios.get(`${apiBase}/profile`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        user.value = response.data.user;
        
        // Cargar todo lo demás
        fetchData();
    } catch (err) {
        if(err.response?.status === 401) {
            localStorage.removeItem('token');
            router.push('/login');
        }
    }
});

const updateProfile = async () => {
    successMsg.value = '';
    errorMsg.value = '';
    try {
        const token = localStorage.getItem('token');
        const res = await axios.put(`${apiBase}/profile`, {
            name: user.value.name,
            email: user.value.email,
            show_censored_content: user.value.show_censored_content
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        successMsg.value = "Perfil actualizado correctamente.";
        user.value = res.data.user;
        store.updateCensorship(user.value.show_censored_content);
    } catch (err) {
        errorMsg.value = "Error al actualizar perfil.";
    }
};

const changePassword = async () => {
    successMsg.value = '';
    errorMsg.value = '';
    try {
        const token = localStorage.getItem('token');
        await axios.put(`${apiBase}/profile/password`, {
            current_password: oldPassword.value,
            new_password: newPassword.value,
            new_password_confirmation: newPasswordConfirm.value
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        successMsg.value = "Contraseña cambiada exitosamente.";
        oldPassword.value = '';
        newPassword.value = '';
        newPasswordConfirm.value = '';
    } catch (err) {
        errorMsg.value = translateLaravelErrors(err.response?.data?.error || err.response?.data?.message || "Error al cambiar la contraseña. Verifica tus datos.");
    }
};

const downloadInvoice = async (orderId) => {
    try {
        const token = localStorage.getItem('token');
        const response = await axios.get(`${apiBase}/orders/${orderId}/invoice`, {
            headers: { Authorization: `Bearer ${token}` },
            responseType: 'blob'
        });
        
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `factura_${orderId}.pdf`);
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (err) {
        console.error("Error downloading invoice", err);
        store.notify("Error al descargar la factura", 'error');
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    
    <LoadingState v-if="loading" />

    <template v-else>
        <div v-if="successMsg" class="mb-4 bg-neon-green/10 border border-neon-green/30 text-neon-green p-3 rounded-xl text-xs">{{ successMsg }}</div>
        <div v-if="errorMsg" class="mb-4 bg-red-500/10 border border-red-500/30 text-red-500 p-3 rounded-xl text-xs">{{ errorMsg }}</div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Sidebar Navigation -->
        <aside class="col-span-1">
            <div class="bg-gamer-card border border-gray-800 rounded-3xl p-6 text-center">
                <div class="w-16 h-16 bg-gradient-to-r from-neon-purple to-neon-blue rounded-full mx-auto mb-3 flex items-center justify-center font-black text-xl shadow-neon-purple/20">
                    {{ user.name ? user.name[0].toUpperCase() : 'U' }}
                </div>
                <h3 class="font-bold text-base text-gray-200">{{ user.name }}</h3>
                <p class="text-xs text-gray-500 mb-2">{{ user.email }}</p>

                <!-- Status / Rango -->
                <div class="mb-4 flex flex-col items-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/5 border border-white/10 rounded-full text-[10px] font-black uppercase tracking-widest select-none transition-transform" :class="{
                        'text-orange-400 border-orange-400/30': user.rank_name === 'Bronce',
                        'text-gray-300 border-gray-300/30': user.rank_name === 'Plata',
                        'text-yellow-400 border-yellow-400/30': user.rank_name === 'Oro',
                        'text-cyan-400 border-cyan-400/30 shadow-neon-cyan/20': user.rank_name === 'Platino'
                    }">
                        <i class="fas fa-crown text-[8px]"></i>
                        Rango: {{ user.rank_name }}
                    </span>
                    
                    <!-- Progress Bar to Next Rank -->
                    <div class="w-full mt-3 px-4">
                        <div class="flex justify-between text-[8px] font-black uppercase text-gray-600 mb-1">
                            <span>Progreso Nivel</span>
                            <span>{{ Math.round(user.rank_progress || 0) }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-800 rounded-full overflow-hidden border border-white/5">
                            <div class="h-full bg-gradient-to-r from-neon-purple to-neon-blue transition-all duration-1000 shadow-neon-blue/20" :style="{ width: (user.rank_progress || 0) + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- Points Card -->
                <div class="mb-6 bg-black/20 border border-gray-800 rounded-2xl p-4 flex flex-col items-center gap-2">
                    <span class="text-[9px] uppercase font-black text-gray-500 tracking-widest">Saldo Soul Points</span>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-coins text-neon-blue"></i>
                        <span class="text-2xl font-black text-white italic tracking-tighter">{{ user.points || 0 }}</span>
                    </div>
                    <router-link to="/games" class="text-[9px] text-neon-blue hover:underline uppercase font-bold">Ganar más puntos</router-link>
                </div>

                <nav class="flex flex-col gap-1 text-left">
                    <button @click="activeTab = 'info'" :class="{'bg-neon-blue/10 border-l-2 border-neon-blue text-neon-blue': activeTab === 'info', 'text-gray-400 hover:text-white': activeTab !== 'info'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Datos Básicos</button>
                    <button @click="activeTab = 'orders'" :class="{'bg-neon-blue/10 border-l-2 border-neon-blue text-neon-blue': activeTab === 'orders', 'text-gray-400 hover:text-white': activeTab !== 'orders'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Mis Pedidos ({{ orders.length }})</button>
                    <button @click="activeTab = 'auctions'" :class="{'bg-neon-green/10 border-l-2 border-neon-green text-neon-green': activeTab === 'auctions', 'text-gray-400 hover:text-white': activeTab !== 'auctions'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Mis Subastas ({{ auctions.length + (user.won_auctions?.length || 0) }})</button>
                    <button @click="activeTab = 'raffles'" :class="{'bg-neon-purple/10 border-l-2 border-neon-purple text-neon-purple': activeTab === 'raffles', 'text-gray-400 hover:text-white': activeTab !== 'raffles'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Mis Sorteos ({{ raffles.length + (user.won_raffles?.length || 0) }})</button>

                    <button @click="activeTab = 'coupons'" :class="{'bg-yellow-400/10 border-l-2 border-yellow-400 text-yellow-400': activeTab === 'coupons', 'text-gray-400 hover:text-white': activeTab !== 'coupons'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Mis Cupones ({{ coupons.length }})</button>

                    <button @click="activeTab = 'addresses'" :class="{'bg-neon-blue/10 border-l-2 border-neon-blue text-neon-blue': activeTab === 'addresses', 'text-gray-400 hover:text-white': activeTab !== 'addresses'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Direcciones ({{ addresses.length }})</button>
                    


                    <router-link v-if="user.is_admin" to="/admin" class="mt-4 px-4 py-2 bg-gradient-to-r from-neon-purple to-neon-blue text-white rounded-xl font-bold text-center text-xs uppercase shadow-neon-purple hover:scale-105 transition duration-300">Admin Panel</router-link>
                </nav>
            </div>
        </aside>

        <!-- Main Content Panel Area -->
        <main class="col-span-1 md:col-span-3">
            <div class="bg-gamer-card border border-gray-800 rounded-3xl p-6 h-full">

                <!-- Tab: Info Account General panel -->
                <div v-if="activeTab === 'info'" class="space-y-8">
                    <div>
                        <h2 class="text-xl font-black mb-1 text-neon-blue">Datos de la Cuenta</h2>
                        <p class="text-xs text-gray-400 mb-6">Actualiza tu información personal de contacto.</p>
                        
                        <form @submit.prevent="updateProfile" class="max-w-md space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Nombre de usuario</label>
                                <input v-model="user.name" type="text" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-neon-blue/50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Correo Electrónico</label>
                                <input v-model="user.email" type="email" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-neon-blue/50" required>
                            </div>
                            <button type="submit" class="bg-neon-blue text-gamer-dark font-black text-xs uppercase px-6 py-2.5 rounded-xl shadow-neon-blue hover:scale-105 transition cursor-pointer">Guardar Cambios</button>
                        </form>
                    </div>

                    <div class="border-t border-gray-800/80 pt-6">
                        <h2 class="text-xl font-black mb-1 text-neon-green">Privacidad y Seguridad</h2>
                        <p class="text-xs text-gray-400 mb-6">Gestiona tus preferencias de visualización.</p>

                        <div class="max-w-md bg-black/20 border border-gray-800 rounded-2xl p-4 flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-200">Quitar Censura de Imágenes</h4>
                                <p class="text-[10px] text-gray-500">Si activas esto, las imágenes marcadas como sensibles se verán sin desenfoque.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" v-model="user.show_censored_content" @change="updateProfile" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neon-green peer-checked:shadow-neon-green/50"></div>
                            </label>
                        </div>
                    </div>

                    <div class="border-t border-gray-800/80 pt-6">
                        <h2 class="text-xl font-black mb-1 text-neon-purple">Seguridad</h2>
                        <p class="text-xs text-gray-400 mb-6">Gestiona la contraseña de tu cuenta gamer.</p>

                        <form @submit.prevent="changePassword" class="max-w-md space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Contraseña Actual</label>
                                <input v-model="oldPassword" type="password" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-neon-purple/50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Nueva Contraseña</label>
                                <input v-model="newPassword" type="password" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-neon-purple/50" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-400 mb-1 uppercase">Confirmar Contraseña</label>
                                <input v-model="newPasswordConfirm" type="password" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-neon-purple/50" required>
                            </div>
                            <button type="submit" class="bg-neon-purple text-white font-black text-xs uppercase px-6 py-2.5 rounded-xl shadow-neon-purple hover:scale-105 transition cursor-pointer">Actualizar Contraseña</button>
                        </form>
                    </div>
                </div>

                <!-- Tab: Orders List -->
                <div v-if="activeTab === 'orders'">
                    <h2 class="text-xl font-black mb-1 text-neon-blue">Mis Pedidos</h2>
                    <p class="text-xs text-gray-400 mb-6">Lista de tus últimas compras realizadas.</p>

                    <div v-if="orders.length === 0" class="text-center py-12 text-gray-600 text-sm bg-black/10 rounded-2xl border border-gray-800">
                        No has realizado ninguna compra todavía.
                    </div>
                    <div v-else class="space-y-4">
                        <!-- Order row card template -->
                        <div v-for="order in orders" :key="order.id" class="border border-gray-800 bg-gray-900/40 rounded-xl p-4 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                            <div>
                                <span class="text-xs font-bold text-neon-blue">#{{ order.id }}</span>
                                <h4 class="text-sm font-bold text-gray-200 mt-0.5">Total: <span class="text-white">{{order.total}}€</span></h4>
                                <p v-if="order.tax_type" class="text-[9px] text-gray-500 italic">(Inc. {{ order.tax_type }})</p>
                                <p class="text-[10px] text-gray-500 mt-1">Fecha: {{ new Date(order.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-800 border border-gray-700 text-gray-300 text-[10px] px-2 py-0.5 rounded-md uppercase">
                                    {{ order.status === 'pending' ? 'Pendiente' : (order.status === 'completed' ? 'Completado' : (order.status === 'cancelled' ? 'Cancelado' : order.status)) }}
                                </span>
                                <button @click="selectedOrder = order" class="text-[10px] text-neon-blue hover:underline cursor-pointer">Detalles</button>
                            </div>
                        </div>

                        <!-- Pagination Controls -->
                        <div v-if="ordersMeta.last_page > 1" class="mt-8 flex justify-center items-center gap-4">
                            <button @click="fetchOrders(ordersMeta.current_page - 1)" 
                                    :disabled="ordersMeta.current_page === 1"
                                    class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                                Anterior
                            </button>
                            <span class="text-[10px] font-black uppercase text-gray-400">Página {{ ordersMeta.current_page }} de {{ ordersMeta.last_page }}</span>
                            <button @click="fetchOrders(ordersMeta.current_page + 1)" 
                                    :disabled="ordersMeta.current_page === ordersMeta.last_page"
                                    class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                                Siguiente
                            </button>
                        </div>
                    </div>

                </div>

                <!-- Tab: Auctions List -->
                <div v-if="activeTab === 'auctions'">
                    <h2 class="text-xl font-black mb-1 text-neon-green">Mis Subastas</h2>
                    <p class="text-xs text-gray-400 mb-6">Subastas donde eres el mayor postor actual.</p>

                    <div v-if="auctions.length === 0" class="text-center py-12 text-gray-600 text-sm bg-black/10 rounded-2xl border border-gray-800">
                        No estás liderando ninguna subasta activa.
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="auc in auctions" :key="auc.id" class="border border-gray-800 bg-gray-900/40 rounded-xl p-4 flex flex-col sm:flex-row justify-between hover:border-neon-green/30 transition gap-2">
                            <div>
                                <h4 class="text-sm font-bold text-gray-200">{{ auc.name }}</h4>
                                <p class="text-[10px] text-gray-500 mt-1">Tu Puja Actual: <span class="text-white font-black">{{auc.price}}€</span></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <router-link :to="`/auctions/${auc.id}`" class="text-[10px] text-neon-blue hover:underline">Ver Subasta</router-link>
                            </div>
                        </div>
                    </div>

                    <!-- Subastas Ganadas -->
                    <div v-if="user.won_auctions?.length > 0" class="mt-8 border-t border-gray-800 pt-6">
                        <h4 class="text-md font-black text-neon-green mb-3 flex items-center gap-2">
                             🏆 Subastas Ganadas
                        </h4>
                        <div class="space-y-4">
                            <div v-for="auc in user.won_auctions" :key="'won-'+auc.id" class="border border-neon-green bg-green-500/10 shadow-lg shadow-green-500/20 rounded-xl p-4 flex flex-col sm:flex-row justify-between hover:scale-[1.01] transition duration-300 gap-2">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-200">{{ auc.name }}</h4>
                                    <p class="text-[10px] text-gray-400 mt-1">Precio Final: <span class="text-white font-black">{{auc.price}}€</span></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="bg-green-600/20 text-green-400 text-[9px] px-2 py-0.5 rounded-full uppercase">Completada</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tab: Raffles List -->
                <div v-if="activeTab === 'raffles'">
                    <h2 class="text-xl font-black mb-1 text-neon-purple">Mis Sorteos</h2>
                    <p class="text-xs text-gray-400 mb-6">Sorteos en los que estás participando activos.</p>

                    <div v-if="raffles.length === 0" class="text-center py-12 text-gray-600 text-sm bg-black/10 rounded-2xl border border-gray-800">
                        No estás registrado en ningún sorteo activo actualmente.
                    </div>
                    <div v-else class="space-y-4">
                        <!-- Raffle row card template -->
                        <div v-for="r in raffles" :key="r.id" class="border border-gray-800 bg-gray-900/40 rounded-xl p-4 flex flex-col sm:flex-row justify-between hover:border-neon-purple/30 transition">
                            <div>
                                <h4 class="text-sm font-bold text-gray-200">{{ r.title }}</h4>
                                <p class="text-[10px] text-gray-500 mt-1">Acaba en: <span class="text-neon-blue">{{ r.time_left }}</span></p>
                            </div>
                            <div class="text-right sm:text-center">
                                <span class="block text-[10px] text-gray-500">Tus Entradas:</span>
                                <span class="text-sm font-black text-white">{{ r.user_entries }}</span>
                                <span class="block text-[9px] text-gray-600 mt-0.5">Prob: {{ r.user_chance }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sorteos Ganados -->
                    <div v-if="user.won_raffles?.length > 0" class="mt-8 border-t border-gray-800 pt-6">
                        <h4 class="text-md font-black text-neon-purple mb-3 flex items-center gap-2">
                             🏆 Sorteos Ganados
                        </h4>
                        <div class="space-y-4">
                            <div v-for="r in user.won_raffles" :key="'won-'+r.id" class="border border-neon-purple bg-purple-500/10 shadow-lg shadow-purple-500/20 rounded-xl p-4 flex flex-col sm:flex-row justify-between hover:scale-[1.01] transition duration-300 gap-2">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-200">{{ r.title }}</h4>
                                    <p class="text-[10px] text-gray-400 mt-1">Sorteo finalizado</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="bg-purple-600/20 text-purple-400 text-[9px] px-2 py-0.5 rounded-full uppercase">¡Ganador!</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tab: Coupons List -->
                <div v-if="activeTab === 'coupons'">
                    <h2 class="text-xl font-black mb-1 text-yellow-400">Mis Cupones</h2>
                    <p class="text-xs text-gray-400 mb-6">Gestiona tus cupones de descuento activos.</p>

                    <div v-if="coupons.length === 0" class="text-center py-12 text-gray-600 text-sm bg-black/10 rounded-2xl border border-gray-800">
                        No tienes cupones de descuento activos actualmente.
                    </div>
                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div v-for="cp in coupons" :key="cp.id" class="border-2 border-dashed border-gray-800 bg-gray-900/40 rounded-2xl p-4 relative group hover:border-yellow-400/50 transition">
                            <div class="flex justify-between items-start mb-2">
                                <span class="bg-yellow-400 text-gamer-dark px-2 py-0.5 rounded text-[10px] font-black uppercase italic tracking-tighter">-{{ cp.value }}{{ cp.type === 'percentage' ? '%' : '€' }}</span>
                                <span v-if="cp.expires_at" class="text-[9px] text-gray-500 uppercase font-bold">Vence: {{ new Date(cp.expires_at).toLocaleDateString() }}</span>
                            </div>
                            <h4 class="text-lg font-black text-white tracking-widest uppercase py-2 bg-black/30 rounded-lg text-center border border-gray-800 group-hover:border-yellow-400/30 transition select-all">
                                {{ cp.code }}
                            </h4>
                            <p class="text-[9px] text-gray-500 mt-2 text-center uppercase font-bold">Min. Compra: <span class="text-gray-300">{{ cp.min_purchase }}€</span></p>
                        </div>
                    </div>
                </div>

                <!-- Tab: Addresses list index fallback style triggers crud -->
                <div v-if="activeTab === 'addresses'">
                    <div class="flex justify-between items-center mb-6 border-b border-gray-800 pb-2">
                        <div>
                            <h2 class="text-xl font-black text-neon-blue">Mis Direcciones</h2>
                            <p class="text-xs text-gray-400 mt-1">Libreta de direcciones de envío.</p>
                        </div>
                        <router-link to="/profile/addresses" class="bg-neon-blue/20 border border-neon-blue/30 text-neon-blue text-[10px] font-bold px-3 py-1.5 rounded-xl hover:bg-neon-blue hover:text-gamer-dark shadow-neon-blue/10 transition cursor-pointer">Gestionar Direcciones</router-link>
                    </div>

                    <div v-if="addresses.length === 0" class="text-center py-12 text-gray-600 text-sm bg-black/10 rounded-2xl border border-gray-800">
                        No posees direcciones registradas todavía.
                    </div>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div v-for="addr in addresses" :key="addr.id" class="border border-gray-800 bg-gray-900/40 rounded-xl p-4 flex flex-col relative" :class="{'border-neon-blue/50 bg-neon-blue/5': addr.is_default}">
                            <div class="flex justify-between">
                                <h4 class="text-sm font-bold text-gray-200">{{ addr.street_address }}</h4>
                                <span v-if="addr.is_default" class="bg-neon-blue/20 border border-neon-blue/50 text-neon-blue text-[9px] px-1.5 py-0.5 rounded">Por defecto</span>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1">{{ addr.city }}, {{ addr.state }}</p>
                            <p class="text-[10px] text-gray-500 mt-0.5">C.P: {{ addr.postal_code }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal Detalles del Pedido -->
    <div v-if="selectedOrder" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
        <div class="bg-gamer-card border border-gray-800 rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-lg font-black text-neon-blue">Detalles del Pedido #{{ selectedOrder.id }}</h3>
                <button @click="selectedOrder = null" class="text-gray-500 hover:text-white text-2xl cursor-pointer">&times;</button>
            </div>
            <div class="p-6 max-h-[60vh] overflow-y-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="border-b border-gray-800 text-gray-400 text-xs">
                            <th class="pb-2">Producto</th>
                            <th class="pb-2 text-center">Cant</th>
                            <th class="pb-2 text-right">Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in selectedOrder.items" :key="item.id" class="border-b border-gray-800/30">
                            <td class="py-3 text-gray-200 font-bold text-xs">{{ item.product?.full_name || item.product?.name || 'N/A' }}</td>
                            <td class="py-3 text-center text-gray-400 text-xs">{{ item.quantity }}</td>
                             <td class="py-3 text-right text-white text-xs font-bold">{{ item.price }}€</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-6 border-t border-gray-800 bg-gray-900/40 space-y-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400">Subtotal:</span>
                    <span class="text-gray-200 font-bold">{{ selectedOrder.subtotal || selectedOrder.total }}€</span>
                </div>
                <div v-if="selectedOrder.discount_amount" class="flex justify-between items-center text-xs">
                    <span class="text-neon-red font-bold uppercase tracking-widest text-[9px]">Descuento aplicado:</span>
                    <span class="text-neon-red font-bold">-{{ selectedOrder.discount_amount }}€</span>
                </div>
                <div v-if="selectedOrder.tax_amount" class="flex justify-between items-center text-xs">
                    <span class="text-gray-400">Impuestos ({{ selectedOrder.tax_type }}):</span>
                    <span class="text-gray-200 font-bold">{{ selectedOrder.tax_amount }}€</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-800">
                    <span class="text-xs text-gray-400 font-black uppercase">Total pagado:</span>
                     <span class="text-lg font-black text-white">{{ selectedOrder.total }}€</span>
                </div>
                
                <!-- Invoice Action -->
                <div class="pt-4 flex gap-3">
                    <button @click="downloadInvoice(selectedOrder.id)" class="flex-grow bg-neon-blue/20 border border-neon-blue/40 text-neon-blue py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-neon-blue hover:text-gamer-dark transition shadow-neon-blue/10">
                        <i class="fas fa-file-pdf mr-2"></i> Descargar Factura
                    </button>
                    <button @click="selectedOrder = null" class="px-6 bg-gray-800 text-gray-400 py-3 rounded-xl font-bold text-xs uppercase hover:bg-gray-700 transition">Cerrar</button>
                </div>
            </div>
        </div>
    </div>



    </template>
  </div>
</template>
