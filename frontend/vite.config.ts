import { defineConfig } from 'vite';

export default defineConfig({
  root: '.',
  publicDir: 'public',
  build: {
    outDir: 'dist',
    rollupOptions: {
      input: {
        main: 'src/pages/index.html',
        botenverhuur: 'src/pages/botenverhuur.html',
        camping: 'src/pages/camping.html',
        contact: 'src/pages/contact.html',
        jachthaven: 'src/pages/jachthaven.html',
        paymentSuccess: 'src/pages/payment-success.html',
        paymentSimulation: 'src/pages/payment-simulation.html',
        teKoop: 'src/pages/te-koop.html',
        vakantiehuis: 'src/pages/vakantiehuis.html',
        vaarkaart: 'src/pages/vaarkaart.html',
        adminLogin: 'src/pages/admin-login.html',
        admin: 'src/pages/admin/admin-simple.html'
      }
    },
    assetsDir: 'assets',
    sourcemap: true
  },
  server: {
    proxy: {
      '/api': {
        target: 'http://localhost:5001',
        changeOrigin: true,
        secure: false,
      }
    }
  }
});

