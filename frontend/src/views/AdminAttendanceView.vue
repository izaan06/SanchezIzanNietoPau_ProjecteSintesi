<template>
  <div class="admin-attendance-view">
    <div class="header">
      <h2>Registre de Jornada</h2>
      <button @click="fetchLogs" class="btn-secondary" :disabled="loading">
        <RefreshCw :class="['icon-sm', { 'spin': loading }]" /> Refrescar
      </button>
    </div>

    <div v-if="loading" class="loading">Carregant registres...</div>

    <div v-else class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>Treballador</th>
            <th>Entrada</th>
            <th>Sortida</th>
            <th>Durada</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="logs.length === 0">
            <td colspan="5" class="empty-msg">No hi ha registres de fitxatge.</td>
          </tr>
          <tr v-for="log in logs" :key="log.id">
            <td>
              <div class="worker-cell">
                <div class="worker-avatar">{{ log.worker?.name?.charAt(0) }}</div>
                <span>{{ log.worker?.name }}</span>
              </div>
            </td>
            <td>
              <div class="time-cell">
                <Calendar class="icon-xs" /> {{ formatDate(log.clock_in) }}
                <span class="time-val">{{ formatTime(log.clock_in) }}</span>
              </div>
            </td>
            <td>
              <div v-if="log.clock_out" class="time-cell">
                <Calendar class="icon-xs" /> {{ formatDate(log.clock_out) }}
                <span class="time-val">{{ formatTime(log.clock_out) }}</span>
              </div>
              <span v-else class="status-badge active">En curs</span>
            </td>
            <td>
              <span v-if="log.clock_out" class="duration">
                {{ calculateDuration(log.clock_in, log.clock_out) }}
              </span>
              <span v-else>--</span>
            </td>
            <td class="notes-cell" :title="log.notes">{{ log.notes || '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { RefreshCw, Calendar, Clock } from 'lucide-vue-next'
import api from '../api/axios'

const logs = ref([])
const loading = ref(false)

const fetchLogs = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/attendance')
    logs.value = res.data.data
  } catch (err) {
    console.error("Error carregant fitxatges:", err)
  } finally {
    loading.value = false
  }
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('ca-ES', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('ca-ES', { hour: '2-digit', minute: '2-digit' })
}

const calculateDuration = (start, end) => {
  const diff = Math.floor((new Date(end) - new Date(start)) / 1000)
  const h = Math.floor(diff / 3600)
  const m = Math.floor((diff % 3600) / 60)
  return `${h}h ${m}m`
}

onMounted(fetchLogs)
</script>

<style scoped>
.admin-attendance-view {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 0.5rem;
}

.btn-secondary {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.6rem 1.2rem;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  color: var(--text-primary);
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(8px);
}

.btn-secondary:hover:not(:disabled) {
  background: var(--accent-primary);
  border-color: var(--accent-primary);
  box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
  transform: translateY(-2px);
}

.btn-secondary:active:not(:disabled) {
  transform: translateY(0);
}

.btn-secondary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.table-responsive {
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  border-radius: 16px;
  border: 1px solid var(--glass-border);
  overflow: hidden;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th, .data-table td {
  padding: 1.25rem 1.5rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

.worker-cell {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.worker-avatar {
  width: 32px;
  height: 32px;
  background: var(--bg-tertiary);
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: 700;
  font-size: 0.8rem;
}

.time-cell {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  font-size: 0.85rem;
  color: var(--text-secondary);
}

.time-val {
  font-weight: 700;
  color: var(--text-primary);
}

.duration {
  font-weight: 600;
  color: var(--accent-primary);
}

.status-badge.active {
  background: rgba(16, 185, 129, 0.1);
  color: #10b981;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
}

.notes-cell {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  font-size: 0.85rem;
  color: var(--text-muted);
}

.empty-msg {
  text-align: center;
  padding: 3rem !important;
  color: var(--text-muted);
  font-style: italic;
}

.spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.icon-sm { width: 18px; height: 18px; }
.icon-xs { width: 14px; height: 14px; opacity: 0.6; }
</style>
