import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                parchment: '#f5f4ed',
                ivory: '#faf9f5',
                sand: '#e8e6dc',
                ink: '#141413',
                charcoal: '#30302e',
                warmText: '#5e5d59',
                mutedText: '#87867f',
                terracotta: '#c96442',
                terracottaSoft: '#d97757',
                borderCream: '#f0eee6',
                ringWarm: '#d1cfc5',
                dangerWarm: '#b53333',
                focusBlue: '#3898ec',
            },
            fontFamily: {
                serifDisplay: ['Cormorant Garamond', 'ui-serif', 'serif'],
                sansBody: ['Manrope', 'ui-sans-serif', 'sans-serif'],
            },
            boxShadow: {
                ringWarm: '0 0 0 1px #d1cfc5',
                whisper: '0 4px 24px rgba(0, 0, 0, 0.05)',
            },
            borderRadius: {
                soft: '12px',
                card: '16px',
                hero: '32px',
            },
        },
    },

    plugins: [forms],
};
