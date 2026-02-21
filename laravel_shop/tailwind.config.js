import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'gamer-dark': '#0b0e14',
                'gamer-card': '#161b22',
                'neon-blue': '#00d2ff',
                'neon-purple': '#9d00ff',
                'neon-red': '#ff0055',
                'neon-green': '#39ff14',
            },
            boxShadow: {
                'neon-blue': '0 0 5px #00d2ff, 0 0 20px #00d2ff',
                'neon-purple': '0 0 5px #9d00ff, 0 0 20px #9d00ff',
                'neon-red': '0 0 5px #ff0055, 0 0 20px #ff0055',
            }
        },
    },

    plugins: [forms],
};