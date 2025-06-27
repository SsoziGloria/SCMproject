// tailwind.config.js
module.exports = {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        // add other paths as needed
    ],
    corePlugins: {
        preflight: false, // <--- disables Tailwind's base resets
    },
    theme: {
        extend: {},
    },
    plugins: [],
}