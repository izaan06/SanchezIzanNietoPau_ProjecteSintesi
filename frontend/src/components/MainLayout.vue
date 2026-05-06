<template>
  <div class="layout-wrapper">
    <!-- Sidebar Lateral -->
    <aside class="sidebar">
      <div class="logo-container">
        <div class="logo-box">
          <Sparkles class="logo-icon" />
        </div>
        <h1 class="logo-text">Event<span class="text-accent">AI</span></h1>
      </div>

      <nav class="nav-menu">
        <div class="nav-group-label">MENÚ PRINCIPAL</div>
        
        <!-- Admin Links -->
        <template v-if="userRole === 'admin'">
          <router-link to="/" class="nav-item" exact-active-class="active">
            <LayoutDashboard class="icon" />
            <span>Dashboard Admin</span>
          </router-link>
          
          <router-link to="/events" class="nav-item" active-class="active">
            <CalendarDays class="icon" />
            <span>Esdeveniments</span>
          </router-link>

          <router-link to="/calendar" class="nav-item" active-class="active">
            <CalendarDays class="icon" />
            <span>Calendari Mestre</span>
          </router-link>

          <router-link to="/clients" class="nav-item" active-class="active">
            <Users class="icon" />
            <span>Clients</span>
          </router-link>

          <router-link to="/workers" class="nav-item" active-class="active">
            <Briefcase class="icon" />
            <span>Treballadors</span>
          </router-link>

          <router-link to="/users" class="nav-item" active-class="active">
            <UserCog class="icon" />
            <span>Usuaris</span>
          </router-link>

          <router-link to="/appointments" class="nav-item" active-class="active">
            <Sparkles class="icon" />
            <span>Sol·licituds IA</span>
          </router-link>

          <router-link to="/time-off" class="nav-item" active-class="active">
            <CalendarDays class="icon" />
            <span>Gestió de Vacances</span>
          </router-link>
        </template>

        <!-- Worker Links -->
        <template v-if="userRole === 'worker'">
          <router-link to="/worker" class="nav-item" active-class="active">
            <CalendarDays class="icon" />
            <span>El meu Calendari</span>
          </router-link>
        </template>

        <!-- Client Links -->
        <template v-if="userRole === 'client'">
          <router-link :to="{ name: 'client-portal', query: { tab: 'home' } }" class="nav-item" :class="{ active: $route.query.tab === 'home' || !$route.query.tab }">
            <Home class="icon" />
            <span>Inici</span>
          </router-link>

          <router-link :to="{ name: 'client-portal', query: { tab: 'form' } }" class="nav-item" :class="{ active: $route.query.tab === 'form' }">
            <Sparkles class="icon" />
            <span>Nova Sol·licitud</span>
          </router-link>

          <router-link :to="{ name: 'client-portal', query: { tab: 'status' } }" class="nav-item" :class="{ active: $route.query.tab === 'status' }">
            <CalendarDays class="icon" />
            <span>Les Meves Sol·licituds</span>
          </router-link>

          <router-link :to="{ name: 'client-portal', query: { tab: 'menus' } }" class="nav-item" :class="{ active: $route.query.tab === 'menus' }">
            <Utensils class="icon" />
            <span>Catàleg de Menús</span>
          </router-link>

          <router-link :to="{ name: 'client-portal', query: { tab: 'gallery' } }" class="nav-item" :class="{ active: $route.query.tab === 'gallery' }">
            <Image class="icon" />
            <span>Galeria d'Èxits</span>
          </router-link>
        </template>
      </nav>

      <div class="sidebar-footer">
        <button @click="handleLogout" class="logout-btn">
          <LogOut class="icon" />
          <span>Tancar Sessió</span>
        </button>
      </div>
    </aside>

    <!-- Àrea Principal -->
    <div class="main-content">
      <!-- Capçalera Superior -->
      <header class="top-navbar">
        <div class="page-title">
          <h2>{{ currentPageTitle }}</h2>
        </div>
        <div class="user-profile">
          <button class="notification-btn">
            <Bell class="icon-sm" />
            <span class="badge">1</span>
          </button>
          <div class="avatar">
            <User class="icon-sm" />
          </div>
          <div class="user-info">
            <span class="user-name">{{ userName }}</span>
            <span class="user-role">{{ roleLabel }}</span>
          </div>
        </div>
      </header>

      <!-- Contingut de la Pàgina -->
      <main class="page-container">
        <router-view v-slot="{ Component }">
          <transition name="fade" mode="out-in">
            <component :is="Component" />
          </transition>
        </router-view>
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { 
  LayoutDashboard, 
  CalendarDays, 
  Users, 
  Briefcase, 
  LogOut,
  Sparkles,
  User,
  UserCog,
  Bell,
  Utensils,
  Image,
  Home
} from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()

