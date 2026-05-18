<script setup>
import { ref, onMounted, watch, onUnmounted, computed } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import LoadingState from '../../components/LoadingState.vue';
import { store } from '../../utils/store';
import { apiBase } from '../../utils/api';
import { Line, Doughnut } from 'vue-chartjs';
import { 
  Chart as ChartJS, 
  Title, 
  Tooltip, 
  Legend, 
  LineElement, 
  PointElement, 
  CategoryScale, 
  LinearScale, 
  ArcElement,
  Filler
} from 'chart.js';

ChartJS.register(
  Title, Tooltip, Legend, 
  LineElement, PointElement, 
  CategoryScale, LinearScale, 
  ArcElement, Filler
);

const router = useRouter();
// apiBase ahora se importa de utils/api
const products = ref([]);
const categories = ref([]);
const users = ref([]);
const auctions = ref([]);
const raffles = ref([]);
const messages = ref([]);
const orders = ref([]);
const ordersMeta = ref({ current_page: 1, last_page: 1 });
const productsMeta = ref({ current_page: 1, last_page: 1 });
const usersMeta = ref({ current_page: 1, last_page: 1 });
const reviewsMeta = ref({ current_page: 1, last_page: 1 });
const auctionsMeta = ref({ current_page: 1, last_page: 1 });

const reviews = ref([]);
const stats = ref(null); 
const selectedUserToBan = ref(null);
const banType = ref('account'); // 'account' o 'chat'
const ticketsSupport = ref([]);
const selectedTicket = ref(null);
const adminReply = ref('');
const statusTicket = ref('open');

// Custom Support Delete Modal State
const showSupportDeleteModal = ref(false);
const supportTicketToDelete = ref(null);

const loading = ref(true);
const error = ref(null);

const activeTab = ref(localStorage.getItem('admin_active_tab') || 'analytics'); // Default to analytics/saved

const userSearch = ref('');
const userSort = ref('latest');
const productSearch = ref('');
const orderSearch = ref('');
const reviewSearch = ref('');
let searchTimeout = null;

watch([userSearch, userSort], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        usersMeta.value.current_page = 1;
        fetchUsers();
    }, 500);
});

watch(productSearch, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        productsMeta.value.current_page = 1;
        fetchProducts();
    }, 500);
});

watch(orderSearch, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        ordersMeta.value.current_page = 1;
        fetchAdminOrders();
    }, 500);
});

watch(reviewSearch, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        reviewsMeta.value.current_page = 1;
        fetchReviews();
    }, 500);
});
const currentUser = ref(null);
const newMessage = ref('');
let chatInterval = null;
let pollInterval = null;

const showModal = ref(false);
const showRaffleModal = ref(false);
const showUserModal = ref(false);
const isEditingUser = ref(false);
const editingUserId = ref(null);
const editingProduct = ref(null);

const userForm = ref({ name: '', email: '', password: '', password_confirmation: '', is_admin: false });
const userErrors = ref({});

const form = ref({
    name: '',
    description: '',
    price: '',
    original_price: '',
    stock: '',
    category_id: '',
    is_exclusive: false,
    featured: false,
    trending: false,
    is_censored: false,
    image: null,
    image_url: ''
});

const raffleForm = ref({
    title: '',
    description: '',
    start_date: '',
    draw_date: '',
    ticket_price: '',
    max_entries: '',
    product_id: '',
    image_url: ''
});

const isEditingRaffle = ref(false);
const editingRaffleId = ref(null);

const coupons = ref([]);
const showCouponModal = ref(false);
const couponForm = ref({
    code: '',
    type: 'percentage',
    value: '',
    min_purchase: '',
    usage_limit: '',
    expires_at: '',
    is_active: true
});


const flagsToWatch = [
    'is_exclusive', 'featured', 'trending', 'is_censored', 
    'is_anime', 'is_marvel', 'is_star_wars', 'is_dc'
];

flagsToWatch.forEach(flag => {
    watch(() => form.value[flag], (newVal) => {
    });
});
const couponErrors = ref({});

const formErrors = ref({});

// Configure axios interceptor for auth token
axios.interceptors.request.use(config => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

const confirmSupportDelete = (id) => {
    supportTicketToDelete.value = id;
    showSupportDeleteModal.value = true;
};

const deleteSupportTicket = async () => {
    if (!supportTicketToDelete.value) return;
    try {
        await axios.delete(`${apiBase}/support/${supportTicketToDelete.value}`);
        store.notify("Ticket eliminado correctamente", 'success');
        showSupportDeleteModal.value = false;
        selectedTicket.value = null;
        fetchSupportTickets();
    } catch (err) {
        store.notify("Error al eliminar ticket", 'error');
    }
};

const fetchSupportTickets = async () => {
    try {
        const res = await axios.get(`${apiBase}/admin/support`);
        ticketsSupport.value = res.data.tickets;
    } catch (err) {
        console.error("Error fetching support tickets", err);
    }
};

const updateSupportTicket = async () => {
    if (!selectedTicket.value) return;
    try {
        await axios.put(`${apiBase}/admin/support/${selectedTicket.value.id}`, {
            admin_reply: adminReply.value,
            status: statusTicket.value
        });
        store.notify("Ticket actualizado correctamente");
        selectedTicket.value = null;
        fetchSupportTickets();
    } catch (err) {
        store.notify("Error al actualizar ticket: " + (err.response?.data?.message || err.message), 'error');
    }
};

const fetchAdminOrders = async (page = 1) => {
    try {
        const params = { page };
        if (orderSearch.value) params.search = orderSearch.value;
        const res = await axios.get(`${apiBase}/admin/orders`, { params });
        orders.value = res.data.orders.data;
        ordersMeta.value = {
            current_page: res.data.orders.current_page,
            last_page: res.data.orders.last_page
        };
    } catch (err) {
        console.error("Error fetching admin orders", err);
    }
};

const checkAdmin = async () => {
    try {
        const response = await axios.get(`${apiBase}/me`);
        const user = response.data.user;
        currentUser.value = user;
        if (!user || !user.is_admin) {
            store.notify('Acceso denegado. Se requieren permisos de administrador.', 'error');
            router.push('/');
        }
    } catch (err) {
        store.notify('Debes iniciar sesión primero.', 'error');
        router.push('/login');
    }
};

const fetchProducts = async (page = 1) => {
    try {
        const params = { page };
        if (productSearch.value) params.search = productSearch.value;
        const res = await axios.get(`${apiBase}/admin/products`, { params });
        products.value = res.data.products;
        categories.value = res.data.categories;
        productsMeta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page
        };
    } catch (err) {
        console.error("Error fetching products", err);
    }
};

const fetchUsers = async (page = 1) => {
    try {
        const params = { page, sort: userSort.value };
        if (userSearch.value) params.search = userSearch.value;
        const res = await axios.get(`${apiBase}/admin/users`, { params });
        users.value = res.data.users;
        usersMeta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page
        };
    } catch (err) {
        console.error("Error fetching users", err);
    }
};

const fetchAuctions = async (page = 1) => {
    try {
        const res = await axios.get(`${apiBase}/auctions?page=${page}`);
        auctions.value = res.data.activeAuctions.data;
        auctionsMeta.value = {
            current_page: res.data.activeAuctions.current_page,
            last_page: res.data.activeAuctions.last_page
        };
    } catch (err) {
        console.error("Error fetching auctions", err);
    }
};

const fetchReviews = async (page = 1) => {
    try {
        const params = { page };
        if (reviewSearch.value) params.search = reviewSearch.value;
        const res = await axios.get(`${apiBase}/admin/reviews`, { params });
        reviews.value = res.data.reviews;
        reviewsMeta.value = {
            current_page: res.data.current_page,
            last_page: res.data.last_page
        };
    } catch (err) {
        console.error("Error fetching reviews", err);
    }
};

