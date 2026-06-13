/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // Brand Colors
        primary: {
          50: '#f5f0ff',
          100: '#ebe0ff',
          200: '#d4bfff',
          300: '#b894ff',
          400: '#9966ff',
          500: '#8049cc',  // Main brand color
          600: '#6643c5',  // Secondary purple
          700: '#5535a8',
          800: '#44298a',
          900: '#331f6b',
        },
        accent: {
          50: '#f5f0ff',
          100: '#ebe0ff',
          200: '#d4bfff',
          300: '#b894ff',
          400: '#9966ff',
          500: '#8049cc',
          600: '#6643c5',
          700: '#5535a8',
          800: '#44298a',
          900: '#331f6b',
        },
        // Dark theme colors
        dark: {
          bg: '#2D2D2D',       // Main dark background
          card: '#1A1A1A',     // Card backgrounds
          border: '#3D3D3D',
          primary: '#2D2D2D',
          secondary: '#1A1A1A',
        },
        // Semantic colors
        success: {
          50: '#e8f5e9',
          100: '#c8e6c9',
          200: '#a5d6a7',
          300: '#81c784',
          400: '#66bb6a',
          500: '#4CAF50',     // Success green
          600: '#43a047',
          700: '#388e3c',
          800: '#2e7d32',
          900: '#1b5e20',
        },
        error: {
          50: '#ffebee',
          100: '#ffcdd2',
          200: '#ef9a9a',
          300: '#e57373',
          400: '#ef5350',
          500: '#F44336',     // Error red
          600: '#e53935',
          700: '#d32f2f',
          800: '#c62828',
          900: '#b71c1c',
        },
        warning: {
          50: '#fff3e0',
          100: '#ffe0b2',
          200: '#ffcc80',
          300: '#ffb74d',
          400: '#ffa726',
          500: '#FF9800',     // Warning amber
          600: '#fb8c00',
          700: '#f57c00',
          800: '#ef6c00',
          900: '#e65100',
        },
        // Text colors
        light: '#EFEAF3',     // Light text on dark backgrounds
        muted: '#808080',     // Gray muted elements
      },
      backgroundColor: {
        'dark-primary': '#2D2D2D',
        'dark-secondary': '#1A1A1A',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['JetBrains Mono', 'monospace'],
      },
      animation: {
        'fade-in': 'fadeIn 0.3s ease-out',
        'slide-up': 'slideUp 0.4s ease-out',
        'pulse-slow': 'pulse 3s infinite',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { opacity: '0', transform: 'translateY(20px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      },
    },
  },
  plugins: [],
}

