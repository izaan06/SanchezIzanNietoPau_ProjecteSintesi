<template>
  <div class="dashboard-view">
    
    <!-- Secció de KPIs Reals -->
    <div class="kpi-grid">
      <div class="kpi-card glass-card">
        <div class="kpi-header">
          <div class="kpi-icon blue"><TrendingUp /></div>
          <span class="kpi-title">Ingressos Totals</span>
        </div>
        <div class="kpi-value">€{{ stats.total_revenue?.toLocaleString() || '0' }}</div>
        <div class="kpi-trend positive">Facturació acumulada</div>
      </div>

      <div class="kpi-card glass-card highlight-profit">
        <div class="kpi-header">
          <div class="kpi-icon green"><Sparkles /></div>
          <span class="kpi-title">Benefici Net IA</span>
        </div>
        <div class="kpi-value success">€{{ stats.total_profit?.toLocaleString() || '0' }}</div>
        <div class="kpi-trend positive">↑ ROI optimitzat per IA</div>
      </div>

      <div class="kpi-card glass-card">
        <div class="kpi-header">
          <div class="kpi-icon purple"><Users /></div>
          <span class="kpi-title">Clients Reals</span>
        </div>
        <div class="kpi-value">{{ stats.total_clients || '0' }}</div>
        <div class="kpi-trend neutral">Base de dades activa</div>
      </div>

      <div class="kpi-card glass-card">
        <div class="kpi-header">
          <div class="kpi-icon indigo"><Briefcase /></div>
          <span class="kpi-title">Treballadors</span>
        </div>
        <div class="kpi-value">{{ stats.total_workers || '0' }}</div>
        <div class="kpi-trend neutral">Personal disponible</div>
      </div>
    </div>

    <!-- Secció d'Anàlisi Visual -->
    <div class="content-grid">
      
      <!-- Gràfic de Rentabilitat -->
      <div class="panel glass-card chart-panel">
        <div class="panel-header">
          <h3><PieChart class="icon-sm" /> Distribució Financera</h3>
        </div>
        <div class="panel-body chart-container">
          <Doughnut v-if="chartDataReady" :data="profitChartData" :options="chartOptions" />
          <div v-else class="loading-chart">Carregant dades...</div>
        </div>
      </div>

      <!-- Pròxims Esdeveniments Reals -->
      <div class="panel glass-card events-panel">
        <div class="panel-header">
          <h3><CalendarDays class="icon-sm" /> Pròxims Esdeveniments</h3>
          <router-link to="/events" class="btn-text">Veure tots</router-link>
        </div>
        <div class="panel-body">
          <ul class="event-list" v-if="stats.recent_events?.length > 0">
            <li v-for="event in stats.recent_events" :key="event.id" class="event-item">
              <div class="event-date">
                <span class="day">{{ new Date(event.date).getDate() }}</span>
                <span class="month">{{ formatMonth(event.date) }}</span>
              </div>
              <div class="event-info">
                <h4>{{ event.name }}</h4>
                <p>{{ event.client?.name || 'Client no assignat' }} · {{ event.location }}</p>
              </div>
              <div :class="['status-dot', event.status || 'confirmed']"></div>
            </li>
          </ul>
          <div v-else class="empty-dashboard">
            <CalendarX class="icon-lg muted" />
            <p>No hi ha esdeveniments programats</p>
          </div>
        </div>
      </div>

      <!-- Estadístiques d'Estat -->
      <div class="panel glass-card status-panel">
        <div class="panel-header">
          <h3><BarChart3 class="icon-sm" /> Estats de l'Equip</h3>
        </div>
        <div class="panel-body chart-container-mini">
          <Bar v-if="chartDataReady" :data="statusChartData" :options="barOptions" />
        </div>
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { 
  CalendarDays, Users, Briefcase, Sparkles, TrendingUp, 
  PieChart, BarChart3, CalendarX 
} from 'lucide-vue-next'
import api from '../api/axios'
import { Doughnut, Bar } from 'vue-chartjs'
import { 
  Chart as ChartJS, Title, Tooltip, Legend, 
  ArcElement, CategoryScale, LinearScale, BarElement 
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, BarElement)

const stats = ref({
  total_events: 0,
  total_clients: 0,
  total_workers: 0,
  total_revenue: 0,
  total_profit: 0,
  total_operational_cost: 0,
  recent_events: [],
  events_by_status: []
})

const isLoading = ref(true)
const chartDataReady = ref(false)

const fetchDashboardData = async () => {
  try {
    const response = await api.get('/dashboard')
    stats.value = response.data
    chartDataReady.value = true
  } catch (error) {
    console.error('Error fetching dashboard stats:', error)
  } finally {
    isLoading.value = false
  }
}

onMounted(fetchDashboardData)

// Configuració dels gràfics
const profitChartData = computed(() => ({
  labels: ['Benefici Net', 'Cost Operatiu'],
  datasets: [{
    data: [stats.value.total_profit, stats.value.total_operational_cost],
    backgroundColor: ['#10b981', '#ef4444'],
    borderColor: 'rgba(15, 23, 42, 0.8)',
    borderWidth: 5
  }]
}))

