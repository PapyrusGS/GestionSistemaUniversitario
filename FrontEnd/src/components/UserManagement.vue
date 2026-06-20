<script setup>
import { onMounted, reactive, ref } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const users = ref([])
const roles = ref([])
const loading = ref(false)
const submittings = ref(false)

const successMessage = ref('')
const errorMessage = ref('')
const errors = ref({})

const form = reactive({
  nombre1: '',
  nombre2: '',
  apellido1: '',
  apellido2: '',
  ci: '',
  correo: '',
  password: '',
  idRol: '',
})

async function fetchUsers() {
  loading.value = true
  try {
    const { data } = await props.api.get('/users')
    users.value = data.users
  } catch (error) {
    console.error('Error fetching users:', error)
    errorMessage.value = 'No se pudieron cargar los usuarios.'
  } finally {
    loading.value = false
  }
}

async function fetchRoles() {
  try {
    const { data } = await props.api.get('/roles')
    roles.value = data.roles
  } catch (error) {
    console.error('Error fetching roles:', error)
  }
}

function resetForm() {
  form.nombre1 = ''
  form.nombre2 = ''
  form.apellido1 = ''
  form.apellido2 = ''
  form.ci = ''
  form.correo = ''
  form.password = ''
  form.idRol = ''
  errors.value = {}
}

function resetMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

async function submitForm() {
  submittings.value = true
  resetMessages()
  errors.value = {}

  try {
    const { data } = await props.api.post('/users', form)
    successMessage.value = data.message || 'Usuario registrado correctamente.'
    users.value.push(data.user)
    resetForm()
  } catch (error) {
    const response = error.response?.data
    if (response?.errors) {
      errors.value = response.errors
      errorMessage.value = 'Por favor, corrige los errores de validación.'
    } else {
      errorMessage.value = response?.message || 'Ocurrió un error al registrar el usuario.'
    }
  } finally {
    submittings.value = false
  }
}

onMounted(() => {
  fetchUsers()
  fetchRoles()
})
</script>

