<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';
const user = ref({});
const oldPassword = ref('');
const newPassword = ref('');
const newPasswordConfirm = ref('');

const successMsg = ref('');
const errorMsg = ref('');

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
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Mi Perfil</h1>
        <router-link to="/profile/addresses" class="bg-indigo-600 text-white px-4 py-2 rounded shadow hover:bg-indigo-700 transition">Mis Direcciones</router-link>
    </div>

    <div v-if="successMsg" class="mb-4 bg-green-100 text-green-800 p-3 rounded">{{ successMsg }}</div>
    <div v-if="errorMsg" class="mb-4 bg-red-100 text-red-800 p-3 rounded">{{ errorMsg }}</div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Quick Links -->
            <div class="bg-white p-6 rounded shadow border">
                <h3 class="text-xl font-bold mb-4">Atajos Rápidos</h3>
                <ul class="space-y-3">
                    <li>
                        <router-link to="/cart" class="text-emerald-600 font-bold hover:underline flex items-center">
                            <i class="fas fa-shopping-cart mr-2"></i> Mi Carrito
                        </router-link>
                    </li>
                    <li v-if="user && (user.is_admin === 1 || user.is_admin === true)">
                        <router-link to="/admin" class="text-neon-purple font-black hover:underline flex items-center">
                            <i class="fas fa-shield-alt mr-2"></i> Panel de Administración
                        </router-link>
                    </li>
                    <li>
                        <router-link to="/auctions" class="text-emerald-600 font-bold hover:underline flex items-center">
                            <i class="fas fa-gavel mr-2"></i> Subastas Activas
                        </router-link>
                    </li>
                </ul>
            </div>
        <!-- Update Profile Info -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Datos Básicos</h2>
            <form @submit.prevent="updateProfile">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Nombre</label>
                    <input v-model="user.name" type="text" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Correo Electrónico</label>
                    <input v-model="user.email" type="email" class="w-full border rounded px-3 py-2" required>
                </div>
                <button type="submit" class="w-full bg-emerald-600 text-white py-2 rounded hover:bg-emerald-700 transition">
                    Guardar Cambios
                </button>
            </form>
        </div>

        <!-- Update Password -->
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold mb-4 border-b pb-2">Cambiar Contraseña</h2>
            <form @submit.prevent="changePassword">
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Contraseña Actual</label>
                    <input v-model="oldPassword" type="password" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Nueva Contraseña</label>
                    <input v-model="newPassword" type="password" class="w-full border rounded px-3 py-2" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-bold mb-2">Confirmar Nueva Contraseña</label>
                    <input v-model="newPasswordConfirm" type="password" class="w-full border rounded px-3 py-2" required>
                </div>
                <button type="submit" class="w-full bg-gray-800 text-white py-2 rounded hover:bg-gray-900 transition">
                    Cambiar Contraseña
                </button>
            </form>
        </div>
    </div>
  </div>
</template>