const fetchDashboardData = async (silent = false) => {
    if (!silent) loading.value = true;
    try {
        await Promise.all([
            fetchProducts(productsMeta.value.current_page || 1),
            fetchUsers(usersMeta.value.current_page || 1),
            fetchAuctions(auctionsMeta.value.current_page || 1),
            fetchAdminOrders(ordersMeta.value.current_page || 1),
            fetchReviews(reviewsMeta.value.current_page || 1),
            axios.get(`${apiBase}/admin/stats`).then(res => { stats.value = res.data; }),
            axios.get(`${apiBase}/admin/coupons`).then(res => { coupons.value = res.data.coupons; }),
            axios.get(`${apiBase}/raffles`).then(res => { raffles.value = res.data.raffles; })
        ]);
        
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

const salesChartData = computed(() => {
    if (!stats.value || !stats.value.charts.sales) return null;
    return {
        labels: stats.value.charts.sales.map(d => d.date),
        datasets: [{
            label: 'Ventas (€)',
            data: stats.value.charts.sales.map(d => d.revenue),
            borderColor: '#00f2ff',
            backgroundColor: 'rgba(0, 242, 255, 0.1)',
            fill: true,
            tension: 0.4
        }]
    };
});

const userChartData = computed(() => {
    if (!stats.value || !stats.value.charts.users) return null;
    return {
        labels: stats.value.charts.users.map(d => d.date),
        datasets: [{
            label: 'Nuevos Usuarios',
            data: stats.value.charts.users.map(d => d.count),
            borderColor: '#7000ff',
            backgroundColor: 'rgba(112, 0, 255, 0.1)',
            fill: true,
            tension: 0.4
        }]
    };
});

const categoryChartData = computed(() => {
    if (!stats.value || !stats.value.charts.categories) return null;
    return {
        labels: stats.value.charts.categories.map(c => c.name),
        datasets: [{
            data: stats.value.charts.categories.map(c => c.total_sold),
            backgroundColor: ['#00f2ff', '#7000ff', '#ff00d4', '#00ff4c', '#ffea00'],
            borderWidth: 0
        }]
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false }
    },
    scales: {
        y: { 
            grid: { color: 'rgba(255,255,255,0.05)' }, 
            ticks: { color: '#666', font: { size: 10 } } 
        },
        x: { 
            grid: { display: false }, 
            ticks: { color: '#666', font: { size: 10 } } 
        }
    }
};

const donutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: { color: '#888', font: { size: 10 }, padding: 20, usePointStyle: true }
        }
    }
};

const fetchMessages = async () => {
    try {
        const res = await axios.get(`${apiBase}/chat?admin=true`);
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
        store.notify("Error al enviar mensaje", 'error');
    }
};

const clearChat = async () => {
    store.confirm("Vaciar Chat", "¿Seguro que quieres vaciar todo el historial del chat? Esta acción no se puede deshacer.", async () => {
        try {
            await axios.delete(`${apiBase}/chat/clear`);
            store.notify("Chat vaciado con éxito.");
            messages.value = [];
        } catch (err) {
            store.notify("Error al vaciar el chat: " + (err.response?.data?.message || err.message), 'error');
        }
    });
};



watch(activeTab, (newTab) => {
    localStorage.setItem('admin_active_tab', newTab);
    if (newTab === 'chat') {
        fetchMessages();
        chatInterval = setInterval(fetchMessages, 3000);
    } else if (newTab === 'tickets') {
        fetchSupportTickets();
    } else {
        if(chatInterval) {
            clearInterval(chatInterval);
            chatInterval = null;
        }
    }
});

onUnmounted(() => {
    if (chatInterval) clearInterval(chatInterval);
    if (pollInterval) clearInterval(pollInterval);
});

const editRaffle = (r) => {
    isEditingRaffle.value = true;
    editingRaffleId.value = r.id;
    raffleForm.value = {
        title: r.title,
        description: r.description,
        start_date: r.start_date ? r.start_date.substring(0, 16).replace(' ', 'T') : '',
        draw_date: r.draw_date ? r.draw_date.substring(0, 16).replace(' ', 'T') : '',
        ticket_price: r.ticket_price,
        max_entries: r.max_entries,
        product_id: r.product ? r.product.id : '',
        image_url: r.image_url || ''
    };
    showRaffleModal.value = true;
};

const closeRaffleModal = () => {
    showRaffleModal.value = false;
    isEditingRaffle.value = false;
    editingRaffleId.value = null;
    raffleForm.value = { title: '', description: '', start_date: '', draw_date: '', ticket_price: '', max_entries: '', product_id: '', image_url: '' };
};

const saveRaffle = async () => {
    try {
        if (isEditingRaffle.value) {
            await axios.post(`${apiBase}/admin/raffles/${editingRaffleId.value}`, raffleForm.value);
            store.notify('Sorteo actualizado exitosamente.');
        } else {
            await axios.post(`${apiBase}/admin/raffles`, raffleForm.value);
            store.notify('Sorteo creado exitosamente.');
        }
        closeRaffleModal();
        fetchDashboardData();
    } catch (err) {
        store.notify(err.response?.data?.message || "Error al procesar sorteo", 'error');
    }
};

const drawWinner = async (raffleId) => {
    store.confirm("Realizar Sorteo", "¿Seguro que quieres realizar el sorteo ya?", async () => {
        try {
            const res = await axios.post(`${apiBase}/admin/raffles/${raffleId}/draw`);
            store.notify("¡Sorteo realizado! Ganador: " + res.data.winner.name);
            fetchDashboardData();
        } catch (err) {
            store.notify(err.response?.data?.message || "Error al realizar sorteo", 'error');
        }
    });
};

const cancelRaffle = async (raffleId) => {
    store.confirm("Cancelar Sorteo", "¿Seguro que quieres cancelar este sorteo?", async () => {
        try {
            await axios.post(`${apiBase}/admin/raffles/${raffleId}/cancel`);
            store.notify("¡Sorteo cancelado con éxito!");
            fetchDashboardData();
        } catch (err) {
            store.notify(err.response?.data?.message || "Error al cancelar sorteo", 'error');
        }
    });
};


const showBanModal = ref(false);
const banUserItem = ref(null);
const banReason = ref('Incumplimiento de normas');
const banDuration = ref('24');

const openBanModal = (user) => {
    if (user.is_banned) {
        unbanUser(user);
    } else {
        banUserItem.value = user;
        banReason.value = 'Incumplimiento de normas';
        banDuration.value = '24';
        showBanModal.value = true;
    }
};

const openQuickBanModal = (msg) => {
    banUserItem.value = { id: msg.user_id, name: msg.user_name };
    banReason.value = 'Moderación chat';
    banDuration.value = '1';
    showBanModal.value = true;
};

const banSelectedUser = () => {
    if (selectedUserToBan.value) {
        openQuickBanModal({ user_id: selectedUserToBan.value.id, user_name: selectedUserToBan.value.name });
    }
};

const submitBan = async () => {
    const user = banUserItem.value;
    if (!user) return;
    try {
        const token = localStorage.getItem('token');
        await axios.post(`${apiBase}/admin/users/${user.id}/ban`, {
            reason: banReason.value,
            duration: banDuration.value,
            type: banType.value
        }, { headers: { Authorization: `Bearer ${token}` } });
        alert("Usuario baneado con éxito.");
        showBanModal.value = false;
        fetchDashboardData(); 
    } catch (err) {
        console.error("DEBUG BAN ERROR:", err.response || err);
        alert("Error al banear: " + (err.response?.data?.message || err.response?.data?.error || err.message));
    }
};


const unbanUser = async (user) => {
    store.confirm("Desbanear Usuario", `¿Seguro que quieres desbanear a ${user.name}?`, async () => {
        try {
            const token = localStorage.getItem('token');
            const res = await axios.post(`${apiBase}/admin/users/${user.id}/unban`, {}, {
                headers: { Authorization: `Bearer ${token}` }
            });
            store.notify(res.data.message || "Usuario desbaneado con éxito.");
            fetchDashboardData();
        } catch (err) {
            store.notify("Error al desbanear: " + (err.response?.data?.message || err.response?.data?.error || err.message), 'error');
        }
    });
};



const fixImageUrl = (url) => {
    if (!url) return '';
    // Eliminar /api del base para apuntar a la raíz del backend (Render)
    const apiBaseNoApi = apiBase.replace('/api', '');
    return url.replace('http://localhost:8000', apiBaseNoApi);
};

const toggleUserAdmin = async (user) => {
    if (!currentUser.value?.is_super_admin) return;
    try {
        const res = await axios.post(`${apiBase}/admin/users/${user.id}/toggle-admin`);
        store.notify(res.data.message, "success");
        fetchDashboardData();
    } catch (err) {
        store.notify(err.response?.data?.message || "Error al cambiar rol", "error");
    }
};

