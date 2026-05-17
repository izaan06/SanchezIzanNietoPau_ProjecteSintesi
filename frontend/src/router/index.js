import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from '../components/MainLayout.vue'

// ==========================================
// DEFINICIÓ DE RUTES (VUE ROUTER)
// ==========================================
// Cada objecte representa una ruta (URL) i quin component s'ha de mostrar a la pantalla.
const routes = [
  {
    path: '/',
    name: 'landing',
    component: () => import('../views/LandingView.vue'), // Importació mandrosa (lazy load) per optimitzar el rendiment
    meta: { requiresGuest: true } // Només accessible si NO has iniciat sessió
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/features',
    name: 'features',
    component: () => import('../views/FeaturesView.vue'),
    meta: { requiresGuest: false }
  },
  {
    path: '/about',
    name: 'about',
    component: () => import('../views/AboutView.vue'),
    meta: { requiresGuest: false }
  },
  {
    path: '/app',
    component: MainLayout,
    meta: { requiresAuth: true }, // Totes les rutes dins de /app requereixen estar loguejat
    children: [
      // --- RUTES ADMINISTRADOR ---
      {
        path: 'dashboard',
        name: 'dashboard',
        component: () => import('../views/DashboardView.vue'),
        meta: { roles: ['admin'] }
      },
      {
        path: 'worker',
        name: 'worker-dashboard',
        component: () => import('../views/WorkerDashboard.vue'),
        meta: { roles: ['worker', 'admin'] }
      },
      {
        path: 'client-portal',
        name: 'client-portal',
        component: () => import('../views/ClientPortal.vue'),
        meta: { roles: ['client', 'admin'] }
      },
      {
        path: 'events',
        name: 'events',
        component: () => import('../views/EventsView.vue'),
        meta: { roles: ['admin'] }
      },
      {
        path: 'clients',
        name: 'clients',
        component: () => import('../views/ClientsView.vue'),
        meta: { roles: ['admin'] }
      },
      {
        path: 'workers',
        name: 'workers',
        component: () => import('../views/WorkersView.vue'),
        meta: { roles: ['admin'] }
      },
      {
        path: 'users',
        name: 'users',
        component: () => import('../views/UsersView.vue'),
        meta: { roles: ['admin'] }
      },
      {
        path: 'appointments',
        name: 'appointments',
        component: () => import('../views/AppointmentsView.vue'),
        meta: { roles: ['admin'] }
      },
      {
        path: 'time-off',
        name: 'admin-time-off',
        component: () => import('../views/AdminTimeOffView.vue'),
        meta: { roles: ['admin'] }
      },

      {
        path: 'calendar',
        name: 'admin-calendar',
        component: () => import('../views/CalendarView.vue'),
        meta: { roles: ['admin'] }
      }
    ]
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'not-found',
    component: () => import('../views/NotFoundView.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// ==========================================
// GUÀRDIES DE NAVEGACIÓ (Navigation Guards)
// ==========================================
// Aquest codi s'executa ABANS de cada canvi de pàgina. Actua com un vigilant de seguretat.
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')
  const isAuthenticated = !!token

  // 1. Si la ruta requereix autenticació i no està autenticat -> Login
  if (to.meta.requiresAuth && !isAuthenticated) {
    return next({ name: 'login' })
  }

  // 2. Si la ruta és per a convidats (ex: anar al Login) però l'usuari ja està loguejat -> L'enviem al seu panell principal
  if (to.meta.requiresGuest && isAuthenticated) {
    if (role === 'admin') return next({ name: 'dashboard' })
    if (role === 'worker') return next({ name: 'worker-dashboard' })
    return next({ name: 'client-portal' })
  }

  // 3. Comprovació de Rols (Control d'Accés)
  // Si la ruta té definida una llista de rols permesos (to.meta.roles) i el rol de l'usuari no hi és...
  if (to.meta.roles && !to.meta.roles.includes(role)) {
    // Evitar bucle infinit: si ja anem a la ruta de destí, no redirigir de nou
    let targetName = 'client-portal'
    if (role === 'admin') targetName = 'dashboard'
    else if (role === 'worker') targetName = 'worker-dashboard'

    if (to.name === targetName) {
      return next()
    } else {
      return next({ name: targetName })
    }
  }

  // 4. En qualsevol altre cas, permetre la navegació
  next()
})

export default router
