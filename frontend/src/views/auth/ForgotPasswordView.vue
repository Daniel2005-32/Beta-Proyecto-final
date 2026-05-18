<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { apiBase } from '../../utils/api';
import { store } from '../../utils/store';
import LoadingState from '../../components/LoadingState.vue';

const email = ref('');
const loading = ref(false);
const successMsg = ref('');
const errorMsg = ref('');

const handleSubmit = async () => {
    loading.value = true;
    successMsg.value = '';
    errorMsg.value = '';
    try {
        const res = await axios.post(`${apiBase}/forgot-password`, { email: email.value });
        successMsg.value = res.data.message;
        email.value = '';
    } catch (err) {
        errorMsg.value = err.response?.data?.error || "Error al solicitar recuperación. Verifica tu correo.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4 py-12 bg-gamer-dark text-white">
    <div class="max-w-md w-full space-y-8 bg-gamer-card p-10 rounded-3xl border border-white/10 shadow-2xl relative overflow-hidden">
        <!-- Glow Effect -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-neon-purple/20 blur-[100px] rounded-full"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-neon-blue/20 blur-[100px] rounded-full"></div>

        <div class="text-center relative z-10">
            <h2 class="text-3xl font-black italic tracking-tighter uppercase mb-2">
                Recuperar <span class="text-neon-purple">Acceso</span>
            </h2>
            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Ingresa tu email para restablecer contraseña</p>
        </div>

        <div v-if="successMsg" class="bg-neon-green/10 border border-neon-green/30 text-neon-green p-4 rounded-xl text-xs text-center animate-pulse">
            {{ successMsg }}
        </div>
        <div v-if="errorMsg" class="bg-red-500/10 border border-red-500/30 text-red-500 p-4 rounded-xl text-xs text-center">
            {{ errorMsg }}
        </div>

        <form @submit.prevent="handleSubmit" class="mt-8 space-y-6 relative z-10">
            <div class="space-y-4">
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-1 tracking-widest">Correo Electrónico</label>
                    <input v-model="email" type="email" required 
                           class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3.5 text-sm focus:border-neon-purple outline-none transition text-white placeholder:text-gray-700"
                           placeholder="tu@email.com">
                </div>
            </div>

            <button type="submit" :disabled="loading" 
                    class="w-full py-4 bg-gradient-to-r from-neon-purple to-neon-blue rounded-xl font-black text-xs uppercase tracking-[0.2em] shadow-lg shadow-neon-purple/20 hover:scale-[1.02] active:scale-95 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <span v-if="!loading">Enviar Enlace</span>
                <span v-else>Procesando...</span>
            </button>

            <div class="text-center">
                <router-link to="/login" class="text-[10px] font-bold text-gray-500 hover:text-neon-purple uppercase transition tracking-widest">
                    <i class="fas fa-arrow-left mr-2"></i> Volver al Login
                </router-link>
            </div>
        </form>
    </div>
  </div>
</template>
