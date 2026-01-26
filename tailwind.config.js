/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'kai-blue': '#001D4B',
        'kai-orange': '#FF7300',
        'kai-bg': '#F8FAFC',
        'kai-txt': '#1E293B',
      },
    },
  },
  plugins: [],
}