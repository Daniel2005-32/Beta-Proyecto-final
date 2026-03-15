import './bootstrap';
import { createApp } from 'vue';
import BanModal from './components/BanModal.vue';

// Crear la app Vue y montarla en un elemento específico
const app = createApp({});

// Registrar el componente globalmente
app.component('ban-modal', BanModal);

// Montar en un div con id 'app' si existe
app.mount('#app');