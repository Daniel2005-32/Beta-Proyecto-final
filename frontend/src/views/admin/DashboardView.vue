<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
const products = ref([]);
const categories = ref([]);
const loading = ref(true);
const error = ref(null);

const showModal = ref(false);
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
        const resProducts = await axios.get(`${apiBase}/admin/products`);
        products.value = resProducts.data.products || [];
        categories.value = resProducts.data.categories || [];
    } catch (err) {
        error.value = err.response ? 
            (err.response.data.message || JSON.stringify(err.response.data)) : 
            err.message;
        console.error("Dashboard api error", err.response || err);
    } finally {
        loading.value = false;
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
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-neon-blue to-neon-purple">
                Inventario de Productos
            </h2>
            <button @click="openModal()" class="bg-neon-blue text-gamer-dark px-6 py-2 rounded font-black hover:bg-blue-400 transition hover:shadow-neon-blue">
                + Nuevo Producto
            </button>
        </div>

        <div v-if="error" class="bg-red-900 border border-red-500 text-white p-4 mb-4 rounded shadow-xl">
            API Error: {{ error }}
        </div>

        <div v-if="loading" class="text-center py-10 animate-pulse text-neon-blue">
            Cargando inventario...
        </div>

        <!-- Products Table -->
        <div v-else class="bg-gamer-card rounded-lg overflow-hidden border border-gray-800 shadow-xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-900 text-gray-400 uppercase text-xs">
                        <th class="p-4 border-b border-gray-800">Foto</th>
                        <th class="p-4 border-b border-gray-800">Nombre</th>
                        <th class="p-4 border-b border-gray-800">Precio</th>
                        <th class="p-4 border-b border-gray-800">Stock</th>
                        <th class="p-4 border-b border-gray-800">Categoría</th>
                        <th class="p-4 border-b border-gray-800 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products" :key="product.id" class="hover:bg-gray-800 transition border-b border-gray-800/50">
                        <td class="p-4">
                            <div class="w-12 h-12 bg-gray-900 rounded overflow-hidden flex items-center justify-center">
                                <img v-if="product.image_url" :src="product.image_url" class="w-full h-full object-cover">
                                <span v-else class="text-[10px] text-gray-500">N/A</span>
                            </div>
                        </td>
                        <td class="p-4 font-bold text-white max-w-xs truncate">{{ product.name }}</td>
                        <td class="p-4 text-neon-green font-mono">${{ product.price }}</td>
                        <td class="p-4">
                            <span :class="{'text-red-500 font-bold': product.stock === 0}">{{ product.stock }} u.</span>
                        </td>
                        <td class="p-4 text-gray-400 text-sm">{{ product.category ? product.category.name : 'N/A' }}</td>
                        <td class="p-4 text-right">
                            <button @click="openModal(product)" class="text-neon-blue hover:text-white mr-4 transition cursor-pointer text-sm font-bold uppercase">Editar</button>
                            <button @click="deleteProduct(product.id)" class="text-neon-red hover:text-red-400 transition cursor-pointer text-sm font-bold uppercase">Borrar</button>
                        </td>
                    </tr>
                    <tr v-if="products.length === 0">
                        <td colspan="6" class="p-8 text-center text-gray-500">No hay productos en la base de datos.</td>
                    </tr>
                </tbody>
            </table>
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
