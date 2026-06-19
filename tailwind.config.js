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
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                workspace: {
                    background: '#0B0F17',
                    surface: '#121826',
                    elevated: '#1A2234',
                    border: '#263042',
                    text: '#F8FAFC',
                    secondary: '#94A3B8',
                    accent: '#6366F1',
                    success: '#22C55E',
                    warning: '#F59E0B',
                    danger: '#EF4444'
                }
            }
        },
    },

    plugins: [forms],
};
