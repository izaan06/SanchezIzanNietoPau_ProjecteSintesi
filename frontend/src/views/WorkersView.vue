<template>
  <div class="workers-view">
    <div class="header">
      <h2>Gestió de Personal</h2>
      <button @click="openCreateModal" class="btn-primary">
        <Plus class="icon-sm" /> Nou Treballador
      </button>
    </div>

    <!-- Modal Form -->
    <div v-if="showModal" class="modal-overlay">
      <div class="glass-card modal-content">
        <h3>{{ isEditing ? 'Editar Treballador' : 'Nou Treballador' }}</h3>
        
        <form @submit.prevent="saveWorker" class="form-compact">


          <div class="form-grid">
            <div class="form-group">
              <label>Nom Complet</label>
              <input v-model="currentWorker.name" type="text" placeholder="Nom" required />
            </div>
            <div class="form-group">
              <label>Rol Principal</label>
              <input v-model="currentWorker.role" type="text" placeholder="Ex: Coordinadora" required />
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Ubicació</label>
              <input v-model="currentWorker.location" type="text" placeholder="Ciutat" />
            </div>
            <div class="form-group">
              <label>IA Rating (1-5)</label>
              <input v-model="currentWorker.rating" type="number" step="0.1" min="1" max="5" />
            </div>
          </div>

          <div class="form-grid">
            <div class="form-group">
              <label>Etiquetes Especialitat</label>
              <input 
                :value="currentWorker.specialization_tags?.join(', ')" 
                @input="e => currentWorker.specialization_tags = e.target.value.split(',').map(t => t.trim())"
                type="text" 
                placeholder="Ex: wedding, party" 
              />
            </div>
            <div class="form-group">
              <label>Cost/h (€)</label>
              <input v-model="currentWorker.cost_per_hour" type="number" step="0.01" required />
            </div>
          </div>

          <div class="checkbox-group">
            <input v-model="currentWorker.availability" type="checkbox" id="avail" />
            <label for="avail">Disponible per esdeveniments</label>
          </div>

          <div class="modal-actions">
            <button type="button" @click="closeModal" class="btn-secondary" :disabled="isSaving">Cancel·lar</button>
            <button type="submit" class="btn-primary" :disabled="isSaving">
              {{ isSaving ? 'Guardant...' : 'Guardar' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="loading" class="loading">Carregant treballadors...</div>
    
    <div v-else class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom Complet</th>
            <th>Rol / Especialitat</th>
            <th>Cost/h</th>
            <th>Estat</th>
            <th>Accions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="worker in workers" :key="worker.id">
            <td>#{{ worker.id }}</td>
            <td class="primary-col">{{ worker.name }}</td>
            <td><span class="role-badge">{{ worker.role }}</span></td>
            <td>{{ worker.cost_per_hour }} €/h</td>
            <td>
              <span 
                class="status-badge" 
                :class="isBusyToday(worker) ? 'busy' : 'available'"
              >
                {{ isBusyToday(worker) ? 'Ocupat' : 'Disponible' }}
              </span>
            </td>
            <td class="actions-cell">
              <button @click="editWorker(worker)" class="btn-icon edit"><Edit2 class="icon-sm" /></button>
              <button @click="deleteWorker(worker.id)" class="btn-icon delete"><Trash2 class="icon-sm" /></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Plus, Edit2, Trash2 } from 'lucide-vue-next'
import api from '../api/axios'

const workers = ref([])

const loading = ref(true)
const showModal = ref(false)
const isEditing = ref(false)

const currentWorker = ref({
  id: null,
  user_id: null,
  name: '',
  role: '',
  cost_per_hour: 0,
  availability: true
})

const isBusyToday = (worker) => {
  if (!worker.availability) return true;
  // Comprovar si té algun esdeveniment actiu per avui
  const today = new Date().toISOString().split('T')[0];
  return worker.events?.some(event => {
    const eventDate = new Date(event.date).toISOString().split('T')[0];
    const isToday = eventDate === today;
    const status = event.status?.toLowerCase();
    const isActive = status !== 'finalitzat' && status !== 'completed' && status !== 'cancel·lat' && status !== 'cancelled';
    return isToday && isActive;
  });
}

const fetchWorkers = async () => {
  loading.value = true
  try {
    const res = await api.get('/workers')
    workers.value = res.data
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}



const openCreateModal = () => {
  isEditing.value = false
  currentWorker.value = { id: null, user_id: null, name: '', role: '', cost_per_hour: 0, availability: true }
  showModal.value = true
}

const editWorker = (worker) => {
  isEditing.value = true
  currentWorker.value = { ...worker }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const isSaving = ref(false)

const saveWorker = async () => {
  if (isSaving.value) return
  isSaving.value = true
  try {
    if (isEditing.value) {
      await api.put(`/workers/${currentWorker.value.id}`, currentWorker.value)
    } else {
      await api.post('/workers', currentWorker.value)
    }
    await fetchWorkers()
    closeModal()
  } catch (err) {
    alert('Error al guardar el treballador')
    console.error(err)
  } finally {
    isSaving.value = false
  }
}

const deleteWorker = async (id) => {
  if (!confirm('Segur que vols eliminar aquest treballador?')) return
  try {
    await api.delete(`/workers/${id}`)
    fetchWorkers()
  } catch (err) {
    console.error(err)
  }
}

onMounted(fetchWorkers)
</script>

<style scoped>
.workers-view {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(4px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: #1e293b;
  width: 100%;
  max-width: 550px;
  max-height: 90vh;
  border-radius: 16px;
  padding: 1.5rem;
  overflow-y: auto;
  border: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.modal-content h3 {
  margin-bottom: 1rem;
  font-size: 1.25rem;
  color: white;
}

.form-compact {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.35rem;
}

.form-group label {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--text-secondary);
}

.form-group input, .form-group select {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 0.6rem;
  border-radius: 8px;
  color: white;
  font-size: 0.85rem;
  font-family: inherit;
}

.checkbox-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
  cursor: pointer;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}

.table-responsive {
  overflow-x: auto;
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  border-radius: 16px;
  border: 1px solid var(--glass-border);
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

.role-badge {
  background-color: rgba(99, 102, 241, 0.15);
  color: var(--accent-primary);
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.available { background-color: rgba(16, 185, 129, 0.1); color: var(--success); }
.status-badge.busy { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }

.user-link { color: var(--success); font-weight: 600; display: flex; align-items: center; gap: 0.3rem; }
.user-none { color: var(--text-muted); font-style: italic; font-size: 0.85rem; }

.btn-icon {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--glass-border);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  color: var(--text-primary);
}

.btn-secondary {
  background: transparent;
  border: 1px solid var(--border-color);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  cursor: pointer;
}

.icon-xs { width: 14px; height: 14px; }
.icon-sm { width: 18px; height: 18px; }
</style>
