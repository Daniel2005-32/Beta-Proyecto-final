export const apiBase = import.meta.env.VITE_API_URL 
    ? (import.meta.env.VITE_API_URL.endsWith('/api') ? import.meta.env.VITE_API_URL : import.meta.env.VITE_API_URL + '/api') 
    : (window.location.hostname.includes('localhost') 
        ? 'http://localhost:8000/api' 
        : 'https://proyecto-final-desplegar.onrender.com/api');