<template>
  <div class="user-management">
    <div class="header-section">
      <h3>Gestión de Usuarios y Roles</h3>
      <p class="subtitle">HU-ADM-02 (Registro de usuarios) y HU-ADM-03 (Asignación de roles)</p>
    </div>

    <div class="layout-grid">
      <!-- Form Section -->
      <section class="form-section card">
        <h4>Registrar Nuevo Usuario</h4>
        <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
        <div v-if="errorMessage" class="alert error">{{ errorMessage }}</div>

        <form class="form-grid" @submit.prevent="submitForm">
          <div class="two-cols">
            <label>
              <span>Primer nombre *</span>
              <input v-model.trim="form.nombre1" type="text" required :disabled="submittings" />
              <span v-if="errors.nombre1" class="field-error">{{ errors.nombre1[0] }}</span>
            </label>
            <label>
              <span>Segundo nombre</span>
              <input v-model.trim="form.nombre2" type="text" :disabled="submittings" />
              <span v-if="errors.nombre2" class="field-error">{{ errors.nombre2[0] }}</span>
            </label>
          </div>

          <div class="two-cols">
            <label>
              <span>Primer apellido *</span>
              <input v-model.trim="form.apellido1" type="text" required :disabled="submittings" />
              <span v-if="errors.apellido1" class="field-error">{{ errors.apellido1[0] }}</span>
            </label>
            <label>
              <span>Segundo apellido</span>
              <input v-model.trim="form.apellido2" type="text" :disabled="submittings" />
              <span v-if="errors.apellido2" class="field-error">{{ errors.apellido2[0] }}</span>
            </label>
          </div>

          <div class="two-cols">
            <label>
              <span>Carnet (CI) *</span>
              <input v-model.trim="form.ci" type="text" required :disabled="submittings" />
              <span v-if="errors.ci" class="field-error">{{ errors.ci[0] }}</span>
            </label>
            <label>
              <span>Correo Institucional *</span>
              <input v-model.trim="form.correo" type="email" required :disabled="submittings" />
              <span v-if="errors.correo" class="field-error">{{ errors.correo[0] }}</span>
            </label>
          </div>

          <div class="two-cols">
            <label>
              <span>Contraseña *</span>
              <input v-model="form.password" type="password" required :disabled="submittings" autocomplete="new-password" />
              <span v-if="errors.password" class="field-error">{{ errors.password[0] }}</span>
            </label>
            <label>
              <span>Asignar Rol *</span>
              <select v-model="form.idRol" required :disabled="submittings">
                <option value="" disabled>Seleccione un rol...</option>
                <option v-for="rol in roles" :key="rol.idRol" :value="rol.idRol">
                  {{ rol.nombre }}
                </option>
              </select>
              <span v-if="errors.idRol" class="field-error">{{ errors.idRol[0] }}</span>
            </label>
          </div>

          <button class="primary" type="submit" :disabled="submittings">
            {{ submittings ? 'Registrando...' : 'Registrar y Asignar Rol' }}
          </button>
        </form>
      </section>

      <!-- List Section -->
      <section class="list-section card">
        <div class="list-header">
          <h4>Usuarios Registrados</h4>
          <button class="secondary btn-refresh" type="button" @click="fetchUsers" :disabled="loading">
            <span v-if="loading">Cargando...</span>
            <span v-else>Actualizar</span>
          </button>
        </div>

        <div class="table-container">
          <table class="user-table">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>CI</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="users.length === 0">
                <td colspan="5" class="empty-state">No hay usuarios registrados.</td>
              </tr>
              <tr v-for="usr in users" :key="usr.idUsuario">
                <td>
                  <div class="user-name">
                    <strong>{{ usr.nombreCompleto }}</strong>
                  </div>
                </td>
                <td><code>{{ usr.ci }}</code></td>
                <td>{{ usr.correo }}</td>
                <td>
                  <span class="role-badge-small" :data-role="usr.rol">
                    {{ usr.rol || 'Sin Rol' }}
                  </span>
                </td>
                <td>
                  <span class="status-indicator" :class="{ active: usr.estado }">
                    {{ usr.estado ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
</template>

<style scoped>
.user-management {
  display: grid;
  gap: 1.5rem;
  width: 100%;
}

.header-section {
  border-bottom: 1px solid var(--panel-border);
  padding-bottom: 0.75rem;
}

.header-section h3 {
  margin: 0;
  font-size: 1.6rem;
  color: var(--text);
}

.subtitle {
  margin: 0.25rem 0 0;
  font-size: 0.9rem;
  color: var(--muted);
}

.layout-grid {
  display: grid;
  grid-template-columns: 1.1fr 1.9fr;
  gap: 1.5rem;
  align-items: start;
}

h4 {
  margin: 0 0 1rem;
  font-size: 1.2rem;
  color: var(--primary);
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  padding-bottom: 0.5rem;
}

.field-error {
  display: block;
  font-size: 0.75rem;
  color: var(--danger);
  margin-top: 0.25rem;
}

select {
  width: 100%;
  border: 1px solid rgba(180, 204, 255, 0.14);
  border-radius: 0.9rem;
  background: rgba(6, 10, 23, 0.72);
  color: var(--text);
  padding: 0.95rem 1rem;
  outline: none;
  font-family: inherit;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23aab5d4'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-size: 1.25rem;
}

select:focus {
  border-color: rgba(125, 211, 252, 0.8);
  box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.14);
}

select option {
  background: var(--bg);
  color: var(--text);
}

.list-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.list-header h4 {
  margin: 0;
  border-bottom: none;
  padding-bottom: 0;
}

.btn-refresh {
  padding: 0.5rem 1rem;
  font-size: 0.85rem;
}

.table-container {
  overflow-x: auto;
  border-radius: 0.8rem;
  border: 1px solid rgba(180, 204, 255, 0.08);
}

.user-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.9rem;
}

.user-table th,
.user-table td {
  padding: 0.85rem 1rem;
  border-bottom: 1px solid rgba(180, 204, 255, 0.05);
}

.user-table th {
  background: rgba(255, 255, 255, 0.02);
  color: var(--muted);
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
}

.user-table tbody tr:hover {
  background: rgba(255, 255, 255, 0.02);
}

.empty-state {
  text-align: center;
  color: var(--muted);
  padding: 2rem !important;
}

code {
  background: rgba(255, 255, 255, 0.05);
  padding: 0.2rem 0.4rem;
  border-radius: 0.35rem;
  font-family: monospace;
}

.role-badge-small {
  display: inline-block;
  padding: 0.25rem 0.6rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.role-badge-small[data-role='Administrador'] {
  color: #fef3c7;
  background: rgba(245, 158, 11, 0.12);
  border: 1px solid rgba(245, 158, 11, 0.24);
}

.role-badge-small[data-role='Docente'] {
  color: #bfdbfe;
  background: rgba(59, 130, 246, 0.12);
  border: 1px solid rgba(59, 130, 246, 0.24);
}

.role-badge-small[data-role='Estudiante'] {
  color: #bbf7d0;
  background: rgba(34, 197, 94, 0.12);
  border: 1px solid rgba(34, 197, 94, 0.24);
}

.status-indicator {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.8rem;
  color: var(--muted);
}

.status-indicator::before {
  content: '';
  width: 0.45rem;
  height: 0.45rem;
  border-radius: 999px;
  background: #f43f5e;
}

.status-indicator.active::before {
  background: var(--success);
  box-shadow: 0 0 8px var(--success);
}

@media (max-width: 1200px) {
  .layout-grid {
    grid-template-columns: 1fr;
  }
}
</style>
