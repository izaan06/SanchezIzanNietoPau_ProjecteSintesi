import { createRouter, createWebHistory } from 'vue-router'
import MainLayout from '../components/MainLayout.vue'

const routes = [
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/LoginView.vue'),
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    component: MainLayout,
    meta: { requiresAuth: true },
    children: [
      {
        path: '',
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
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Navigation Guards
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')
  const role = localStorage.getItem('role')
  const isAuthenticated = !!token

  if (to.meta.requiresAuth && !isAuthenticated) {
    next({ name: 'login' })
  } else if (to.meta.requiresGuest && isAuthenticated) {
    // Redirigir usuari ja loguejat segons el seu rol
    if (role === 'admin') next({ name: 'dashboard' })
    else if (role === 'worker') next({ name: 'worker-dashboard' })
    else next({ name: 'client-portal' })
  } else if (to.meta.roles && !to.meta.roles.includes(role)) {
    // Si l'usuari no té el rol necessari, redirigir-lo a la seva home
    if (role === 'admin') next({ name: 'dashboard' })
    else if (role === 'worker') next({ name: 'worker-dashboard' })
    else next({ name: 'client-portal' })
  } else {
    next()
  }
})

export default router
