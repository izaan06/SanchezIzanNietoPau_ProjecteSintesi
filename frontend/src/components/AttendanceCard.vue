<template>
  <div class="glass-card attendance-card">
    <div class="card-header">
      <div class="header-main">
        <Clock class="icon-md" />
        <h3>Registre de Jornada</h3>
      </div>
      <div :class="['status-indicator', isClockedIn ? 'active' : 'inactive']">
        {{ isClockedIn ? 'Treballant' : 'Fora de servei' }}
      </div>
    </div>

    <div class="attendance-content">
      <div v-if="isClockedIn" class="timer-section">
        <span class="timer-label">Temps transcorregut:</span>
        <span class="timer-value">{{ elapsedTime }}</span>
        <p class="start-time">Entrada a les: {{ formatTime(lastSession?.clock_in) }}</p>
      </div>
      
      <div v-else class="welcome-section">
        <p>Encara no has fitxat avui.</p>
        <p class="subtext">Recorda fitxar en començar la teva jornada.</p>
      </div>

      <div class="attendance-actions">
        <button 
          v-if="!isClockedIn" 
          @click="handleClockIn" 
          class="btn-clock-in"
          :disabled="loading"
        >
          <Play class="icon-sm" /> Fitxar Entrada
        </button>
        <button 
          v-else 
          @click="handleClockOut" 
          class="btn-clock-out"
          :disabled="loading"
        >
          <Square class="icon-sm" /> Fitxar Sortida
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Clock, Play, Square } from 'lucide-vue-next'
import api from '../api/axios'

const isClockedIn = ref(false)
const lastSession = ref(null)
const loading = ref(false)
const currentTime = ref(new Date())
let timerInterval = null

const fetchStatus = async () => {
  try {
    const res = await api.get('/worker/attendance/status')
    isClockedIn.value = res.data.is_clocked_in
    lastSession.value = res.data.last_session
  } catch (err) {
    console.error("Error carregant estat de fitxatge:", err)
  }
}

const handleClockIn = async () => {
  loading.value = true
  try {
    const res = await api.post('/worker/attendance/in')
    isClockedIn.value = true
    lastSession.value = res.data.data
  } catch (err) {
    alert(err.response?.data?.error || 'Error al fitxar entrada')
  } finally {
    loading.value = false
  }
}

const handleClockOut = async () => {
  if (!confirm('Vols tancar la sessió actual?')) return
  loading.value = true
  try {
    await api.post('/worker/attendance/out')
    isClockedIn.value = false
    lastSession.value = null
  } catch (err) {
    alert(err.response?.data?.error || 'Error al fitxar sortida')
  } finally {
    loading.value = false
  }
}

const formatTime = (dateStr) => {
  if (!dateStr) return '--:--'
  return new Date(dateStr).toLocaleTimeString('ca-ES', { hour: '2-digit', minute: '2-digit' })
}

const elapsedTime = computed(() => {
  if (!isClockedIn.value || !lastSession.value?.clock_in) return '00:00:00'
  const start = new Date(lastSession.value.clock_in)
  const diff = Math.floor((currentTime.value - start) / 1000)
  
  const h = Math.floor(diff / 3600).toString().padStart(2, '0')
  const m = Math.floor((diff % 3600) / 60).toString().padStart(2, '0')
  const s = (diff % 60).toString().padStart(2, '0')
  
  return `${h}:${m}:${s}`
})

onMounted(() => {
  fetchStatus()
  timerInterval = setInterval(() => {
    currentTime.value = new Date()
  }, 1000)
})

onUnmounted(() => {
  if (timerInterval) clearInterval(timerInterval)
})
</script>

<style scoped>
.attendance-card {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.header-main {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.header-main h3 {
  font-size: 1.1rem;
  margin: 0;
}

.status-indicator {
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  padding: 0.25rem 0.6rem;
  border-radius: 20px;
  letter-spacing: 0.05em;
}

.status-indicator.active {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
}

.status-indicator.inactive {
  background: rgba(255, 255, 255, 0.05);
  color: var(--text-muted);
}

.attendance-content {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  text-align: center;
}

.timer-section {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.timer-label {
  font-size: 0.8rem;
  color: var(--text-secondary);
}

.timer-value {
  font-size: 2.5rem;
  font-weight: 700;
  font-family: monospace;
  color: var(--accent-primary);
  text-shadow: 0 0 20px rgba(99, 102, 241, 0.3);
}

.start-time {
  font-size: 0.75rem;
  color: var(--text-muted);
}

.welcome-section p {
  font-size: 0.9rem;
  margin: 0;
}

.subtext {
  font-size: 0.75rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
}

.attendance-actions {
  display: flex;
  justify-content: center;
}

.btn-clock-in, .btn-clock-out {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 1rem;
  border-radius: 12px;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  transition: all 0.2s;
  border: none;
}

.btn-clock-in {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  color: white;
  box-shadow: 0 4px 15px -3px rgba(16, 185, 129, 0.4);
}

.btn-clock-in:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.5);
}

.btn-clock-out {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.btn-clock-out:hover {
  background: #ef4444;
  color: white;
}

.btn-clock-in:disabled, .btn-clock-out:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none !important;
}

.icon-md { width: 22px; height: 22px; color: var(--accent-primary); }
.icon-sm { width: 18px; height: 18px; }
.icon-xs { width: 14px; height: 14px; }
</style>
