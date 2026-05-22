<template>
  <div class="clients-view">
    <div class="header">
      <div class="header-titles">
        <h2>Cartera de Clients</h2>
        <p class="subtitle">Gestiona les dades de contacte i facturació de les empreses o particulars.</p>
      </div>
      <button @click="openCreateForm" class="btn-primary">
        <Plus class="icon-sm" /> Nou Client
      </button>
    </div>

    <!-- Missatges de Feedback -->
    <div v-if="message" :class="['alert', messageType]">
      {{ message }}
    </div>

    <div class="table-responsive glass-card no-padding">
      <div v-if="loading" class="loading-state">
        Carregant clients...
      </div>
      <div v-else-if="clients.length === 0" class="empty-state">
        Encara no hi ha cap client registrat.
      </div>
      <table v-else class="data-table">
        <thead>
          <tr>
            <th>Client / Empresa</th>
            <th>Contacte Principal</th>
            <th>Ubicació / Direcció</th>
            <th class="text-right">Accions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in clients" :key="client.id" class="client-row">
            <td>
              <div class="client-cell">
                <div class="client-avatar">{{ client.name[0]?.toUpperCase() }}</div>
                <div class="client-info">
                  <span class="client-name">{{ client.name }}</span>
                  <span class="client-id">ID: #{{ client.id }}</span>
                </div>
              </div>
            </td>
            <td>
              <div class="contact-cell">
                <div class="contact-item"><Mail class="icon-xs" /> {{ client.email }}</div>
                <div class="contact-item muted"><Phone class="icon-xs" /> {{ client.phone || 'Sense telèfon' }}</div>
              </div>
            </td>
            <td>
              <div class="contact-item"><MapPin class="icon-xs" /> {{ client.address || 'Sense adreça' }}</div>
            </td>
            <td class="actions-cell text-right">
              <div class="actions-group">
                <button @click="openEditForm(client)" class="btn-action edit" title="Editar">
                  <Edit2 class="icon-xs" />
                </button>
                <button @click="confirmDelete(client.id)" class="btn-action delete" title="Eliminar">
                  <Trash2 class="icon-xs" />
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- MODAL DE CREACIÓ / EDICIÓ -->
    <div v-if="showForm" class="modal-overlay" @click.self="cancelForm">
      <div class="modal-content-small form-modal">
        <div class="modal-header">
          <h3>{{ isEditing ? 'Editar Client' : 'Nou Client' }}</h3>
          <button @click="cancelForm" class="close-btn"><X class="icon-sm" /></button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="submitForm">
            <div class="form-group">
              <label>Nom o Raó Social</label>
              <input v-model="formData.name" type="text" required placeholder="Ex: Eventos Corporativos S.L." />
            </div>
            <div class="form-row">
              <div class="form-group">
                <label>Correu Electrònic</label>
                <input v-model="formData.email" type="email" required placeholder="contacte@empresa.com" />
              </div>
              <div class="form-group">
                <label>Telèfon</label>
                <input v-model="formData.phone" type="tel" maxlength="9" @input="formData.phone = formData.phone.replace(/[^0-9]/g, '')" placeholder="600000000" />
              </div>
            </div>
            <div class="form-group">
              <label>Adreça de Facturació</label>
              <textarea v-model="formData.address" rows="2" placeholder="Carrer, Número, Ciutat, CP..."></textarea>
            </div>
            
            <div class="form-actions-full mt-4">
              <button type="submit" class="btn btn-primary btn-block" :disabled="isSaving">
                {{ isSaving ? 'Processant...' : (isEditing ? 'Actualitzar Client' : 'Crear Client') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Plus, Edit2, Trash2, Mail, Phone, MapPin, X } from 'lucide-vue-next'
import api from '../api/axios'

const clients = ref([])
const loading = ref(true)
const isSaving = ref(false)
const showForm = ref(false)
const isEditing = ref(false)
const message = ref('')
const messageType = ref('')

const formData = ref({
  id: null,
  name: '',
  email: '',
  phone: '',
  address: ''
})

const fetchClients = async () => {
  loading.value = true
  try {
    const res = await api.get('/clients')
    clients.value = res.data
  } catch (error) {
    console.error('Error carregant clients:', error)
    showMessage('Error al carregar les dades.', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(fetchClients)

const openCreateForm = () => {
  isEditing.value = false
  formData.value = { id: null, name: '', email: '', phone: '', address: '' }
  showForm.value = true
}

const openEditForm = (client) => {
  isEditing.value = true
  formData.value = { ...client }
  showForm.value = true
}

const cancelForm = () => {
  showForm.value = false
}

const submitForm = async () => {
  isSaving.value = true
  try {
    if (isEditing.value) {
      await api.put(`/clients/${formData.value.id}`, formData.value)
      showMessage('Client actualitzat correctament', 'success')
    } else {
      await api.post('/clients', formData.value)
      showMessage('Client creat correctament', 'success')
    }
    showForm.value = false
    fetchClients()
  } catch (error) {
    console.error('Error desant client:', error)
    if (error.response?.data?.errors?.email) {
      showMessage('Aquest email ja està registrat en un altre client.', 'error')
    } else {
      showMessage('Error al desar les dades.', 'error')
    }
  } finally {
    isSaving.value = false
  }
}

const confirmDelete = async (id) => {
  if (confirm('Segur que vols eliminar aquest client? Es poden veure afectats els seus esdeveniments.')) {
    try {
      await api.delete(`/clients/${id}`)
      showMessage('Client eliminat.', 'success')
      fetchClients()
    } catch (error) {
      console.error('Error eliminant client:', error)
      showMessage('No es pot eliminar un client que té esdeveniments actius.', 'error')
    }
  }
}

const showMessage = (msg, type) => {
  message.value = msg
  messageType.value = type
  setTimeout(() => { message.value = '' }, 3000)
}
</script>

<style scoped>
.clients-view {
  display: flex;
  flex-direction: column;
  gap: 2.5rem;
  padding: 1.5rem;
  max-width: 1400px;
  margin: 0 auto;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
}

.header h2 { font-size: 2.2rem; font-weight: 900; margin-bottom: 0.5rem; color: #fff; }
.subtitle { color: var(--text-muted); font-size: 1rem; }

.glass-card {
  background: rgba(15, 23, 42, 0.6);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 24px;
  box-shadow: 0 20px 40px rgba(0,0,0,0.3);
}

.no-padding { padding: 0 !important; overflow: hidden; }

/* Taula Moderna */
.data-table { width: 100%; border-collapse: collapse; }
.data-table th { 
  padding: 1.5rem 2rem; 
  text-align: left; 
  color: var(--text-muted); 
  font-size: 0.8rem; 
  text-transform: uppercase; 
  letter-spacing: 0.1em;
  background: rgba(255,255,255,0.02);
  border-bottom: 1px solid rgba(255,255,255,0.05);
}

.client-row { transition: all 0.2s; border-bottom: 1px solid rgba(255,255,255,0.03); }
.client-row:hover { background: rgba(255,255,255,0.02); }
.client-row td { padding: 1.5rem 2rem; }

.client-cell { display: flex; align-items: center; gap: 1.25rem; }
.client-avatar { 
  width: 48px; height: 48px; 
  background: linear-gradient(135deg, #475569, #1e293b); 
  border-radius: 14px; 
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 1.2rem; font-weight: 800;
  border: 1px solid rgba(255,255,255,0.1);
}
.client-info { display: flex; flex-direction: column; }
.client-name { font-weight: 700; color: #fff; font-size: 1.1rem; margin-bottom: 0.2rem; }
.client-id { font-size: 0.8rem; color: var(--text-muted); }

.contact-cell { display: flex; flex-direction: column; gap: 0.4rem; }
.contact-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: #fff; }
.contact-item.muted { color: var(--text-muted); font-size: 0.85rem; }

.actions-group { display: flex; gap: 0.5rem; justify-content: flex-end; }
.btn-action {
  height: 40px; width: 40px; padding: 0;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 12px;
  color: #fff;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.2s;
}
.btn-action:hover { background: rgba(255,255,255,0.1); transform: translateY(-2px); }
.btn-action.edit:hover { color: #3b82f6; border-color: rgba(59, 130, 246, 0.3); }
.btn-action.delete:hover { background: rgba(239, 68, 68, 0.15); color: #ef4444; border-color: rgba(239, 68, 68, 0.3); }

/* Modals */
.modal-overlay {
  position: fixed; inset: 0; background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);
  z-index: 2000; display: flex; align-items: center; justify-content: center; padding: 2rem;
}
.modal-content-small {
  background: #0b0f1a; width: 100%; max-width: 550px; border-radius: 28px;
  border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 40px 80px rgba(0,0,0,0.6);
  animation: modalUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes modalUp { from { opacity: 0; transform: translateY(20px) scale(0.95); } to { opacity: 1; transform: translateY(0) scale(1); } }

.modal-header { padding: 1.5rem 2rem; border-bottom: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-between; align-items: center; }
.modal-body { padding: 2rem; }

.form-group { margin-bottom: 1.5rem; }
.form-group label { display: block; color: var(--text-muted); margin-bottom: 0.5rem; font-size: 0.9rem; font-weight: 600; }
.form-group input, .form-group textarea {
  width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px; padding: 0.75rem 1rem; color: #fff; font-family: inherit;
}
.form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--accent-primary); }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

.close-btn { 
  background: rgba(255,255,255,0.05); border: none; color: #fff; 
  padding: 0.5rem; border-radius: 10px; cursor: pointer; transition: all 0.2s;
}
.close-btn:hover { background: rgba(239, 68, 68, 0.2); color: #ef4444; }

.alert { padding: 1rem; border-radius: 12px; margin-bottom: 1rem; font-weight: 600; text-align: center; }
.alert.success { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
.alert.error { background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); }
.loading-state, .empty-state { padding: 4rem; text-align: center; color: var(--text-muted); font-size: 1.1rem; }
</style>
