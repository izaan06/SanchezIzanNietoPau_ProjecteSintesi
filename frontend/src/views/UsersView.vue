<template>
  <div class="users-view">
    <div class="header">
      <h2>Gestió d'Usuaris</h2>
      <p class="subtitle">Només accessible per Administradors</p>
    </div>

    <div v-if="loading" class="loading">Carregant la llista d'usuaris...</div>
    
    <div v-else class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Rol Actual</th>
            <th>Canviar Rol</th>
            <th>Accions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id">
            <td>#{{ user.id }}</td>
            <td class="primary-col">{{ user.name }}</td>
            <td>{{ user.email }}</td>
            <td>
              <span :class="['role-pill', user.role]">{{ user.role.toUpperCase() }}</span>
            </td>

            <td>
              <select 
                :value="user.role" 
                @change="updateUserRole(user, $event.target.value)"
                class="role-select"
              >
                <option value="client">Client</option>
                <option value="worker">Treballador</option>
                <option value="admin">Administrador</option>
              </select>
            </td>
            <td>
              <button 
                @click="deleteUser(user.id)" 
                class="btn-icon delete"
                :disabled="user.id === currentUserId"
              >
                <Trash2 class="icon-sm" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Trash2 } from 'lucide-vue-next'
import api from '../api/axios'

const users = ref([])
const loading = ref(true)
const currentUserId = ref(null) // Per no auto-eliminar-se

const fetchUsers = async () => {
  loading.value = true
  try {
    const res = await api.get('/users')
    users.value = res.data
    
    // Obtenir ID de l'usuari actual (opcionalment des d'un endpoint /me)
    const meRes = await api.get('/me')
    currentUserId.value = meRes.data.id
  } catch (err) {
    console.error(err)
  } finally {
    loading.value = false
  }
}

const updateUserRole = async (user, newRole) => {
  if (!confirm(`Segur que vols canviar el rol de ${user.name} a ${newRole}?`)) return
  
  try {
    await api.patch(`/users/${user.id}/role`, { role: newRole })
    await fetchUsers() // Refrescar per veure canvis (com la creació de Worker)
  } catch (err) {
    alert('Error al canviar el rol')
    console.error(err)
  }
}

const deleteUser = async (id) => {
  if (!confirm('Segur que vols eliminar aquest usuari? Aquesta acció no es pot desfer.')) return
  try {
    await api.delete(`/users/${id}`)
    fetchUsers()
  } catch (err) {
    console.error(err)
  }
}

onMounted(fetchUsers)
</script>

<style scoped>
.users-view {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.header h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.subtitle {
  color: var(--text-muted);
  font-size: 0.9rem;
}

.table-responsive {
  background: var(--glass-bg);
  backdrop-filter: blur(12px);
  border-radius: 16px;
  border: 1px solid var(--glass-border);
  overflow-x: auto;
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

.primary-col {
  font-weight: 600;
  color: var(--text-primary);
}

.role-pill {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.75rem;
  font-weight: 700;
}

.role-pill.admin { background: rgba(139, 92, 246, 0.2); color: #a78bfa; }
.role-pill.worker { background: rgba(16, 185, 129, 0.2); color: #34d399; }
.role-pill.client { background: rgba(148, 163, 184, 0.2); color: #cbd5e1; }



.role-select {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid var(--border-color);
  color: white;
  padding: 0.5rem;
  border-radius: 8px;
  font-family: inherit;
  cursor: pointer;
}

.btn-icon.delete:hover { background: rgba(239, 68, 68, 0.15); color: #ef4444; border-color: #ef4444; }

.btn-icon:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.icon-sm { width: 18px; height: 18px; }
</style>
