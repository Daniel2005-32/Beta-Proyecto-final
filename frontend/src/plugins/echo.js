import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Determinar configuración dinámica del Host
const getWsConfig = () => {
    const apiBase = import.meta.env.VITE_API_URL || window.location.origin;
    let host = window.location.hostname;
    let useTls = window.location.protocol === 'https:';

    try {
        const url = new URL(apiBase);
        if (url.hostname && url.hostname !== '' && !url.hostname.includes('onrender.com')) {
            host = url.hostname;
        } else if (url.hostname.includes('onrender.com')) {
             // Si es Render, preferimos siempre el hostname actual del navegador para evitar subdominios antiguos
             host = window.location.hostname;
        }
        useTls = url.protocol === 'https:';
    } catch (e) {
        host = window.location.hostname;
        useTls = window.location.protocol === 'https:';
    }

    const isLocal = host.includes('localhost') || host.includes('127.0.0.1');
    const port = import.meta.env.VITE_REVERB_PORT || (isLocal ? 8080 : (useTls ? 443 : 80));

    return { host, port, useTls, isLocal };
};

const wsConfig = getWsConfig();

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY || 'xlcnihr08u1nibdzmfdk',
    wsHost: wsConfig.host,
    wsPort: wsConfig.port,
    wssPort: wsConfig.port,
    forceTLS: wsConfig.isLocal ? false : wsConfig.useTls,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});

export default window.Echo;
