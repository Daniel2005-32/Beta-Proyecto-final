<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

import { store } from '../../utils/store';
import { translateLaravelErrors } from '../../utils/errorTranslator';

const router = useRouter();
import { apiBase } from '@/utils/api';
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
        store.setAuth(response.data.user, response.data.access_token);
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
        
        router.push('/');
    } catch (err) {
        if (err.response && err.response.data && err.response.data.errors) {
            error.value = translateLaravelErrors(err.response.data.errors);
        } else if (err.response && err.response.data && err.response.data.error) {
            error.value = translateLaravelErrors(err.response.data.error);
        } else if (err.response && err.response.data && err.response.data.message) {
            error.value = translateLaravelErrors(err.response.data.message);
        } else {
            error.value = 'Ha ocurrido un error al registrar el usuario.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gamer-dark py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Fondos neón -->
    <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-neon-purple/10 rounded-full filter blur-3xl"></div>
    <div class="absolute bottom-1/4 right-1/4 w-72 h-72 bg-neon-blue/10 rounded-full filter blur-3xl"></div>

    <div class="max-w-md w-full space-y-8 bg-gamer-card p-8 rounded-2xl border border-gray-800 shadow-2xl relative z-10 shadow-neon-purple/10">
      <div>
        <h2 class="mt-6 text-center text-3xl font-black uppercase italic tracking-tighter text-white">
          Crear <span class="text-neon-purple">Cuenta</span>
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="register">
        <div v-if="error" class="bg-red-900/50 border border-red-500 text-white px-4 py-3 rounded-lg text-xs" role="alert">
          <span class="block sm:inline">{{ error }}</span>
        </div>
        
        <div class="rounded-md shadow-sm space-y-4">
           <div>
            <label for="name" class="block text-xs uppercase text-gray-400 mb-1 font-bold">Nombre Completo</label>
            <input id="name" name="name" type="text" required class="appearance-none rounded-xl relative block w-full px-4 py-3 bg-gray-900 border border-gray-700 placeholder-gray-600 text-white focus:outline-none focus:ring-0 focus:border-neon-purple transition text-sm" placeholder="Ej: Daniel López" v-model="form.name">
          </div>
          <div>
            <label for="email-address" class="block text-xs uppercase text-gray-400 mb-1 font-bold">Correo electrónico</label>
            <input id="email-address" name="email" type="email" autocomplete="email" required class="appearance-none rounded-xl relative block w-full px-4 py-3 bg-gray-900 border border-gray-700 placeholder-gray-600 text-white focus:outline-none focus:ring-0 focus:border-neon-purple transition text-sm" placeholder="ejemplo@correo.com" v-model="form.email">
          </div>
          <div>
            <label for="password" class="block text-xs uppercase text-gray-400 mb-1 font-bold">Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="new-password" required class="appearance-none rounded-xl relative block w-full px-4 py-3 bg-gray-900 border border-gray-700 placeholder-gray-600 text-white focus:outline-none focus:ring-0 focus:border-neon-purple transition text-sm" placeholder="••••••••" v-model="form.password">
          </div>
          <div>
            <label for="password_confirmation" class="block text-xs uppercase text-gray-400 mb-1 font-bold">Confirmar Contraseña</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required class="appearance-none rounded-xl relative block w-full px-4 py-3 bg-gray-900 border border-gray-700 placeholder-gray-600 text-white focus:outline-none focus:ring-0 focus:border-neon-purple transition text-sm" placeholder="••••••••" v-model="form.password_confirmation">
          </div>
        </div>

        <div>
          <button type="submit" :disabled="loading" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-black rounded-xl text-white bg-neon-purple hover:bg-white hover:text-gamer-dark hover:shadow-neon-purple transition duration-300 disabled:opacity-50 uppercase tracking-wider">
            {{ loading ? 'Registrando...' : 'Unirse al Gremio' }}
          </button>
        </div>
        <div class="text-xs text-center">
            <router-link to="/login" class="font-bold text-gray-400 hover:text-neon-purple transition">¿Ya tienes cuenta? <span class="text-neon-blue">Inicia sesión aquí</span></router-link>
        </div>
      </form>
    </div>
  </div>
</template>