const statusChartData = computed(() => ({
  labels: stats.value.events_by_status.map(s => formatStatusLabel(s.status)),
  datasets: [{
    label: 'Esdeveniments',
    data: stats.value.events_by_status.map(s => s.count),
    backgroundColor: '#6366f1',
    borderRadius: 10
  }]
}))

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { position: 'bottom', labels: { color: '#94a3b8', font: { weight: 'bold' } } }
  }
}

const barOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false } },
  scales: {
    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
  }
}

const formatMonth = (date) => {
  return new Date(date).toLocaleString('ca-ES', { month: 'short' }).toUpperCase()
}

const formatStatusLabel = (s) => {
  const map = {
    'confirmed': 'Confirmat',
    'planning': 'Planificant',
    'in_progress': 'En Marxa',
    'completed': 'Finalitzat',
    'cancelled': 'Cancel·lat'
  };
  return map[s] || s;
}
</script>

<style scoped>
.dashboard-view {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
}

.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 1.5rem;
}

.glass-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  padding: 2rem;
  transition: transform 0.3s;
}

.glass-card:hover { transform: translateY(-5px); }

.highlight-profit {
  border: 1px solid rgba(16, 185, 129, 0.2);
  background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(15, 23, 42, 0.6));
}

.kpi-header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
.kpi-icon {
  width: 48px; height: 48px; border-radius: 14px;
  display: flex; justify-content: center; align-items: center; color: white;
}
.kpi-icon.blue { background: #3b82f6; box-shadow: 0 8px 16px rgba(59, 130, 246, 0.2); }
.kpi-icon.purple { background: #8b5cf6; box-shadow: 0 8px 16px rgba(139, 92, 246, 0.2); }
.kpi-icon.green { background: #10b981; box-shadow: 0 8px 16px rgba(16, 185, 129, 0.2); }
.kpi-icon.indigo { background: #6366f1; box-shadow: 0 8px 16px rgba(99, 102, 241, 0.2); }

.kpi-title { color: var(--text-muted); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; }
.kpi-value { font-size: 2.5rem; font-weight: 900; color: #fff; margin-bottom: 0.5rem; letter-spacing: -0.04em; }
.kpi-value.success { color: #10b981; }

.kpi-trend { font-size: 0.8rem; font-weight: 600; color: var(--text-muted); }

/* Content Grid */
.content-grid {
  display: grid;
  grid-template-columns: 1fr 1.2fr 1fr;
  gap: 2rem;
}

.panel { padding: 2rem; display: flex; flex-direction: column; height: 500px; }
.panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
.panel-header h3 { font-size: 1.1rem; font-weight: 800; color: #fff; text-transform: uppercase; letter-spacing: 0.1em; display: flex; align-items: center; gap: 0.75rem; }

.chart-container { flex: 1; position: relative; }
.chart-container-mini { flex: 1; padding: 1rem 0; }

.event-list { list-style: none; display: flex; flex-direction: column; gap: 1rem; overflow-y: auto; }
.event-item {
  display: flex; align-items: center; gap: 1.25rem; padding: 1.25rem;
  background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 18px; transition: all 0.2s;
}
.event-item:hover { background: rgba(255, 255, 255, 0.05); transform: scale(1.02); }

.event-date {
  background: rgba(99, 102, 241, 0.1); color: var(--accent-primary);
  min-width: 54px; height: 54px; border-radius: 12px;
  display: flex; flex-direction: column; justify-content: center; align-items: center;
}
.event-date .day { font-size: 1.2rem; font-weight: 800; }
.event-date .month { font-size: 0.65rem; font-weight: 900; }

.event-info h4 { font-size: 1rem; color: #fff; margin-bottom: 0.25rem; }
.event-info p { font-size: 0.8rem; color: var(--text-muted); }

.status-dot { width: 12px; height: 12px; border-radius: 50%; }
.status-dot.confirmed { background: #3b82f6; box-shadow: 0 0 10px #3b82f6; }
.status-dot.planning { background: #8b5cf6; box-shadow: 0 0 10px #8b5cf6; }
.status-dot.in_progress { background: #f59e0b; box-shadow: 0 0 10px #f59e0b; }
.status-dot.completed { background: #10b981; box-shadow: 0 0 10px #10b981; }

.btn-text { color: var(--accent-primary); font-weight: 700; text-decoration: none; font-size: 0.9rem; }

.loading-chart, .empty-dashboard { 
  display: flex; flex-direction: column; align-items: center; justify-content: center; 
  height: 100%; color: var(--text-muted); gap: 1rem; 
}

@media (max-width: 1400px) {
  .content-grid { grid-template-columns: 1fr 1fr; }
  .status-panel { grid-column: span 2; height: 350px; }
}
@media (max-width: 900px) {
  .content-grid { grid-template-columns: 1fr; }
  .status-panel { grid-column: span 1; }
}
</style>
