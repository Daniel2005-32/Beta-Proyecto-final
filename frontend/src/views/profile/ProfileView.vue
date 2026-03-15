<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
const user = ref({});
const activeTab = ref('info'); // 'info', 'orders', 'auctions', 'raffles', 'addresses'

const orders = ref([]);
const raffles = ref([]);
const addresses = ref([]);
const auctions = ref([]);

const oldPassword = ref('');
const newPassword = ref('');
const newPasswordConfirm = ref('');

const successMsg = ref('');
const errorMsg = ref('');

const fetchData = async () => {
    try {
        const token = localStorage.getItem('token');
        const headers = { Authorization: `Bearer ${token}` };
        
        // Cargar Órdenes, Sorteos, Direcciones y Subastas paralelamente
        const [ordRes, rafRes, addRes, aucRes] = await Promise.all([
            axios.get(`${apiBase}/orders`, { headers }).catch(() => ({ data: { orders: [] } })),
            axios.get(`${apiBase}/raffles`, { headers }).catch(() => ({ data: { raffles: [] } })),
            axios.get(`${apiBase}/addresses`, { headers }).catch(() => ({ data: { addresses: [] } })),
            axios.get(`${apiBase}/auctions`, { headers }).catch(() => ({ data: { activeAuctions: { data: [] } } }))
        ]);

        orders.value = ordRes.data.orders || [];
        raffles.value = (rafRes.data.raffles || []).filter(r => r.user_entries > 0);
        addresses.value = addRes.data.addresses || [];
        
        // Filtrar subastas donde soy el ganador actual (mejor postor)
        const allAuctions = aucRes.data.activeAuctions?.data || [];
        auctions.value = allAuctions.filter(auc => auc.auctionWinner?.id === user.value.id);
    } catch (e) {
        console.error("Error loading parallel profile data", e);
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
            email: user.value.email
        }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        successMsg.value = "Perfil actualizado correctamente.";
        user.value = res.data.user;
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
        errorMsg.value = err.response?.data?.error || "Error al cambiar la contraseña. Verifica tus datos.";
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-7xl text-white">
    
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
                <p class="text-xs text-gray-500 mb-6">{{ user.email }}</p>

                <nav class="flex flex-col gap-1 text-left">
                    <button @click="activeTab = 'info'" :class="{'bg-neon-blue/10 border-l-2 border-neon-blue text-neon-blue': activeTab === 'info', 'text-gray-400 hover:text-white': activeTab !== 'info'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Datos Básicos</button>
                    <button @click="activeTab = 'orders'" :class="{'bg-neon-blue/10 border-l-2 border-neon-blue text-neon-blue': activeTab === 'orders', 'text-gray-400 hover:text-white': activeTab !== 'orders'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Mis Pedidos ({{ orders.length }})</button>
                    <button @click="activeTab = 'auctions'" :class="{'bg-neon-green/10 border-l-2 border-neon-green text-neon-green': activeTab === 'auctions', 'text-gray-400 hover:text-white': activeTab !== 'auctions'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Mis Subastas ({{ auctions.length }})</button>
                    <button @click="activeTab = 'raffles'" :class="{'bg-neon-purple/10 border-l-2 border-neon-purple text-neon-purple': activeTab === 'raffles', 'text-gray-400 hover:text-white': activeTab !== 'raffles'}" class="px-4 py-2 text-xs font-bold uppercase tracking-wider rounded-r-lg transition">Mis Sorteos ({{ raffles.length }})</button>
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
                                <h4 class="text-sm font-bold text-gray-200 mt-0.5">Total: <span class="text-neon-green">${{ order.total }}</span></h4>
                                <p class="text-[10px] text-gray-500 mt-1">Fecha: {{ new Date(order.created_at).toLocaleDateString() }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-gray-800 border border-gray-700 text-gray-300 text-[10px] px-2 py-0.5 rounded-md uppercase">{{ order.status }}</span>
                                <button class="text-[10px] text-neon-blue hover:underline">Detalles</button>
                            </div>
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
                                <p class="text-[10px] text-gray-500 mt-1">Tu Puja Actual: <span class="text-neon-green font-black">${{ auc.price }}</span></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <router-link :to="`/auctions/${auc.id}`" class="text-[10px] text-neon-blue hover:underline">Ver Subasta</router-link>
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
                                <span class="text-sm font-black text-neon-purple">{{ r.user_entries }}</span>
                                <span class="block text-[9px] text-gray-600 mt-0.5">Prob: {{ r.user_chance }}%</span>
                            </div>
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
  </div>
</template>
