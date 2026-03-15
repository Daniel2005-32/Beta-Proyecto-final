<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') : 'http://localhost:8000/api';
const addresses = ref([]);
const loading = ref(true);

// New address form state
const showForm = ref(false);
const formData = ref({
    name: '',
    phone: '',
    street: '',
    number: '',
    complement: '',
    city: '',
    state: '',
    zipcode: '',
    is_default: false
});

const loadAddresses = async () => {
    loading.value = true;
    try {
        const token = localStorage.getItem('token');
        const res = await axios.get(`${apiBase}/addresses`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        addresses.value = res.data.addresses || [];
    } catch (err) {
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    if (!localStorage.getItem('token')) {
        router.push('/login');
        return;
    }
    loadAddresses();
});

const saveAddress = async () => {
    try {
        const token = localStorage.getItem('token');
        await axios.post(`${apiBase}/addresses`, formData.value, {
            headers: { Authorization: `Bearer ${token}` }
        });
        
        // Reset and reload
        showForm.value = false;
        formData.value = { name: '', phone: '', street: '', number: '', complement: '', city: '', state: '', zipcode: '', is_default: false };
        await loadAddresses();
    } catch (err) {
        alert('Error al guardar la dirección');
    }
};

const deleteAddress = async (id) => {
    if(!confirm("¿Deseas eliminar esta dirección?")) return;
    try {
        const token = localStorage.getItem('token');
        await axios.delete(`${apiBase}/addresses/${id}`, {
            headers: { Authorization: `Bearer ${token}` }
        });
        await loadAddresses();
    } catch (err) {
        alert('Error al eliminar');
    }
};

const setDefault = async (id) => {
    try {
        const token = localStorage.getItem('token');
        await axios.patch(`${apiBase}/addresses/${id}/set-default`, {}, {
            headers: { Authorization: `Bearer ${token}` }
        });
        await loadAddresses();
    } catch (err) {
        alert('Error al establecer como predeterminada');
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Mis Direcciones</h1>
        <div>
            <router-link to="/profile" class="text-emerald-600 hover:underline mr-4">Volver al Perfil</router-link>
            <button @click="showForm = true" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">
                + Nueva Dirección
            </button>
        </div>
    </div>

    <!-- Create Form -->
    <div v-if="showForm" class="bg-white p-6 rounded shadow mb-8 border border-indigo-200">
        <h2 class="font-bold text-xl mb-4">Añadir Dirección</h2>
        <form @submit.prevent="saveAddress" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div><label class="block text-gray-700 text-sm mb-1">Nombre Identificativo (Ej: Casa)</label>
            <input v-model="formData.name" required class="w-full border rounded px-3 py-2"></div>
            
            <div><label class="block text-gray-700 text-sm mb-1">Télefono</label>
            <input v-model="formData.phone" required class="w-full border rounded px-3 py-2"></div>

            <div class="md:col-span-2"><label class="block text-gray-700 text-sm mb-1">Calle</label>
            <input v-model="formData.street" required class="w-full border rounded px-3 py-2"></div>

            <div><label class="block text-gray-700 text-sm mb-1">Número</label>
            <input v-model="formData.number" required class="w-full border rounded px-3 py-2"></div>

            <div><label class="block text-gray-700 text-sm mb-1">Complemento (Opcional)</label>
            <input v-model="formData.complement" class="w-full border rounded px-3 py-2"></div>

            <div><label class="block text-gray-700 text-sm mb-1">Ciudad</label>
            <input v-model="formData.city" required class="w-full border rounded px-3 py-2"></div>

            <div><label class="block text-gray-700 text-sm mb-1">Estado / Provincia</label>
            <input v-model="formData.state" required class="w-full border rounded px-3 py-2"></div>

            <div><label class="block text-gray-700 text-sm mb-1">Código Postal</label>
            <input v-model="formData.zipcode" required class="w-full border rounded px-3 py-2"></div>

            <div class="md:col-span-2 flex items-center mt-2">
                <input type="checkbox" v-model="formData.is_default" id="is_default" class="mr-2">
                <label for="is_default" class="text-gray-700">Establecer como predeterminada</label>
            </div>

            <div class="md:col-span-2 flex justify-end gap-2 mt-4">
                <button type="button" @click="showForm = false" class="px-4 py-2 border rounded hover:bg-gray-50 text-gray-700">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded hover:bg-emerald-700">Guardar Dirección</button>
            </div>
        </form>
    </div>

    <!-- Address List -->
    <div v-if="loading" class="text-center text-gray-500 py-8">Cargando direcciones...</div>
    <div v-else-if="addresses.length === 0" class="text-center bg-gray-50 p-8 rounded shadow text-gray-600">
        No tienes direcciones guardadas aún.
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="address in addresses" :key="address.id" 
             class="border rounded shadow-sm p-4 relative" 
             :class="{'border-emerald-500 bg-emerald-50': address.is_default, 'bg-white': !address.is_default}">
            
            <div v-if="address.is_default" class="absolute top-2 right-2 bg-emerald-500 text-white text-xs px-2 py-1 rounded-full">
                Predeterminada
            </div>

            <h3 class="font-bold text-lg text-gray-800">{{ address.name }}</h3>
            <p class="text-gray-600 text-sm">{{ address.street }} {{ address.number }} <span v-if="address.complement">({{ address.complement }})</span></p>
            <p class="text-gray-600 text-sm">{{ address.zipcode }}, {{ address.city }} ({{ address.state }})</p>
            <p class="text-gray-600 text-sm mt-2 font-medium">📞 {{ address.phone }}</p>

            <div class="mt-4 flex gap-2 pt-3 border-t">
                <button v-if="!address.is_default" @click="setDefault(address.id)" class="text-xs text-indigo-600 hover:underline">Hacer Predeterminada</button>
                <div class="flex-grow"></div>
                <button @click="deleteAddress(address.id)" class="text-xs text-red-600 hover:underline cursor-pointer">Eliminar</button>
            </div>
        </div>
    </div>
  </div>
</template>
