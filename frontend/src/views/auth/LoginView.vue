<script setup>
import { ref, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import axios from 'axios';

import { store } from '../../utils/store';
import { translateLaravelErrors } from '../../utils/errorTranslator';

const router = useRouter();
const route = useRoute();
import { apiBase } from '../../utils/api';
const form = ref({
    email: '',
    password: ''
});

const error = ref(null);
const loading = ref(false);

onMounted(() => {
    if (route.query.message) {
        error.value = route.query.message;
    }
});

const login = async () => {
    loading.value = true;
    error.value = null;
    
    try {
        const response = await axios.post(`${apiBase}/login`, form.value);
        store.setAuth(response.data.user, response.data.access_token);
        
        // Configurar axios para futuras peticiones
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.access_token}`;
        
        router.push('/');

    } catch (err) {
        if (err.response && err.response.data && err.response.data.message) {
            error.value = translateLaravelErrors(err.response.data.message);
        } else if (err.response && err.response.data && err.response.data.error) {
            error.value = translateLaravelErrors(err.response.data.error);
        } else {
            error.value = 'Ha ocurrido un error al iniciar sesión.';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gamer-dark py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Fondos neón -->
    <div class="absolute top-1/4 left-1/4 w-72 h-72 bg-neon-green/10 rounded-full filter blur-3xl"></div>
    <div class="absolute bottom-1/4 right-1/4 w-72 h-72 bg-neon-purple/10 rounded-full filter blur-3xl"></div>

    <div class="max-w-md w-full space-y-8 bg-gamer-card p-8 rounded-2xl border border-gray-800 shadow-2xl relative z-10 shadow-neon-green/10">
      <div>
        <h2 class="mt-6 text-center text-3xl font-black uppercase italic tracking-tighter text-white">
          Iniciar <span class="text-[#00D2FF]">Sesión</span>
        </h2>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="login">
        <div v-if="error" class="bg-red-900/50 border border-red-500 text-white px-4 py-3 rounded-lg text-xs" role="alert">
          <span class="block sm:inline">{{ error }}</span>
        </div>
        
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="email-address" class="block text-xs uppercase text-gray-400 mb-1 font-bold">Correo electrónico</label>
            <input id="email-address" name="email" type="email" autocomplete="email" required class="appearance-none rounded-xl relative block w-full px-4 py-3 bg-gray-900 border border-gray-700 placeholder-gray-600 text-white focus:outline-none focus:ring-0 focus:border-[#00D2FF] transition text-sm" placeholder="ejemplo@correo.com" v-model="form.email">
          </div>
          <div>
            <label for="password" class="block text-xs uppercase text-gray-400 mb-1 font-bold">Contraseña</label>
            <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-xl relative block w-full px-4 py-3 bg-gray-900 border border-gray-700 placeholder-gray-600 text-white focus:outline-none focus:ring-0 focus:border-[#00D2FF] transition text-sm" placeholder="••••••••" v-model="form.password">
          </div>
        </div>

        <div>
          <button type="submit" :disabled="loading" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-black rounded-xl text-gamer-dark bg-[#00D2FF] hover:bg-white hover:shadow-neon-blue transition duration-300 disabled:opacity-50 uppercase tracking-wider">
            {{ loading ? 'Iniciando...' : 'Entrar al Gremio' }}
          </button>
        </div>
        <div class="text-xs text-center flex flex-col gap-3">
            <router-link to="/forgot-password" class="font-bold text-neon-blue hover:text-white transition uppercase tracking-tighter">¿Olvidaste tu contraseña?</router-link>
            <router-link to="/register" class="font-bold text-gray-400 hover:text-neon-green transition">¿No tienes cuenta? <span class="text-neon-purple">Regístrate aquí</span></router-link>
        </div>
      </form>
    </div>
  </div>
</template>

