<template>
  <div class="appointments-view">
    <div class="header">
      <h2>Sol·licituds i Pressupostos IA</h2>
      <p class="subtitle">Gestiona les peticions dels clients i deixa que la IA t'ajudi.</p>
    </div>

    <div v-if="loading" class="loading">Carregant sol·licituds...</div>

    <div v-else class="appointments-grid">
      <div v-for="app in appointments" :key="app.id" class="glass-card appointment-card" :class="app.status">
        <div class="card-top">
          <div class="card-badge">{{ app.status.toUpperCase() }}</div>
          <button @click="deleteRequest(app.id)" class="btn-delete-card" title="Eliminar sol·licitud">
            <Trash2 class="icon-xs" />
          </button>
        </div>
        
        <div class="card-main">
          <div class="event-info">
            <div class="title-row">
              <h3>{{ app.event_type }}</h3>
              <span class="client-name">{{ app.name }} | {{ app.phone || 'Sense telèfon' }}</span>
              <p class="client-email">{{ app.email }}</p>
            </div>
            
            <div class="meta-grid">
              <div class="meta-item">
                <CalendarDays class="icon-xs" /> 
                <span>{{ formatDate(app.desired_date) }}</span>
              </div>
              <div class="meta-item">
                <Clock class="icon-xs" /> 
                <span v-if="app.start_time">{{ app.start_time }} - {{ app.end_time || '??' }}</span>
                <span v-else>Hora a concretar</span>
              </div>
              <div class="meta-item">
                <Users class="icon-xs" /> 
                <span>{{ app.attendees }} assistents</span>
              </div>
              <div class="meta-item">
                <MapPin class="icon-xs" /> 
                <span>{{ app.location_name || 'Ubicació no definida' }}</span>
              </div>
            </div>

            <div class="details-section">
              <div class="detail-pill" v-if="app.wants_music">
                <Music class="icon-xs" /> {{ app.music_type || 'Música' }}
              </div>
              <div class="detail-pill" v-if="app.menu_type">
                <Cake class="icon-xs" /> {{ app.menu_type }}
              </div>
              <div class="detail-pill danger" v-if="app.dietary_requirements">
                <Sparkles class="icon-xs" /> {{ app.dietary_requirements }}
              </div>
            </div>

            <div class="client-message" v-if="app.message">
              <strong>Missatge:</strong> {{ app.message }}
            </div>
          </div>

          <div class="ai-box">
            <div class="ai-header">
              <Sparkles class="icon-sm ai-icon" />
              <span>Anàlisi de l'IA</span>
            </div>
            
            <div class="budget-comparison">
              <div class="budget-item">
                <span class="label">Preu de Venda (IA)</span>
                <span class="ai-budget">{{ app.ai_estimated_budget }}€</span>
              </div>
              <div class="budget-item" v-if="app.ai_estimated_profit">
                <span class="label">Benefici Net (25%)</span>
                <span class="profit-value">+{{ app.ai_estimated_profit }}€</span>
              </div>
              <div class="budget-item" v-if="app.client_budget">
                <span class="label">Pressupost Client</span>
                <span class="client-budget" :class="budgetDiffClass(app)">{{ app.client_budget }}€</span>
              </div>
            </div>

            <div class="cost-breakdown" v-if="app.ai_operational_cost">
               <span class="label">Cost Operatiu Estimat:</span>
               <span class="cost-value">{{ app.ai_operational_cost }}€</span>
            </div>
            
            <div class="ai-address-box" v-if="app.ai_address">
              <MapPin class="icon-xs ai-icon" />
              <strong>Adreça Suggerida:</strong> {{ app.ai_address }}
            </div>

            <ul class="ai-recs">
              <li v-for="(rec, i) in app.ai_recommendations" :key="i">{{ rec }}</li>
            </ul>
          </div>
        </div>

        <div class="card-footer" v-if="app.status === 'pending'">
          <button @click="rejectRequest(app.id)" class="btn-text danger">Rebutjar</button>
          <button @click="approveRequest(app.id)" class="btn-primary">
            Aprovar i Assignar IA
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { 
  CalendarDays, 
  Users, 
  Sparkles, 
  Clock, 
  MapPin, 
  Music, 
  Cake,
  Trash2
} from 'lucide-vue-next'
import api from '../api/axios'

// --- ESTAT DE LA VISTA ---
const appointments = ref([]) // Llista de sol·licituds (Appointment Requests)
const loading = ref(true)      // Indicador de càrrega de dades

/**
 * Carrega totes les sol·licituds des de l'API.
 */
const fetchAppointments = async () => {
  try {
    const res = await api.get('/appointments')
    appointments.value = res.data.data
  } catch (err) {
    console.error("Error carregant sol·licituds:", err)
  } finally {
    loading.value = false
  }
}

/**
 * Aprova una sol·licitud, la qual cosa crea un esdeveniment real
 * i dispara la lògica d'assignació automàtica de la IA.
 */
const approveRequest = async (id) => {
  if (!confirm('Vols aprovar aquest esdeveniment? La IA assignarà els millors treballadors automàticament.')) return
  try {
    await api.post(`/appointments/${id}/approve`)
    alert('Esdeveniment creat i treballadors assignats!')
    fetchAppointments()
  } catch (err) {
    console.error("Error aprovant sol·licitud:", err)
  }
}

