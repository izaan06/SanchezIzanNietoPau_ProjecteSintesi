<template>
  <div class="workers-view">
    <div class="header">
      <h2>Gestió de Personal</h2>
      <button class="btn-primary">
        <Plus class="icon-sm" /> Nou Treballador
      </button>
    </div>

    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nom Complet</th>
            <th>Rol / Especialitat</th>
            <th>Cost per Hora (€)</th>
            <th>Estat</th>
            <th>Accions</th>
          </tr>
        </thead>
        <tbody>
          <!-- Dades fictícies per a la presentació de disseny (a substituir per v-for) -->
          <tr v-for="i in 6" :key="i">
            <td>#{{ 200 + i }}</td>
            <td class="primary-col">Treballador {{ i }}</td>
            <td>
              <span class="role-badge">{{ i % 2 === 0 ? 'Seguretat' : 'Logística' }}</span>
            </td>
            <td>{{ 15 + i }}.00 €/h</td>
            <td>
              <span class="status-badge" :class="i % 3 === 0 ? 'busy' : 'available'">
                {{ i % 3 === 0 ? 'Assignat' : 'Disponible' }}
              </span>
            </td>
            <td class="actions-cell">
              <button class="btn-icon edit"><Edit2 class="icon-sm" /></button>
              <button class="btn-icon delete"><Trash2 class="icon-sm" /></button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { Plus, Edit2, Trash2 } from 'lucide-vue-next'
// Implementar la connexió amb api.get('/workers') aquí.
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

.header h2 {
  font-size: 1.5rem;
  font-weight: 600;
  color: var(--text-primary);
}

.btn-primary {
  display: flex;
  align-items: center;
  gap: 0.5rem;
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

.data-table th {
  background-color: rgba(15, 23, 42, 0.4);
  font-weight: 600;
  color: var(--text-secondary);
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.05em;
}

.data-table tr:hover td { background-color: rgba(255, 255, 255, 0.02); }
.data-table tr:last-child td { border-bottom: none; }

.primary-col {
  font-weight: 600;
  color: var(--text-primary);
}

.role-badge {
  background-color: rgba(99, 102, 241, 0.15);
  color: var(--accent-primary);
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge {
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.available { background-color: rgba(16, 185, 129, 0.1); color: var(--success); }
.status-badge.busy { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }

.actions-cell {
  display: flex;
  gap: 0.5rem;
}

.btn-icon {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid var(--glass-border);
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 8px;
  transition: all 0.2s ease;
  color: var(--text-primary);
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-icon:hover {
  transform: translateY(-2px);
}

.btn-icon.edit:hover { background: rgba(59, 130, 246, 0.15); color: #3b82f6; border-color: #3b82f6; }
.btn-icon.delete:hover { background: rgba(239, 68, 68, 0.15); color: #ef4444; border-color: #ef4444; }

.icon-sm { width: 18px; height: 18px; }
</style>
