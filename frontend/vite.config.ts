import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  root: '..',
  publicDir: 'public',
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: {
        main: 'pages/index.html',
        botenverhuur: 'pages/botenverhuur.html',
        camping: 'pages/camping.html',
        contact: 'pages/contact.html',
        booking: 'pages/booking.html',
        paymentSuccess: 'pages/payment-success.html',
        paymentFailure: 'pages/payment-failure.html',
        offline: 'pages/offline.html',
        teKoop: 'pages/te-koop.html',
        vakantiehuis: 'pages/vakantiehuis.html',
        vaarkaart: 'pages/vaarkaart.html',
        adminLogin: 'pages/admin-login.html'
      }
    },
    assetsDir: 'assets',
    sourcemap: process.env.NODE_ENV !== 'production'
  },
  server: {
    port: 8888,
    host: '0.0.0.0',
    open: '/pages/index.html',
    proxy: {
      '/api': {
        target: 'http://localhost:5001',
        changeOrigin: true,
        secure: false
      }
    }
  },
  envPrefix: 'VITE_'
});
