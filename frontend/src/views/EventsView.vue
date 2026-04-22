<template>
  <div class="events-view">
    <div v-if="selectedEventId" class="worker-assignment-container glass-card">
      <div class="header-back">
        <button @click="selectedEventId = null" class="btn-text">
          <ArrowLeft class="icon-sm" /> Tornar a Esdeveniments
        </button>
      </div>
      <WorkerAssignment :eventId="selectedEventId" />
    </div>

    <div v-else>
      <div class="header">
        <h2>Llistat d'Esdeveniments</h2>
        <button @click="openCreateForm" class="btn-primary" v-if="!showForm">
          <Plus class="icon-sm" /> Nou Esdeveniment
        </button>
        <button @click="cancelForm" class="btn-secondary" v-else>
          Tornar a la llista
        </button>
      </div>

    <!-- Feedback message -->
    <div v-if="message" :class="['alert', messageType]">
      {{ message }}
    </div>

    <!-- Form for Create/Edit -->
    <div v-if="showForm" class="form-card">
      <h3>{{ isEditing ? 'Editar Esdeveniment' : 'Crear Nou Esdeveniment' }}</h3>
      <form @submit.prevent="submitForm">
        <div class="form-group">
          <label>Títol</label>
          <input v-model="formData.title" type="text" required placeholder="Nom de l'esdeveniment" />
        </div>
        <div class="form-group">
          <label>Descripció</label>
          <textarea v-model="formData.description" rows="3" placeholder="Detalls de l'esdeveniment"></textarea>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Data d'inici</label>
            <input v-model="formData.start_date" type="datetime-local" required />
          </div>
          <div class="form-group">
            <label>Data final</label>
            <input v-model="formData.end_date" type="datetime-local" />
          </div>
        </div>
        <div class="form-group">
          <label>Ubicació</label>
          <input v-model="formData.location" type="text" placeholder="Lloc de l'esdeveniment" />
        </div>
        
        <div class="form-actions">
          <button type="button" @click="cancelForm" class="btn btn-secondary">Cancel·lar</button>
          <button type="submit" class="btn btn-primary" :disabled="isLoading">
            {{ isLoading ? 'Desant...' : (isEditing ? 'Actualitzar' : 'Crear') }}
          </button>
        </div>
      </form>
    </div>

    <!-- List of Events -->
    <div v-else class="events-list">
      <div v-if="isLoading && events.length === 0" class="loading">
        Carregant esdeveniments...
      </div>
      <div v-else-if="events.length === 0" class="empty-state">
        No hi ha cap esdeveniment. Crea'n un de nou!
      </div>
      <div v-else class="table-responsive">
        <table class="events-table">
          <thead>
            <tr>
              <th>Títol</th>
              <th>Data d'inici</th>
              <th>Ubicació</th>
              <th>Accions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="event in events" :key="event.id">
              <td>
                <div class="event-title">{{ event.title }}</div>
                <div class="event-desc">{{ truncate(event.description, 50) }}</div>
              </td>
              <td>{{ formatDate(event.start_date) }}</td>
              <td>{{ event.location || '-' }}</td>
              <td class="actions-cell">
                <button @click="handleManageWorkers(event.id)" class="btn-icon workers" title="Gestionar Treballadors">
                  👥
                </button>
                <button @click="openEditForm(event)" class="btn-icon edit" title="Editar">
                  ✏️
                </button>
                <button @click="confirmDelete(event.id)" class="btn-icon delete" title="Eliminar">
                  🗑️
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Plus, ArrowLeft } from 'lucide-vue-next';
import api from '../api/axios';
import WorkerAssignment from '../components/WorkerAssignment.vue';

const selectedEventId = ref(null);

const handleManageWorkers = (id) => {
  selectedEventId.value = id;
};

// Estat
const events = ref([]);
const isLoading = ref(false);
const showForm = ref(false);
const isEditing = ref(false);
const message = ref('');
const messageType = ref('');

// Dades del formulari
const initialFormState = {
  title: '',
  description: '',
  start_date: '',
  end_date: '',
  location: ''
};
const formData = ref({ ...initialFormState });
const currentEventId = ref(null);

