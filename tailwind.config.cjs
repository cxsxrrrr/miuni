/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './**/*.php',
    './**/*.html',
    './js/**/*.js'
  ],
  safelist: [
    { pattern: /^(bg|text)-(valencia|paarl|danube|harvest-gold|english-walnut|aqua-forest|alizarin-crimson|spice|quicksand)$/ },
    { pattern: /^hover:bg-(valencia|alizarin-crimson|paarl|danube|harvest-gold|english-walnut|aqua-forest|spice|quicksand)$/ }
  ],
  theme: {
    extend: {
      colors: {
        valencia: '#d83330',
        paarl: '#965626',
        danube: '#7da7d7',
        'harvest-gold': '#e1ab70',
        'english-walnut': '#372721',
        'aqua-forest': '#5eaa8a',
        'alizarin-crimson': '#e52520',
        spice: '#6c412e',
        quicksand: '#c29e8d'
      }
      ,
      fontFamily: {
        // Make Tailwind's font-sans map to Arial Rounded MT Bold first
        sans: ['"Arial Rounded MT Bold"', 'Arial', 'Helvetica', 'sans-serif']
      }
    },
  },
  plugins: [],
}
