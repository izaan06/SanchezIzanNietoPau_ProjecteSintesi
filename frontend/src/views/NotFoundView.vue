<template>
  <div class="not-found-container">
    <div class="glass-card not-found-card">
      <div class="error-code">404</div>
      <div class="icon-wrapper">
        <MapPinOff class="icon-xl" />
      </div>
      <h1 class="title">Pàgina no trobada</h1>
      <p class="description">
        Sembla que l'esdeveniment o la pàgina que busques s'ha perdut en el temps o encara no ha estat programada.
      </p>
      <div class="actions">
        <button @click="goHome" class="btn-primary">
          <Home class="icon-sm" />
          Tornar a l'inici
        </button>
        <button @click="goBack" class="btn-secondary">
          <ArrowLeft class="icon-sm" />
          Anar enrere
        </button>
      </div>
    </div>

    <!-- Elements decoratius de fons -->
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { MapPinOff, Home, ArrowLeft } from 'lucide-vue-next'

const router = useRouter()

const goHome = () => {
  const role = localStorage.getItem('role')
  if (!role) {
    router.push('/')
  } else if (role === 'admin') {
    router.push('/app/dashboard')
  } else if (role === 'worker') {
    router.push('/app/worker')
  } else {
    router.push('/app/client-portal')
  }
}

const goBack = () => {
  router.back()
}
</script>

<style scoped>
.not-found-container {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: var(--bg-primary);
  position: relative;
  overflow: hidden;
  padding: 2rem;
}

.not-found-card {
  max-width: 500px;
  width: 100%;
  padding: 4rem 2rem;
  text-align: center;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1.5rem;
}

.error-code {
  font-size: 12rem;
  font-weight: 900;
  line-height: 1;
  background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  opacity: 0.07;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -80%);
  z-index: -1;
  user-select: none;
}

.icon-wrapper {
  width: 100px;
  height: 100px;
  background: rgba(99, 102, 241, 0.1);
  border-radius: 30px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: var(--accent-primary);
  margin-bottom: 1rem;
}

.icon-xl {
  width: 50px;
  height: 50px;
}

.title {
  font-size: 2.5rem;
  font-weight: 800;
  color: var(--text-primary);
  margin: 0;
}

.description {
  color: var(--text-secondary);
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 1rem;
}

.actions {
  display: flex;
  gap: 1rem;
  width: 100%;
}

.btn-primary, .btn-secondary {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1rem;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1rem;
}

.btn-primary {
  background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
  color: white;
  border: none;
  box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.4);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px -5px rgba(99, 102, 241, 0.5);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.05);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
}

.icon-sm {
  width: 20px;
  height: 20px;
}

/* Blobs decoratius */
.blob {
  position: absolute;
  border-radius: 50%;
  filter: blur(80px);
  z-index: 1;
}

.blob-1 {
  width: 400px;
  height: 400px;
  background: rgba(99, 102, 241, 0.15);
  top: -100px;
  left: -100px;
}

.blob-2 {
  width: 300px;
  height: 300px;
  background: rgba(236, 72, 153, 0.1);
  bottom: -50px;
  right: -50px;
}
</style>
