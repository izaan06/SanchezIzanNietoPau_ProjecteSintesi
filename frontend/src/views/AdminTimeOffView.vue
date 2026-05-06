<template>
  <div class="admin-timeoff">
    <div class="header">
      <h2>Gestió de Vacances i Festes</h2>
      <p class="subtitle">Aprova o rebutja les peticions de l'equip.</p>
    </div>

    <div v-if="loading" class="loading">Carregant peticions...</div>

    <div v-else class="requests-list">
      <div v-if="requests.length === 0" class="empty-state">
        No hi ha peticions pendents.
      </div>
      
      <div v-for="req in requests" :key="req.id" class="glass-card request-card" :class="req.status">
        <div class="req-header">
          <div class="worker-info">
            <span class="avatar">{{ req.worker.name.charAt(0) }}</span>
            <div>
              <h3>{{ req.worker.name }}</h3>
              <p>{{ req.worker.role }}</p>
            </div>
          </div>
          <div :class="['status-badge', req.status]">{{ req.status.toUpperCase() }}</div>
        </div>

        <div class="req-body">
          <div class="dates">
            <Calendar class="icon-sm" />
            <span>{{ formatDate(req.start_date) }} - {{ formatDate(req.end_date) }}</span>
          </div>
          <p class="reason" v-if="req.reason">"{{ req.reason }}"</p>
        </div>

        <div class="req-footer" v-if="req.status === 'pending'">
          <button @click="updateStatus(req.id, 'rejected')" class="btn-outline-danger">Rebutjar</button>
          <button @click="updateStatus(req.id, 'approved')" class="btn-success">Aprovar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Calendar } from 'lucide-vue-next'
import api from '../api/axios'

const requests = ref([])
const loading = ref(true)

const fetchRequests = async () => {
  try {
    const res = await api.get('/admin/time-off')
    requests.value = res.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const updateStatus = async (id, status) => {
  try {
    await api.patch(`/admin/time-off/${id}`, { status })
    fetchRequests()
  } catch (err) {
    console.error(err)
  }
}

const formatDate = (d) => new Date(d).toLocaleDateString()

onMounted(fetchRequests)
</script>

<style scoped>
.admin-timeoff {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.requests-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 1.5rem;
}

.request-card {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.req-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.worker-info {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.avatar {
  width: 40px;
  height: 40px;
  background: var(--bg-tertiary);
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: 700;
}

.worker-info h3 { font-size: 1rem; margin: 0; }
.worker-info p { font-size: 0.8rem; color: var(--text-muted); margin: 0; }

.status-badge {
  font-size: 0.7rem;
  font-weight: 800;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
}

.status-badge.pending { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
.status-badge.approved { background: rgba(16, 185, 129, 0.2); color: #10b981; }
.status-badge.rejected { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

.req-body {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.dates {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.reason {
  font-style: italic;
  font-size: 0.9rem;
  color: var(--text-secondary);
}

.req-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 0.5rem;
  padding-top: 1rem;
  border-top: 1px solid var(--border-color);
}

.btn-success { background: var(--success); color: white; border: none; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; }
.btn-outline-danger { background: transparent; border: 1px solid var(--danger); color: var(--danger); padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; }

.icon-sm { width: 18px; height: 18px; }
</style>
