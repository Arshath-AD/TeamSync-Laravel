import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
                /* ── Dark theme tokens ── */
                workspace: {
                    background: '#0B0F17',
                    surface:    '#111827',
                    elevated:   '#1A2234',
                    border:     '#1F2D42',
                    text:       '#F0F4FF',
                    secondary:  '#7A8BA6',
                    muted:      '#3A4F6A',
                    accent:     '#1A6B8A',   /* Struct teal */
                    accentHov:  '#1D7EA3',
                    success:    '#22C55E',
                    warning:    '#F59E0B',
                    danger:     '#EF4444',
                    info:       '#38BDF8',
                    glass:      'rgba(17,24,39,0.75)',
                },
                /* ── Light theme tokens ── */
                light: {
                    background: '#F4F6F9',
                    surface:    '#FFFFFF',
                    elevated:   '#EEF1F6',
                    border:     '#D9DFE8',
                    text:       '#0F172A',
                    secondary:  '#5A6880',
                    muted:      '#A0AEBA',
                    accent:     '#1A6B8A',
                    accentHov:  '#1D7EA3',
                    glass:      'rgba(255,255,255,0.80)',
                },
            },
            backdropBlur: {
                xs: '2px',
            },
            boxShadow: {
                glass: '0 4px 24px 0 rgba(0,0,0,0.35)',
                'glass-sm': '0 2px 12px 0 rgba(0,0,0,0.20)',
                panel: '0 1px 3px 0 rgba(0,0,0,0.40)',
            },
            borderRadius: {
                DEFAULT: '6px',
            },
        },
    },

    plugins: [forms],
};
