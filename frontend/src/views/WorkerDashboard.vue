<template>
  <div class="worker-dashboard">
    <div v-if="loading" class="loading-state">
      <div class="loader"></div>
      <p>Carregant el teu espai...</p>
    </div>

    <div v-else-if="error" class="error-state">
      <p>{{ error }}</p>
    </div>

    <div v-else class="dashboard-grid">
      <!-- Resum del Treballador -->
      <div class="glass-card profile-card">
        <div class="profile-header">
          <div class="profile-avatar">
            <User class="icon-lg" />
          </div>
          <div class="profile-info">
            <h3>{{ workerInfo.name }}</h3>
            <p class="role-tag">{{ workerInfo.role }}</p>
          </div>
        </div>
        <div class="profile-stats">
          <div class="stat-item">
            <span class="stat-label">Cost/Hora</span>
            <span class="stat-value">{{ workerInfo.cost_per_hour }}€</span>
          </div>
          <div class="stat-item">
            <span class="stat-label">Disponibilitat</span>
            <span :class="['status-badge', workerInfo.availability ? 'available' : 'busy']">
              {{ workerInfo.availability ? 'Disponible' : 'Ocupat' }}
            </span>
          </div>
        </div>
      </div>



      <!-- Llista d'Esdeveniments Propers -->
      <div class="glass-card events-card">
        <div class="card-header">
          <h3>Els meus Esdeveniments</h3>
          <span class="count-badge">{{ events.length }}</span>
        </div>
        <div class="events-list">
          <div v-if="events.length === 0" class="empty-list">
            <p>No tens cap esdeveniment assignat actualment.</p>
          </div>
          <div v-for="event in events" :key="event.id" class="event-item">
            <div class="event-date">
              <span class="day">{{ formatDate(event.date, 'D') }}</span>
              <span class="month">{{ formatDate(event.date, 'MMM') }}</span>
            </div>
            <div class="event-details">
              <h4>{{ event.name }}</h4>
              <p class="event-meta">
                <MapPin class="icon-xs" /> {{ event.location || 'Ubicació per confirmar' }}
              </p>
              <div class="event-tags">
                <span class="tag">Hores: {{ event.my_hours }}h</span>
                <span :class="['tag', 'status-' + event.status.toLowerCase()]">{{ event.status }}</span>
              </div>
            </div>
            <div class="event-colleagues" v-if="event.colleagues && event.colleagues.length > 0">
              <p class="section-title">Companys:</p>
              <div class="avatars-group">
                <div v-for="colleague in event.colleagues" :key="colleague.id" class="mini-avatar" :title="colleague.name">
                  {{ colleague.name.charAt(0) }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Secció de Companys -->
      <div class="glass-card colleagues-card">
        <div class="card-header">
          <h3>Companys de l'Equip</h3>
        </div>
        <div class="colleagues-list">
          <div v-for="colleague in colleagues" :key="colleague.id" class="colleague-item">
            <div class="colleague-avatar">{{ colleague.name.charAt(0) }}</div>
            <div class="colleague-info">
              <span class="name">{{ colleague.name }}</span>
              <span class="role">{{ colleague.role }}</span>
            </div>
          </div>
        </div>
      </div>
      <!-- Secció de Vacances / Festa -->
      <div class="glass-card timeoff-card">
        <div class="card-header">
          <h3>Sol·licitar Festa</h3>
        </div>
        <form @submit.prevent="requestTimeOff" class="timeoff-form">
          <div class="form-row">
            <div class="form-group">
              <label>Inici</label>
              <input v-model="timeOffForm.start_date" type="date" required />
            </div>
            <div class="form-group">
              <label>Fi</label>
              <input v-model="timeOffForm.end_date" type="date" required />
            </div>
          </div>
          <button type="submit" class="btn-primary-sm" :disabled="submittingTimeOff">
            Enviar Sol·licitud
          </button>
        </form>
        
        <div class="my-requests">
          <p class="section-title">Les meves peticions:</p>
          <div v-for="req in timeOffRequests" :key="req.id" class="request-item">
            <span>{{ formatDate(req.start_date) }} - {{ formatDate(req.end_date) }}</span>
            <span :class="['status-small', req.status]">{{ req.status }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { User, MapPin } from 'lucide-vue-next'
import api from '../api/axios'

const loading = ref(true)
const error = ref(null)
const workerInfo = ref({})
const events = ref([])
const colleagues = ref([])
const timeOffRequests = ref([])
const submittingTimeOff = ref(false)

const timeOffForm = ref({
  start_date: '',
  end_date: '',
  reason: 'Vacances'
})

const fetchDashboardData = async () => {
  try {
    loading.value = true
    const response = await api.get('/worker/my-events')
    workerInfo.value = response.data.worker
    events.value = response.data.events
    
    const colleaguesResponse = await api.get('/worker/my-colleagues')
    colleagues.value = colleaguesResponse.data.colleagues
    
    const timeOffResponse = await api.get('/worker/time-off')
    timeOffRequests.value = timeOffResponse.data
  } catch (err) {
    error.value = 'No s\'ha pogut carregar la informació del dashboard.'
    console.error(err)
  } finally {
    loading.value = false
  }
}

const requestTimeOff = async () => {
  try {
    submittingTimeOff.value = true
    await api.post('/worker/time-off', timeOffForm.value)
    alert('Sol·licitud enviada!')
    timeOffForm.value = { start_date: '', end_date: '', reason: 'Vacances' }
    fetchDashboardData()
  } catch (err) {
    alert('Error enviant la sol·licitud.')
  } finally {
    submittingTimeOff.value = false
  }
}




const formatDate = (dateStr, format) => {
  const date = new Date(dateStr)
  if (format === 'D') return date.getDate()
  if (format === 'MMM') return date.toLocaleString('default', { month: 'short' }).toUpperCase()
  return date.toLocaleDateString()
}

onMounted(() => {
  fetchDashboardData()
})
</script>

<style scoped>
.worker-dashboard {
  color: var(--text-primary);
}

.dashboard-grid {
  display: grid;
  grid-template-columns: 350px 1fr;
  grid-template-rows: auto 1fr;
  gap: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

/* Profile Card */
.profile-card {
  grid-column: 1;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.profile-avatar {
  width: 64px;
  height: 64px;
  background: linear-gradient(135deg, var(--accent-primary) 0%, var(--accent-secondary) 100%);
  border-radius: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
}

.profile-info h3 {
  font-size: 1.25rem;
  margin: 0;
}

.role-tag {
  color: var(--accent-primary);
  font-size: 0.9rem;
  font-weight: 600;
}

.profile-stats {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding-top: 1.5rem;
  border-top: 1px solid var(--border-color);
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.stat-label {
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.stat-value {
  font-weight: 600;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.available { background: rgba(16, 185, 129, 0.2); color: #10b981; }
.status-badge.busy { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

/* Events Card */
.events-card {
  grid-column: 2;
  grid-row: 1 / 3;
  padding: 2rem;
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.count-badge {
  background: var(--accent-primary);
  color: white;
  padding: 0.2rem 0.6rem;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 700;
}

.events-list {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.event-item {
  display: flex;
  gap: 1.5rem;
  padding: 1.5rem;
  background: rgba(255, 255, 255, 0.03);
  border-radius: 12px;
  border: 1px solid var(--border-color);
  transition: transform 0.2s;
}

.event-item:hover {
  transform: translateX(10px);
  border-color: var(--accent-primary);
}

.event-date {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-width: 60px;
  background: var(--bg-tertiary);
  border-radius: 10px;
  padding: 0.5rem;
}

.event-date .day { font-size: 1.5rem; font-weight: 700; }
.event-date .month { font-size: 0.75rem; color: var(--accent-primary); font-weight: 700; }

.event-details {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.event-meta {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.event-tags {
  display: flex;
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.tag {
  font-size: 0.75rem;
  padding: 0.2rem 0.6rem;
  border-radius: 6px;
  background: var(--bg-secondary);
  color: var(--text-secondary);
  border: 1px solid var(--border-color);
}

.tag.status-pendent { color: #f59e0b; }
.tag.status-confirmat { color: #10b981; }

.avatars-group {
  display: flex;
  gap: -0.5rem;
  margin-top: 0.5rem;
}

.mini-avatar {
  width: 28px;
  height: 28px;
  background: var(--bg-tertiary);
  border: 2px solid var(--bg-secondary);
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 0.7rem;
  font-weight: 700;
}

/* Colleagues Card */
.colleagues-card {
  grid-column: 1;
  padding: 1.5rem;
}

.colleagues-list {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

.colleague-item {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.colleague-avatar {
  width: 36px;
  height: 36px;
  background: var(--bg-tertiary);
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: 700;
  font-size: 0.8rem;
}

.colleague-info {
  display: flex;
  flex-direction: column;
}

.colleague-info .name { font-size: 0.9rem; font-weight: 600; }
.colleague-info .role { font-size: 0.75rem; color: var(--text-muted); }

/* TimeOff Card */
.timeoff-card {
  grid-column: 1;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.timeoff-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.timeoff-form .form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.timeoff-form .form-group {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.timeoff-form label { font-size: 0.75rem; color: var(--text-secondary); }

.timeoff-form input {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--border-color);
  padding: 0.5rem;
  border-radius: 8px;
  color: white;
  font-size: 0.85rem;
}

.btn-primary-sm {
  background: var(--accent-primary);
  color: white;
  border: none;
  padding: 0.6rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 0.85rem;
}

.my-requests {
  margin-top: 1rem;
  border-top: 1px solid var(--border-color);
  padding-top: 1rem;
}

.request-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.8rem;
  padding: 0.4rem 0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.status-small {
  padding: 0.1rem 0.4rem;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 700;
}

.status-small.pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.status-small.approved { background: rgba(16, 185, 129, 0.2); color: #10b981; }
.status-small.rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

.loading-state, .error-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 300px;
}

.loader {
  width: 40px;
  height: 40px;
  border: 4px solid var(--border-color);
  border-top-color: var(--accent-primary);
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 1rem;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.icon-lg { width: 32px; height: 32px; }
.icon-xs { width: 14px; height: 14px; }
</style>
