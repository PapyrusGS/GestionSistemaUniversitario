<script setup>
import { onMounted, reactive, ref, computed } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const users = ref([])
const roles = ref([])
const careers = ref([])
const loading = ref(false)
const submittings = ref(false)

const successMessage = ref('')
const errorMessage = ref('')
const errors = ref({})
const filterRol = ref('')
const showCreateModal = ref(false)

const filteredUsers = computed(() => {
  if (!filterRol.value) {
    return users.value
  }
  return users.value.filter(usr => usr.rol === filterRol.value)
})

const form = reactive({
  nombre1: '',
  nombre2: '',
  apellido1: '',
  apellido2: '',
  ci: '',
  correo: '',
  password: '',
  idRol: '',
  idCarrera: '',
})

async function fetchUsers() {
  loading.value = true
  try {
    const { data } = await props.api.get('/users')
    users.value = (data.data ?? data).users
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
    roles.value = (data.data ?? data).roles
  } catch (error) {
    console.error('Error fetching roles:', error)
  }
}

async function fetchCareers() {
  try {
    const { data } = await props.api.get('/carreras')
    careers.value = (data.data ?? data).carreras || []
  } catch (error) {
    console.error('Error fetching careers:', error)
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
  form.idCarrera = ''
  errors.value = {}
}

function resetMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

const isStudent = () => {
  const selectedRol = roles.value.find(r => r.idRol === Number(form.idRol))
  return selectedRol && selectedRol.nombre === 'Estudiante'
}

async function submitForm() {
  submittings.value = true
  resetMessages()
  errors.value = {}

  try {
    const payload = { ...form }
    if (!isStudent()) {
      delete payload.idCarrera
    }
    const { data } = await props.api.post('/users', payload)
    const resPayload = data.data ?? data
    successMessage.value = data.message || 'Usuario registrado correctamente.'
    users.value.push(resPayload.user)
    resetForm()
    showCreateModal.value = false
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
  fetchCareers()
})
</script>

<template>
  <div class="user-management">
    <div class="header-section">
      <div>
        <h3>Gestión de Usuarios y Roles</h3>
        <p class="subtitle">Registro de usuarios y asignación de roles</p>
      </div>
      <button class="primary" type="button" @click="showCreateModal = true">Agregar Usuario</button>
    </div>

    <div class="layout-grid">
      <!-- List Section -->
      <section class="list-section card">
        <div class="list-header">
          <h4>Usuarios Registrados</h4>
          <div class="list-actions">
            <select v-model="filterRol" class="filter-select">
              <option value="">Todos los roles</option>
              <option value="Administrador">Administradores</option>
              <option value="Docente">Docentes</option>
              <option value="Estudiante">Estudiantes</option>
            </select>
            <button class="secondary btn-refresh" type="button" @click="fetchUsers" :disabled="loading">
              <span v-if="loading">Cargando...</span>
              <span v-else>Actualizar</span>
            </button>
          </div>
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
              <tr v-if="filteredUsers.length === 0">
                <td colspan="5" class="empty-state">No hay usuarios registrados.</td>
              </tr>
              <tr v-for="usr in filteredUsers" :key="usr.idUsuario">
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

    <!-- Modal de creación de usuario -->
    <Teleport to="body">
      <div v-if="showCreateModal" class="backdrop" @mousedown.self="showCreateModal = false">
        <div class="create-modal">
          <div class="modal-header">
            <h4>Registrar Nuevo Usuario</h4>
            <button class="close-btn" type="button" @click="showCreateModal = false">&times;</button>
          </div>

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

            <div v-if="isStudent()" class="two-cols">
              <label>
                <span>Carrera *</span>
                <select v-model="form.idCarrera" required :disabled="submittings">
                  <option value="" disabled>Seleccione una carrera...</option>
                  <option v-for="carrera in careers" :key="carrera.idCarrera" :value="carrera.idCarrera">
                    {{ carrera.nombre }}
                  </option>
                </select>
                <span v-if="errors.idCarrera" class="field-error">{{ errors.idCarrera[0] }}</span>
              </label>
              <div></div>
            </div>

            <div class="form-actions">
              <button class="secondary" type="button" @click="showCreateModal = false">Cancelar</button>
              <button class="primary" type="submit" :disabled="submittings">
                {{ submittings ? 'Registrando...' : 'Registrar y Asignar Rol' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.user-management {
  display: grid;
  gap: 1.5rem;
  width: 100%;
}

.header-section {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
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
  grid-template-columns: 1fr;
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

.list-actions {
  display: flex;
  gap: 0.75rem;
  align-items: center;
}

.filter-select {
  padding: 0.45rem 2.2rem 0.45rem 0.8rem;
  font-size: 0.85rem;
  border: 1.5px solid #cbd5e1;
  border-radius: 0.6rem;
  background: #ffffff;
  color: #0f172a;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.6rem center;
  background-size: 1rem;
  outline: none;
  font-family: inherit;
}

.filter-select:focus {
  border-color: #3c4f4d;
  box-shadow: 0 0 0 3px rgba(60, 79, 77, 0.12);
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

.backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: grid;
  place-items: center;
  padding: 1rem;
  z-index: 40;
}

.create-modal {
  width: min(100%, 36rem);
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 1.4rem;
  padding: 2rem 1.75rem;
  max-height: 90vh;
  overflow-y: auto;
  text-align: left;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.create-modal .modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 0.75rem;
  margin-bottom: 1.25rem;
}

.create-modal .modal-header h4 {
  margin: 0;
  font-size: 1.15rem;
  color: #0f172a;
  border-bottom: none;
  padding-bottom: 0;
}

.create-modal .close-btn {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 1.5rem;
  cursor: pointer;
  line-height: 1;
  padding: 0;
}

.create-modal .close-btn:hover {
  color: #0f172a;
}

.create-modal label span {
  color: #475569;
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.create-modal input,
.create-modal select {
  width: 100%;
  border: 1.5px solid #cbd5e1;
  border-radius: 0.9rem;
  background: #ffffff;
  color: #0f172a;
  padding: 0.95rem 1rem;
  outline: none;
  font-family: inherit;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23475569'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-size: 1.25rem;
}

.create-modal input:focus,
.create-modal select:focus {
  border-color: #3c4f4d;
  box-shadow: 0 0 0 3px rgba(60, 79, 77, 0.12);
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 1.5rem;
}
</style>
