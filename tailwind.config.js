import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './packages/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    50: '#f0f9ff',
                    100: '#e0f2fe',
                    200: '#bae6fd',
                    500: '#0ea5e9',
                    600: '#0284c7',
                    800: '#075985',
                    900: '#0c4a6e'
                },
                accent: {
                    500: '#f59e0b'
                },
                dark: {
                    900: '#111827'
                }
            },
            fontFamily: {
                sans: ['Tajawal', ...defaultTheme.fontFamily.sans],
                en: ['Cairo', ...defaultTheme.fontFamily.sans]
            },
            screens: {
                'xs': '475px'
            }
        },
    },
    plugins: [],
};
