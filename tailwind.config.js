const defaultTheme = require('tailwindcss/defaultTheme');
const colors = require("tailwindcss/colors");

module.exports = {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        colors: {
            transparent: "transparent",
            current: "currentColor",
            gray: colors.zinc,
            blue: colors.blue,
            indigo: colors.blue,
            white: colors.white,
            black: colors.black,
            red: colors.red,
            emerald: colors.emerald,
            success: colors.emerald,
            green: colors.emerald,
            primary: colors.blue,
            secondary: colors.orange,
            warning: colors.amber,
            yellow: colors.amber,
            danger: colors.red,
            bg: colors.slate,
        },
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    variants: {
        extend: {
            opacity: ["disabled"],
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
