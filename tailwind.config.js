/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/js/Pages/vue/*.vue",
    "./resources/js/Pages/vue/components/*.vue",
    "./resources/js/Pages/vue/components/modals/*.vue",
    "./resources/js/Pages/vue/components/alerts/*.vue",
    "./resources/js/*.js",
    "./resources/views/*.blade.php",
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require("daisyui")
  ],
}

