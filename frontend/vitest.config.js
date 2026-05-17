import { defineConfig } from 'vitest/config'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  test: {
    // Utilitzem jsdom per simular el navegador de forma ràpida a la consola
    environment: 'jsdom',
    globals: true, // Ens permet fer servir "describe" i "it" sense importar-los a cada fitxer
  }
})
