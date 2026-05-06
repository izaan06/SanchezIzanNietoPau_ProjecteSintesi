<template>
  <div class="calendar-view">
    <div class="header-actions mb-4">
      <div class="legend glass-card">
        <div class="legend-item"><span class="dot confirmed"></span> Confirmat</div>
        <div class="legend-item"><span class="dot planning"></span> Planificant</div>
        <div class="legend-item"><span class="dot in_progress"></span> En Marxa</div>
        <div class="legend-item"><span class="dot completed"></span> Finalitzat</div>
      </div>
    </div>

    <div class="calendar-container glass-card">
      <FullCalendar :options="calendarOptions" />
    </div>

    <!-- Modal Ràpid de Detalls -->
    <div v-if="selectedEvent" class="modal-overlay" @click.self="selectedEvent = null">
      <div class="modal-content-mini glass-card">
        <div class="modal-header">
          <h3>{{ selectedEvent.title }}</h3>
          <button @click="selectedEvent = null" class="close-btn"><X class="icon-sm" /></button>
        </div>
        <div class="modal-body">
          <div class="info-row">
            <Clock class="icon-xs" /> <span>{{ formatTime(selectedEvent.start) }}</span>
          </div>
          <div class="info-row">
            <MapPin class="icon-xs" /> <span>{{ selectedEvent.extendedProps.location || 'Sense ubicació' }}</span>
          </div>
          <div class="info-row">
            <User class="icon-xs" /> <span>{{ selectedEvent.extendedProps.client || 'Sense client' }}</span>
          </div>
          <div class="mt-4">
            <router-link :to="{ name: 'events' }" class="btn-primary btn-block text-center">
              Gestionar Esdeveniment
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import interactionPlugin from '@fullcalendar/interaction'
import { X, Clock, MapPin, User } from 'lucide-vue-next'
import api from '../api/axios'

const events = ref([])
const selectedEvent = ref(null)

const calendarOptions = ref({
  plugins: [dayGridPlugin, interactionPlugin],
  initialView: 'dayGridMonth',
  headerToolbar: {
    left: 'prev,next today',
    center: 'title',
    right: 'dayGridMonth,dayGridWeek'
  },
  locale: 'ca',
  events: [],
  eventClick: (info) => {
    selectedEvent.value = info.event
  },
  height: 'auto',
  eventTimeFormat: {
    hour: '2-digit',
    minute: '2-digit',
    meridiem: false
  }
})

const fetchEvents = async () => {
  try {
    const response = await api.get('/events')
    const mappedEvents = response.data.map(event => ({
      id: event.id,
      title: event.name,
      start: event.date,
      backgroundColor: getStatusColor(event.status),
      borderColor: getStatusColor(event.status),
      extendedProps: {
        location: event.location,
        client: event.client?.name,
        status: event.status
      }
    }))
    calendarOptions.value.events = mappedEvents
  } catch (error) {
    console.error('Error fetching events for calendar:', error)
  }
}

const getStatusColor = (status) => {
  const colors = {
    'confirmed': '#3b82f6',
    'planning': '#8b5cf6',
    'in_progress': '#f59e0b',
    'completed': '#10b981',
    'cancelled': '#ef4444'
  }
  return colors[status] || '#6366f1'
}

const formatTime = (date) => {
  return new Date(date).toLocaleTimeString('ca-ES', { hour: '2-digit', minute: '2-digit' })
}

onMounted(fetchEvents)
</script>

<style>
/* Estils globals per FullCalendar per sobreescriure el seu CSS per defecte */
.fc {
  --fc-border-color: rgba(255, 255, 255, 0.08);
  --fc-daygrid-event-dot-width: 10px;
  --fc-page-bg-color: transparent;
  --fc-neutral-bg-color: rgba(255, 255, 255, 0.02);
  --fc-list-event-hover-bg-color: rgba(255, 255, 255, 0.05);
  color: #fff;
  font-family: inherit;
}

.fc .fc-toolbar-title { font-size: 1.5rem; font-weight: 800; text-transform: capitalize; }
.fc .fc-button-primary { 
  background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); 
  font-weight: 600; text-transform: capitalize; padding: 0.6rem 1.2rem;
}
.fc .fc-button-primary:hover { background: rgba(255,255,255,0.1); border-color: var(--accent-primary); }
.fc .fc-button-active { background: var(--accent-primary) !important; border-color: var(--accent-primary) !important; }

.fc-daygrid-day-number { padding: 8px !important; font-weight: 600; color: var(--text-muted); }
.fc-day-today { background: rgba(99, 102, 241, 0.05) !important; }

.fc-event { 
  cursor: pointer; padding: 4px 8px; border-radius: 6px; font-weight: 600; font-size: 0.85rem; 
  box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s;
}
.fc-event:hover { transform: scale(1.02); z-index: 10; }

.calendar-view { display: flex; flex-direction: column; gap: 1.5rem; }

.legend { display: flex; gap: 2rem; padding: 1rem 2rem; width: fit-content; }
.legend-item { display: flex; align-items: center; gap: 0.75rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }
.dot { width: 12px; height: 12px; border-radius: 50%; }
.dot.confirmed { background: #3b82f6; box-shadow: 0 0 8px #3b82f6; }
.dot.planning { background: #8b5cf6; box-shadow: 0 0 8px #8b5cf6; }
.dot.in_progress { background: #f59e0b; box-shadow: 0 0 8px #f59e0b; }
.dot.completed { background: #10b981; box-shadow: 0 0 8px #10b981; }

.calendar-container { padding: 2rem; min-height: 700px; }

.glass-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
}

/* Modal Detalls */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center; z-index: 1000;
}
.modal-content-mini { width: 100%; max-width: 400px; padding: 2rem; animation: pop 0.3s ease; }
@keyframes pop { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }

.modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.modal-header h3 { font-size: 1.25rem; font-weight: 800; color: #fff; }

.info-row { display: flex; align-items: center; gap: 1rem; margin-bottom: 0.75rem; color: var(--text-secondary); }

.close-btn { 
  background: rgba(255,255,255,0.05); border: none; color: #fff; 
  width: 32px; height: 32px; border-radius: 8px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}
.close-btn:hover { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

.btn-primary.btn-block { display: block; width: 100%; text-decoration: none; padding: 0.8rem; border-radius: 12px; font-weight: 700; }
</style>
