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
              {{ worker.name }}
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

// --- ESTAT DEL COMPONENT ---
const allWorkers = ref([]);      // Llista de tots els treballadors del sistema
const assignedWorkers = ref([]); // Treballadors assignats a aquest esdeveniment concret
const selectedWorkerId = ref(''); // ID del treballador seleccionat al desplegable
const isLoading = ref(false);     // Indicador de càrrega
const isAssigning = ref(false);   // Indicador de procés d'assignació en curs
const message = ref('');          // Missatge de feedback
const messageType = ref('');      // Tipus de missatge (success/error)

/**
 * Computed: Filtra la llista de tots els treballadors per mostrar només
 * aquells que encara NO estan assignats a l'esdeveniment actual.
 */
const availableWorkers = computed(() => {
  const assignedIds = assignedWorkers.value.map(w => w.id);
  return allWorkers.value.filter(w => !assignedIds.includes(w.id));
});

// --- MÈTODES API ---

/**
 * Obté la llista global de treballadors del sistema.
 */
const fetchAllWorkers = async () => {
  try {
    const response = await api.get('/workers');
    allWorkers.value = response.data.data || response.data;
  } catch (error) {
    console.error("Error carregant treballadors globals:", error);
    showMessage('Error carregant la llista de treballadors', 'error');
  }
};

/**
 * Obté els treballadors que ja estan vinculats a aquest esdeveniment.
 */
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

/**
 * Assigna el treballador seleccionat a l'esdeveniment actual.
 */
const assignWorker = async () => {
  if (!selectedWorkerId.value) return;
  
  isAssigning.value = true;
  try {
    await api.post(`/events/${props.eventId}/workers`, {
      worker_id: selectedWorkerId.value
    });
    showMessage('Treballador assignat correctament', 'success');
    selectedWorkerId.value = '';
    await fetchAssignedWorkers(); // Recarreguem per veure el nou treballador a la llista
  } catch (error) {
    console.error("Error assignant treballador:", error);
    showMessage(error.response?.data?.message || 'Error al assignar el treballador', 'error');
  } finally {
    isAssigning.value = false;
  }
};

/**
 * Elimina el vincle entre un treballador i l'esdeveniment.
 */
const unassignWorker = async (workerId) => {
  if (!confirm("Vols desassignar aquest treballador de l'esdeveniment?")) return;
  
  try {
    await api.delete(`/events/${props.eventId}/workers/${workerId}`);
    showMessage('Treballador desassignat', 'success');
    await fetchAssignedWorkers(); // Actualitzem la llista
  } catch (error) {
    console.error("Error desassignant treballador:", error);
    showMessage('Error al desassignar', 'error');
  }
};

/**
 * Mostra un missatge temporal a la pantalla.
 */
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
  gap: 3.5rem;
  padding: 1rem;
}

h3 {
  font-size: 2rem;
  font-weight: 900;
  color: #fff;
  letter-spacing: -0.04em;
  margin-bottom: 1rem;
}

h4 {
  font-size: 0.85rem;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  gap: 1rem;
}
h4::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.08); }

.assigned-section, .assign-section {
  background: rgba(255,255,255,0.02);
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 24px;
  padding: 2.5rem;
}

.worker-list {
  list-style: none;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.worker-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 1.5rem;
  background: rgba(255,255,255,0.03);
  border-radius: 16px;
  border: 1px solid rgba(255,255,255,0.05);
  transition: all 0.3s ease;
}

.worker-item:hover {
  background: rgba(255,255,255,0.05);
  transform: translateX(5px);
}

.worker-info {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.worker-name {
  font-weight: 700;
  color: #fff;
  font-size: 1.05rem;
}

.worker-role {
  font-size: 0.8rem;
  color: var(--accent-primary);
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.btn-remove {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
  width: 32px;
  height: 32px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-remove:hover {
  background: #ef4444;
  color: white;
}

.assign-form {
  display: flex;
  gap: 1.5rem;
  align-items: stretch;
}

.worker-select {
  flex: 1;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px;
  padding: 1rem 1.25rem;
  color: #fff;
  font-size: 1rem;
  font-family: inherit;
}

.btn-assign {
  background: var(--accent-primary);
  color: white;
  border: none;
  padding: 0 2.5rem;
  border-radius: 14px;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-assign:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px -5px rgba(99, 102, 241, 0.5);
}

.btn-assign:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.empty-state {
  padding: 3rem;
  text-align: center;
  border: 2px dashed rgba(255,255,255,0.05);
  border-radius: 20px;
  color: var(--text-muted);
  font-style: italic;
}

.alert {
  padding: 1.5rem;
  border-radius: 18px;
  font-weight: 600;
  margin-top: 1rem;
}

.alert.success { background: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2); }
.alert.error { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

.loading {
  padding: 5rem;
  text-align: center;
  color: var(--text-muted);
  font-weight: 600;
}
</style>
