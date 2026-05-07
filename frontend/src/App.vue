<template>
  <router-view v-slot="{ Component }">
    <transition name="fade" mode="out-in">
      <component :is="Component" />
    </transition>
  </router-view>
</template>

<script setup>
// L'App.vue ara actua únicament com a portal de les rutes (SPA).
// El disseny de la capçalera i menú es mou a MainLayout.vue
</script>

<style>
/* CSS Reset i Global Styles Premium */
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

:root {
  --bg-primary: #0f172a;
  --bg-secondary: #1e293b;
  --bg-tertiary: #334155;
  --accent-primary: #6366f1; /* Indigo */
  --accent-secondary: #8b5cf6; /* Violet */
  --text-primary: #f8fafc;
  --text-secondary: #cbd5e1;
  --text-muted: #94a3b8;
  --danger: #ef4444;
  --success: #10b981;
  --border-color: rgba(255, 255, 255, 0.1);
  --glass-bg: rgba(30, 41, 59, 0.7);
  --glass-border: rgba(255, 255, 255, 0.05);
}

html {
  font-size: 14px;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  margin: 0;
  padding: 0;
  background-color: var(--bg-primary);
  font-family: 'Outfit', system-ui, -apple-system, sans-serif;
  color: var(--text-primary);
  min-height: 100vh;
  -webkit-font-smoothing: antialiased;
}

/* Transicions suaus per a les vistes del router */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

/* Estils globals per targetes "Glassmorphism" */
.glass-card {
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--glass-border);
  border-radius: 16px;
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
}

/* Botons globals */
.btn-primary {
  background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
  color: white;
  border: none;
  padding: 0.85rem 1.75rem;
  border-radius: 14px;
  font-weight: 700;
  font-family: 'Outfit', sans-serif;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.8rem;
  letter-spacing: 0.02em;
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px -3px rgba(99, 102, 241, 0.6);
}

.btn-primary:active {
  transform: translateY(0);
}

/* Ocultar completament tots els scrollbars */
::-webkit-scrollbar {
  display: none;
}

/* Estils per als dropdowns (select/option) en dark mode */
select option {
  background-color: var(--bg-secondary);
  color: var(--text-primary);
}
</style>
