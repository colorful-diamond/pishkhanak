/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  variants: {
    extend: {
      ringColor: [],
      ringOffsetColor: [],
      ringOffsetWidth: [],
      ringWidth: [],
    },
  },
  theme: {
    extend: {
      fontFamily: {
        'sans': ['IRANSansWebFaNum', 'IRANSans', 'system-ui', 'sans-serif'],
        'iran': ['IRANSans', 'system-ui', 'sans-serif'],
        'iran-fa': ['IRANSansWebFaNum', 'IRANSans', 'system-ui', 'sans-serif'],
      },
      minHeight: {
        'screen/2': '50vh',
      },
      zIndex: {
        '60': '60',
      },
      colors: {
        'green-25': '#F8FFF8', // 50% more lighter light green
        'red-25': '#FFF8F8',   // 50% more lighter light red
        'blue-25': '#F8FBFF',  // 50% more lighter light blue
        'sky-25': '#F8FCFF',  // 50% more lighter light sky
        'yellow-25': '#FFFFF8', // 50% more lighter light yellow
        'purple-25': '#F8F8FF', // 50% more lighter light purple
        'orange-25': '#FFF8F3', // 50% more lighter light orange
        'pink-25': '#FFF8FB',   // 50% more lighter light pink
        'teal-25': '#F8FFFF',   // 50% more lighter light teal
        'brown-25': '#F8F3FF',  // 50% more lighter light brown
        'gray-25': '#FBFBFB',   // 50% more lighter light gray
        'cyan-25': '#F8FFFB',   // 50% more lighter light cyan
        'lime-25': '#F8FFFA',   // 50% more lighter light lime
        'dark-blue-25': '#F8F9FB',   // 50% more lighter light dark blue
        'dark-blue-50': '#F1F5F9',   // Very light dark blue
        'dark-blue-100': '#E2E8F0',  // Light dark blue
        'dark-blue-200': '#CBD5E1',  // Lighter dark blue
        'dark-blue-300': '#94A3B8',  // Medium light dark blue
        'dark-blue-400': '#64748B',  // Medium dark blue
        'dark-blue-500': '#475569',  // Normal dark blue
        'dark-blue-600': '#334155',  // Darker dark blue
        'dark-blue-700': '#1E293B',  // Dark dark blue
        'dark-blue-800': '#0F172A',  // Very dark blue
        'dark-blue-900': '#020617',  // Darkest blue
        'primary-normal': '#36A9E2',
        'primary-lightest': '#E0F2FD', // Corresponds to a 50 shade
        'primary-lighter': '#99DFF8',  // Corresponds to a 200 shade
        'primary-light': '#47C0EF',    // Corresponds to a 400 shade
        'primary-dark': '#2D88C0',     // Corresponds to a 600 shade
        'primary-darker': '#246A9E',   // Corresponds to a 700 shade
        'primary-darkest': '#122E5A',  // Corresponds to a 900 shade
        'secondary-normal': '#fcd34d',
        'secondary-lightest': '#fefce8', // Corresponds to a 50 shade (yellow-50)
        'secondary-lighter': '#fef08a',  // Corresponds to a 200 shade (yellow-200)
        'secondary-light': '#fde047',    // Corresponds to a 300 shade (yellow-300, slightly lighter than normal)
        'secondary-dark': '#eab308',     // Corresponds to a 600 shade (yellow-600)
        'secondary-darker': '#a16207',   // Corresponds to a 700 shade (yellow-700)
        'secondary-darkest': '#713f12',  // Corresponds to a 900 shade (yellow-900)
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms')({
      strategy: 'class', // This ensures form styles are only applied when explicitly used
    }),
    // Custom direction utilities
    function({ addUtilities }) {
      const newUtilities = {
        '.dir-ltr': {
          direction: 'ltr',
        },
        '.dir-rtl': {
          direction: 'rtl',
        },
        '.dir-auto': {
          direction: 'auto',
        },
        '.dir-inherit': {
          direction: 'inherit',
        },
        '.dir-initial': {
          direction: 'initial',
        },
        '.dir-unset': {
          direction: 'unset',
        },
      }
      addUtilities(newUtilities)
    },
  ],
}