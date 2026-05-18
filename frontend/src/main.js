import './main.css';
import './plugins/echo.js';

import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';
import { store } from './utils/store';

// Restaurar token si existe
const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

// Interceptor global para errores
axios.interceptors.response.use(
    response => response,
    error => {
        const { response } = error;
        if (response) {
            if (response.status === 401) {
                store.clearAuth();
                if (router.currentRoute.value.path !== '/login') {
                    router.push('/login');
                    store.notify("Sesión expirada.", "error");
                }
            } else if (response.status === 403) {
                store.notify("Acceso denegado.", "error");
            } else if (response.status === 500) {
                store.notify("Error en el servidor. Reintenta.", "error");
            }
        } else if (error.request) {
            store.notify("Sin conexión al servidor.", "error");
        }
        return Promise.reject(error);
    }
);

const app = createApp(App);

app.use(router);

app.mount('#app');
