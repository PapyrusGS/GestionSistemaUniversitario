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
  if (!filterRol.value) return users.value
  return users.value.filter(usr => usr.rol === filterRol.value)
})

const form = reactive({
  nombre1: '',
  nombre2: '',
  apellido1: '',
  apellido2: '',
  ci: '',
  correo: '',
  telefono: '',
  password: '',
  idRol: '',
  idCarrera: '',
})

const formErrors = reactive({
  nombre1: '',
  nombre2: '',
  apellido1: '',
  apellido2: '',
  ci: '',
  correo: '',
  telefono: '',
  password: '',
  idRol: '',
  idCarrera: '',
})

const selectedRolObj = computed(() =>
  roles.value.find(r => r.idRol === Number(form.idRol)) || null
)

const roleName = computed(() => selectedRolObj.value?.nombre || '')
const isStudent = computed(() => roleName.value === 'Estudiante')
const isDocente = computed(() => roleName.value === 'Docente')
const isAdmin = computed(() => roleName.value === 'Administrador')

const validators = {
  nombre1: v => !v.trim() && 'El primer nombre es obligatorio.',
  nombre2: v => v && v.length > 255 && 'Máximo 255 caracteres.',
  apellido1: v => !v.trim() && 'El primer apellido es obligatorio.',
  apellido2: v => v && v.length > 255 && 'Máximo 255 caracteres.',
  ci: v => {
    if (!v.trim()) return 'El CI es obligatorio.'
    if (!/^\d+$/.test(v.trim())) return 'Solo se permiten números.'
  },
  correo: v => {
    if (!v.trim()) return 'El correo es obligatorio.'
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim())) return 'Formato de correo inválido.'
  },
  telefono: v => v && !/^\d{7,15}$/.test(v.trim()) && 'Ingrese un teléfono válido (7-15 dígitos).',
  password: v => {
    if (!v) return 'La contraseña es obligatoria.'
    if (v.length < 8) return 'Mínimo 8 caracteres.'
    if (!/[a-zA-Z]/.test(v)) return 'Debe contener al menos una letra.'
    if (!/[0-9]/.test(v)) return 'Debe contener al menos un número.'
  },
  idRol: v => !v && 'Seleccione un rol.',
  idCarrera: v => isStudent.value && !v && 'Seleccione una carrera.',
}

function validateField(field) {
  const fn = validators[field]
  if (!fn) return
  formErrors[field] = fn(form[field]) || ''
}

function validateAll() {
  let valid = true
  Object.keys(validators).forEach(field => {
    const fn = validators[field]
    if (!fn) return
    const msg = fn(form[field])
    formErrors[field] = msg || ''
    if (msg) valid = false
  })
  return valid
}

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
  form.telefono = ''
  form.password = ''
  form.idRol = ''
  form.idCarrera = ''
  errors.value = {}
  Object.keys(formErrors).forEach(k => formErrors[k] = '')
}

function resetMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

function openModal() {
  resetForm()
  resetMessages()
  showCreateModal.value = true
}

