import preset from './../vendor/filament/support/tailwind.config.preset'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

export default {
    presets: [preset],
    content: [
        './../app/Filament/**/*.php',
        './../resources/views/filament/**/*.blade.php',
        './../resources/views/livewire/**/*.blade.php',
        './../vendor/filament/**/*.blade.php',
        './../vendor/archilex/filament-filter-sets/**/*.php',
    ],
    theme: {
        extend: {
          colors: {
            custom: {
                50: '#E0E8F9',
                100: '#BED0F7',
                200: '#98AEEB',
                300: '#7B93DB',
                400: '#647ACB',
                500: '#4C63B6',
                600: '#4055A8',
                700: '#35469C',
                800: '#2D3A8C',
                900: '#19216C',
                950: '#111827',
            },
          },
        },
    },
    plugins: [
        forms, 
        typography
    ],
}