import { defineConfig } from "vite";
import symfonyPlugin from "vite-plugin-symfony";
import { fileURLToPath, URL } from 'node:url'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
    plugins: [
        symfonyPlugin(),
        vue(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)), 
            '@assets':  fileURLToPath(new URL('./src/assets', import.meta.url)),
        }
    },
    build: {
        rollupOptions: {
            input: {
                vue: "./assets/vue/main.js",
                app: "./assets/app.scss"
            },
        }
    },
});
