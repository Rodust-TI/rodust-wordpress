/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./src/**/*.{js,css}",
    "./assets/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        'rodust-primary': '#1e40af',
        'rodust-secondary': '#64748b',
        'rodust-accent': '#f59e0b',
        'rodust-dark': '#1d2327',
      },
      fontFamily: {
        'sans': ['Outfit', 'system-ui', 'sans-serif'],
        'outfit': ['Outfit', 'sans-serif'],
      },
      container: {
        center: true,
        padding: '1rem',
        screens: {
          sm: '640px',
          md: '768px',
          lg: '1024px',
          xl: '1280px',
          '2xl': '1320px',
        },
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}