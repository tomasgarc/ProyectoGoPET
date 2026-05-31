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
                brand: {
                    50: '#f3f8f6',
                    100: '#e2f0ea',
                    200: '#c8e2d7',
                    500: '#4f8a77',
                    600: '#3b6d5d',
                    700: '#2f574a',
                    900: '#1c352d',
                },
                accent: {
                    50: '#faf9f5',
                    100: '#f3f1e8',
                    600: '#8c866d',
                    950: '#22211c',
                }
            },
            fontFamily: {
                sans: ['Nunito', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
