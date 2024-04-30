/** @type {import('tailwindcss').Config} */
const colors = require("tailwindcss/colors");
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    '50': '#faf5ff',
                    '100': '#f4e8ff',
                    '200': '#ebd5ff',
                    '300': '#dbb4fe',
                    '400': '#c384fc',
                    '500': '#aa55f7',
                    '600': '#9333ea',
                    '700': '#7c22ce',
                    '800': '#6821a8',
                    '900': '#541c87',
                    '950': '#380764',
                },
            }
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ]
};