const userRole = ref(localStorage.getItem('role') || 'client')
const userName = ref(localStorage.getItem('userName') || 'Usuari')

const roleLabel = computed(() => {
  switch (userRole.value) {
    case 'admin': return 'Administrador'
    case 'worker': return 'Treballador'
    case 'client': return 'Client'
    default: return 'Usuari'
  }
})

const currentPageTitle = computed(() => {
  switch (route.name) {
    case 'dashboard': return 'Panell de Control Admin'
    case 'worker-dashboard': return 'El meu Espai de Treball'
    case 'client-portal': return 'Portal del Client'
    case 'events': return 'Gestió d\'Esdeveniments'
    case 'clients': return 'Cartera de Clients'
    case 'workers': return 'Gestió de Personal'
    case 'admin-calendar': return 'Calendari Mestre'
    default: return 'EventAI Manager'
  }
})

const handleLogout = () => {
  localStorage.removeItem('token')
  localStorage.removeItem('role')
  localStorage.removeItem('userName')
  router.push({ name: 'login' })
}
</script>

<style scoped>
.layout-wrapper {
  display: flex;
  height: 100vh;
  overflow: hidden;
  background-color: var(--bg-primary);
}

/* --- SIDEBAR --- */
.sidebar {
  width: 280px;
  background-color: var(--bg-secondary);
  border-right: 1px solid var(--border-color);
  display: flex;
  flex-direction: column;
  transition: all 0.3s ease;
  z-index: 10;
}

.logo-container {
  padding: 2rem 1.5rem;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.logo-box {
  background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
  width: 40px;
  height: 40px;
  border-radius: 12px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.4);
}

.logo-icon {
  color: white;
  width: 20px;
  height: 20px;
}

.logo-text {
  font-size: 1.5rem;
  font-weight: 700;
  letter-spacing: -0.03em;
  color: var(--text-primary);
}

.text-accent {
  color: var(--accent-primary);
}

.nav-menu {
  flex: 1;
  padding: 0 1rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  overflow-y: auto;
}

.nav-group-label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-muted);
  letter-spacing: 0.05em;
  margin-bottom: 0.5rem;
  padding-left: 1rem;
  margin-top: 1rem;
}

.nav-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 0.875rem 1rem;
  color: var(--text-secondary);
  text-decoration: none;
  border-radius: 10px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.icon {
  width: 20px;
  height: 20px;
  opacity: 0.8;
  transition: all 0.2s ease;
}

.nav-item:hover {
  background-color: rgba(255, 255, 255, 0.05);
  color: var(--text-primary);
}

.nav-item.active {
  background: linear-gradient(90deg, rgba(99, 102, 241, 0.15) 0%, transparent 100%);
  color: var(--accent-primary);
  border-left: 3px solid var(--accent-primary);
}

.nav-item.active .icon {
  color: var(--accent-primary);
  opacity: 1;
}

.sidebar-footer {
  padding: 1.5rem;
  border-top: 1px solid var(--border-color);
}

.logout-btn {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.875rem 1rem;
  background-color: transparent;
  color: var(--text-secondary);
  border: 1px solid transparent;
  border-radius: 10px;
  font-family: inherit;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
}

.logout-btn:hover {
  background-color: rgba(239, 68, 68, 0.1);
  color: var(--danger);
}

/* --- MAIN CONTENT --- */
.main-content {
  flex: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
}

.top-navbar {
  height: 80px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
  background-color: rgba(15, 23, 42, 0.8);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border-bottom: 1px solid var(--border-color);
  z-index: 5;
}

.page-title h2 {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text-primary);
}

.user-profile {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.notification-btn {
  background: transparent;
  border: none;
  color: var(--text-secondary);
  position: relative;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 50%;
  transition: all 0.2s;
}

.notification-btn:hover {
  background-color: rgba(255, 255, 255, 0.05);
  color: var(--text-primary);
}

.badge {
  position: absolute;
  top: 0;
  right: 0;
  background-color: var(--danger);
  color: white;
  font-size: 0.65rem;
  font-weight: bold;
  width: 16px;
  height: 16px;
  display: flex;
  justify-content: center;
  align-items: center;
  border-radius: 50%;
  border: 2px solid var(--bg-primary);
}

.avatar {
  width: 40px;
  height: 40px;
  background-color: var(--bg-tertiary);
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  color: var(--text-secondary);
}

.icon-sm {
  width: 18px;
  height: 18px;
}

.user-info {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: var(--text-primary);
}

.user-role {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.page-container {
  flex: 1;
  overflow-y: auto;
  padding: 2rem;
  position: relative;
}

/* Fons decoratiu per a l'àrea de contingut */
.page-container::before {
  content: '';
  position: absolute;
  top: -150px;
  right: -100px;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
  border-radius: 50%;
  z-index: -1;
  pointer-events: none;
}
</style>
