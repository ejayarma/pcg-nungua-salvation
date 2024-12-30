import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: 'selector',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'hero-pattern': "url('/resources/images/inside-church.jpeg'), linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.9))",
            },
            backgroundSize: {
                '50%': '100% 50%',
                '16': '4rem',
            }
        },
    },

    // plugins: [forms],
};
