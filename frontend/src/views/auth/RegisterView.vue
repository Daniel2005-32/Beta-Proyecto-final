<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const router = useRouter();
const apiBase = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';
const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const error = ref(null);
const loading = ref(false);

const register = async () => {
    loading.value = true;
    error.value = null;
    
    if(form.value.password !== form.value.password_confirmation) {
        error.value = "Las contraseñas no coinciden.";
        loading.value = false;
        return;
    }
    
    try {
        const response = await axios.post(`${apiBase}/register`, form.value);
        localStorage.setItem('token', response.data.access_token);
        localStorage.setItem('user', JSON.stringify(response.data.user));
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
        
        router.push('/');
    } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
            error.value = err.response.data.message;
        } else {
            error.value = 'Ha ocurrido un error al registrar el usuario.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-8 rounded shadow">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Registrar Cuenta
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="register">
        <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
          <span class="block sm:inline">{{ error }}</span>
        </div>
        
        <div class="rounded-md shadow-sm -space-y-px">
           <div>
            <label for="name" class="sr-only">Nombre Completo</label>
            <input id="name" name="name" type="text" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm" placeholder="Nombre completo" v-model="form.name">
          </div>
          <div>
            <label for="email-address" class="sr-only">Correo electrónico</label>
            <input id="email-address" name="email" type="email" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm" placeholder="Correo electrónico" v-model="form.email">
          </div>
          <div>
            <label for="password" class="sr-only">Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm" placeholder="Contraseña (Mín. 8 caracteres)" v-model="form.password">
          </div>
          <div>
            <label for="password_confirmation" class="sr-only">Confirmar Contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-sm" placeholder="Confirmar Contraseña" v-model="form.password_confirmation">
          </div>
        </div>

        <div>
          <button type="submit" :disabled="loading" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50">
            {{ loading ? 'Registrando...' : 'Registrarse' }}
          </button>
        </div>
        <div class="text-sm text-center">
            <router-link to="/login" class="font-medium text-emerald-600 hover:text-emerald-500">¿Ya tienes cuenta? Inicia sesión aquí</router-link>
        </div>
      </form>
    </div>
  </div>
</template>
