<template>
  <div class="events-view">
    <!-- Vista de Assignació de Treballadors -->
    <div v-if="selectedEventId" class="worker-assignment-container glass-card">
      <div class="header-back">
        <button @click="selectedEventId = null" class="btn-text">
          <ArrowLeft class="icon-sm" /> Tornar a Esdeveniments
        </button>
      </div>
      <WorkerAssignment :eventId="selectedEventId" />
    </div>

    <!-- Vista Principal de Llistat -->
    <div v-else>
      <div class="header">
        <div class="header-titles">
          <h2>Gestió d'Esdeveniments</h2>
          <p class="subtitle">Administra, planifica i controla la rendibilitat dels teus esdeveniments.</p>
        </div>
        <button @click="openCreateForm" class="btn-primary">
          <Plus class="icon-sm" /> Nou Esdeveniment
        </button>
      </div>

      <!-- Missatges de Feedback -->
      <div v-if="message" :class="['alert', messageType]">
        {{ message }}
      </div>

      <!-- Llistat d'Esdeveniments -->
      <div class="events-list">
        <div v-if="isLoading && events.length === 0" class="loading">
          Carregant esdeveniments...
        </div>
        <div v-else-if="events.length === 0" class="empty-state">
          No hi ha cap esdeveniment. Comença creant-ne un!
        </div>
        <div v-else class="table-responsive glass-card no-padding">
          <table class="events-table">
            <thead>
              <tr>
                <th>Esdeveniment</th>
                <th>Estat</th>
                <th>Data i Lloc</th>
                <th>Rendibilitat</th>
                <th class="text-right">Accions</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="event in events" :key="event.id" class="event-row">
                <td>
                  <div class="event-main-cell">
                    <div class="event-avatar">
                      <Calendar class="icon-sm" />
                    </div>
                    <div class="event-info">
                      <span class="event-name">{{ event.name }}</span>
                      <span class="event-type">{{ event.type || 'General' }}</span>
                    </div>
                  </div>
                </td>
                <td>
                  <div :class="['status-badge', event.status || 'confirmed']">
                    {{ formatStatus(event.status || 'confirmed') }}
                  </div>
                </td>
                <td>
                  <div class="event-logistic-cell">
                    <div class="logistic-item">
                      <Clock class="icon-xs" /> {{ formatDateShort(event.date) }}
                    </div>
                    <div class="logistic-item muted">
                      <MapPin class="icon-xs" /> {{ event.location || 'Per determinar' }}
                    </div>
                  </div>
                </td>
                <td>
                  <div class="profit-cell" v-if="event.appointment_request">
                    <span class="profit-value success">+{{ event.appointment_request.ai_estimated_profit }}€</span>
                    <span class="profit-label">Benefici Net</span>
                  </div>
                  <div class="profit-cell muted" v-else>-</div>
                </td>
                <td class="actions-cell text-right">
                  <div class="actions-group">
                    <button @click="openDetails(event)" class="btn-action view" title="Control total">
                      <Eye class="icon-xs" /> Detalls
                    </button>
                    <button @click="handleManageWorkers(event.id)" class="btn-action workers" title="Equip">
                      <Users class="icon-xs" />
                    </button>
                    <button @click="openEditForm(event)" class="btn-action edit">
                      <Edit2 class="icon-xs" />
                    </button>
                    <button @click="confirmDelete(event.id)" class="btn-action delete">
                      <Trash2 class="icon-xs" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL DE CREACIÓ / EDICIÓ -->
    <div v-if="showForm" class="modal-overlay" @click.self="cancelForm">
      <div class="modal-content-small form-modal">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Editar Esdeveniment' : 'Nou Esdeveniment' }}</h3>
          <button @click="cancelForm" class="close-btn"><X class="icon-sm" /></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitForm">
            <div class="form-group">
              <label>Nom de l'Esdeveniment</label>
              <input v-model="formData.name" type="text" required placeholder="Ex: Boda de Maria i Joan" />
            </div>
            <div class="form-group">
              <label>Descripció</label>
              <textarea v-model="formData.description" rows="3" placeholder="Detalls de l'esdeveniment..."></textarea>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Data</label>
                <input v-model="formData.date" type="datetime-local" required />
              </div>
              <div class="form-group">
                <label>Ubicació</label>
                <input v-model="formData.location" type="text" placeholder="Ciutat o lloc" />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Assistents</label>
                <input v-model="formData.assistants" type="number" min="0" />
              </div>
              <div class="form-group">
                <label>Tipus</label>
                <input v-model="formData.type" type="text" placeholder="Boda, Corporatiu, etc." />
              </div>
            </div>
            <div class="form-group" v-if="!isEditing">
              <label>Client</label>
              <select v-model="formData.client_id" required>
                <option v-for="client in clients" :key="client.id" :value="client.id">
                  {{ client.name }}
                </option>
              </select>
            </div>
            
            <div class="form-actions-full">
              <button type="submit" class="btn btn-primary btn-block" :disabled="isLoading">
                {{ isLoading ? 'Processant...' : (isEditing ? 'Actualitzar Esdeveniment' : 'Crear Esdeveniment') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- MODAL DE DETALLS (DASHBOARD DE L'ESDEVENIMENT) -->
    <div v-if="showDetails" class="modal-overlay" @click.self="closeDetails">
      <div class="modal-content-large dashboard-modal">
        <div class="modal-header">
          <div class="header-main-info">
            <div class="header-top">
              <div :class="['status-badge-large', detailEvent?.status || 'confirmed']">
                {{ formatStatus(detailEvent?.status || 'confirmed') }}
              </div>
              <h2 class="modal-title">{{ detailEvent?.name }}</h2>
            </div>
            <div class="modal-subtitle">
              <span class="date-badge"><Calendar class="icon-xs" /> {{ formatDate(detailEvent?.date) }}</span>
              <span class="location-badge" v-if="detailEvent?.location"><MapPin class="icon-xs" /> {{ detailEvent.location }}</span>
            </div>
          </div>
          <div class="header-actions">
            <button @click="closeDetails" class="close-btn">
              <X class="icon-sm" />
            </button>
          </div>
        </div>
        
        <div class="modal-body" v-if="detailEvent">
          <div class="dashboard-grid">
            
            <!-- Columna Esquerra: Estat i Tasques -->
            <div class="dashboard-column main-controls">
              <section class="control-section">
                <h3 class="section-title">Canviar Estat</h3>
                <div class="status-selector">
                  <button 
                    v-for="s in ['confirmed', 'planning', 'in_progress', 'completed', 'cancelled']" 
                    :key="s"
                    :class="['status-opt', s, { active: detailEvent.status === s }]"
                    @click="updateEventStatus(s)"
                  >
                    {{ formatStatus(s) }}
                  </button>
                </div>
              </section>

              <section class="control-section mt-4">
                <div class="section-header">
                  <h3 class="section-title">Checklist de Planificació</h3>
                  <span class="task-count">{{ completedTasksCount }}/{{ detailEvent.tasks?.length || 0 }}</span>
                </div>
                <div class="task-list-wrapper">
                  <div class="add-task-bar">
                    <input 
                      v-model="newTaskText" 
                      @keyup.enter="addTask"
                      type="text" 
                      placeholder="Escriu una nova tasca i prem Enter..." 
                    />
                    <button @click="addTask" class="btn-add-inline" title="Afegir tasca">
                      <Plus class="icon-sm" />
                    </button>
                  </div>
                  <div class="tasks-scroll">
                    <div v-for="(task, index) in detailEvent.tasks" :key="index" class="task-item">
                      <label class="task-checkbox">
                        <input type="checkbox" v-model="task.completed" @change="saveTasks" />
                        <span class="checkmark"></span>
                        <span :class="{ completed: task.completed }">{{ task.text }}</span>
                      </label>
                      <button @click="removeTask(index)" class="btn-remove-task"><X class="icon-xs" /></button>
                    </div>
                    <div v-if="!detailEvent.tasks || detailEvent.tasks.length === 0" class="empty-tasks">
                      <CheckCircle2 class="icon-sm muted" />
                      <span>No hi ha tasques pendents.</span>
                    </div>
                  </div>
                </div>
              </section>
            </div>

            <!-- Columna Dreta: IA i Financers -->
            <div class="dashboard-column analytics">
              <section class="detail-section ai-card" v-if="detailEvent.appointment_request">
                <h3 class="ai-title"><Sparkles class="icon-sm" /> Rendibilitat IA</h3>
                
                <div class="fin-summary">
                  <div class="fin-main">
                    <span class="label">Benefici Net Estimat</span>
                    <span class="value success">+{{ detailEvent.appointment_request.ai_estimated_profit }}€</span>
                  </div>
                  <div class="fin-list">
                    <div class="fin-item">
                      <span class="label">Venda Suggerida:</span>
                      <span class="value">{{ detailEvent.appointment_request.ai_estimated_budget }} €</span>
                    </div>
                    <div class="fin-item">
                      <span class="label">Cost Operatiu:</span>
                      <span class="value">{{ detailEvent.appointment_request.ai_operational_cost }} €</span>
                    </div>
                  </div>
                </div>

                <div class="ai-recs" v-if="detailEvent.appointment_request.ai_recommendations">
                  <p class="recs-label">Recomanacions estratègiques:</p>
                  <ul class="recs-list">
                    <li v-for="(rec, i) in detailEvent.appointment_request.ai_recommendations" :key="i">
                      <span class="bullet">✦</span>
                      <span class="text">{{ rec }}</span>
                    </li>
                  </ul>
                </div>
              </section>

              <section class="detail-section mt-section">
                <h3 class="section-title">Client i Serveis</h3>
                <div class="info-group-card">
                  <div class="client-mini">
                    <div class="client-avatar">{{ detailEvent.client?.name?.[0] }}</div>
                    <div class="client-info">
                      <strong>{{ detailEvent.client?.name }}</strong>
                      <span class="email-text">{{ detailEvent.client?.email }}</span>
                    </div>
                  </div>
                  <div class="services-mini">
                    <div class="s-item">
                      <Music class="icon-sm" /> 
                      <div class="s-text">
                        <strong>Música</strong>
                        <span>{{ detailEvent.appointment_request?.wants_music ? 'Inclosa al pressupost' : 'Sense música' }}</span>
                      </div>
                    </div>
                    <div class="s-item">
                      <Cake class="icon-sm" /> 
                      <div class="s-text">
                        <strong>Càtering</strong>
                        <span>{{ detailEvent.appointment_request?.menu_type || 'Sense càtering' }}</span>
                      </div>
                    </div>
                    <div v-if="detailEvent.appointment_request?.dietary_requirements" class="s-item alert">
                      <Sparkles class="icon-sm" /> 
                      <div class="s-text">
                        <strong>Requisits Dietètics</strong>
                        <span>{{ detailEvent.appointment_request.dietary_requirements }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { 
  Plus, ArrowLeft, X, Sparkles, Music, Cake, 
  Calendar, MapPin, Clock, Eye, Users, Edit2, Trash2, CheckCircle2 
} from 'lucide-vue-next';
import api from '../api/axios';
import WorkerAssignment from '../components/WorkerAssignment.vue';

const events = ref([]);
const isLoading = ref(false);
const message = ref('');
const messageType = ref('');
const showForm = ref(false);
const isEditing = ref(false);
const selectedEventId = ref(null);
const showDetails = ref(false);
const detailEvent = ref(null);
const newTaskText = ref('');

const formData = ref({
  id: null,
  name: '',
  description: '',
  date: '',
  location: '',
  type: '',
  assistants: 0,
  client_id: null
});

const clients = ref([]);

const fetchClients = async () => {
  try {
    const response = await api.get('/clients');
    clients.value = response.data;
  } catch (error) {
    console.error('Error fetching clients:', error);
  }
};

const completedTasksCount = computed(() => {
  if (!detailEvent.value?.tasks) return 0;
  return detailEvent.value.tasks.filter(t => t.completed).length;
});

const openDetails = (event) => {
  detailEvent.value = { ...event };
  if (!detailEvent.value.tasks) detailEvent.value.tasks = [];
  showDetails.value = true;
};

const closeDetails = () => {
  showDetails.value = false;
  detailEvent.value = null;
};

const fetchEvents = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/events');
    events.value = response.data;
  } catch (error) {
    console.error('Error fetching events:', error);
    message.value = 'Error al carregar els esdeveniments';
    messageType.value = 'error';
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchEvents();
  fetchClients();
});

const handleManageWorkers = (id) => {
  selectedEventId.value = id;
};

const openCreateForm = () => {
  isEditing.value = false;
  formData.value = {
    id: null,
    name: '',
    description: '',
    date: '',
    location: '',
    type: '',
    assistants: 0,
    client_id: clients.value[0]?.id || null
  };
  showForm.value = true;
};

const openEditForm = (event) => {
  isEditing.value = true;
  formData.value = {
    id: event.id,
    name: event.name,
    description: event.description,
    date: event.date ? new Date(event.date).toISOString().slice(0, 16) : '',
    location: event.location,
    type: event.type,
    assistants: event.assistants,
    client_id: event.client_id
  };
  showForm.value = true;
};

const cancelForm = () => {
  showForm.value = false;
  isEditing.value = false;
};

const submitForm = async () => {
  isLoading.value = true;
  try {
    if (isEditing.value) {
      await api.put(`/events/${formData.value.id}`, formData.value);
      message.value = 'Esdeveniment actualitzat correctament';
    } else {
      await api.post('/events', formData.value);
      message.value = 'Esdeveniment creat correctament';
    }
    messageType.value = 'success';
    showForm.value = false;
    fetchEvents();
  } catch (error) {
    console.error('Error saving event:', error);
    message.value = 'Error al desar l\'esdeveniment';
    messageType.value = 'error';
  } finally {
    isLoading.value = false;
    setTimeout(() => { message.value = ''; }, 3000);
  }
};

const updateEventStatus = async (status) => {
  const oldStatus = detailEvent.value.status;
  detailEvent.value.status = status;
  const idx = events.value.findIndex(e => e.id === detailEvent.value.id);
  if (idx !== -1) events.value[idx].status = status;

  try {
    await api.put(`/events/${detailEvent.value.id}`, { status });
  } catch (error) {
    console.error('Error updating status:', error);
    detailEvent.value.status = oldStatus;
    if (idx !== -1) events.value[idx].status = oldStatus;
  }
};

const addTask = () => {
  if (!newTaskText.value.trim()) return;
  if (!detailEvent.value.tasks) detailEvent.value.tasks = [];
  detailEvent.value.tasks.push({ text: newTaskText.value, completed: false });
  newTaskText.value = '';
  saveTasks();
};

const removeTask = (index) => {
  detailEvent.value.tasks.splice(index, 1);
  saveTasks();
};

const saveTasks = async () => {
  try {
    await api.put(`/events/${detailEvent.value.id}`, { tasks: detailEvent.value.tasks });
    const idx = events.value.findIndex(e => e.id === detailEvent.value.id);
    if (idx !== -1) events.value[idx].tasks = detailEvent.value.tasks;
  } catch (error) {
    console.error('Error saving tasks:', error);
  }
};

const confirmDelete = async (id) => {
  if (confirm('Estàs segur que vols eliminar aquest esdeveniment? S\'eliminarà també la sol·licitud associada.')) {
    try {
      await api.delete(`/events/${id}`);
      message.value = 'Esdeveniment i sol·licitud eliminats';
      messageType.value = 'success';
      fetchEvents();
    } catch (error) {
      console.error('Error deleting event:', error);
      message.value = 'Error al eliminar l\'esdeveniment';
      messageType.value = 'error';
    } finally {
      setTimeout(() => { message.value = ''; }, 3000);
    }
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('ca-ES', options);
};

const formatDateShort = (dateString) => {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('ca-ES', { day: '2-digit', month: 'short' });
};

const formatStatus = (s) => {
  const map = {
    'confirmed': 'Confirmat',
    'planning': 'Planificant',
    'in_progress': 'En Marxa',
    'completed': 'Finalitzat',
    'cancelled': 'Cancel·lat'
  };
  return map[s] || s;
};

const truncate = (text, length) => {
  if (!text) return '';
  return text.length > length ? text.substring(0, length) + '...' : text;
};
</script>

<style scoped>
.events-view {
  padding: 2rem;
  color: var(--text-primary);
  max-width: 1600px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-bottom: 2.5rem;
}

.header h2 { font-size: 2.2rem; font-weight: 900; margin-bottom: 0.5rem; color: #fff; }
.subtitle { color: var(--text-muted); font-size: 1rem; }

.glass-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 28px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.no-padding { padding: 0 !important; overflow: hidden; }

/* Taula Moderna */
.events-table { width: 100%; border-collapse: collapse; }
.events-table th { 
  padding: 1.5rem 2rem; 
  text-align: left; 
  color: var(--text-muted); 
  font-size: 0.8rem; 
  text-transform: uppercase; 
  letter-spacing: 0.1em;
  background: rgba(255,255,255,0.02);
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.event-row { transition: all 0.2s; border-bottom: 1px solid rgba(255,255,255,0.03); }
.event-row:hover { background: rgba(255,255,255,0.02); }

.event-row td { padding: 1.75rem 2rem; }

.event-main-cell { display: flex; align-items: center; gap: 1.25rem; }
.event-avatar { 
  width: 48px; height: 48px; 
  background: linear-gradient(135deg, var(--accent-primary), #4f46e5); 
  border-radius: 14px; 
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
}

.event-info { display: flex; flex-direction: column; }
.event-name { font-weight: 700; color: #fff; font-size: 1.1rem; margin-bottom: 0.2rem; }
.event-type { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }

/* Badges */
.status-badge {
  padding: 0.4rem 1rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 800;
  display: inline-block;
  text-transform: uppercase;
}
.status-badge.confirmed { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3); }
.status-badge.planning { background: rgba(139, 92, 246, 0.15); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3); }
.status-badge.in_progress { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); }
.status-badge.completed { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
.status-badge.cancelled { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }

.event-logistic-cell { display: flex; flex-direction: column; gap: 0.4rem; }
.logistic-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: #fff; }
.logistic-item.muted { color: var(--text-muted); font-size: 0.85rem; }

.profit-cell { display: flex; flex-direction: column; }
.profit-value { font-weight: 800; font-size: 1.1rem; }
.profit-value.success { color: #10b981; }
.profit-label { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; }

.actions-group { display: flex; gap: 0.5rem; justify-content: flex-end; }
.btn-action {
  height: 40px; padding: 0 1rem;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px;
  color: #fff;
  display: flex; align-items: center; gap: 0.5rem;
  cursor: pointer; transition: all 0.2s;
  font-size: 0.85rem; font-weight: 600;
}
.btn-action:hover { background: rgba(255,255,255,0.1); transform: translateY(-2px); }
.btn-action.view { background: var(--accent-primary); border-color: var(--accent-primary); }
.btn-action.delete:hover { background: #ef4444; border-color: #ef4444; }

/* Modals */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.9);
  backdrop-filter: blur(15px);
  z-index: 2000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem;
}

.modal-content-small {
  background: #0b0f1a;
  width: 100%; max-width: 550px;
  border-radius: 32px;
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 40px 80px rgba(0,0,0,0.6);
  display: flex; flex-direction: column; overflow: hidden;
}

.modal-content-large {
  background: #0b0f1a;
  width: 100%; max-width: 1100px;
  height: 90vh;
  border-radius: 36px;
  border: 1px solid rgba(255,255,255,0.1);
  box-shadow: 0 40px 80px rgba(0,0,0,0.6);
  display: flex; flex-direction: column; overflow: hidden;
  animation: modalUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalUp {
  from { opacity: 0; transform: translateY(40px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

.modal-header {
  padding: 2rem 3rem;
  background: rgba(255,255,255,0.02);
  border-bottom: 1px solid rgba(255,255,255,0.05);
  display: flex; justify-content: space-between; align-items: center;
  flex-shrink: 0;
}

.modal-body { 
  padding: 3rem; 
  overflow-y: auto;
  flex: 1;
}

.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; }
.form-group input, .form-group textarea, .form-group select {
  width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px; padding: 0.75rem 1rem; color: #fff; transition: all 0.2s;
}

.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

/* Checklist */
.task-list-wrapper {
  background: rgba(255,255,255,0.02); border-radius: 20px; border: 1px solid rgba(255,255,255,0.05); padding: 1.5rem;
}
.add-task-bar { 
  display: flex; align-items: center; gap: 0.5rem; 
  background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px; padding: 0.25rem 0.25rem 0.25rem 1rem;
  margin-bottom: 1.5rem;
}
.add-task-bar input { 
  flex: 1; background: none; border: none; padding: 0.75rem 0; color: #fff; font-size: 0.9rem;
}
.add-task-bar input:focus { outline: none; }
.btn-add-inline { 
  width: 38px; height: 38px; background: var(--accent-primary); border: none; 
  border-radius: 10px; color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
  transition: all 0.2s;
}

.tasks-scroll { max-height: 350px; overflow-y: auto; display: flex; flex-direction: column; gap: 0.75rem; }
.task-item { 
  display: flex; justify-content: space-between; align-items: center; 
  padding: 1rem; background: rgba(255,255,255,0.03); border-radius: 14px;
}

.task-checkbox {
  display: flex;
  align-items: center;
  gap: 1.25rem;
  cursor: pointer;
  flex: 1;
}

.task-checkbox input[type="checkbox"] {
  width: 18px;
  height: 18px;
  cursor: pointer;
  accent-color: var(--accent-primary);
}

.task-checkbox .completed {
  text-decoration: line-through;
  opacity: 0.5;
}

/* DASHBOARD COLUMNA DRETA (REDISEÑADA) */
.ai-card { 
  background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(99, 102, 241, 0.02));
  border: 1px solid rgba(99, 102, 241, 0.2); 
  border-radius: 28px; 
  padding: 2.5rem; 
}
.ai-title { color: var(--accent-primary); display: flex; align-items: center; gap: 0.75rem; font-weight: 900; margin-bottom: 2rem; text-transform: uppercase; letter-spacing: 0.1em; font-size: 0.9rem; }

.fin-summary { 
  background: rgba(0,0,0,0.3); 
  padding: 2rem; 
  border-radius: 20px; 
  margin-bottom: 2.5rem;
  border: 1px solid rgba(255,255,255,0.05);
}
.fin-main { margin-bottom: 2.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 2.5rem; }
.fin-main .label { font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-bottom: 1.25rem; display: block; }
.fin-main .value { font-size: 3.5rem; font-weight: 900; color: #10b981; line-height: 1.2; }

.fin-list { display: flex; flex-direction: column; gap: 1.5rem; }
.fin-item { display: flex; justify-content: space-between; align-items: center; font-size: 1rem; }
.fin-item .label { color: var(--text-muted); font-weight: 600; }
.fin-item .value { color: #fff; font-weight: 800; font-size: 1.2rem; }

.recs-label { font-size: 0.9rem; font-weight: 800; color: #fff; margin-bottom: 1.25rem; text-transform: uppercase; letter-spacing: 0.05em; }
.recs-list { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 1rem; }
.recs-list li { display: flex; gap: 1rem; line-height: 1.6; color: var(--text-secondary); font-size: 0.95rem; }
.recs-list .bullet { color: var(--accent-primary); font-weight: 900; flex-shrink: 0; }

.info-group-card { 
  background: rgba(255,255,255,0.02); 
  border: 1px solid rgba(255,255,255,0.05); 
  border-radius: 24px; 
  padding: 2rem; 
}
.client-mini { 
  display: flex; 
  align-items: center; 
  gap: 1.25rem; 
  margin-bottom: 2rem; 
  padding-bottom: 1.5rem; 
  border-bottom: 1px solid rgba(255,255,255,0.08); 
}
.client-avatar { 
  width: 52px; height: 52px; 
  background: linear-gradient(135deg, #334155, #1e293b); 
  border-radius: 16px; 
  display: flex; align-items: center; justify-content: center; 
  font-weight: 900; color: #fff; font-size: 1.2rem;
  border: 1px solid rgba(255,255,255,0.1);
}
.client-info strong { display: block; color: #fff; font-size: 1.1rem; }
.email-text { font-size: 0.9rem; color: var(--text-muted); }

.services-mini { display: flex; flex-direction: column; gap: 1.25rem; }
.s-item { display: flex; align-items: flex-start; gap: 1.25rem; }
.s-item .icon-sm { color: var(--accent-primary); margin-top: 0.2rem; }
.s-text { display: flex; flex-direction: column; }
.s-text strong { font-size: 0.9rem; color: #fff; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.2rem; }
.s-text span { font-size: 0.95rem; color: var(--text-muted); }
.s-item.alert .icon-sm { color: #f59e0b; }
.s-item.alert span { color: #f59e0b; font-weight: 700; }

/* Dashboard Generics */
.modal-title { font-size: 2.2rem; font-weight: 900; color: #fff; letter-spacing: -0.04em; }
.modal-subtitle { display: flex; gap: 1.5rem; color: var(--text-muted); font-weight: 600; font-size: 0.95rem; }
.dashboard-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; }
.section-title { 
  font-size: 0.85rem; 
  text-transform: uppercase; 
  letter-spacing: 0.15em; 
  color: var(--text-muted); 
  margin-bottom: 2.5rem; 
  font-weight: 800; 
  display: flex; 
  align-items: center; 
  gap: 1.5rem; 
}
.section-title::after { content: ''; flex: 1; height: 1px; background: rgba(255,255,255,0.08); }

/* Status Selector */
.status-selector { display: flex; flex-wrap: wrap; gap: 1.25rem; margin-bottom: 4.5rem; }
.status-opt {
  padding: 0.8rem 1.5rem; 
  border-radius: 14px; 
  border: 1px solid rgba(255,255,255,0.08);
  background: rgba(255,255,255,0.03); 
  color: var(--text-muted); 
  font-weight: 700; 
  font-size: 0.85rem;
  cursor: pointer; 
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.status-opt:hover {
  background: rgba(255,255,255,0.08);
  color: #fff;
  transform: translateY(-2px);
}

.status-opt.active {
  background: var(--accent-primary);
  color: white;
  border-color: var(--accent-primary);
  box-shadow: 0 8px 20px -5px rgba(99, 102, 241, 0.5);
}

.empty-tasks {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 2rem;
  color: var(--text-muted);
  font-style: italic;
}

.mt-section {
  margin-top: 5rem !important;
}

@media (max-width: 900px) {
  .dashboard-grid { grid-template-columns: 1fr; }
}

.close-btn { 
  background: rgba(255, 255, 255, 0.05); 
  border: 1px solid rgba(255, 255, 255, 0.1); 
  color: rgba(255, 255, 255, 0.8); 
  padding: 0.6rem; 
  border-radius: 12px; 
  cursor: pointer; 
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover { 
  background: rgba(239, 68, 68, 0.2); 
  color: #ff4d4d; 
  border-color: rgba(239, 68, 68, 0.4);
  box-shadow: 0 0 20px rgba(239, 68, 68, 0.4); 
  transform: rotate(90deg) scale(1.1); 
}
</style>
