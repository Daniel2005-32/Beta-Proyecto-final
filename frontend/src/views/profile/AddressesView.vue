<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import LoadingState from '../../components/LoadingState.vue';
import { useRoute, useRouter } from 'vue-router';
import { store } from '../../utils/store';

const route = useRoute();
const router = useRouter();
import { apiBase } from '@/utils/api';
const addresses = ref([]);
const loading = ref(true);

const isEditing = ref(false);
const editingId = ref(null);

// New address form state
const showForm = ref(false);
const formData = ref({
    name: '',
    phone: '',
    street: '',
    number: '',
    city: '',
    state: '',
    zipcode: '',
    is_default: false
});

const editAddress = (address) => {
    isEditing.value = true;
    editingId.value = address.id;
    formData.value = { ...address };
    showForm.value = true;
};

const validatePhone = (e) => {
    formData.value.phone = e.target.value.replace(/[^0-9]/g, '').substring(0, 9);
};

const validateZipcode = (e) => {
    formData.value.zipcode = e.target.value.replace(/[^0-9]/g, '').substring(0, 5);
};

const validateState = (e) => {
    formData.value.state = e.target.value.replace(/[^A-Za-z]/g, '').substring(0, 2).toUpperCase();
};

const validateNumber = (e) => {
    formData.value.number = e.target.value.replace(/[^0-9]/g, '');
};

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
        router.push('/register');
        return;
    }
    loadAddresses();
});

const saveAddress = async () => {
    // Validaciones estrictas
    const phoneRegex = /^[0-9]{9}$/;
    if (!phoneRegex.test(formData.value.phone)) {
        store.notify('El teléfono debe tener exactamente 9 números.', 'error');
        return;
    }
    const zipRegex = /^[0-9]{5}$/;
    if (!zipRegex.test(formData.value.zipcode)) {
        store.notify('El código postal debe tener exactamente 5 números.', 'error');
        return;
    }
    if (isNaN(formData.value.number) || String(formData.value.number).trim() === '') {
        store.notify('El número debe ser un valor numérico.', 'error');
        return;
    }

    try {
        const token = localStorage.getItem('token');
        if (isEditing.value) {
            await axios.put(`${apiBase}/addresses/${editingId.value}`, formData.value, {
                headers: { Authorization: `Bearer ${token}` }
            });
        } else {
            await axios.post(`${apiBase}/addresses`, formData.value, {
                headers: { Authorization: `Bearer ${token}` }
            });
        }
        
        // Reset and reload
        showForm.value = false;
        isEditing.value = false;
        editingId.value = null;
        formData.value = { name: '', phone: '', street: '', number: '', city: '', state: '', zipcode: '', is_default: false };
        await loadAddresses();

        // Redirección inteligente si venimos del checkout
        if (route.query.redirect) {
            router.push(route.query.redirect);
        }
    } catch (err) {
        store.notify(isEditing.value ? 'Error al actualizar la dirección' : 'Error al guardar la dirección', 'error');
    }
};

const deleteAddress = async (id) => {
    store.confirm("Eliminar Dirección", "¿Deseas eliminar esta dirección?", async () => {
        try {
            const token = localStorage.getItem('token');
            await axios.delete(`${apiBase}/addresses/${id}`, {
                headers: { Authorization: `Bearer ${token}` }
            });
            await loadAddresses();
        } catch (err) {
            store.notify('Error al eliminar', 'error');
        }
    });
};

const setDefault = async (id) => {
    try {
        const token = localStorage.getItem('token');
        await axios.patch(`${apiBase}/addresses/${id}/set-default`, {}, {
            headers: { Authorization: `Bearer ${token}` }
        });
        await loadAddresses();
    } catch (err) {
        store.notify('Error al establecer como predeterminada', 'error');
    }
};
</script>