/**
 * Rebutja una sol·licitud i en canvia l'estat.
 */
const rejectRequest = async (id) => {
  try {
    await api.patch(`/appointments/${id}`, { status: 'rejected' })
    fetchAppointments()
  } catch (err) {
    console.error("Error rebutjant sol·licitud:", err)
  }
}

const deleteRequest = async (id) => {
  if (!confirm('Segur que vols eliminar aquesta sol·licitud permanentment?')) return
  try {
    await api.delete(`/appointments/${id}`)
    fetchAppointments()
  } catch (err) {
    console.error("Error eliminant sol·licitud:", err)
    alert('Error al eliminar la sol·licitud')
  }
}

/**
 * Formata la data per mostrar-la de forma llegible (català).
 */
const formatDate = (date) => date ? new Date(date).toLocaleDateString('ca-ES', { day: '2-digit', month: 'long', year: 'numeric' }) : 'A concretar'

/**
 * Determina la classe CSS segons la diferència entre el pressupost del client
 * i el preu estimat per la IA.
 */
const budgetDiffClass = (app) => {
  if (!app.client_budget) return ''
  const diff = app.client_budget - app.ai_estimated_budget
  if (diff >= 0) return 'budget-ok'       // Client paga més del que costa
  if (diff > -500) return 'budget-warning' // Client està a prop del preu IA
  return 'budget-danger'                 // Pressupost del client massa baix
}

onMounted(fetchAppointments)
</script>

<style scoped>
.appointments-view {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.appointments-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(500px, 1fr));
  gap: 1.5rem;
}

.appointment-card {
  padding: 2rem;
  position: relative;
  border-left: 4px solid var(--accent-primary);
}

.appointment-card.accepted { border-left-color: var(--success); }
.appointment-card.rejected { border-left-color: var(--danger); opacity: 0.7; }

.appointment-card.accepted { border-left-color: var(--success); }
.appointment-card.rejected { border-left-color: var(--danger); opacity: 0.7; }

.card-top {
  position: absolute;
  top: 1rem;
  right: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.card-badge {
  position: static;
  font-size: 0.65rem;
  font-weight: 800;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  background: rgba(255, 255, 255, 0.1);
}

.btn-delete-card {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
  width: 24px;
  height: 24px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-delete-card:hover {
  background: #ef4444;
  color: white;
}

.card-main {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.title-row {
  margin-bottom: 1.5rem;
}

.title-row h3 { font-size: 1.5rem; margin-bottom: 0.25rem; }
.client-name { color: var(--accent-primary); font-weight: 600; font-size: 1rem; }
.client-email { font-size: 0.8rem; color: var(--text-muted); }

.meta-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--text-secondary);
  font-size: 0.9rem;
}

.details-section {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  margin-bottom: 1.5rem;
}

.detail-pill {
  background: rgba(255, 255, 255, 0.05);
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.4rem;
  border: 1px solid rgba(255, 255, 255, 0.1);
}

.detail-pill.danger {
  border-color: var(--danger);
  color: var(--danger);
  background: rgba(239, 68, 68, 0.05);
}

.client-message {
  font-size: 0.85rem;
  background: rgba(255, 255, 255, 0.03);
  padding: 1rem;
  border-radius: 8px;
  line-height: 1.5;
}

.ai-box {
  background: rgba(99, 102, 241, 0.05);
  border: 1px dashed var(--accent-primary);
  border-radius: 12px;
  padding: 1.5rem;
}

.ai-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  color: var(--accent-primary);
  font-weight: 700;
  font-size: 0.8rem;
  margin-bottom: 1rem;
}

.budget-comparison {
  display: flex;
  justify-content: space-between;
  margin-bottom: 1.5rem;
  background: rgba(0,0,0,0.2);
  padding: 1rem;
  border-radius: 10px;
}

.budget-item {
  display: flex;
  flex-direction: column;
}

.budget-item .label {
  font-size: 0.7rem;
  color: var(--text-muted);
  text-transform: uppercase;
  margin-bottom: 0.25rem;
}

.ai-budget { font-size: 1.5rem; font-weight: 800; color: white; }
.profit-value { font-size: 1.5rem; font-weight: 800; color: #10b981; }
.client-budget { font-size: 1.5rem; font-weight: 800; }

.budget-ok { color: #10b981; }
.budget-warning { color: #f59e0b; }
.budget-danger { color: #ef4444; }

.cost-breakdown {
  margin-top: -1rem;
  margin-bottom: 1.5rem;
  padding: 0.5rem 1rem;
  font-size: 0.8rem;
  color: var(--text-muted);
  display: flex;
  gap: 0.5rem;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
}

.cost-value { font-weight: 600; color: var(--text-secondary); }

.ai-address-box {
  background: rgba(99, 102, 241, 0.1);
  padding: 0.75rem;
  border-radius: 8px;
  font-size: 0.85rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.ai-recs {
  padding-left: 1.2rem;
  font-size: 0.85rem;
  color: var(--text-secondary);
}

.ai-recs li { margin-bottom: 0.4rem; }

.card-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

.btn-text.danger { color: var(--danger); background: transparent; border: none; cursor: pointer; }
.btn-text.danger:hover { text-decoration: underline; }

.icon-xs { width: 14px; height: 14px; }
.ai-icon { color: var(--accent-primary); }
</style>
