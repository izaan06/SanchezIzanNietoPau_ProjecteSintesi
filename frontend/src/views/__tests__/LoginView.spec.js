import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import LoginView from '../LoginView.vue'
import { createRouter, createWebHistory } from 'vue-router'

// 1. Simulem (mock) l'API perquè el test no faci peticions reals a internet
vi.mock('@/api/axios', () => {
  return {
    default: {
      post: vi.fn(() => Promise.resolve({ data: { token: 'fake-token' } }))
    }
  }
})

// 2. Creem un router fals per al test, ja que LoginView utilitza vue-router
const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: { template: '<div>Home</div>' } },
    { path: '/login', name: 'login', component: LoginView }
  ]
})

describe('LoginView.vue', () => {
  it('mostra el formulari de login correctament', async () => {
    // Muntem el component en un entorn virtual
    const wrapper = mount(LoginView, {
      global: {
        plugins: [router] // Li passem el router fals
      }
    })

    // Comprovem que hi hagi un input per l'email i un per la contrasenya
    expect(wrapper.find('input[type="email"]').exists()).toBe(true)
    expect(wrapper.find('input[type="password"]').exists()).toBe(true)
    
    // Comprovem que el botó de fer login existeix
    expect(wrapper.find('button[type="submit"]').exists()).toBe(true)
  })
})

/* =====================================================================
 * INSTRUCCIONS PER EXECUTAR AQUESTS TESTS:
 * 1. Obre un terminal a la carpeta 'frontend'
 * 2. Si no has instal·lat les dependències (jo ho he llançat ara, però per si de cas): 
 *    npm install -D vitest @vue/test-utils jsdom
 * 3. Executa la comanda: npm run test:unit
 * =====================================================================
 */