// Mètodes API
const fetchEvents = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/events');
    // Depenent de com retornis des de Laravel (pot venir paginat o dins 'data')
    events.value = response.data.data || response.data;
  } catch (error) {
    showMessage('Error al carregar els esdeveniments', 'error');
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

const submitForm = async () => {
  isLoading.value = true;
  try {
    if (isEditing.value) {
      await api.put(`/events/${currentEventId.value}`, formData.value);
      showMessage('Esdeveniment actualitzat correctament', 'success');
    } else {
      await api.post('/events', formData.value);
      showMessage('Esdeveniment creat correctament', 'success');
    }
    cancelForm();
    fetchEvents(); // Recarrega la llista
  } catch (error) {
    showMessage(error.response?.data?.message || 'Error al desar. Revisa les dades.', 'error');
    console.error(error);
  } finally {
    isLoading.value = false;
  }
};

const deleteEvent = async (id) => {
  try {
    await api.delete(`/events/${id}`);
    showMessage('Esdeveniment eliminat correctament', 'success');
    fetchEvents(); // Recarrega la llista
  } catch (error) {
    showMessage('Error al eliminar l\'esdeveniment', 'error');
    console.error(error);
  }
};

// Controladors de l'UI
const openCreateForm = () => {
  isEditing.value = false;
  formData.value = { ...initialFormState };
  showForm.value = true;
};

const openEditForm = (event) => {
  isEditing.value = true;
  currentEventId.value = event.id;
  // Formatejar les dates pel camp <input type="datetime-local">
  formData.value = {
    title: event.title,
    description: event.description || '',
    start_date: formatForInput(event.start_date),
    end_date: formatForInput(event.end_date),
    location: event.location || ''
  };
  showForm.value = true;
};

const cancelForm = () => {
  showForm.value = false;
  formData.value = { ...initialFormState };
  currentEventId.value = null;
};

const confirmDelete = (id) => {
  if (confirm('Estàs segur que vols eliminar aquest esdeveniment? Aquesta acció no es pot desfer.')) {
    deleteEvent(id);
  }
};

const showMessage = (msg, type) => {
  message.value = msg;
  messageType.value = type;
  setTimeout(() => {
    message.value = '';
  }, 5000); // S'amaga en 5 segons
};

// Funcions d'Utilitat
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('ca-ES', { 
    year: 'numeric', month: 'short', day: 'numeric',
    hour: '2-digit', minute: '2-digit'
  }).format(date);
};

const formatForInput = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  // Ajustar la zona horària pel format requerit 'yyyy-MM-ddThh:mm'
  return new Date(date.getTime() - (date.getTimezoneOffset() * 60000))
    .toISOString()
    .slice(0, 16);
};

const truncate = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};

// Inici
onMounted(() => {
  fetchEvents();
});
</script>

<style scoped>
.events-view {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.worker-assignment-container {
  padding: 2rem;
}

.header-back {
  margin-bottom: 1.5rem;
}

.btn-text {
  background: none;
  border: none;
  color: var(--text-secondary);
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-family: inherit;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-text:hover {
  color: var(--text-primary);
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.header h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.btn-secondary {
  background-color: rgba(255, 255, 255, 0.1);
  color: var(--text-primary);
  border: 1px solid var(--border-color);
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-family: inherit;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn-secondary:hover {
  background-color: rgba(255, 255, 255, 0.2);
}

.btn-icon {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--glass-border);
  cursor: pointer;
  font-size: 1.1rem;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s ease;
  color: var(--text-primary);
}

.btn-icon:hover {
  background: rgba(255, 255, 255, 0.15);
  transform: translateY(-2px);
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

.form-card {
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  padding: 2rem;
  border-radius: 16px;
  border: 1px solid var(--glass-border);
}

.form-group {
  margin-bottom: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-row {
  display: flex;
  gap: 1.5rem;
}

.form-row .form-group { flex: 1; }

label {
  font-weight: 600;
  font-size: 0.85rem;
  color: var(--text-secondary);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

input, textarea {
  padding: 0.75rem 1rem;
  background-color: rgba(15, 23, 42, 0.6);
  border: 1px solid var(--border-color);
  border-radius: 10px;
  color: var(--text-primary);
  font-family: inherit;
  font-size: 1rem;
  transition: all 0.2s ease;
}

input:focus, textarea:focus {
  outline: none;
  border-color: var(--accent-primary);
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 2rem;
}

.table-responsive {
  overflow-x: auto;
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  border-radius: 16px;
  border: 1px solid var(--glass-border);
}

.events-table {
  width: 100%;
  border-collapse: collapse;
}

.events-table th, .events-table td {
  padding: 1.25rem 1.5rem;
  text-align: left;
  border-bottom: 1px solid var(--border-color);
}

.events-table th {
  background-color: rgba(15, 23, 42, 0.4);
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}

.events-table tr:last-child td { border-bottom: none; }
.events-table tr:hover td { background-color: rgba(255, 255, 255, 0.02); }

.event-title {
  font-weight: 600;
  color: var(--text-primary);
  font-size: 1.05rem;
}

.event-desc {
  font-size: 0.85rem;
  color: var(--text-muted);
  margin-top: 0.25rem;
}

.actions-cell {
  display: flex;
  gap: 0.5rem;
}

.empty-state, .loading {
  text-align: center;
  padding: 4rem 2rem;
  color: var(--text-muted);
  background: rgba(15, 23, 42, 0.4);
  border-radius: 16px;
  border: 1px dashed var(--border-color);
}
</style>