async function submitForm() {
  submittings.value = true
  resetMessages()
  errors.value = {}

  if (!validateAll()) {
    submittings.value = false
    errorMessage.value = 'Corrige los errores antes de enviar.'
    return
  }

  try {
    const payload = { ...form }
    if (!isStudent.value) {
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
  <div class="um-root">

    <!-- Encabezado de sección -->
    <div class="um-header">
      <div>
        <h3 class="um-title">Gestión de Usuarios</h3>
        <p class="um-subtitle">Registro de usuarios y asignación de roles</p>
      </div>
      <button class="um-btn-primary" type="button" @click="openModal">
        <i class="ti ti-user-plus"></i> Agregar Usuario
      </button>
    </div>

    <!-- Barra de filtros y listado -->
    <div class="um-list-bar">
      <span class="um-list-count">
        {{ filteredUsers.length }} usuario{{ filteredUsers.length !== 1 ? 's' : '' }}
      </span>
      <div class="um-list-actions">
        <div class="um-filter-group">
          <i class="ti ti-filter"></i>
          <select v-model="filterRol" class="um-filter-select">
            <option value="">Todos los roles</option>
            <option value="Administrador">Administradores</option>
            <option value="Docente">Docentes</option>
            <option value="Estudiante">Estudiantes</option>
          </select>
        </div>
        <button class="um-btn-ghost" type="button" @click="fetchUsers" :disabled="loading">
          <i class="ti" :class="loading ? 'ti-loader-2 um-spin' : 'ti-refresh'"></i>
          {{ loading ? 'Cargando...' : 'Actualizar' }}
        </button>
      </div>
    </div>

    <!-- Tabla -->
    <div class="um-table-wrap">
      <table class="um-table">
        <thead>
          <tr>
            <th>Nombre completo</th>
            <th>CI</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredUsers.length === 0">
            <td colspan="5" class="um-empty">
              <i class="ti ti-users-minus"></i>
              <span>No hay usuarios registrados.</span>
            </td>
          </tr>
          <tr v-for="usr in filteredUsers" :key="usr.idUsuario">
            <td><strong>{{ usr.nombreCompleto }}</strong></td>
            <td><code class="um-code">{{ usr.ci }}</code></td>
            <td class="um-muted">{{ usr.correo }}</td>
            <td>
              <span class="um-badge" :data-role="usr.rol">
                {{ usr.rol || 'Sin Rol' }}
              </span>
            </td>
            <td>
              <span class="um-status" :class="{ 'um-status--active': usr.estado }">
                {{ usr.estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>

  <!-- ── MODAL ───────────────────────────────────────────── -->
  <Teleport to="body">
    <div v-if="showCreateModal" class="um-backdrop" @mousedown.self="showCreateModal = false">
      <div class="um-modal">

        <div class="um-modal-header">
          <h4>Registrar nuevo usuario</h4>
          <button class="um-close" type="button" @click="showCreateModal = false">
            <i class="ti ti-x"></i>
          </button>
        </div>

        <div v-if="successMessage" class="uni-alert uni-alert--success">{{ successMessage }}</div>
        <div v-if="errorMessage"   class="uni-alert uni-alert--error">{{ errorMessage }}</div>

        <form @submit.prevent="submitForm" class="um-form">

          <!-- Selección de rol -->
          <div class="um-step">
            <div class="um-step-label">
              <span class="um-step-num">1</span>
              Seleccionar rol
            </div>
            <div class="um-rol-picker">
              <label
                v-for="rol in roles"
                :key="rol.idRol"
                class="um-rol-card"
                :class="{ 'um-rol-card--selected': Number(form.idRol) === rol.idRol }"
                :data-role="rol.nombre"
              >
                <input
                  type="radio"
                  :value="rol.idRol"
                  v-model.number="form.idRol"
                  :disabled="submittings"
                  hidden
                  @change="validateField('idRol')"
                />
                <i class="ti"
                  :class="{
                    'ti-shield-check': rol.nombre === 'Administrador',
                    'ti-school':       rol.nombre === 'Docente',
                    'ti-book-2':       rol.nombre === 'Estudiante',
                    'ti-user':         !['Administrador','Docente','Estudiante'].includes(rol.nombre),
                  }"
                ></i>
                <span>{{ rol.nombre }}</span>
              </label>
            </div>
            <span v-if="formErrors.idRol" class="um-field-error">{{ formErrors.idRol }}</span>
            <span v-else-if="errors.idRol" class="um-field-error">{{ errors.idRol[0] }}</span>
          </div>

          <!-- Campos comunes (nombre, apellido, CI, correo, password) -->
          <template v-if="form.idRol">
            <div class="um-step">
              <div class="um-step-label">
                <span class="um-step-num">2</span>
                Datos personales
              </div>

              <div class="um-grid-2">
                <label class="um-field">
                  <span>Primer nombre *</span>
                  <input v-model.trim="form.nombre1" type="text" required :disabled="submittings" placeholder="Ej: María" @input="validateField('nombre1')" />
                  <span v-if="formErrors.nombre1" class="um-field-error">{{ formErrors.nombre1 }}</span>
                  <span v-else-if="errors.nombre1" class="um-field-error">{{ errors.nombre1[0] }}</span>
                </label>
                <label class="um-field">
                  <span>Segundo nombre</span>
                  <input v-model.trim="form.nombre2" type="text" :disabled="submittings" placeholder="Opcional" @input="validateField('nombre2')" />
                  <span v-if="formErrors.nombre2" class="um-field-error">{{ formErrors.nombre2 }}</span>
                  <span v-else-if="errors.nombre2" class="um-field-error">{{ errors.nombre2[0] }}</span>
                </label>
                <label class="um-field">
                  <span>Primer apellido *</span>
                  <input v-model.trim="form.apellido1" type="text" required :disabled="submittings" placeholder="Ej: García" @input="validateField('apellido1')" />
                  <span v-if="formErrors.apellido1" class="um-field-error">{{ formErrors.apellido1 }}</span>
                  <span v-else-if="errors.apellido1" class="um-field-error">{{ errors.apellido1[0] }}</span>
                </label>
                <label class="um-field">
                  <span>Segundo apellido</span>
                  <input v-model.trim="form.apellido2" type="text" :disabled="submittings" placeholder="Opcional" @input="validateField('apellido2')" />
                  <span v-if="formErrors.apellido2" class="um-field-error">{{ formErrors.apellido2 }}</span>
                  <span v-else-if="errors.apellido2" class="um-field-error">{{ errors.apellido2[0] }}</span>
                </label>
                <label class="um-field">
                  <span>Carnet (CI) *</span>
                  <input v-model.trim="form.ci" type="text" required :disabled="submittings" placeholder="Ej: 12345678" @input="validateField('ci')" />
                  <span v-if="formErrors.ci" class="um-field-error">{{ formErrors.ci }}</span>
                  <span v-else-if="errors.ci" class="um-field-error">{{ errors.ci[0] }}</span>
                </label>
                <label class="um-field">
                  <span>Teléfono</span>
                  <input v-model.trim="form.telefono" type="text" :disabled="submittings" placeholder="Ej: 76543210" @input="validateField('telefono')" />
                  <span v-if="formErrors.telefono" class="um-field-error">{{ formErrors.telefono }}</span>
                  <span v-else-if="errors.telefono" class="um-field-error">{{ errors.telefono[0] }}</span>
                </label>
              </div>
              <label class="um-field um-field--full">
                <span>Correo institucional *</span>
                <input v-model.trim="form.correo" type="email" required :disabled="submittings" placeholder="usuario@uni.edu.bo" @input="validateField('correo')" />
                <span v-if="formErrors.correo" class="um-field-error">{{ formErrors.correo }}</span>
                <span v-else-if="errors.correo" class="um-field-error">{{ errors.correo[0] }}</span>
              </label>
            </div>

            <!-- Seguridad -->
            <div class="um-step">
              <div class="um-step-label">
                <span class="um-step-num">3</span>
                Seguridad
              </div>
              <div class="um-grid-2">
                <label class="um-field">
                  <span>Contraseña *</span>
                  <input v-model="form.password" type="password" required :disabled="submittings" autocomplete="new-password" placeholder="Mínimo 8 caracteres" @input="validateField('password')" />
                  <span v-if="formErrors.password" class="um-field-error">{{ formErrors.password }}</span>
                  <span v-else-if="errors.password" class="um-field-error">{{ errors.password[0] }}</span>
                </label>
              </div>
            </div>

            <!-- (solo Estudiante): Carrera -->
            <div v-if="isStudent" class="um-step">
              <div class="um-step-label">
                <span class="um-step-num">4</span>
                Carrera académica
              </div>
              <div class="um-grid-2">
                <label class="um-field">
                  <span>Carrera *</span>
                  <select v-model="form.idCarrera" required :disabled="submittings" @change="validateField('idCarrera')">
                    <option value="" disabled>Seleccione una carrera...</option>
                    <option v-for="carrera in careers" :key="carrera.idCarrera" :value="carrera.idCarrera">
                      {{ carrera.nombre }}
                    </option>
                  </select>
                  <span v-if="formErrors.idCarrera" class="um-field-error">{{ formErrors.idCarrera }}</span>
                  <span v-else-if="errors.idCarrera" class="um-field-error">{{ errors.idCarrera[0] }}</span>
                </label>
              </div>
            </div>

            <!-- Nota contextual por rol -->
            <div class="um-rol-note" :data-role="roleName">
              <i class="ti"
                :class="{
                  'ti-info-circle': true,
                }"
              ></i>
              <span v-if="isAdmin">El administrador tendrá acceso completo al sistema: usuarios, carreras, materias y reportes.</span>
              <span v-else-if="isDocente">El docente podrá gestionar cursos, calificaciones y seguimiento de estudiantes asignados.</span>
              <span v-else-if="isStudent">El estudiante podrá inscribirse a materias, consultar notas e historial académico.</span>
              <span v-else>Selecciona un rol para ver los permisos correspondientes.</span>
            </div>

            <!-- Acciones -->
            <div class="um-form-actions">
              <button class="um-btn-ghost" type="button" @click="showCreateModal = false" :disabled="submittings">
                Cancelar
              </button>
              <button class="um-btn-primary" type="submit" :disabled="submittings">
                <i class="ti" :class="submittings ? 'ti-loader-2 um-spin' : 'ti-user-check'"></i>
                {{ submittings ? 'Registrando...' : 'Registrar usuario' }}
              </button>
            </div>
          </template>

          <!-- Estado vacío si no eligió rol -->
          <div v-else class="um-rol-placeholder">
            <i class="ti ti-arrow-up"></i>
            Elige un rol para continuar con el registro
          </div>

        </form>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
/* ── Raíz ── */
.um-root {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  width: 100%;
}

/* ── Encabezado ── */
.um-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--color-linen, #d0cfca);
  flex-wrap: wrap;
}
.um-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a1a1a;
}
.um-subtitle {
  margin: 3px 0 0;
  font-size: 0.8rem;
  color: #5b5c5e;
}

/* ── Botones ── */
.um-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #4e615e;
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 9px 18px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}
.um-btn-primary:hover:not(:disabled) { background: #3b4a48; }
.um-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.um-btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  color: #5b5c5e;
  border: 1.5px solid #d0cfca;
  border-radius: 20px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.um-btn-ghost:hover:not(:disabled) { background: #f4f4f2; color: #1a1a1a; }
.um-btn-ghost:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Barra de lista ── */
.um-list-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  flex-wrap: wrap;
}
.um-list-count {
  font-size: 12px;
  color: #5b5c5e;
  font-weight: 600;
}
.um-list-actions {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}
.um-filter-group {
  display: flex;
  align-items: center;
  gap: 6px;
  border: 1.5px solid #d0cfca;
  border-radius: 20px;
  padding: 0 10px;
  background: #fff;
}
.um-filter-group i { font-size: 14px; color: #5b5c5e; }
.um-filter-select {
  background: transparent;
  border: none;
  outline: none;
  font-family: inherit;
  font-size: 12px;
  font-weight: 600;
  color: #1a1a1a;
  padding: 7px 4px;
  cursor: pointer;
  appearance: none;
}

/* ── Tabla ── */
.um-table-wrap {
  overflow-x: auto;
  border-radius: 10px;
  border: 1px solid #e8e8e5;
}
.um-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
  text-align: left;
}
.um-table th,
.um-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f0f0ee;
}
.um-table th {
  background: #fafaf9;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #5b5c5e;
}
.um-table tbody tr:last-child td { border-bottom: none; }
.um-table tbody tr:hover { background: #fafaf9; }
.um-muted { color: #5b5c5e; font-size: 0.8rem; }
.um-code {
  background: #f0f0ee;
  padding: 2px 6px;
  border-radius: 5px;
  font-family: monospace;
  font-size: 0.8rem;
}
.um-empty {
  text-align: center;
  color: #8c9f96;
  padding: 2.5rem 1rem !important;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.85rem;
}
.um-empty i { font-size: 2rem; opacity: 0.4; }

/* ── Badge de rol ── */
.um-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.um-badge[data-role="Administrador"] { background: #fef3c7; color: #92400e; }
.um-badge[data-role="Docente"]       { background: #dce8f4; color: #1e4a6e; }
.um-badge[data-role="Estudiante"]    { background: #ddf0e6; color: #1a5235; }

/* ── Estado ── */
.um-status {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 11px;
  color: #5b5c5e;
}
.um-status::before {
  content: '';
  width: 6px; height: 6px;
  border-radius: 50%;
  background: #d1a0a0;
}
.um-status--active::before {
  background: #4e9e6b;
  box-shadow: 0 0 6px #4e9e6b55;
}

/* ── Backdrop ── */
.um-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: grid;
  place-items: center;
  padding: 1rem;
  z-index: 50;
}

/* ── Modal ── */
.um-modal {
  width: min(100%, 36rem);
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 24px 48px rgba(0,0,0,0.14);
  overflow-y: auto;
  max-height: 92vh;
}

.um-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.75rem 1rem;
  border-bottom: 1px solid #e8e8e5;
}
.um-modal-header h4 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: #1a1a1a;
}
.um-close {
  background: transparent;
  border: none;
  color: #5b5c5e;
  font-size: 1.2rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px; height: 30px;
  border-radius: 50%;
  transition: background 0.15s;
}
.um-close:hover { background: #f0f0ee; color: #1a1a1a; }

/* ── Formulario ── */
.um-form {
  display: flex;
  flex-direction: column;
  gap: 0;
  padding: 0 0 1.5rem;
}

/* ── Pasos ── */
.um-step {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
  padding: 1.25rem 1.75rem;
  border-bottom: 1px solid #f0f0ee;
}
.um-step-label {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #8c9f96;
}
.um-step-num {
  width: 20px; height: 20px;
  background: #4e615e;
  color: #fff;
  border-radius: 50%;
  font-size: 10px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ── Rol picker ── */
.um-rol-picker {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}
.um-rol-card {
  flex: 1;
  min-width: 100px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  padding: 1rem 0.5rem;
  border: 2px solid #e8e8e5;
  border-radius: 12px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  color: #5b5c5e;
  background: #fafaf9;
  transition: border-color 0.15s, background 0.15s, color 0.15s;
}
.um-rol-card i { font-size: 1.6rem; }
.um-rol-card:hover {
  border-color: #8c9f96;
  background: #f4f4f2;
  color: #1a1a1a;
}
.um-rol-card--selected {
  border-color: #4e615e !important;
  background: #edf4f2 !important;
  color: #1a1a1a !important;
}
.um-rol-card--selected[data-role="Administrador"] {
  border-color: #b07d2e !important;
  background: #fef9ec !important;
}
.um-rol-card--selected[data-role="Docente"] {
  border-color: #2a6090 !important;
  background: #edf3fb !important;
}
.um-rol-card--selected[data-role="Estudiante"] {
  border-color: #2a7a4b !important;
  background: #edf7f1 !important;
}

/* ── Grid de campos ── */
.um-grid-2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.85rem;
}
.um-field {
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.um-field span {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #5b5c5e;
}
.um-field input,
.um-field select {
  width: 100%;
  background: #fafaf9;
  border: 1.5px solid #d0cfca;
  border-radius: 10px;
  color: #1a1a1a;
  padding: 0.7rem 0.9rem;
  font-family: inherit;
  font-size: 13px;
  outline: none;
  transition: border-color 0.15s, box-shadow 0.15s;
  appearance: none;
}
.um-field input:focus,
.um-field select:focus {
  border-color: #4e615e;
  box-shadow: 0 0 0 3px rgba(78,97,94,0.1);
}
.um-field input::placeholder { color: #a0a0a0; }
.um-field select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%235b5c5e'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 1rem;
  padding-right: 2.5rem;
}
.um-field-error {
  font-size: 11px;
  color: #b85c5c;
  margin-top: 2px;
}

/* ── Nota contextual ── */
.um-rol-note {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin: 0 1.75rem;
  padding: 0.75rem 1rem;
  border-radius: 10px;
  font-size: 12px;
  color: #2b3d36;
  background: #edf4f2;
  border: 1px solid #8c9f96;
}
.um-rol-note i { font-size: 15px; flex-shrink: 0; margin-top: 1px; }

/* ── Placeholder sin rol ── */
.um-rol-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 2rem 1.75rem;
  font-size: 12px;
  font-weight: 600;
  color: #8c9f96;
  text-align: center;
}
.um-rol-placeholder i { font-size: 1rem; }

/* ── Acciones del formulario ── */
.um-form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.6rem;
  padding: 1rem 1.75rem 0;
}

/* ── Spin animation ── */
.um-spin { animation: um-rotate 0.8s linear infinite; }
@keyframes um-rotate { to { transform: rotate(360deg); } }

/* ── Alerts heredadas del sistema ── */
.uni-alert {
  margin: 0 1.75rem;
  padding: 0.65rem 1rem;
  border-radius: 10px;
  font-size: 12px;
  border: 1px solid;
}
.uni-alert--success {
  background: #edf4f2;
  border-color: #8c9f96;
  color: #2b3d36;
}
.uni-alert--error {
  background: #faf0f0;
  border-color: #dca6a6;
  color: #7a2424;
}

/* ── Responsive ── */
@media (max-width: 480px) {
  .um-grid-2 { grid-template-columns: 1fr; }
  .um-rol-picker { flex-direction: column; }
  .um-rol-card { flex-direction: row; justify-content: flex-start; padding: 0.75rem 1rem; }
}
</style>