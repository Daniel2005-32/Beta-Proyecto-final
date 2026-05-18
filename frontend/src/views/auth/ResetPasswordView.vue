<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';
import { apiBase } from '../../utils/api';
import { store } from '../../utils/store';

const route = useRoute();
const router = useRouter();

const form = ref({
    token: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const loading = ref(false);
const errorMsg = ref('');
const successMsg = ref('');

onMounted(() => {
    form.value.token = route.params.token;
    form.value.email = route.query.email || '';
});

const handleReset = async () => {
    loading.value = true;
    errorMsg.value = '';
    successMsg.value = '';
    try {
        const res = await axios.post(`${apiBase}/reset-password`, form.value);
        successMsg.value = res.data.message;
        store.notify(res.data.message, 'success');
        setTimeout(() => {
            router.push('/login');
        }, 3000);
    } catch (err) {
        errorMsg.value = err.response?.data?.error || "Error al restablecer contraseña. El enlace puede haber expirado.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center px-4 py-12 bg-[#0a0a0c] text-white relative overflow-hidden">
    <!-- Ambient Glow Background -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-neon-purple/10 blur-[150px] rounded-full"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-neon-blue/10 blur-[150px] rounded-full"></div>
    </div>

    <div class="max-w-md w-full space-y-8 bg-[#121214] p-10 rounded-[2.5rem] border border-white/5 shadow-[0_20px_50px_rgba(0,0,0,0.5)] relative z-10 animate-fade-in-up">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <div class="inline-block p-4 rounded-2xl bg-white/5 border border-white/10 mb-4 group hover:border-neon-blue transition-colors duration-500">
                <i class="fas fa-shield-halved text-3xl text-neon-blue group-hover:scale-110 transition-transform"></i>
            </div>
            <h2 class="text-4xl font-black italic tracking-tighter uppercase mb-1">
                Soul <span class="text-neon-blue drop-shadow-[0_0_10px_rgba(0,210,255,0.3)]">Guild</span>
            </h2>
            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.3em]">Security Protocol Activated</p>
        </div>

        <div v-if="successMsg" class="bg-green-500/10 border border-green-500/30 text-green-400 p-5 rounded-2xl text-xs text-center backdrop-blur-md animate-pulse">
            <i class="fas fa-check-circle mb-2 text-xl"></i>
            <p class="font-black uppercase italic">{{ successMsg }}</p>
            <p class="mt-2 text-[9px] opacity-70 tracking-widest font-bold">Redirigiendo al campo de batalla...</p>
        </div>

        <div v-if="errorMsg" class="bg-red-500/10 border border-red-500/30 text-red-500 p-5 rounded-2xl text-xs text-center backdrop-blur-md">
            <i class="fas fa-triangle-exclamation mb-2 text-xl"></i>
            <p class="font-black uppercase italic">{{ errorMsg }}</p>
        </div>

        <form v-if="!successMsg" @submit.prevent="handleReset" class="space-y-6 relative z-10">
            <input type="hidden" v-model="form.token">
            
            <div class="space-y-5">
                <div class="group">
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2 tracking-widest pl-1 group-focus-within:text-neon-blue transition-colors">Identidad Gamer</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-700 text-xs"></i>
                        <input v-model="form.email" type="email" required readonly
                               class="w-full bg-black/40 border border-white/5 rounded-2xl pl-11 pr-4 py-4 text-sm text-gray-500 outline-none cursor-not-allowed">
                    </div>
                </div>

                <div class="group">
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2 tracking-widest pl-1 group-focus-within:text-neon-blue transition-colors">Nueva Contraseña</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-xs"></i>
                        <input v-model="form.password" type="password" required 
                               class="w-full bg-black/60 border border-white/10 rounded-2xl pl-11 pr-4 py-4 text-sm focus:border-neon-blue focus:ring-1 focus:ring-neon-blue/20 outline-none transition-all text-white placeholder:text-gray-800"
                               placeholder="Min. 8 caracteres">
                    </div>
                </div>

                <div class="group">
                    <label class="block text-[10px] font-black uppercase text-gray-500 mb-2 tracking-widest pl-1 group-focus-within:text-neon-blue transition-colors">Confirmar Acceso</label>
                    <div class="relative">
                        <i class="fas fa-check-double absolute left-4 top-1/2 -translate-y-1/2 text-gray-600 text-xs"></i>
                        <input v-model="form.password_confirmation" type="password" required 
                               class="w-full bg-black/60 border border-white/10 rounded-2xl pl-11 pr-4 py-4 text-sm focus:border-neon-blue focus:ring-1 focus:ring-neon-blue/20 outline-none transition-all text-white placeholder:text-gray-800"
                               placeholder="Repite la clave">
                    </div>
                </div>
            </div>

            <button type="submit" :disabled="loading" 
                    class="w-full py-5 bg-gradient-to-r from-neon-blue to-neon-purple rounded-2xl font-black text-xs uppercase tracking-[0.3em] shadow-[0_10px_30px_rgba(0,210,255,0.2)] hover:scale-[1.02] active:scale-95 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed overflow-hidden group/btn relative">
                <div class="absolute inset-0 bg-white/20 translate-x-[-100%] group-hover/btn:translate-x-[100%] transition-transform duration-500 skew-x-[-20deg]"></div>
                <span v-if="!loading" class="relative z-10 flex items-center justify-center gap-2">
                    Actualizar Contraseña <i class="fas fa-bolt text-xs"></i>
                </span>
                <span v-else class="relative z-10 flex items-center justify-center gap-2">
                    Procesando <i class="fas fa-spinner fa-spin"></i>
                </span>
            </button>
        </form>

        <div class="text-center pt-6 border-t border-white/5">
            <router-link to="/login" class="text-[10px] font-bold text-gray-600 hover:text-white uppercase transition-colors tracking-widest">
                <i class="fas fa-arrow-left mr-2"></i> Cancelar Protocolo
            </router-link>
        </div>
    </div>
  </div>
</template>
