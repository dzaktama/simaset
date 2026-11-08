/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  // [PERF] Kelas yang dibentuk dinamis di Blade (mis. bg-{{ $guide->color }}-50) harus di-safelist
  // karena Tailwind CDN (runtime compiler) sudah dihapus dari layout.
  safelist: [
    { pattern: /^bg-(blue|indigo|green|emerald|red|yellow|amber|orange|purple|pink|gray|teal|cyan)-50$/ },
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
      },
    },
  },
  plugins: [],
}