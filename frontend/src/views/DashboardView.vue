<template>
  <div class="dashboard-view">
    
    <!-- Secció de KPIs -->
    <div class="kpi-grid">
      <div class="kpi-card glass-card">
        <div class="kpi-header">
          <div class="kpi-icon blue"><CalendarDays /></div>
          <span class="kpi-title">Esdeveniments Actius</span>
        </div>
        <div class="kpi-value">24</div>
        <div class="kpi-trend positive">↑ 12% des del darrer mes</div>
      </div>

      <div class="kpi-card glass-card">
        <div class="kpi-header">
          <div class="kpi-icon purple"><Users /></div>
          <span class="kpi-title">Clients Registrats</span>
        </div>
        <div class="kpi-value">156</div>
        <div class="kpi-trend positive">↑ 4 nous aquesta setmana</div>
      </div>

      <div class="kpi-card glass-card">
        <div class="kpi-header">
          <div class="kpi-icon green"><Briefcase /></div>
          <span class="kpi-title">Treballadors Disponibles</span>
        </div>
        <div class="kpi-value">89</div>
        <div class="kpi-trend neutral">→ Estable</div>
      </div>

      <div class="kpi-card glass-card">
        <div class="kpi-header">
          <div class="kpi-icon indigo"><Sparkles /></div>
          <span class="kpi-title">Costos (Predits per IA)</span>
        </div>
        <div class="kpi-value">€45.2K</div>
        <div class="kpi-trend negative">↑ 5% per sobre de la mitjana</div>
      </div>
    </div>

    <!-- Secció de Contingut Combinat -->
    <div class="content-grid">
      
      <!-- Pròxims Esdeveniments -->
      <div class="panel glass-card">
        <div class="panel-header">
          <h3>Pròxims Esdeveniments</h3>
          <button class="btn-text">Veure tots</button>
        </div>
        <div class="panel-body">
          <ul class="event-list">
            <li v-for="i in 4" :key="i" class="event-item">
              <div class="event-date">
                <span class="day">1{{ i + 2 }}</span>
                <span class="month">MAR</span>
              </div>
              <div class="event-info">
                <h4>Casament al Castell</h4>
                <p>150 assistents · 5 treballadors</p>
              </div>
              <div class="event-status" :class="i % 2 === 0 ? 'status-pending' : 'status-ready'">
                {{ i % 2 === 0 ? 'Pendent' : 'Preparat' }}
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Predicció Ràpida (Microservei IA) -->
      <div class="panel glass-card ai-panel">
        <div class="panel-header">
          <h3><Sparkles class="icon-sm" /> Simulador de Cost (IA)</h3>
        </div>
        <div class="panel-body">
          <p class="ai-desc">Connectat amb el microservei de Flask per predir costos de manera immediata.</p>
          <form @submit.prevent="simulateCost" class="ai-form">
            <div class="form-row">
              <div class="form-group">
                <label>Tipus</label>
                <select v-model="simForm.type">
                  <option value="wedding">Casament</option>
                  <option value="corporate">Corporatiu</option>
                  <option value="party">Festa Privada</option>
                </select>
              </div>
              <div class="form-group">
                <label>Assistents</label>
                <input type="number" v-model="simForm.assistants" min="10" />
              </div>
            </div>
            <button type="submit" class="btn-primary w-full" :disabled="isPredicting">
              {{ isPredicting ? 'Calculant...' : 'Predir Cost ara' }}
            </button>
          </form>

          <div v-if="predictedCost" class="prediction-result">
            <span class="pred-label">Cost Estimat:</span>
            <span class="pred-value">€{{ predictedCost.toLocaleString() }}</span>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { CalendarDays, Users, Briefcase, Sparkles } from 'lucide-vue-next'
import api from '../api/axios'

const isPredicting = ref(false)
const predictedCost = ref(null)

const simForm = ref({
  type: 'wedding',
  assistants: 100,
  workers: 5,
  hours: 8,
  cost_per_hour: 20
})

const simulateCost = async () => {
  isPredicting.value = true
  try {
    const response = await api.post('/events/predict-cost', simForm.value)
    predictedCost.value = response.data.predicted_cost
  } catch (error) {
    console.error('Error in prediction:', error)
    // Fallback fictici per a la demo de disseny si el backend no està corrent
    predictedCost.value = 15450.50 
  } finally {
    isPredicting.value = false
  }
}
</script>

<style scoped>
.dashboard-view {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* KPIs */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 1.5rem;
}

.kpi-card {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.kpi-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.kpi-icon {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  color: white;
}

.kpi-icon.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.kpi-icon.purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.kpi-icon.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.kpi-icon.indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }

.kpi-icon svg { width: 20px; height: 20px; }

.kpi-title {
  color: var(--text-secondary);
  font-size: 0.9rem;
  font-weight: 500;
}

.kpi-value {
  font-size: 2rem;
  font-weight: 700;
  color: var(--text-primary);
  letter-spacing: -0.05em;
}

.kpi-trend {
  font-size: 0.8rem;
  font-weight: 600;
}

.kpi-trend.positive { color: var(--success); }
.kpi-trend.neutral { color: var(--text-muted); }
.kpi-trend.negative { color: var(--danger); }

/* Content Grid */
.content-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}

.panel {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
}

.panel-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 1rem;
}

.panel-header h3 {
  font-size: 1.25rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-text {
  background: none;
  border: none;
  color: var(--accent-primary);
  font-weight: 600;
  cursor: pointer;
}

.btn-text:hover {
  text-decoration: underline;
}

.event-list {
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.event-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 12px;
  background-color: rgba(15, 23, 42, 0.4);
  border: 1px solid var(--border-color);
  transition: all 0.2s ease;
}

.event-item:hover {
  background-color: rgba(255, 255, 255, 0.05);
}

.event-date {
  background-color: rgba(99, 102, 241, 0.15);
  color: var(--accent-primary);
  min-width: 60px;
  height: 60px;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.event-date .day { font-size: 1.2rem; font-weight: 700; }
.event-date .month { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; }

.event-info {
  flex: 1;
}

.event-info h4 {
  font-size: 1rem;
  font-weight: 600;
  margin-bottom: 0.2rem;
}

.event-info p {
  font-size: 0.85rem;
  color: var(--text-muted);
}

.event-status {
  padding: 0.4rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-pending { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
.status-ready { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }

/* AI Panel */
.ai-desc {
  font-size: 0.9rem;
  color: var(--text-secondary);
  margin-bottom: 1.5rem;
}

.ai-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-row {
  display: flex;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
}

label {
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

select, input {
  width: 100%;
  padding: 0.75rem 1rem;
  background-color: rgba(15, 23, 42, 0.6);
  border: 1px solid var(--border-color);
  border-radius: 8px;
  color: var(--text-primary);
  font-family: inherit;
  font-size: 0.95rem;
  transition: all 0.2s;
}

select:focus, input:focus {
  outline: none;
  border-color: var(--accent-primary);
}

.w-full {
  width: 100%;
  margin-top: 0.5rem;
}

.prediction-result {
  margin-top: 1.5rem;
  padding: 1.5rem;
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(139, 92, 246, 0.15) 100%);
  border: 1px solid rgba(139, 92, 246, 0.3);
  border-radius: 12px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  animation: fade-in 0.5s ease;
}

.pred-label {
  font-size: 0.9rem;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.pred-value {
  font-size: 2.5rem;
  font-weight: 700;
  background: -webkit-linear-gradient(0deg, #60a5fa, #c084fc);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

@keyframes fade-in {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}
</style>
