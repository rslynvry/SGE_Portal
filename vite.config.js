import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    /*server: {
        host: '0.0.0.0',
        hmr: {
          host: 'localhost'
        },
      },
    server: {
        host: '0.0.0.0', // true
        port: 8080,

        /*hmr: {
            host: true,
            p
        }
    }, 
    */
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});