const adjustUserPoints = async (user) => {
    const amountStr = window.prompt(`Ajustar puntos para ${user.name} (Escribe +/- cantidad, ej: +100 o -50):`, "0");
    if (amountStr === null) return;
    
    const amount = parseInt(amountStr);
    if (isNaN(amount) || amount === 0) {
        store.notify("Cantidad no válida", "error");
        return;
    }

    try {
        const token = localStorage.getItem('token');
        const res = await axios.post(`${apiBase}/admin/users/${user.id}/points`, { amount }, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        if (res.data.status === 'success') {
            user.points = res.data.new_points;
            store.notify(res.data.message, "success");
            fetchDashboardData(); // Refrescar para asegurar sincronía
        }
    } catch (err) {
        store.notify(err.response?.data?.message || "Error al ajustar puntos", "error");
    }
};

const deleteUser = async (user) => {
    store.confirm("Eliminar Usuario", '¿Estás seguro de eliminar este usuario?', async () => {
        try {
            await axios.delete(`${apiBase}/admin/users/${user.id}`);
            store.notify("Usuario eliminado.");
            fetchDashboardData();
        } catch (err) {
            store.notify("Error al eliminar usuario", 'error');
        }
    });
};

const approveReview = async (reviewId) => {
    try {
        const res = await axios.post(`${apiBase}/admin/reviews/${reviewId}/approve`);
        alert(res.data.message || "Valoración aprobada.");
        fetchDashboardData();
    } catch (err) {
        alert("Error al aprobar la valoración.");
    }
};

const deleteReview = async (reviewId) => {
    store.confirm("Eliminar Valoración", "¿Seguro que quieres eliminar esta valoración?", async () => {
        try {
            await axios.delete(`${apiBase}/admin/reviews/${reviewId}`);
            store.notify("Valoración eliminada.");
            fetchDashboardData();
        } catch (err) {
            store.notify("Error al eliminar la valoración.", 'error');
        }
    });
};

const extendAuction = async (auctionId) => {
    store.ask("Extender Subasta", "¿Cuántas horas quieres extender la subasta?", "24", async (hours) => {
        if (!hours) return;
        try {
            await axios.post(`${apiBase}/auctions/${auctionId}/extend`, { hours });
            store.notify("Subasta extendida.");
            fetchDashboardData();
        } catch (err) {
            store.notify("Error al extender subasta", 'error');
        }
    });
};

const forceEndAuction = async (auctionId) => {
    store.confirm("Finalizar Subasta", "¿Seguro que quieres forzar el fin de esta subasta?", async () => {
        try {
            await axios.post(`${apiBase}/auctions/${auctionId}/force-end`);
            store.notify("Subasta finalizada.");
            fetchDashboardData();
        } catch (err) {
            store.notify("Error al finalizar subasta", 'error');
        }
    });
};

const updateOrderStatus = async (orderId, status) => {
    const statusText = status === 'completed' ? 'Completar' : 'Cancelar';
    store.confirm(`${statusText} Pedido`, `¿Seguro que quieres marcar este pedido como ${status}?`, async () => {
        try {
            await axios.post(`${apiBase}/admin/orders/${orderId}/status`, { status });
            store.notify(`Pedido #${orderId} actualizado a ${status}.`);
            fetchDashboardData();
        } catch (err) {
            store.notify("Error al actualizar estado del pedido: " + (err.response?.data?.message || err.message), 'error');
        }
    });
};

const startAuction = async (product) => {
    store.confirm("Lanzar Subasta", `¿Seguro que quieres lanzar una subasta para "${product.name}"? Bajará el precio un 20% durante 24h.`, async () => {
        try {
            const res = await axios.post(`${apiBase}/auctions/${product.id}/start`);
            store.notify(res.data.message || "Subasta iniciada.");
            fetchDashboardData();
        } catch (err) {
            store.notify("Error al iniciar subasta: " + (err.response?.data?.error || err.message), 'error');
        }
    });
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
            featured: product.featured,
            trending: product.trending,
            is_anime: product.is_anime || false,
            is_marvel: product.is_marvel || false,
            is_star_wars: product.is_star_wars || false,
            is_dc: product.is_dc || false,
            is_censored: product.is_censored || false,
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
            featured: false,
            trending: false,
            is_anime: false,
            is_marvel: false,
            is_star_wars: false,
            is_dc: false,
            is_censored: false,
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
    const file = event.target.files[0];
    if (file) {
        if (file && file.size > 100 * 1024 * 1024) { // 100MB
            store.notify("La imagen excede el límite permitido de 100MB.", 'error');
            event.target.value = ''; // Resetear input
            return;
        }
        form.value.image = file;
        form.value.image_url = ''; // Limpiar URL para dar prioridad al archivo
    }
};


const openUserModal = (user = null) => {
    userErrors.value = {};
    if (user) {
        isEditingUser.value = true;
        editingUserId.value = user.id;
        userForm.value = {
            name: user.name,
            email: user.email,
            password: '',
            password_confirmation: '',
            is_admin: user.is_admin
        };
    } else {
        isEditingUser.value = false;
        editingUserId.value = null;
        userForm.value = { name: '', email: '', password: '', password_confirmation: '', is_admin: false };
    }
    showUserModal.value = true;
};

const saveUser = async () => {
    userErrors.value = {};
    try {
        const token = localStorage.getItem('token');
        if (isEditingUser.value) {
            await axios.put(`${apiBase}/admin/users/${editingUserId.value}`, userForm.value, {
                headers: { Authorization: `Bearer ${token}` }
            });
            store.notify("Usuario actualizado con éxito.");
        } else {
            await axios.post(`${apiBase}/admin/users`, userForm.value, {
                headers: { Authorization: `Bearer ${token}` }
            });
            store.notify("Usuario creado con éxito.");
        }
        showUserModal.value = false;
        fetchDashboardData();
    } catch (err) {
        if (err.response?.status === 422) {
            userErrors.value = err.response.data.errors || {};
        } else {
            store.notify("Error al procesar usuario: " + (err.response?.data?.message || err.message), 'error');
        }
    }
};

const saveProduct = async () => {
    formErrors.value = {};
    
    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    
    const cleanPrice = (val) => val ? String(val).replace(',', '.') : '';
    formData.append('price', cleanPrice(form.value.price));
    formData.append('original_price', form.value.original_price ? cleanPrice(form.value.original_price) : '');

    
    formData.append('stock', form.value.stock);
    formData.append('category_id', form.value.category_id);
    formData.append('is_exclusive', form.value.is_exclusive ? 1 : 0);
    formData.append('featured', form.value.featured ? 1 : 0);
    formData.append('trending', form.value.trending ? 1 : 0);
    formData.append('is_anime', form.value.is_anime ? 1 : 0);
    formData.append('is_marvel', form.value.is_marvel ? 1 : 0);
    formData.append('is_star_wars', form.value.is_star_wars ? 1 : 0);
    formData.append('is_dc', form.value.is_dc ? 1 : 0);
    formData.append('is_censored', form.value.is_censored ? 1 : 0);
    
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
            store.notify('Producto actualizado.');
        } else {
            await axios.post(`${apiBase}/products`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            store.notify('Producto creado exitosamente.');
        }
        closeModal();
        fetchDashboardData();
    } catch (err) {
        if (err.response && err.response.data.errors) {
            formErrors.value = err.response.data.errors;
        } else {
            const errorMsg = err.response ? JSON.stringify(err.response.data) : err.message;
            store.notify('Error al guardar el producto: ' + errorMsg, 'error');
        }
    }
};

const saveCoupon = async () => {
    couponErrors.value = {};
    try {
        await axios.post(`${apiBase}/admin/coupons`, couponForm.value);
        store.notify("Cupón creado con éxito.");
        showCouponModal.value = false;
        couponForm.value = { code: '', type: 'percentage', value: '', min_purchase: '', usage_limit: '', expires_at: '', is_active: true };
        fetchDashboardData();
    } catch (err) {
        if (err.response?.status === 422) {
            couponErrors.value = err.response.data.errors;
        } else {
            store.notify("Error al crear cupón", 'error');
        }
    }
};

const toggleCoupon = async (coupon) => {
    try {
        await axios.post(`${apiBase}/admin/coupons/${coupon.id}/toggle`);
        store.notify("Estado del cupón actualizado.");
        fetchDashboardData();
    } catch (err) {
        store.notify("Error al actualizar cupón", 'error');
    }
};

const deleteCoupon = async (id) => {
    store.confirm("Eliminar Cupón", "¿Seguro que quieres borrar este cupón?", async () => {
        try {
            await axios.delete(`${apiBase}/admin/coupons/${id}`);
            store.notify("Cupón eliminado.");
            fetchDashboardData();
        } catch (err) {
            store.notify("Error al eliminar cupón", 'error');
        }
    });
};

const deleteProduct = async (id) => {
    store.confirm("Eliminar Producto", '¿Estás seguro de eliminar este producto?', async () => {
        try {
            await axios.delete(`${apiBase}/products/${id}`);
            store.notify("Producto eliminado.");
            fetchDashboardData();
        } catch (err) {
            store.notify('Error al eliminar producto', 'error');
        }
    });
};

