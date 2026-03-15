<script setup>
import { ref, onMounted, watch, onUnmounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
const products = ref([]);
const categories = ref([]);
const users = ref([]);
const auctions = ref([]);
const raffles = ref([]);
const messages = ref([]);

const loading = ref(true);
const error = ref(null);

const activeTab = ref('products'); // 'products', 'users', 'auctions', 'raffles', 'chat'
const newMessage = ref('');
let chatInterval = null;

const showModal = ref(false);
const showRaffleModal = ref(false);
const editingProduct = ref(null);

const form = ref({
    name: '',
    description: '',
    price: '',
    original_price: '',
    stock: '',
    category_id: '',
    is_exclusive: false,
    image: null,
    image_url: ''
});

const raffleForm = ref({
    title: '',
    description: '',
    draw_date: '',
    ticket_price: '',
    max_entries: '',
    product_id: ''
});

const formErrors = ref({});

// Configure axios interceptor for auth token
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

const checkAdmin = async () => {
    try {
        const response = await axios.get(`${apiBase}/me`);
        const user = response.data.user;
        if (!user || !user.is_admin) {
            alert('Acceso denegado. Se requieren permisos de administrador.');
            router.push('/');
        }
    } catch (err) {
        alert('Debes iniciar sesión primero.');
        router.push('/login');
    }
};

const fetchDashboardData = async () => {
    loading.value = true;
    try {
        const [resProducts, resUsers, resRaffles, resAuctions] = await Promise.all([
            axios.get(`${apiBase}/admin/products`),
            axios.get(`${apiBase}/admin/users`).catch(() => ({ data: { users: [] } })),
            axios.get(`${apiBase}/raffles`).catch(() => ({ data: { raffles: [] } })),
            axios.get(`${apiBase}/auctions`).catch(() => ({ data: { activeAuctions: { data: [] } } }))
        ]);

        products.value = resProducts.data.products || [];
        categories.value = resProducts.data.categories || [];
        users.value = resUsers.data.users || [];
        raffles.value = resRaffles.data.raffles || [];
        auctions.value = resAuctions.data.activeAuctions?.data || [];
        
        if (activeTab.value === 'chat') {
            fetchMessages();
        }
    } catch (err) {
        error.value = err.response ? 
            (err.response.data.message || JSON.stringify(err.response.data)) : 
            err.message;
        console.error("Dashboard api error", err.response || err);
    } finally {
        loading.value = false;
    }
};

const fetchMessages = async () => {
    try {
        const res = await axios.get(`${apiBase}/chat`);
        messages.value = res.data;
    } catch (err) {}
};

const sendMessage = async () => {
    if(!newMessage.value.trim()) return;
    try {
        await axios.post(`${apiBase}/chat`, { message: newMessage.value });
        newMessage.value = '';
        fetchMessages();
    } catch (err) {
        alert("Error al enviar mensaje");
    }
};

watch(activeTab, (newTab) => {
    if (newTab === 'chat') {
        fetchMessages();
        chatInterval = setInterval(fetchMessages, 3000);
    } else {
        if(chatInterval) {
            clearInterval(chatInterval);
            chatInterval = null;
        }
    }
});

onUnmounted(() => {
    if (chatInterval) clearInterval(chatInterval);
});

const saveRaffle = async () => {
    try {
        await axios.post(`${apiBase}/admin/raffles`, raffleForm.value);
        alert('Sorteo creado exitosamente.');
        showRaffleModal.value = false;
        raffleForm.value = { title: '', description: '', draw_date: '', ticket_price: '', max_entries: '', product_id: '' };
        fetchDashboardData();
    } catch (err) {
        alert("Error al crear sorteo");
    }
};

const drawWinner = async (raffleId) => {
    if(!confirm("¿Seguro que quieres realizar el sorteo ya?")) return;
    try {
        const res = await axios.post(`${apiBase}/admin/raffles/${raffleId}/draw`);
        alert("¡Sorteo realizado! Ganador: " + res.data.winner.name);
        fetchDashboardData();
    } catch (err) {
        alert(err.response?.data?.message || "Error al realizar sorteo");
    }
};

const toggleUserBan = async (user) => {
    try {
        const res = await axios.post(`${apiBase}/admin/users/${user.id}/ban`);
        alert(res.data.message);
        fetchDashboardData();
    } catch (err) {
        alert("Error al modificar baneo");
    }
};

const extendAuction = async (auctionId) => {
    const hours = prompt("¿Cuántas horas quieres extender la subasta?", "24");
    if (!hours) return;
    try {
        await axios.post(`${apiBase}/auctions/${auctionId}/extend`, { hours });
        alert("Subasta extendida.");
        fetchDashboardData();
    } catch (err) {
        alert("Error al extender subasta");
    }
};

const forceEndAuction = async (auctionId) => {
    if(!confirm("¿Seguro que quieres forzar el fin de esta subasta?")) return;
    try {
        await axios.post(`${apiBase}/auctions/${auctionId}/force-end`);
        alert("Subasta finalizada.");
        fetchDashboardData();
    } catch (err) {
        alert("Error al finalizar subasta");
    }
};

const openModal = (product = null) => {
    formErrors.value = {};
    if (product) {
        editingProduct.value = product;
        form.value = {
            name: product.name,
            description: product.description,
            price: product.price,
            original_price: product.original_price,
            stock: product.stock,
            category_id: product.category_id,
            is_exclusive: product.is_exclusive,
            image: null,
            image_url: product.image && product.image.startsWith('http') ? product.image : ''
        };
    } else {
        editingProduct.value = null;
        form.value = {
            name: '',
            description: '',
            price: '',
            original_price: '',
            stock: '',
            category_id: (categories.value[0]?.id || ''),
            is_exclusive: false,
            image: null,
            image_url: ''
        };
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingProduct.value = null;
};

const handleImageUpload = (event) => {
    form.value.image = event.target.files[0];
    if (form.value.image) {
        form.value.image_url = ''; // Limpiar URL para dar prioridad al archivo
    }
};

const saveProduct = async () => {
    formErrors.value = {};
    
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    
    const cleanPrice = (val) => val ? String(val).replace(',', '.') : '';
    formData.append('price', cleanPrice(form.value.price));
    if (form.value.original_price) formData.append('original_price', cleanPrice(form.value.original_price));
    
    formData.append('stock', form.value.stock);
    formData.append('category_id', form.value.category_id);
    formData.append('is_exclusive', form.value.is_exclusive ? 1 : 0);
    
    if (form.value.image) {
        formData.append('image', form.value.image);
    } else if (form.value.image_url) {
        formData.append('image_url', form.value.image_url);
    }

    try {
        if (editingProduct.value) {
            formData.append('_method', 'PUT'); // Fake PUT for FormData logic in Laravel
            await axios.post(`${apiBase}/products/${editingProduct.value.id}`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            alert('Producto actualizado.');
        } else {
            await axios.post(`${apiBase}/products`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            alert('Producto creado exitosamente.');
        }
        closeModal();
        fetchDashboardData();
    } catch (err) {
        if (err.response && err.response.data.errors) {
            formErrors.value = err.response.data.errors;
        } else {
            const errorMsg = err.response ? JSON.stringify(err.response.data) : err.message;
            alert('Error al guardar el producto: ' + errorMsg);
        }
    }
};

const deleteProduct = async (id) => {
    if (!confirm('¿Estás seguro de eliminar este producto?')) return;
    try {
        await axios.delete(`${apiBase}/products/${id}`);
        fetchDashboardData();
    } catch (err) {
        alert('Error al eliminar producto');
    }
};

onMounted(async () => {
    await checkAdmin();
    fetchDashboardData();
});
</script>

<template>
  <div class="min-h-screen bg-gamer-dark text-gray-200">
      
    <!-- Admin Header -->
    <header class="bg-gamer-card border-b border-gray-800 p-6 shadow-neon-blue">
      <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-3xl font-black tracking-widest uppercase text-neon-blue drop-shadow-lg">
          <i class="fas fa-shield-alt mr-2"></i> Admin Panel
        </h1>
        <router-link to="/" class="text-gray-400 hover:text-white transition cursor-pointer">
            &larr; Volver a la tienda
        </router-link>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8 max-w-7xl">
        <!-- Error / Loading Guards -->
        <div v-if="error" class="bg-red-900/50 border border-red-500 text-white p-4 mb-6 rounded-xl shadow-neon-red/10 text-xs">
            <h4 class="font-bold">Error de Conexión / Acceso</h4>
            <p class="text-gray-300">{{ error }}</p>
        </div>

        <div v-if="loading" class="text-center py-20 animate-pulse text-neon-blue font-bold text-sm">
            Cargando panel de administración...
        </div>

        <!-- Tabs Bar -->
        <div class="flex border-b border-gray-800 mb-8 gap-4 overflow-x-auto text-xs uppercase tracking-wider font-bold">
            <button @click="activeTab = 'products'" :class="{'border-b-2 border-neon-blue text-neon-blue': activeTab === 'products', 'text-gray-500 hover:text-white': activeTab !== 'products'}" class="pb-3 px-1 transition">Productos</button>
            <button @click="activeTab = 'users'" :class="{'border-b-2 border-neon-blue text-neon-blue': activeTab === 'users', 'text-gray-500 hover:text-white': activeTab !== 'users'}" class="pb-3 px-1 transition">Usuarios</button>
            <button @click="activeTab = 'auctions'" :class="{'border-b-2 border-neon-green text-neon-green': activeTab === 'auctions', 'text-gray-500 hover:text-white': activeTab !== 'auctions'}" class="pb-3 px-1 transition">Subastas</button>
            <button @click="activeTab = 'raffles'" :class="{'border-b-2 border-neon-purple text-neon-purple': activeTab === 'raffles', 'text-gray-500 hover:text-white': activeTab !== 'raffles'}" class="pb-3 px-1 transition">Sorteos</button>
            <button @click="activeTab = 'chat'" :class="{'border-b-2 border-neon-purple text-neon-purple': activeTab === 'chat', 'text-gray-500 hover:text-white': activeTab !== 'chat'}" class="pb-3 px-1 transition">Chat Soporte</button>
        </div>

        <!-- Products Tab -->
        <div v-if="activeTab === 'products'">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Inventario de Productos</h3>
                <button @click="openModal()" class="bg-neon-blue text-gamer-dark px-4 py-1.5 rounded-lg text-xs font-black hover:bg-white hover:shadow-neon-blue transition">+ Nuevo Producto</button>
            </div>

            <!-- Products Table -->
            <div class="bg-gamer-card rounded-xl overflow-hidden border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">Foto</th>
                            <th class="p-3 border-b border-gray-800">Nombre</th>
                            <th class="p-3 border-b border-gray-800">Precio</th>
                            <th class="p-3 border-b border-gray-800">Stock</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products" :key="product.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3">
                                <div class="w-10 h-10 bg-gray-900 rounded overflow-hidden flex items-center justify-center">
                                    <img v-if="product.image_url" :src="product.image_url" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="p-3 font-bold text-gray-200 truncate max-w-xs">{{ product.name }}</td>
                            <td class="p-3 text-neon-green font-black">${{ product.price }}</td>
                            <td><span :class="{'text-red-500 font-bold': product.stock === 0}">{{ product.stock }} u.</span></td>
                            <td class="p-3 text-right">
                                <button @click="openModal(product)" class="text-neon-blue hover:underline mr-3 text-xs">Editar</button>
                                <button @click="deleteProduct(product.id)" class="text-red-500 hover:underline text-xs">Borrar</button>
                            </td>
                        </tr>
                        <tr v-if="products.length === 0">
                            <td colspan="5" class="p-4 text-center text-gray-500 text-xs">No hay productos registrados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Users Tab -->
        <div v-else-if="activeTab === 'users'">
            <h3 class="text-xl font-bold text-white mb-6">Gestión de Usuarios</h3>
            <div class="bg-gamer-card rounded-xl overflow-hidden border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">Nombre</th>
                            <th class="p-3 border-b border-gray-800">Email</th>
                            <th class="p-3 border-b border-gray-800">Rol</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3 font-bold text-gray-200">{{ user.name }}</td>
                            <td class="p-3 text-gray-400">{{ user.email }}</td>
                            <td class="p-3">
                                <span v-if="user.is_admin" class="text-neon-purple font-bold">Admin</span>
                                <span v-else class="text-gray-500">User</span>
                            </td>
                            <td class="p-3 text-right">
                                <button v-if="!user.is_admin" @click="toggleUserBan(user)" class="text-xs transition" :class="user.is_banned ? 'text-green-400 hover:underline' : 'text-red-400 hover:underline'">
                                    {{ user.is_banned ? 'Desbanear' : 'Banear' }}
                                </button>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="4" class="p-4 text-center text-gray-500 text-xs">No hay usuarios registrados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Auctions Tab -->
        <div v-else-if="activeTab === 'auctions'">
            <h3 class="text-xl font-bold text-white mb-6">Control de Subastas</h3>
            <div class="bg-gamer-card rounded-xl overflow-hidden border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">Producto</th>
                            <th class="p-3 border-b border-gray-800">Precio Actual</th>
                            <th class="p-3 border-b border-gray-800">Mejor Postor</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="auc in auctions" :key="auc.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3 font-bold text-gray-200">{{ auc.name }}</td>
                            <td class="p-3 text-neon-green font-black">${{ auc.price }}</td>
                            <td class="p-3 text-gray-400">{{ auc.auctionWinner ? auc.auctionWinner.name : 'Sin pujas' }}</td>
                            <td class="p-3 text-right">
                                <button @click="extendAuction(auc.id)" class="text-neon-blue hover:underline mr-3 text-xs">Extender</button>
                                <button @click="forceEndAuction(auc.id)" class="text-red-400 hover:underline text-xs">Forzar Fin</button>
                            </td>
                        </tr>
                        <tr v-if="auctions.length === 0">
                            <td colspan="4" class="p-4 text-center text-gray-500 text-xs">No hay subastas activas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Raffles Tab -->
        <div v-else-if="activeTab === 'raffles'">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Sorteos de Sistema</h3>
                <button @click="showRaffleModal = true" class="bg-neon-purple text-white px-4 py-1.5 rounded-lg text-xs font-black hover:bg-purple-600 shadow-neon-purple transition">+ Crear Sorteo</button>
            </div>
            
            <div class="bg-gamer-card rounded-xl overflow-hidden border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">Sorteo</th>
                            <th class="p-3 border-b border-gray-800">Tickets</th>
                            <th class="p-3 border-b border-gray-800">Estado</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="r in raffles" :key="r.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3 font-bold text-gray-200">{{ r.title }}</td>
                            <td class="p-3 text-gray-400">{{ r.total_entries }} <span v-if="r.max_entries">/ {{ r.max_entries }}</span></td>
                            <td class="p-3">
                                <span v-if="r.status === 'completed'" class="text-gray-500">Finalizado</span>
                                <span v-else class="text-neon-green font-bold">Activo</span>
                            </td>
                            <td class="p-3 text-right">
                                <button v-if="r.status !== 'completed'" @click="drawWinner(r.id)" class="text-neon-purple hover:underline text-xs">Elegir Ganador</button>
                                <span v-else class="text-[10px] text-gray-600">Ganador: <span class="text-white">{{ r.winner ? r.winner.name : 'N/A' }}</span></span>
                            </td>
                        </tr>
                        <tr v-if="raffles.length === 0">
                            <td colspan="4" class="p-4 text-center text-gray-500 text-xs">No hay sorteos creados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Support Chat Tab -->
        <div v-else-if="activeTab === 'chat'" class="flex flex-col h-[500px]">
            <h3 class="text-xl font-bold text-white mb-4">Chat de Soporte (Global)</h3>
            <div class="bg-gamer-card border border-gray-800 rounded-xl flex-1 flex flex-col overflow-hidden shadow-xl">
                <!-- Message Feed -->
                <div class="flex-1 p-4 overflow-y-auto space-y-3 custom-scrollbar">
                    <div v-for="msg in messages" :key="msg.id" class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-xs text-neon-blue">{{ msg.user_name }}</span>
                            <span class="text-[9px] text-gray-500">{{ msg.time }}</span>
                        </div>
                        <p class="text-sm text-gray-200 bg-gray-900 border border-gray-800/50 px-3 py-1.5 rounded-r-xl rounded-bl-xl mt-0.5 inline-block max-w-lg">{{ msg.message }}</p>
                    </div>
                    <div v-if="messages.length === 0" class="text-center py-20 text-gray-600 text-sm">
                        No hay mensajes recientes en la sala.
                    </div>
                </div>

                <!-- Input Row -->
                <form @submit.prevent="sendMessage" class="p-4 border-t border-gray-800 bg-gray-950 flex gap-2">
                    <input v-model="newMessage" type="text" placeholder="Escribe un mensaje de soporte..." class="flex-1 bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-neon-purple transition text-white">
                    <button type="submit" class="bg-neon-purple text-white px-4 py-2 rounded-xl text-xs font-black shadow-neon-purple hover:bg-purple-600 transition">Enviar</button>
                </form>
            </div>
        </div>
    </main>

    <!-- Modal Form CRUD -->
    <div v-if="showModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-gamer-card border border-gray-700 w-full max-w-2xl rounded-xl shadow-2xl shadow-neon-blue/20 flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">{{ editingProduct ? 'Editar Producto' : 'Crear Producto' }}</h3>
                <button @click="closeModal" class="text-gray-400 hover:text-white">&times;</button>
            </div>
            
            <div class="p-6 overflow-y-auto custom-scrollbar flex-1">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs uppercase text-gray-400 mb-1">Nombre</label>
                        <input type="text" v-model="form.name" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                        <span v-if="formErrors.name" class="text-red-500 text-xs mt-1 block">{{ formErrors.name[0] }}</span>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs uppercase text-gray-400 mb-1">Descripción</label>
                        <textarea v-model="form.description" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition"></textarea>
                        <span v-if="formErrors.description" class="text-red-500 text-xs mt-1 block">{{ formErrors.description[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Precio Actual</label>
                        <input type="number" step="0.01" v-model="form.price" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                        <span v-if="formErrors.price" class="text-red-500 text-xs mt-1 block">{{ formErrors.price[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Precio Original (Rebaja)</label>
                        <input type="number" step="0.01" v-model="form.original_price" placeholder="Opcional" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Stock</label>
                        <input type="number" v-model="form.stock" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                        <span v-if="formErrors.stock" class="text-red-500 text-xs mt-1 block">{{ formErrors.stock[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Categoría</label>
                        <select v-model="form.category_id" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <span v-if="formErrors.category_id" class="text-red-500 text-xs mt-1 block">{{ formErrors.category_id[0] }}</span>
                    </div>

                    <div class="col-span-2 flex items-center justify-between bg-gray-900/50 p-4 rounded border border-gray-800">
                        <div>
                            <label class="text-sm text-gray-300 font-bold">¿Producto Exclusivo?</label>
                            <p class="text-xs text-gray-500">Se mostrará en la sección top del Homepage.</p>
                        </div>
                        <input type="checkbox" v-model="form.is_exclusive" class="w-5 h-5 accent-neon-blue">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs uppercase text-gray-400 mb-1">Fotografía (Archivo)</label>
                        <input type="file" @change="handleImageUpload" accept="image/*" class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-800 file:text-neon-blue hover:file:bg-gray-700">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs uppercase text-gray-400 mb-1">O Dirección (URL) de la Imagen</label>
                        <input type="text" v-model="form.image_url" @input="form.image = null" placeholder="https://link.com/foto.jpg" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                        <p class="text-gray-500 text-xs mt-1">Si seleccionas un archivo local arriba, este tendrá prioridad sobre la URL.</p>
                        <span v-if="formErrors.image_url" class="text-red-500 text-xs mt-1 block">{{ formErrors.image_url[0] }}</span>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-800 flex justify-end gap-3">
                <button @click="closeModal" class="px-6 py-2 rounded text-gray-400 hover:text-white transition cursor-pointer">Cancelar</button>
                <button @click="saveProduct" class="bg-neon-blue text-gamer-dark px-6 py-2 rounded font-black hover:bg-white hover:shadow-neon-blue transition cursor-pointer">
                    Guardar Producto
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Form Create Raffle -->
    <div v-if="showRaffleModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-gamer-card border border-gray-700 w-full max-w-lg rounded-xl shadow-2xl shadow-neon-purple/20 flex flex-col">
            <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">Crear Nuevo Sorteo</h3>
                <button @click="showRaffleModal = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Título</label>
                    <input type="text" v-model="raffleForm.title" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition">
                </div>

                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Descripción</label>
                    <textarea v-model="raffleForm.description" rows="3" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Fecha de Sorteo</label>
                        <input type="datetime-local" v-model="raffleForm.draw_date" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Precio Ticket ($)</label>
                        <input type="number" step="0.01" v-model="raffleForm.ticket_price" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Límite Tickets</label>
                        <input type="number" v-model="raffleForm.max_entries" placeholder="Opcional" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Cód Producto</label>
                        <input type="number" v-model="raffleForm.product_id" placeholder="Opcional" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition">
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-800 flex justify-end gap-3">
                <button @click="showRaffleModal = false" class="px-6 py-2 rounded text-gray-400 hover:text-white transition cursor-pointer">Cancelar</button>
                <button @click="saveRaffle" class="bg-neon-purple text-white px-6 py-2 rounded font-black hover:bg-purple-600 shadow-neon-purple transition cursor-pointer">
                    Crear Sorteo
                </button>
            </div>
        </div>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background-color: #374151;
  border-radius: 10px;
}
</style>
