<template>
  <div class="worker-assignment">
    <h3>Assignació de Treballadors</h3>
    
    <div v-if="isLoading" class="loading">
      Carregant treballadors...
    </div>
    <div v-else>
      
      <!-- Llista de treballadors assignats -->
      <div class="assigned-section">
        <h4>Treballadors Assignats ({{ assignedWorkers.length }})</h4>
        <div v-if="assignedWorkers.length === 0" class="empty-state">
          No hi ha cap treballador assignat a aquest esdeveniment.
        </div>
        <ul v-else class="worker-list">
          <li v-for="worker in assignedWorkers" :key="worker.id" class="worker-item">
            <div class="worker-info">
              <span class="worker-name">{{ worker.name }}</span>
              <span class="worker-role">{{ worker.role || 'Treballador' }}</span>
            </div>
            <button @click="unassignWorker(worker.id)" class="btn-remove" title="Desassignar">
              ✕
            </button>
          </li>
        </ul>
      </div>

      <!-- Formulari d'assignació -->
      <div class="assign-section">
        <h4>Assignar Nou Treballador</h4>
        <form @submit.prevent="assignWorker" class="assign-form">
          <select v-model="selectedWorkerId" required class="worker-select">
            <option value="" disabled>Selecciona un treballador...</option>
            <option 
              v-for="worker in availableWorkers" 
              :key="worker.id" 
              :value="worker.id"
            >
              {{ worker.name }} ({{ worker.email }})
            </option>
          </select>
          <button type="submit" class="btn-assign" :disabled="!selectedWorkerId || isAssigning">
            {{ isAssigning ? 'Assignant...' : 'Assignar' }}
          </button>
        </form>
      </div>

      <!-- Missatges d'error o èxit -->
      <div v-if="message" :class="['alert', messageType]">
        {{ message }}
      </div>
      
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import api from '../api/axios';

const props = defineProps({
  eventId: {
    type: [Number, String],
    required: true
  }
});

// Estat
const allWorkers = ref([]);
const assignedWorkers = ref([]);
const selectedWorkerId = ref('');
const isLoading = ref(false);
const isAssigning = ref(false);
const message = ref('');
const messageType = ref('');

// Computed: Treballadors disponibles que encara NO estan assignats
const availableWorkers = computed(() => {
  const assignedIds = assignedWorkers.value.map(w => w.id);
  return allWorkers.value.filter(w => !assignedIds.includes(w.id));
});

// Mètodes API
const fetchAllWorkers = async () => {
  try {
    // Assumim que la ruta '/workers' o '/users' ens retorna els treballadors
    const response = await api.get('/workers');
    allWorkers.value = response.data.data || response.data;
  } catch (error) {
    console.error("Error carregant treballadors globals:", error);
    showMessage('Error carregant la llista de treballadors', 'error');
  }
};

const fetchAssignedWorkers = async () => {
  if (!props.eventId) return;
  isLoading.value = true;
  try {
    const response = await api.get(`/events/${props.eventId}/workers`);
    assignedWorkers.value = response.data.data || response.data;
  } catch (error) {
    console.error("Error carregant treballadors assignats:", error);
    showMessage('Error carregant els treballadors assignats', 'error');
  } finally {
    isLoading.value = false;
  }
};

const assignWorker = async () => {
  if (!selectedWorkerId.value) return;
  
  isAssigning.value = true;
  try {
    await api.post(`/events/${props.eventId}/workers`, {
      worker_id: selectedWorkerId.value
    });
    showMessage('Treballador assignat correctament', 'success');
    selectedWorkerId.value = '';
    await fetchAssignedWorkers(); // Recarreguem la llista per reflectir els canvis
  } catch (error) {
    console.error("Error assignant treballador:", error);
    showMessage(error.response?.data?.message || 'Error al assignar el treballador', 'error');
  } finally {
    isAssigning.value = false;
  }
};

const unassignWorker = async (workerId) => {
  if (!confirm("Vols desassignar aquest treballador de l'esdeveniment?")) return;
  
  try {
    await api.delete(`/events/${props.eventId}/workers/${workerId}`);
    showMessage('Treballador desassignat', 'success');
    await fetchAssignedWorkers(); // Recarreguem la llista
  } catch (error) {
    console.error("Error desassignant treballador:", error);
    showMessage('Error al desassignar', 'error');
  }
};

const showMessage = (msg, type) => {
  message.value = msg;
  messageType.value = type;
  setTimeout(() => {
    message.value = '';
  }, 4000);
};

// Quan canvia l'eventId per prop, recarreguem els assignats
watch(() => props.eventId, (newId) => {
  if (newId) {
    fetchAssignedWorkers();
  }
});

onMounted(async () => {
  isLoading.value = true;
  await Promise.all([
    fetchAllWorkers(),
    fetchAssignedWorkers()
  ]);
  isLoading.value = false;
});
</script>

<style scoped>
.worker-assignment {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.panel {
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  border: 1px solid var(--glass-border);
  border-radius: 16px;
  padding: 1.5rem;
  box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
}

.panel h3 {
  margin-top: 0;
  margin-bottom: 1.5rem;
  color: var(--text-primary);
  font-size: 1.25rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  border-bottom: 1px solid var(--border-color);
  padding-bottom: 0.75rem;
}

.alert {
  padding: 1rem;
  border-radius: 10px;
  margin-bottom: 1.5rem;
  font-weight: 500;
  border: 1px solid var(--glass-border);
}
.alert.success { background-color: rgba(16, 185, 129, 0.1); color: var(--success); }
.alert.error { background-color: rgba(239, 68, 68, 0.1); color: var(--danger); }
.alert.info { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }

.assign-form {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
  align-items: flex-end;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  flex: 1;
  min-width: 200px;
}

label {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

select {
  padding: 0.75rem 1rem;
  border: 1px solid var(--border-color);
  border-radius: 10px;
  background-color: rgba(15, 23, 42, 0.6);
  color: var(--text-primary);
  font-family: inherit;
  font-size: 1rem;
  transition: all 0.2s;
}

select:focus {
  outline: none;
  border-color: var(--accent-primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

/* Taula de treballadors assignats */
.table-responsive {
  overflow-x: auto;
}

.assigned-table {
  width: 100%;
  border-collapse: collapse;
}

.assigned-table th, .assigned-table td {
  padding: 1rem 1.5rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

.assigned-table th {
  background-color: rgba(15, 23, 42, 0.4);
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}

.assigned-table tr:last-child td { border-bottom: none; }
.assigned-table tr:hover td { background-color: rgba(255, 255, 255, 0.02); }

.worker-name {
  font-weight: 600;
  color: var(--text-primary);
}

.worker-role {
  background-color: rgba(99, 102, 241, 0.15);
  color: var(--accent-primary);
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  display: inline-block;
  margin-top: 0.25rem;
}

.btn-danger {
  background: rgba(239, 68, 68, 0.1);
  color: var(--danger);
  border: 1px solid rgba(239, 68, 68, 0.3);
  padding: 0.5rem 1rem;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-danger:hover {
  background: var(--danger);
  color: white;
  transform: translateY(-2px);
}

.empty-state {
  text-align: center;
  border: 1px dashed #cbd5e1;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #64748b;
}

.alert {
  margin-top: 1rem;
  padding: 0.75rem;
  border-radius: 6px;
  font-size: 0.9rem;
}

.alert.success { background: #dcfce7; color: #166534; }
.alert.error { background: #fee2e2; color: #991b1b; }
</style>