<template>
  <div class="container mx-auto px-4 py-8 max-w-4xl text-white">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-black uppercase italic tracking-tighter border-l-4 border-neon-blue pl-4">Mis <span class="text-neon-blue">Direcciones</span></h1>
        <div class="flex items-center gap-3">
            <router-link to="/profile" class="text-neon-blue hover:underline text-sm font-bold flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Volver
            </router-link>
            <button @click="showForm = true" class="bg-neon-blue text-gamer-dark font-black px-4 py-2 rounded-xl hover:bg-white hover:shadow-neon-blue transition duration-300 text-xs uppercase tracking-wider">
                + Nueva Dirección
            </button>
        </div>
    </div>

    <!-- Create/Edit Form -->
    <div v-if="showForm" class="bg-gamer-card p-6 rounded-2xl border border-gray-800 mb-8 shadow-2xl">
        <h2 class="font-black uppercase text-lg mb-6 border-b border-gray-800 pb-2 text-white">{{ isEditing ? 'Editar Dirección' : 'Añadir Dirección' }}</h2>
        <form @submit.prevent="saveAddress" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-400 font-bold text-xs uppercase tracking-wider mb-1">Nombre Identificativo (Ej: Casa)</label>
                <input v-model="formData.name" required class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-neon-blue transition">
            </div>
            
            <div>
                <label class="block text-gray-400 font-bold text-xs uppercase tracking-wider mb-1">Teléfono</label>
                <input v-model="formData.phone" @input="validatePhone" maxlength="9" required class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-neon-blue transition">
            </div>

            <div class="md:col-span-2">
                <label class="block text-gray-400 font-bold text-xs uppercase tracking-wider mb-1">Calle</label>
                <input v-model="formData.street" required class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-neon-blue transition">
            </div>

            <div>
                <label class="block text-gray-400 font-bold text-xs uppercase tracking-wider mb-1">Número</label>
                <input v-model="formData.number" @input="validateNumber" required class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-neon-blue transition">
            </div>


            <div>
                <label class="block text-gray-400 font-bold text-xs uppercase tracking-wider mb-1">Ciudad</label>
                <input v-model="formData.city" required class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-neon-blue transition">
            </div>

            <div>
                <label class="block text-gray-400 font-bold text-xs uppercase tracking-wider mb-1">Provincia (Siglas)</label>
                <input v-model="formData.state" @input="validateState" maxlength="2" required class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-neon-blue transition">
            </div>

            <div>
                <label class="block text-gray-400 font-bold text-xs uppercase tracking-wider mb-1">Código Postal</label>
                <input v-model="formData.zipcode" @input="validateZipcode" maxlength="5" required class="w-full bg-gray-900 border border-gray-800 rounded-xl px-3 py-2 text-white focus:outline-none focus:border-neon-blue transition">
            </div>

            <div class="md:col-span-2 flex items-center mt-2">
                <input type="checkbox" v-model="formData.is_default" id="is_default" class="mr-2 accent-neon-blue">
                <label for="is_default" class="text-gray-300 text-sm">Establecer como predeterminada</label>
            </div>

            <div class="md:col-span-2 flex justify-end gap-2 mt-4">
                <button type="button" @click="showForm = false; isEditing = false; formData = { name: '', phone: '', street: '', number: '', city: '', state: '', zipcode: '', is_default: false }" class="px-4 py-2 border border-gray-800 bg-gray-900 text-gray-400 rounded-xl hover:bg-gray-800 transition text-sm">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-neon-blue text-gamer-dark font-black rounded-xl hover:bg-white hover:shadow-neon-blue transition uppercase text-sm">{{ isEditing ? 'Actualizar' : 'Guardar' }} Dirección</button>
            </div>
        </form>
    </div>

    <!-- Address List -->
    <LoadingState v-if="loading" />
    <div v-else-if="addresses.length === 0" class="text-center bg-gamer-card border border-gray-800 border-dashed p-12 rounded-2xl text-gray-500 flex flex-col items-center gap-2">
        <svg class="w-12 h-12 text-gray-700 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
        No tienes direcciones guardadas aún.
    </div>
    
    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div v-for="address in addresses" :key="address.id" 
             class="bg-gamer-card rounded-2xl border border-gray-800 p-5 relative shadow-xl hover:border-gray-700 transition" 
             :class="{'border-neon-blue bg-neon-blue/5': address.is_default}">
            
            <div v-if="address.is_default" class="absolute top-4 right-4 bg-neon-blue text-gamer-dark text-[10px] font-black px-2 py-1 rounded-full uppercase tracking-wider">
                Predeterminada
            </div>

            <h3 class="font-bold text-lg text-white mb-1">{{ address.name }}</h3>
            <p class="text-gray-400 text-sm">{{ address.street }} {{ address.number }}</p>
            <p class="text-gray-400 text-sm">{{ address.zipcode }}, {{ address.city }} ({{ address.state }})</p>
            <p class="text-neon-blue text-sm mt-3 font-bold flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.65l1.24 2.87a1 1 0 01-.24 1.1l-1.93 1.93a16.05 16.05 0 006.57 6.57l1.93-1.93a1 1 0 011.1-.24l2.87 1.24a1 1 0 01.65.94V19a2 2 0 01-2 2h-13a2 2 0 01-2-2V5z"></path></svg>
                {{ address.phone }}
            </p>

            <div class="mt-5 flex gap-3 pt-4 border-t border-gray-800/80">
                <button v-if="!address.is_default" @click="setDefault(address.id)" class="text-xs text-neon-blue hover:underline font-bold">Hacer Predeterminada</button>
                <button @click="editAddress(address)" class="text-xs text-neon-purple hover:underline font-bold">Editar</button>
                <div class="flex-grow"></div>
                <button @click="deleteAddress(address.id)" class="text-xs text-neon-red hover:underline font-bold cursor-pointer">Eliminar</button>
            </div>
        </div>
    </div>
  </div>
</template>