onMounted(async () => {
    await checkAdmin();
    fetchDashboardData();
    pollInterval = setInterval(() => fetchDashboardData(true), 10000); // 10s auto-refresh Dashboard (silent)

    // Escuchar el evento de nuevo usuario registrado para no tener que refrescar manually
    if (window.Echo) {
        window.Echo.channel('admin-channel')
            .listen('UserRegistered', (e) => {
                console.log("Nuevo usuario registrado:", e.user);
                // Si estamos en la pestaña de usuarios o en el dashboard general
                fetchDashboardData();
            });
    }
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
    if (window.Echo) {
        window.Echo.leave('admin-channel');
    }
});
const generateCouponCode = () => {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = 'SOUL-';
    for (let i = 0; i < 6; i++) {
        code += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    couponForm.value.code = code;
};

</script>

<template>
  <div class="min-h-screen bg-gamer-dark text-gray-200">
      
    <!-- Admin Header -->
    <header class="bg-gamer-card border-b border-gray-800 p-6 shadow-neon-blue">
      <div class="container mx-auto flex justify-between items-center px-4 md:px-0">
        <h1 class="text-xl md:text-3xl font-black tracking-widest uppercase text-neon-blue drop-shadow-lg">
          <i class="fas fa-shield-alt mr-2"></i> Panel
        </h1>
        <router-link to="/" class="text-gray-400 hover:text-white transition cursor-pointer text-[10px] md:text-xs mr-2 md:mr-0">
            &larr; Volver a la tienda
        </router-link>
      </div>
    </header>

    <main class="container mx-auto px-4 py-8 pb-24 md:pb-8 max-w-7xl">
        <!-- Error / Loading Guards -->







        <!-- Tabs Bar -->
        <div class="flex border-b border-gray-800 mb-8 gap-4 overflow-x-auto text-xs uppercase tracking-wider font-bold no-scrollbar">
            <button @click="activeTab = 'analytics'" :class="{'border-b-2 border-neon-blue text-neon-blue': activeTab === 'analytics', 'text-gray-500 hover:text-white': activeTab !== 'analytics'}" class="pb-3 px-1 transition flex items-center gap-2 whitespace-nowrap flex-shrink-0">
                <i class="fas fa-chart-line"></i> Analíticas
            </button>
            <button @click="activeTab = 'products'" :class="{'border-b-2 border-neon-blue text-neon-blue': activeTab === 'products', 'text-gray-500 hover:text-white': activeTab !== 'products'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Productos</button>
            <button @click="activeTab = 'users'" :class="{'border-b-2 border-neon-blue text-neon-blue': activeTab === 'users', 'text-gray-500 hover:text-white': activeTab !== 'users'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Usuarios</button>
            <button @click="activeTab = 'orders'" :class="{'border-b-2 border-neon-purple text-neon-purple': activeTab === 'orders', 'text-gray-500 hover:text-white': activeTab !== 'orders'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Pedidos</button>
            <button @click="activeTab = 'reviews'" :class="{'border-b-2 border-neon-purple text-neon-purple': activeTab === 'reviews', 'text-gray-500 hover:text-white': activeTab !== 'reviews'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Valoraciones</button>
            <button @click="activeTab = 'coupons'" :class="{'border-b-2 border-yellow-400 text-yellow-400': activeTab === 'coupons', 'text-gray-500 hover:text-white': activeTab !== 'coupons'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Cupones</button>
            <button @click="activeTab = 'auctions'" :class="{'border-b-2 border-sky-400 text-sky-400': activeTab === 'auctions', 'text-gray-500 hover:text-white': activeTab !== 'auctions'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Subastas</button>
            <button @click="activeTab = 'raffles'" :class="{'border-b-2 border-neon-purple text-neon-purple': activeTab === 'raffles', 'text-gray-500 hover:text-white': activeTab !== 'raffles'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Sorteos</button>
            <button @click="activeTab = 'chat'" :class="{'border-b-2 border-neon-purple text-neon-purple': activeTab === 'chat', 'text-gray-500 hover:text-white': activeTab !== 'chat'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Chat Global</button>
            <button @click="activeTab = 'tickets'" :class="{'border-b-2 border-neon-green text-neon-green': activeTab === 'tickets', 'text-gray-500 hover:text-white': activeTab !== 'tickets'}" class="pb-3 px-1 transition whitespace-nowrap flex-shrink-0">Tickets Soporte</button>
        </div>

        <!-- Analytics Tab -->
        <div v-if="activeTab === 'analytics'" class="space-y-8 animate-slide-in">
            <!-- KPI Cards -->
            <div v-if="stats && stats.kpis" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gamer-card border border-gray-800 p-5 rounded-2xl shadow-xl border-l-4 border-neon-blue">
                    <p class="text-[10px] uppercase font-black text-gray-500 tracking-widest mb-1">Ingresos Totales</p>
                    <p class="text-2xl font-black text-white">{{ (stats.kpis.revenue || 0).toFixed(2) }}€</p>
                    <div class="mt-2 text-[10px] text-neon-green font-bold flex items-center gap-1">
                        <i class="fas fa-trending-up"></i> +12% este mes
                    </div>
                </div>
                <div class="bg-gamer-card border border-gray-800 p-5 rounded-2xl shadow-xl border-l-4 border-neon-purple">
                    <p class="text-[10px] uppercase font-black text-gray-500 tracking-widest mb-1">Pedidos Totales</p>
                    <p class="text-2xl font-black text-white">{{ stats.kpis.orders || 0 }}</p>
                    <div class="mt-2 text-[10px] text-neon-purple font-bold">Actualizado en tiempo real</div>
                </div>
                <div class="bg-gamer-card border border-gray-800 p-5 rounded-2xl shadow-xl border-l-4 border-yellow-400">
                    <p class="text-[10px] uppercase font-black text-gray-500 tracking-widest mb-1">Usuarios Registrados</p>
                    <p class="text-2xl font-black text-white">{{ stats.kpis.users || 0 }}</p>
                    <div class="mt-2 text-[10px] text-yellow-400 font-bold">Comunidad Soul Guild</div>
                </div>
                <div class="bg-gamer-card border border-gray-800 p-5 rounded-2xl shadow-xl border-l-4 border-neon-green">
                    <p class="text-[10px] uppercase font-black text-gray-500 tracking-widest mb-1">Productos en Venta</p>
                    <p class="text-2xl font-black text-white">{{ stats.kpis.products || 0 }}</p>
                    <div class="mt-2 text-[10px] text-neon-green font-bold">Catálogo activo</div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Sales Chart -->
                <div class="lg:col-span-2 bg-gamer-card border border-gray-800 p-6 rounded-2xl shadow-xl h-[400px]">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-sm font-black uppercase text-gray-300">Ventas (Últimos 30 días)</h4>
                        <span class="text-[10px] text-gray-500 italic">Datos actualizados</span>
                    </div>
                    <div class="h-[300px]">
                        <Line v-if="salesChartData" :data="salesChartData" :options="chartOptions" />
                        <div v-else class="h-full flex items-center justify-center text-gray-600 text-xs italic">Cargando datos históricos...</div>
                    </div>
                </div>
                
                <!-- Category Donut -->
                <div class="bg-gamer-card border border-gray-800 p-6 rounded-2xl shadow-xl h-[400px]">
                    <h4 class="text-sm font-black uppercase text-gray-300 mb-6">Top Categorías</h4>
                    <div class="h-[280px]">
                        <Doughnut v-if="categoryChartData" :data="categoryChartData" :options="donutOptions" />
                        <div v-else class="h-full flex items-center justify-center text-gray-600 text-xs italic">Analizando inventario...</div>
                    </div>
                </div>
            </div>

            <!-- Second Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                 <!-- User Growth -->
                 <div class="lg:col-span-2 bg-gamer-card border border-gray-800 p-6 rounded-2xl shadow-xl h-[400px]">
                    <h4 class="text-sm font-black uppercase text-gray-300 mb-6">Crecimiento de Usuarios</h4>
                    <div class="h-[300px]">
                        <Line v-if="userChartData" :data="userChartData" :options="chartOptions" />
                    </div>
                </div>

                <!-- Quick Actions/Recent -->
                <div class="bg-gamer-card border border-gray-800 p-6 rounded-2xl shadow-xl">
                    <h4 class="text-sm font-black uppercase text-gray-300 mb-4 border-b border-gray-800 pb-2">Resumen Rápido</h4>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-neon-blue shadow-neon-blue"></div>
                            <span class="text-xs text-gray-400">Nuevos pedidos hoy: <strong class="text-white">5</strong></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-neon-purple shadow-neon-purple"></div>
                            <span class="text-xs text-gray-400">Subastas activas: <strong class="text-white">{{ auctions.length }}</strong></span>
                        </li>
                        <li class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-yellow-400 shadow-yellow-400"></div>
                            <span class="text-xs text-gray-400">Reviews pendientes: <strong class="text-white">{{ reviews.filter(r=>!r.is_approved).length }}</strong></span>
                        </li>
                    </ul>
                    <div class="mt-8 border-t border-gray-800 pt-4">
                        <button @click="activeTab = 'orders'" class="w-full bg-gray-800 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-neon-blue hover:text-gamer-dark transition">Ver todos los pedidos</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Tab -->
        <div v-if="activeTab === 'products'">
            <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mb-6 gap-4">
                <div class="flex items-center gap-4">
                    <h3 class="text-xl font-bold text-white">Inventario de Productos</h3>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" v-model="productSearch" placeholder="Buscar producto..." class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg pl-8 pr-3 py-1.5 focus:border-neon-blue focus:ring-1 focus:outline-none focus:ring-neon-blue w-64 shadow-inner" />
                    </div>
                </div>
                <button @click="openModal()" class="bg-neon-blue text-gamer-dark px-4 py-1.5 rounded-lg text-xs font-black hover:bg-white hover:shadow-neon-blue transition">+ Nuevo Producto</button>
            </div>


            <!-- Products Table -->
            <div class="bg-gamer-card rounded-xl overflow-x-auto border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">Foto</th>
                            <th class="p-3 border-b border-gray-800">Nombre</th>
                            <th class="p-3 border-b border-gray-800">Precio</th>
                            <th class="p-3 border-b border-gray-800">Existencias</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="product in products" :key="product.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3">
                                <div class="w-10 h-10 bg-gray-900 rounded overflow-hidden flex items-center justify-center">
                                    <img v-if="product.image_url" :src="fixImageUrl(product.image_url)" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="p-3 font-bold text-gray-200 truncate max-w-xs">{{ product.full_name || product.name }}</td>
                            <td class="p-3 text-sky-400 font-black">{{product.price}}€</td>
                            <td><span :class="{'text-red-500 font-bold': product.stock === 0}">{{ product.stock }} u.</span></td>
                            <td class="p-3 text-right">
                                <button v-if="product.is_exclusive && product.stock === 1 && !product.is_in_auction" @click="startAuction(product)" class="text-sky-400 hover:underline hover:text-white mr-3 text-xs font-bold">Iniciar Subasta</button>
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

            <!-- Pagination for Products -->
            <div v-if="productsMeta.last_page > 1" class="mt-6 flex justify-center items-center gap-4 pb-6">
                <button @click="fetchProducts(productsMeta.current_page - 1)" 
                        :disabled="productsMeta.current_page === 1"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <span class="text-[10px] font-black uppercase text-gray-400">Página {{ productsMeta.current_page }} de {{ productsMeta.last_page }}</span>
                <button @click="fetchProducts(productsMeta.current_page + 1)" 
                        :disabled="productsMeta.current_page === productsMeta.last_page"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Siguiente
                </button>
            </div>
        </div>

        <!-- Users Tab -->
        <div v-else-if="activeTab === 'users'">
            <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mb-6 gap-4">
                <div class="flex flex-wrap items-center gap-4">
                    <h3 class="text-xl font-bold text-white">Gestión de Usuarios</h3>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" v-model="userSearch" placeholder="Buscar usuario..." class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg pl-8 pr-3 py-1.5 focus:border-neon-purple focus:ring-1 focus:outline-none focus:ring-neon-purple w-48 shadow-inner" />
                    </div>
                    <select v-model="userSort" class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg px-3 py-1.5 focus:border-neon-purple focus:outline-none focus:ring-1 focus:ring-neon-purple shadow-inner cursor-pointer">
                        <option value="latest">Más recientes</option>
                        <option value="oldest">Más antiguos</option>
                        <option value="role">Mayor Importancia</option>
                    </select>
                </div>
                <button @click="openUserModal()" class="bg-gradient-to-r from-neon-purple to-neon-blue text-white px-4 py-2 rounded-xl text-xs font-black shadow-neon-purple/20 hover:scale-105 transition">Crear Usuario</button>
            </div>
            <div class="bg-gamer-card rounded-xl overflow-x-auto border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">Nombre</th>
                            <th class="p-3 border-b border-gray-800">Email</th>
                            <th class="p-3 border-b border-gray-800">Puntos</th>
                            <th class="p-3 border-b border-gray-800">Rol</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in users" :key="user.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3 font-bold text-gray-200">{{ user.name }}</td>
                            <td class="p-3 text-gray-400">{{ user.email }}</td>
                            <td class="p-3">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-neon-blue">{{ user.points || 0 }}</span>
                                    <button v-if="currentUser && currentUser.is_super_admin" 
                                            @click="adjustUserPoints(user)" 
                                            class="text-[10px] bg-gray-800 hover:bg-gray-700 w-5 h-5 flex items-center justify-center rounded-full text-neon-blue transition"
                                            title="Ajustar puntos">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                            <td class="p-3">
                                <span v-if="user.is_super_admin" class="text-neon-blue font-black bg-neon-blue/10 px-1.5 py-0.5 rounded text-[10px] uppercase">Super Admin</span>
                                <span v-else-if="user.is_admin" class="text-neon-purple font-bold">Admin</span>
                                <span v-else class="text-gray-500">User</span>
                            </td>
                            <td class="p-3 text-right flex gap-2 justify-end items-center">
                                <!-- Solo el Super Admin puede conceder o quitar administración y editar -->
                                <button v-if="currentUser && currentUser.is_super_admin && user.id !== currentUser.id && !user.is_super_admin" 
                                        @click="toggleUserAdmin(user)" 
                                        class="text-[10px] bg-neon-purple/10 text-neon-purple px-2 py-1 rounded hover:bg-neon-purple/20 transition uppercase font-black">
                                    {{ user.is_admin ? 'Quitar Admin' : 'Hacer Admin' }}
                                </button>

                                <button v-if="currentUser && currentUser.is_super_admin && user.id !== currentUser.id" 
                                        @click="openUserModal(user)" 
                                        class="text-[10px] bg-white/5 text-gray-400 px-2 py-1 rounded hover:bg-white/10 transition uppercase font-black">
                                    Editar
                                </button>
                                
                                <span v-if="currentUser && user.id === currentUser.id" class="text-gray-600 text-[10px] italic">Eres tú</span>

                                <template v-else>
                                    <!-- No permitir banear a otros administradores si no eres super admin, y nunca a super admins -->
                                    <button v-if="!user.is_super_admin && (!user.is_admin || (currentUser && currentUser.is_super_admin))" @click="openBanModal(user)" class="text-xs transition" :class="user.is_banned ? 'text-green-400 hover:underline' : 'text-red-400 hover:underline'">
                                        {{ user.is_banned ? 'Desbanear' : 'Banear' }}
                                    </button>

                                    
                                    <!-- No permitir eliminar a otros administradores si no eres super admin, y nunca a super admins -->
                                    <button v-if="!user.is_super_admin && (!user.is_admin || (currentUser && currentUser.is_super_admin))" @click="deleteUser(user)" class="text-red-500 hover:underline text-xs">Eliminar</button>
                                </template>
                            </td>
                        </tr>
                        <tr v-if="users.length === 0">
                            <td colspan="4" class="p-4 text-center text-gray-500 text-xs">No hay usuarios registrados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Users -->
            <div v-if="usersMeta.last_page > 1" class="mt-6 flex justify-center items-center gap-4 pb-6">
                <button @click="fetchUsers(usersMeta.current_page - 1)" 
                        :disabled="usersMeta.current_page === 1"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <span class="text-[10px] font-black uppercase text-gray-400">Página {{ usersMeta.current_page }} de {{ usersMeta.last_page }}</span>
                <button @click="fetchUsers(usersMeta.current_page + 1)" 
                        :disabled="usersMeta.current_page === usersMeta.last_page"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Siguiente
                </button>
            </div>
        </div>

        <!-- Auctions Tab -->
        <div v-else-if="activeTab === 'auctions'">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Control de Subastas</h3>
                <button @click="router.push('/auctions/create')" class="bg-neon-green text-gray-900 px-4 py-1.5 rounded-lg text-xs font-black hover:bg-green-400 transition shadow-neon-green/20">+ Nueva Subasta</button>
            </div>

            <div class="bg-gamer-card rounded-xl overflow-x-auto border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
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
                            <td class="p-3 font-bold text-gray-200">{{ auc.full_name || auc.name }}</td>
                            <td class="p-3 text-sky-400 font-black">{{auc.price}}€</td>
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

            <!-- Pagination for Auctions -->
            <div v-if="auctionsMeta.last_page > 1" class="mt-6 flex justify-center items-center gap-4 pb-6">
                <button @click="fetchAuctions(auctionsMeta.current_page - 1)" 
                        :disabled="auctionsMeta.current_page === 1"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <span class="text-[10px] font-black uppercase text-gray-400">Página {{ auctionsMeta.current_page }} de {{ auctionsMeta.last_page }}</span>
                <button @click="fetchAuctions(auctionsMeta.current_page + 1)" 
                        :disabled="auctionsMeta.current_page === auctionsMeta.last_page"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Siguiente
                </button>
            </div>
        </div>

        <!-- Raffles Tab -->
        <div v-else-if="activeTab === 'raffles'">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Sorteos de Sistema</h3>
                <button @click="showRaffleModal = true" class="bg-neon-purple text-white px-4 py-1.5 rounded-lg text-xs font-black hover:bg-purple-600 shadow-neon-purple transition">+ Crear Sorteo</button>
            </div>
            
            <div class="bg-gamer-card rounded-xl overflow-x-auto border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
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
                                <span v-else-if="r.status === 'cancelled'" class="text-red-500">Cancelado</span>
                                <span v-else class="text-sky-400 font-bold uppercase text-[10px] tracking-widest">Activo</span>
                            </td>
                            <td class="p-3 text-right">
                                <div v-if="r.status === 'pending'" class="flex gap-2 justify-end">
                                    <button @click="editRaffle(r)" class="text-neon-blue hover:underline text-xs">Editar</button>
                                    <button @click="drawWinner(r.id)" class="text-neon-purple hover:underline text-xs">Finalizar</button>
                                    <button @click="cancelRaffle(r.id)" class="text-red-500 hover:underline text-xs">Cancelar</button>
                                </div>
                                <span v-else-if="r.status === 'completed'" class="text-[10px] text-gray-600">Ganador: <span class="text-white">{{ r.winner ? r.winner.name : 'N/A' }}</span></span>
                                <span v-else class="text-[10px] text-red-500/70">Sin acciones</span>
                            </td>

                        </tr>
                        <tr v-if="raffles.length === 0">
                            <td colspan="4" class="p-4 text-center text-gray-500 text-xs">No hay sorteos creados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Orders Tab -->
        <div v-else-if="activeTab === 'orders'">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-6">
                <h3 class="text-xl font-bold text-white">Gestión de Pedidos</h3>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" v-model="orderSearch" placeholder="Buscar por ID o email..." class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg pl-8 pr-3 py-1.5 focus:border-sky-400 focus:ring-1 focus:outline-none focus:ring-sky-400 w-64 shadow-inner" />
                </div>
            </div>
            <div class="bg-gamer-card rounded-xl overflow-x-auto border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">ID</th>
                            <th class="p-3 border-b border-gray-800">Usuario</th>
                            <th class="p-3 border-b border-gray-800">Productos</th>
                            <th class="p-3 border-b border-gray-800">Fecha</th>
                            <th class="p-3 border-b border-gray-800">Total</th>
                            <th class="p-3 border-b border-gray-800">Estado</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3 font-bold text-gray-200">#{{ order.id }}</td>
                            <td class="p-3 text-gray-400">{{ order.user ? order.user.name : 'N/A' }}</td>
                            <td class="p-3">
                                <div class="max-w-[200px] space-y-1">
                                    <div v-for="item in order.items" :key="item.id" class="text-[10px] leading-tight">
                                        <span class="text-neon-blue font-black mr-1">{{ item.quantity }}x</span>
                                        <span class="text-gray-400 uppercase tracking-tighter">{{ item.product ? item.product.name : 'Producto eliminado' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3 text-gray-400 text-xs">{{ new Date(order.created_at).toLocaleDateString() }}</td>
                            <td class="p-3 text-sky-400 font-black">{{order.total}}€</td>
                            <td class="p-3">
                                <span :class="{'text-neon-blue': order.status === 'pending', 'text-green-400': order.status === 'completed', 'text-red-400': order.status === 'cancelled'}" class="font-bold uppercase text-[10px] tracking-widest">
                                    {{ order.status === 'pending' ? 'Pendiente' : (order.status === 'completed' ? 'Completado' : 'Cancelado') }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <div v-if="order.status === 'pending'" class="flex gap-2 justify-end">
                                    <button @click="updateOrderStatus(order.id, 'completed')" class="text-green-400 hover:scale-110 transition text-[10px] font-black uppercase">Completar</button>
                                    <button @click="updateOrderStatus(order.id, 'cancelled')" class="text-red-400 hover:scale-110 transition text-[10px] font-black uppercase">Cancelar</button>
                                </div>
                                <span v-else class="text-[10px] text-gray-600 font-bold uppercase tracking-widest italic">- Finalizado -</span>
                            </td>
                        </tr>
                        <tr v-if="orders.length === 0">
                            <td colspan="7" class="p-4 text-center text-gray-500 text-xs">No hay pedidos registrados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Admin Orders -->
            <div v-if="ordersMeta.last_page > 1" class="mt-6 flex justify-center items-center gap-4">
                <button @click="fetchAdminOrders(ordersMeta.current_page - 1)" 
                        :disabled="ordersMeta.current_page === 1"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <span class="text-[10px] font-black uppercase text-gray-400">Página {{ ordersMeta.current_page }} de {{ ordersMeta.last_page }}</span>
                <button @click="fetchAdminOrders(ordersMeta.current_page + 1)" 
                        :disabled="ordersMeta.current_page === ordersMeta.last_page"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Siguiente
                </button>
            </div>
        </div>

        <!-- Reviews Tab -->
        <div v-else-if="activeTab === 'reviews'">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-4 mb-6">
                <h3 class="text-xl font-bold text-white">Valoraciones de Productos</h3>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" v-model="reviewSearch" placeholder="Buscar producto o comentario..." class="bg-gray-900 border border-gray-700 text-white text-xs rounded-lg pl-8 pr-3 py-1.5 focus:border-purple-400 focus:ring-1 focus:outline-none focus:ring-purple-400 w-64 shadow-inner" />
                </div>
            </div>
            <div class="bg-gamer-card rounded-xl overflow-x-auto border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">Producto</th>
                            <th class="p-3 border-b border-gray-800">Usuario</th>
                            <th class="p-3 border-b border-gray-800">Comentario</th>
                            <th class="p-3 border-b border-gray-800">Puntuación</th>
                            <th class="p-3 border-b border-gray-800">Estado</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="rev in reviews" :key="rev.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3 font-bold text-gray-200">{{ rev.product ? rev.product.name : 'N/A' }}</td>
                            <td class="p-3 text-gray-400">{{ rev.user ? rev.user.name : 'N/A' }}</td>
                            <td class="p-3 text-gray-300 truncate max-w-xs">{{ rev.comment }}</td>
                            <td class="p-3 text-sky-400 font-bold tracking-tighter">{{ rev.rating }} / 5</td>
                            <td class="p-3">
                                <span v-if="rev.is_approved" class="text-sky-400 font-bold uppercase text-[10px] tracking-widest">Aprobada</span>
                                <span v-else class="text-neon-blue font-bold animate-pulse">Pendiente</span>
                            </td>
                            <td class="p-3 text-right flex gap-2 justify-end">
                                <button v-if="!rev.is_approved" @click="approveReview(rev.id)" class="text-neon-blue hover:underline text-xs">Aprobar</button>
                                <button @click="deleteReview(rev.id)" class="text-red-500 hover:underline text-xs">Eliminar</button>
                            </td>
                        </tr>
                        <tr v-if="reviews.length === 0">
                            <td colspan="4" class="p-4 text-center text-gray-500 text-xs">No hay valoraciones registradas.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination for Reviews -->
            <div v-if="reviewsMeta.last_page > 1" class="mt-6 flex justify-center items-center gap-4 pb-6">
                <button @click="fetchReviews(reviewsMeta.current_page - 1)" 
                        :disabled="reviewsMeta.current_page === 1"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Anterior
                </button>
                <span class="text-[10px] font-black uppercase text-gray-400">Página {{ reviewsMeta.current_page }} de {{ reviewsMeta.last_page }}</span>
                <button @click="fetchReviews(reviewsMeta.current_page + 1)" 
                        :disabled="reviewsMeta.current_page === reviewsMeta.last_page"
                        class="px-4 py-2 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-neon-blue transition disabled:opacity-30 disabled:cursor-not-allowed">
                    Siguiente
                </button>
            </div>
        </div>

        <!-- Support Chat Tab -->
        <div v-else-if="activeTab === 'chat'" class="flex flex-col h-[500px]">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-white">Chat de Soporte (Global)</h3>
                <button @click="clearChat" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-black transition shadow-lg shadow-red-500/20"><i class="fas fa-trash-alt mr-1"></i> Vaciar Chat</button>
            </div>
            <div class="bg-gamer-card border border-gray-800 rounded-xl flex-1 flex flex-col overflow-hidden shadow-xl">

                <!-- Message Feed -->
                <div class="flex-1 p-4 overflow-y-auto space-y-3 custom-scrollbar">
                    <div v-for="msg in messages" :key="msg.id" class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="font-black text-xs text-neon-blue">{{ msg.user_name }}</span>
                            <span class="text-[9px] text-gray-500">{{ msg.time }}</span>
                            <button v-if="!msg.is_super_admin" @click="openQuickBanModal(msg)" class="text-[10px] text-red-500 hover:underline ml-auto">Banear</button>
                        </div>
                        <p class="text-sm text-gray-200 bg-gray-900 border border-gray-800/50 px-3 py-1.5 rounded-r-xl rounded-bl-xl mt-0.5 inline-block max-w-lg break-words">{{ msg.message }}</p>
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

        <!-- Support Tickets Tab -->
        <div v-else-if="activeTab === 'tickets'">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- List -->
                <div class="lg:col-span-2 space-y-4">
                    <div v-for="ticket in ticketsSupport" :key="ticket.id" 
                         @click="selectedTicket = ticket; adminReply = ticket.admin_reply || ''; statusTicket = ticket.status"
                         class="bg-gamer-card border border-white/5 p-6 rounded-3xl hover:border-neon-green/50 transition cursor-pointer flex justify-between items-center"
                         :class="{'border-neon-green/50': selectedTicket?.id === ticket.id}">
                        <div>
                            <div class="flex items-center gap-3 mb-1">
                                <span class="text-[9px] font-black uppercase bg-white/5 px-2 py-0.5 rounded text-gray-400">#{{ ticket.id }}</span>
                                <h3 class="font-bold text-base">{{ ticket.subject }}</h3>
                            </div>
                            <p class="text-xs text-gray-500">Usuario: <span class="text-neon-cyan">{{ ticket.user?.name }}</span></p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-[10px] font-black px-3 py-1 rounded-full uppercase" :class="{
                                'bg-yellow-500/20 text-yellow-500': ticket.status === 'open',
                                'bg-blue-500/20 text-blue-500': ticket.status === 'pending',
                                'bg-neon-green/20 text-neon-green': ticket.status === 'closed'
                            }">{{ ticket.status === 'open' ? 'Abierto' : (ticket.status === 'closed' ? 'Cerrado' : ticket.status) }}</span>
                            <button @click.stop="confirmSupportDelete(ticket.id)" 
                                    class="p-2 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition-all shadow-lg shadow-red-500/10"
                                    title="Eliminar Ticket">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div v-if="ticketsSupport.length === 0" class="text-center py-10 text-gray-600 italic">No hay tickets pendientes.</div>
                </div>

                <!-- Reply -->
                <div class="lg:col-span-1">
                    <div v-if="selectedTicket" class="bg-gamer-card border border-white/10 p-8 rounded-3xl shadow-2xl sticky top-8">
                        <h2 class="text-xl font-black uppercase italic mb-6 border-l-4 border-neon-green pl-4">Resolver Ticket</h2>
                        <div class="mb-6">
                            <span class="text-[9px] font-black uppercase text-gray-500 block mb-1">Mensaje del Usuario:</span>
                            <p class="text-[11px] text-gray-300 leading-relaxed bg-black/30 p-4 rounded-xl italic">"{{ selectedTicket.message }}"</p>
                        </div>
                        <div class="space-y-4">
                            <textarea v-model="adminReply" rows="5" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs focus:border-neon-green outline-none" placeholder="Tu respuesta..."></textarea>
                            <select v-model="statusTicket" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs">
                                <option value="open">Abierto</option>
                                <option value="pending">Pendiente</option>
                                <option value="closed">Cerrado</option>
                            </select>
                            <div class="pt-4 border-t border-white/5 flex gap-2">
                                <button @click="updateSupportTicket" 
                                        class="flex-grow py-4 bg-neon-green text-gamer-dark font-black uppercase tracking-widest rounded-xl hover:bg-white transition shadow-neon-green/20">
                                    Actualizar
                                </button>
                                <button @click="confirmSupportDelete(selectedTicket.id)" 
                                        class="px-6 bg-red-500/10 border border-red-500/20 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <LoadingState v-if="loading" />
        <!-- Coupons Tab -->
        <div v-else-if="activeTab === 'coupons'">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Gestión de Cupones</h3>
                <button @click="showCouponModal = true" class="bg-yellow-400 text-gamer-dark px-4 py-1.5 rounded-lg text-xs font-black hover:bg-white transition shadow-yellow-400/20">+ Nuevo Cupón</button>
            </div>
            
            <div class="bg-gamer-card rounded-xl overflow-x-auto border border-gray-800 shadow-xl">
                <table class="w-full text-left border-collapse text-sm whitespace-nowrap md:whitespace-normal">
                    <thead>
                        <tr class="bg-gray-900 text-gray-400 uppercase">
                            <th class="p-3 border-b border-gray-800">Código</th>
                            <th class="p-3 border-b border-gray-800">Tipo</th>
                            <th class="p-3 border-b border-gray-800">Valor</th>
                            <th class="p-3 border-b border-gray-800">Uso</th>
                            <th class="p-3 border-b border-gray-800">Estado</th>
                            <th class="p-3 border-b border-gray-800 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="cp in coupons" :key="cp.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                            <td class="p-3 font-bold text-yellow-400">{{ cp.code }}</td>
                            <td class="p-3 text-xs uppercase">{{ cp.type === 'percentage' ? 'Porcentual' : 'Fijo' }}</td>
                            <td class="p-3 font-bold">{{ cp.value }}{{ cp.type === 'percentage' ? '%' : '€' }}</td>
                            <td class="p-3 text-gray-400 text-xs">{{ cp.used_count }} / {{ cp.usage_limit || '∞' }}</td>
                            <td class="p-3">
                                <span :class="cp.is_active ? 'text-neon-green' : 'text-red-500'" class="text-[10px] font-bold uppercase">
                                    {{ cp.is_active ? 'Activo' : 'Pausado' }}
                                </span>
                            </td>
                            <td class="p-3 text-right">
                                <button @click="toggleCoupon(cp)" class="text-gray-400 hover:text-white mr-3 text-xs">
                                    {{ cp.is_active ? 'Pausar' : 'Activar' }}
                                </button>
                                <button @click="deleteCoupon(cp.id)" class="text-red-500 hover:text-red-400 text-xs">Borrar</button>
                            </td>
                        </tr>
                        <tr v-if="coupons.length === 0">
                            <td colspan="6" class="p-4 text-center text-gray-500 text-xs">No hay cupones creados.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modals and Overlays [...] -->
    </main>

    <!-- Create Coupon Modal -->
    <div v-if="showCouponModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm">
        <div class="bg-gamer-card border border-gray-800 rounded-3xl w-full max-w-md p-8 shadow-2xl animate-in zoom-in duration-300">
            <h2 class="text-2xl font-black text-white uppercase italic tracking-tighter mb-6 border-l-4 border-yellow-400 pl-4">Nuevo <span class="text-yellow-400">Cupón</span></h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] uppercase font-black text-gray-500 mb-1">Código del Cupón</label>
                    <div class="flex gap-2">
                        <input v-model="couponForm.code" type="text" placeholder="EJ: SOUL2026" class="flex-grow bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-white text-sm focus:border-yellow-400 outline-none uppercase">
                        <button @click="generateCouponCode" class="bg-gray-800 hover:bg-gray-700 text-yellow-400 px-3 py-2 rounded-xl text-[10px] font-black uppercase transition">Generar</button>
                    </div>
                    <p v-if="couponErrors.code" class="text-red-500 text-[10px] pt-1">{{ couponErrors.code[0] }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] uppercase font-black text-gray-500 mb-1">Tipo</label>
                        <select v-model="couponForm.type" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-white text-sm focus:border-yellow-400 outline-none">
                            <option value="percentage">Porcentaje</option>
                            <option value="fixed">Cantidad Fija</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase font-black text-gray-500 mb-1">Valor ({{ couponForm.type === 'percentage' ? '%' : '€' }})</label>
                        <input v-model="couponForm.value" type="number" step="0.01" :max="couponForm.type === 'percentage' ? 10 : 999" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-white text-sm focus:border-yellow-400 outline-none">
                        <p v-if="couponForm.type === 'percentage'" class="text-[8px] text-gray-500 mt-1 uppercase font-bold">Máximo permitido: 10%</p>
                    </div>
                </div>

                <!-- Campos de mínimo compra y uso eliminados por simplificación (ahora automáticos) -->

                <div>
                    <label class="block text-[10px] uppercase font-black text-gray-500 mb-1">Expira el (Opcional)</label>
                    <input v-model="couponForm.expires_at" type="date" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-2 text-white text-sm focus:border-yellow-400 outline-none">
                </div>
            </div>

            <div class="mt-8 flex gap-3">
                <button @click="showCouponModal = false" class="flex-grow bg-gray-800 text-white py-3 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-gray-700 transition">Cancelar</button>
                <button @click="saveCoupon" class="flex-grow bg-yellow-400 text-gamer-dark py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-white transition shadow-yellow-400/20">Crear Cupón</button>
            </div>
        </div>
    </div>

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
                        <input type="number" step="0.01" v-model="form.price" max="1000000" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                        <span v-if="formErrors.price" class="text-red-500 text-xs mt-1 block">{{ formErrors.price[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Precio Original (Rebaja)</label>
                        <input type="number" step="0.01" v-model="form.original_price" placeholder="Opcional" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                    </div>

                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Existencias</label>
                        <input type="number" v-model="form.stock" max="999999" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                        <span v-if="formErrors.stock" class="text-red-500 text-xs mt-1 block">{{ formErrors.stock[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Categoría</label>
                        <select v-model="form.category_id" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <span v-if="formErrors.category_id" class="text-red-500 text-xs mt-1 block">{{ formErrors.category_id[0] }}</span>
                    </div>

                    <div class="col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4 bg-gray-900/50 p-4 rounded border border-gray-800">
                        <div class="flex flex-col gap-1 items-center justify-center p-2 border border-gray-800 rounded-lg hover:border-neon-blue/30 transition">
                            <label class="text-[10px] text-gray-400 uppercase font-bold">Exclusivo</label>
                            <input type="checkbox" v-model="form.is_exclusive" class="w-5 h-5 accent-neon-blue">
                        </div>
                        <div class="flex flex-col gap-1 items-center justify-center p-2 border border-gray-800 rounded-lg hover:border-neon-purple/30 transition">
                            <label class="text-[10px] text-gray-400 uppercase font-bold">Destacado</label>
                            <input type="checkbox" v-model="form.featured" class="w-5 h-5 accent-neon-purple">
                        </div>
                        <div class="flex flex-col gap-1 items-center justify-center p-2 border border-gray-800 rounded-lg hover:border-neon-green/30 transition">
                            <label class="text-[10px] text-gray-400 uppercase font-bold">Tendencia</label>
                            <input type="checkbox" v-model="form.trending" class="w-5 h-5 accent-neon-green">
                        </div>
                        <div class="flex flex-col gap-1 items-center justify-center p-2 border border-gray-800 rounded-lg hover:border-red-500/30 transition">
                            <label class="text-[10px] text-red-500 uppercase font-bold">Censurado</label>
                            <input type="checkbox" v-model="form.is_censored" class="w-5 h-5 accent-red-600">
                        </div>
                    </div>

                    <div class="col-span-2 grid grid-cols-2 gap-4 bg-gray-900/50 p-4 rounded border border-gray-800">
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-gray-300 font-bold italic">Anime</label>
                            <input type="checkbox" v-model="form.is_anime" class="w-4 h-4 accent-neon-cyan">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-gray-300 font-bold italic">Marvel</label>
                            <input type="checkbox" v-model="form.is_marvel" class="w-4 h-4 accent-neon-red">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-gray-300 font-bold italic">Star Wars</label>
                            <input type="checkbox" v-model="form.is_star_wars" class="w-4 h-4 accent-neon-blue">
                        </div>
                        <div class="flex items-center justify-between">
                            <label class="text-sm text-gray-300 font-bold italic">DC</label>
                            <input type="checkbox" v-model="form.is_dc" class="w-4 h-4 accent-neon-purple">
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs uppercase text-gray-400 mb-1">Dirección (URL) de la Imagen</label>
                        <input type="text" v-model="form.image_url" placeholder="https://link.com/foto.jpg" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
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
                <h3 class="text-xl font-bold text-white">{{ isEditingRaffle ? 'Editar Sorteo' : 'Crear Nuevo Sorteo' }}</h3>
                <button @click="closeRaffleModal" class="text-gray-400 hover:text-white">&times;</button>
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
                        <label class="block text-xs uppercase text-gray-400 mb-1">Fecha de Inicio</label>
                        <input type="datetime-local" v-model="raffleForm.start_date" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Fecha de Sorteo (Fin)</label>
                        <input type="datetime-local" v-model="raffleForm.draw_date" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Precio Ticket ($)</label>
                    <input type="number" step="0.01" v-model="raffleForm.ticket_price" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition text-sm">
                </div>

                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Dirección (URL) de la Imagen (Opcional)</label>
                    <input type="text" v-model="raffleForm.image_url" placeholder="https://link.com/foto.jpg" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition text-sm">
                </div>


                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Límite Tickets</label>
                        <input type="number" v-model="raffleForm.max_entries" placeholder="Opcional" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs uppercase text-gray-400 mb-1">Elegir Producto</label>
                        <select v-model="raffleForm.product_id" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-purple focus:outline-none transition text-sm">
                            <option value="">Seleccione un producto</option>
                            <option v-for="product in products" :key="product.id" :value="product.id">
                                 {{ product.name }} (Existencias: {{ product.stock }})
                            </option>
                        </select>
                    </div>

                </div>
            </div>

            <div class="p-6 border-t border-gray-800 flex justify-end gap-3">
                <button @click="closeRaffleModal" class="px-6 py-2 rounded text-gray-400 hover:text-white transition cursor-pointer">Cancelar</button>
                <button @click="saveRaffle" class="bg-neon-purple text-white px-6 py-2 rounded font-black hover:bg-purple-600 shadow-neon-purple transition cursor-pointer">
                    {{ isEditingRaffle ? 'Guardar Cambios' : 'Crear Sorteo' }}
                </button>
            </div>
        </div>
    </div>
    <!-- Modal Crear Usuario -->

    <div v-if="showUserModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4 backdrop-blur-sm">
        <div class="bg-gamer-card border border-gray-700 w-full max-w-md rounded-xl shadow-2xl shadow-neon-blue/20 flex flex-col">
            <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">{{ isEditingUser ? 'Editar Usuario' : 'Crear Usuario' }}</h3>
                <button @click="showUserModal = false" class="text-gray-400 hover:text-white">&times;</button>
            </div>
            
            <form @submit.prevent="saveUser" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Nombre</label>
                    <input type="text" v-model="userForm.name" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                    <span v-if="userErrors.name" class="text-red-500 text-xs mt-1 block">{{ userErrors.name[0] }}</span>
                </div>

                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Email</label>
                    <input type="email" v-model="userForm.email" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                    <span v-if="userErrors.email" class="text-red-500 text-xs mt-1 block">{{ userErrors.email[0] }}</span>
                </div>

                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Contraseña</label>
                    <input type="password" v-model="userForm.password" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                    <span v-if="userErrors.password" class="text-red-500 text-xs mt-1 block">{{ userErrors.password[0] }}</span>
                </div>

                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Confirmar Contraseña</label>
                    <input type="password" v-model="userForm.password_confirmation" class="w-full bg-gray-900 border border-gray-700 rounded p-2 text-white focus:border-neon-blue focus:outline-none transition">
                </div>

                <div v-if="currentUser && currentUser.is_super_admin" class="flex items-center gap-2">
                    <input type="checkbox" v-model="userForm.is_admin" class="rounded border-gray-700 bg-gray-900 text-neon-blue focus:ring-neon-blue">
                    <label class="text-xs text-gray-300">Es Administrador</label>
                </div>

                <div class="pt-4 border-t border-gray-800 flex justify-end gap-3">
                    <button type="button" @click="showUserModal = false" class="px-4 py-2 rounded text-gray-400 hover:text-white transition text-xs">Cancelar</button>
                    <button type="submit" class="bg-gradient-to-r from-neon-purple to-neon-blue text-white px-6 py-2 rounded font-bold hover:scale-105 transition text-xs">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div v-if="showBanModal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        <div class="bg-gamer-card border border-gray-800 rounded-2xl w-full max-w-md shadow-2xl">
            <div class="p-6 border-b border-gray-800 flex justify-between items-center">
                <h3 class="text-xl font-bold text-white flex items-center gap-2">
                    <i class="fas fa-gavel text-red-500"></i>
                    Banear Usuario
                </h3>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Razón del baneo:</label>
                    <input v-model="banReason" type="text" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-500" placeholder="Ej. Incumplimiento de normas">
                </div>
                
                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Tipo de baneo:</label>
                    <div class="flex gap-4 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="banType" value="account" class="accent-red-500">
                            <span class="text-white text-xs uppercase font-bold">Cuenta Completa</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" v-model="banType" value="chat" class="accent-red-500">
                            <span class="text-white text-xs uppercase font-bold">Solo Chat</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs uppercase text-gray-400 mb-1">Duración:</label>
                    <select v-model="banDuration" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white text-sm focus:outline-none focus:border-red-500">
                        <option value="1">1 hora</option>
                        <option value="6">6 horas</option>
                        <option value="24">24 horas (1 día)</option>
                        <option value="168">1 semana</option>
                        <option value="permanent">Permanente</option>
                    </select>
                </div>
            </div>

            <div class="p-6 border-t border-gray-800 flex justify-end gap-3">
                <button @click="showBanModal = false" class="px-4 py-2 rounded text-gray-400 hover:text-white transition">Cancelar</button>
                <button @click="submitBan" class="bg-red-600 text-white px-6 py-2 rounded font-bold hover:bg-red-700 transition">
                    Confirmar Baneo
                </button>
            </div>
        </div>
    </div>
    <!-- Custom Support Delete Modal -->
    <Teleport to="body">
        <div v-if="showSupportDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="showSupportDeleteModal = false"></div>
            <div class="bg-gamer-card border border-red-500/30 p-8 rounded-3xl max-w-sm w-full relative z-10 shadow-2xl animate-fade-in-up">
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-500/10 text-red-500 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-500/20">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-black uppercase italic mb-2 tracking-tighter">¿Eliminar Ticket?</h3>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-6">Esta acción es permanente y no se puede deshacer.</p>
                    
                    <div class="flex gap-3">
                        <button @click="showSupportDeleteModal = false" 
                                class="flex-grow py-3 bg-white/5 border border-white/10 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-white/10 transition">
                            Cancelar
                        </button>
                        <button @click="deleteSupportTicket" 
                                class="flex-grow py-3 bg-red-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-red-600/20 hover:bg-red-500 transition">
                            Confirmar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
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
