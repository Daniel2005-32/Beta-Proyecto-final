<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LoadingState from '@/components/LoadingState.vue';
import { useRouter } from 'vue-router';

const router = useRouter();
import { apiBase } from '@/utils/api';

const categories = ref([]);
const loading = ref(false);
const error = ref(null);
const success = ref(null);

const form = ref({
    name: '',
    description: '',
    price: '',
    category_id: '',
    duration: 24,
    image_url: ''
});


const imagePreview = ref(null);

onMounted(async () => {
    // Verificar si es admin
    const user = JSON.parse(localStorage.getItem('user') || '{}');
    if (!user.is_admin) {
        router.push('/');
        return;
    }

    try {
        const token = localStorage.getItem('token');
        const res = await axios.get(`${apiBase}/admin/products`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        categories.value = res.data.categories || [];
    } catch (err) {
        error.value = "No se pudieron cargar las categorías.";
    }
});


// No file handlers needed



const submitAuction = async () => {
    loading.value = true;
    error.value = null;
    success.value = null;

    const token = localStorage.getItem('token');
    if (!token) {
        router.push({ path: '/register', query: { message: 'Primero debes registrarte para crear una subasta.' } });
        return;
    }

    try {
        await axios.post(`${apiBase}/user/auctions`, {
            name: form.value.name,
            description: form.value.description,
            price: form.value.price,
            category_id: form.value.category_id,
            duration: form.value.duration,
            image_url: form.value.image_url
        }, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        });

        success.value = "¡Subasta creada correctamente! Redirigiendo...";
        setTimeout(() => {
            router.push('/auctions');
        }, 2000);
    } catch (err) {
        error.value = err.response?.data?.error || err.response?.data?.message || "Error al crear la subasta.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-12 max-w-lg text-white">
    <div class="bg-gamer-card border border-gray-800 rounded-3xl p-8 shadow-2xl shadow-neon-purple/10">
        <div class="flex items-center gap-3 mb-6 border-b border-gray-800 pb-4">
            <div class="w-1.5 h-6 bg-neon-purple"></div>
            <h1 class="text-2xl font-black uppercase tracking-wider">Crear Subasta</h1>
        </div>

        <div v-if="success" class="mb-4 p-4 bg-green-500/10 border border-green-500/30 text-green-400 rounded-xl text-sm font-bold">
            {{ success }}
        </div>
        <div v-if="error" class="mb-4 p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-sm font-bold">
            {{ error }}
        </div>


        <LoadingState v-if="loading && categories.length === 0" />

        <form v-else @submit.prevent="submitAuction" class="space-y-5">
            <!-- Imagen URL -->
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-1.5">URL de la Imagen del Producto</label>
                <input v-model="form.image_url" type="url" required placeholder="https://ejemplo.com/imagen.jpg" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-neon-purple/50 text-gray-200">
                <p class="text-gray-500 text-[10px] mt-1">Sube la foto a un servidor externo (ej: Imgur o similar) e inserta el enlace aquí.</p>
            </div>


            <!-- Nombre -->
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-1.5">Nombre del Artículo</label>
                <input v-model="form.name" type="text" required placeholder="Ej: Figura Anime Exclusiva" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-neon-purple/50 text-gray-200">
            </div>

            <!-- Categoría -->
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-1.5">Categoría</label>
                <select v-model="form.category_id" required class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-neon-purple/50 text-gray-200">
                    <option value="" disabled>Selecciona una categoría</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase mb-1.5">Descripción</label>
                <textarea v-model="form.description" required rows="3" placeholder="Detalla el estado y características del artículo..." class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-neon-purple/50 text-gray-200"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <!-- Precio -->
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-1.5">Precio Inicial (€)</label>
                    <input v-model="form.price" type="number" step="0.01" required placeholder="10.00" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-neon-purple/50 text-gray-200">
                </div>

                <!-- Duración -->
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase mb-1.5">Duración</label>
                    <input v-model="form.duration" type="number" required min="1" max="1000" placeholder="Ej: 24" class="w-full bg-gray-900 border border-gray-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-neon-purple/50 text-gray-200">
                    <p class="text-gray-500 text-[10px] mt-1">Duración en horas. Máximo 1000 horas.</p>
                </div>
            </div>

            <button type="submit" :disabled="loading" class="w-full bg-neon-purple hover:bg-neon-purple/90 text-white font-black uppercase tracking-wider py-3.5 rounded-xl transition shadow-neon-purple flex items-center justify-center gap-2 cursor-pointer">
                <span v-if="loading" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                {{ loading ? 'Creando...' : 'Iniciar Subasta' }}
            </button>
        </form>
    </div>
  </div>
</template>
