<script setup>
import { onMounted, reactive, ref, computed } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const cursos = ref([])
const loading = ref(false)
const submitting = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const confirmTarget = ref(null)
const successMessage = ref('')
const errorMessage = ref('')
const errors = ref({})

const form = reactive({
  idCurso: '',
  capacidad: null,
})

const filterEstado = ref('')
const searchQuery = ref('')

const filteredCursos = computed(() => {
  let list = cursos.value

  // Apply state filter
  if (filterEstado.value !== '') {
    const isTargetActive = filterEstado.value === '1'
    list = list.filter(c => !!c.estado === isTargetActive)
  }

  // Apply search query filter
  if (searchQuery.value.trim() !== '') {
    const q = searchQuery.value.toLowerCase().trim()
    list = list.filter(
      c =>
        c.idCurso.toLowerCase().includes(q) ||
        String(c.capacidad).includes(q)
    )
  }

  return list
})

const localErrors = reactive({
  idCurso: '',
  capacidad: '',
})

function validateFieldIdCurso() {
  if (errors.value.idCurso) {
    errors.value.idCurso = null
  }
  // Convert code to uppercase and remove spaces in real-time
  form.idCurso = (form.idCurso || '').toUpperCase().replace(/\s+/g, '')
  const val = form.idCurso
  if (!val.trim()) {
    localErrors.idCurso = 'El código del curso es obligatorio.'
  } else if (!/^(?:CUR|LAB)-[A-Za-z0-9]+$/.test(val)) {
    localErrors.idCurso = 'El código debe empezar con "CUR-" o "LAB-" seguido de letras o números (ej: CUR-101, LAB-201).'
  } else if (val.length > 20) {
    localErrors.idCurso = 'El código no puede superar los 20 caracteres.'
  } else {
    localErrors.idCurso = ''
  }
}

function validateFieldCapacidad() {
  if (errors.value.capacidad) {
    errors.value.capacidad = null
  }
  const val = form.capacidad
  if (val === null || val === undefined || String(val).trim() === '') {
    localErrors.capacidad = 'La capacidad del curso es obligatoria.'
  } else {
    const num = Number(val)
    if (isNaN(num)) {
      localErrors.capacidad = 'La capacidad debe ser un número entero.'
    } else if (num <= 0) {
      localErrors.capacidad = 'No se aceptan números negativos o cero.'
    } else if (!Number.isInteger(num)) {
      localErrors.capacidad = 'La capacidad debe ser un número entero.'
    } else if (num > 40) {
      localErrors.capacidad = 'La capacidad máxima permitida es de 40 personas.'
    } else {
      localErrors.capacidad = ''
    }
  }
}

async function fetchCursos() {
  loading.value = true
  clearMessages()
  try {
    const { data } = await props.api.get('/cursos-fisicos')
    cursos.value = (data.data ?? data).cursos || []
  } catch (err) {
    errorMessage.value = 'No se pudieron cargar los cursos físicos.'
  } finally {
    loading.value = false
  }
}

async function submitForm() {
  validateFieldIdCurso()
  validateFieldCapacidad()
  if (localErrors.idCurso || localErrors.capacidad) {
    errorMessage.value = 'Por favor, corrige los errores del formulario.'
    return
  }

  submitting.value = true
  clearMessages()
  errors.value = {}

  try {
    let response
    const payload = {
      idCurso: form.idCurso,
      capacidad: Number(form.capacidad),
    }

    if (isEditing.value) {
      response = await props.api.put(`/cursos-fisicos/${form.idCurso}`, payload)
      const data = response.data.data ?? response.data
      const idx = cursos.value.findIndex(c => c.idCurso === form.idCurso)
      if (idx !== -1) {
        cursos.value[idx] = data.curso
      }
      successMessage.value = 'Curso físico actualizado correctamente.'
    } else {
      response = await props.api.post('/cursos-fisicos', payload)
      const data = response.data.data ?? response.data
      cursos.value.unshift(data.curso)
      successMessage.value = 'Curso físico registrado correctamente.'
    }
    closeModal()
  } catch (err) {
    if (err.response && err.response.data && err.response.data.errors) {
      errors.value = err.response.data.errors
    } else {
      errorMessage.value = err.response?.data?.message || 'Ocurrió un error al procesar el formulario.'
    }
  } finally {
    submitting.value = false
  }
}

async function toggleEstado(curso) {
  submitting.value = true
  clearMessages()
  const currentStatus = !!curso.estado
  const endpoint = `/cursos-fisicos/${curso.idCurso}` + (currentStatus ? '' : '/enable')
  const method = currentStatus ? 'delete' : 'patch'

  try {
    const response = await props.api[method](endpoint)
    const data = response.data.data ?? response.data
    const idx = cursos.value.findIndex(c => c.idCurso === curso.idCurso)
    if (idx !== -1) {
      cursos.value[idx] = data.curso
    }
    successMessage.value = currentStatus
      ? 'Curso físico deshabilitado correctamente.'
      : 'Curso físico habilitado correctamente.'
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Error al cambiar el estado del curso físico.'
  } finally {
    submitting.value = false
    confirmTarget.value = null
  }
}

function openCreate() {
  isEditing.value = false
  clearMessages()
  resetForm()
  showModal.value = true
}

function openEdit(curso) {
  isEditing.value = true
  clearMessages()
  form.idCurso = curso.idCurso
  form.capacidad = curso.capacidad
  localErrors.idCurso = ''
  localErrors.capacidad = ''
  errors.value = {}
  showModal.value = true
}

function resetForm() {
  form.idCurso = ''
  form.capacidad = null
  localErrors.idCurso = ''
  localErrors.capacidad = ''
  errors.value = {}
}

function closeModal() {
  showModal.value = false
  resetForm()
}

function clearMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

function confirmAction(curso) {
  confirmTarget.value = curso
}

onMounted(() => {
  fetchCursos()
})
</script>

<template>
  <div class="cfm-root">
    <!-- Header -->
    <div class="cfm-header">
      <div>
        <h3 class="cfm-title">Administración de Aulas Físicas</h3>
        <p class="cfm-subtitle">Registra y administra el espacio físico y las aulas de clases de la universidad.</p>
      </div>
      <button class="cfm-btn-create" type="button" @click="openCreate">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nueva Aula
      </button>
    </div>

    <!-- Alert Panel -->
    <div v-if="successMessage" class="uni-alert uni-alert--success cfm-alert-sm">{{ successMessage }}</div>
    <div v-if="errorMessage" class="uni-alert uni-alert--error cfm-alert-sm">{{ errorMessage }}</div>

    <!-- Search / Filter Bar -->
    <div class="cfm-search-bar">
      <div class="cfm-search-input-wrap">
        <i class="ti ti-search cfm-search-icon"></i>
        <input
          v-model="searchQuery"
          type="text"
          class="cfm-search-input"
          placeholder="Buscar por código de aula o capacidad..."
        />
      </div>

      <div class="cfm-filter-wrap">
        <label class="cfm-filter-label">Estado:</label>
        <select v-model="filterEstado" class="cfm-filter-select">
          <option value="">Todos los estados</option>
          <option value="1">Activas</option>
          <option value="0">Deshabilitadas</option>
        </select>
      </div>
    </div>

    <!-- Table Container -->
    <div class="cfm-table-wrap">
      <div v-if="loading" class="cfm-loading">
        <i class="ti ti-loader-quarter ti-spin"></i> Cargando aulas...
      </div>

      <table class="cfm-table" :class="{ 'cfm-table--dim': loading }">
        <thead>
          <tr>
            <th>Código de Aula</th>
            <th>Capacidad (Personas)</th>
            <th>Estado</th>
            <th>Fecha Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="filteredCursos.length === 0">
            <td colspan="5" class="cfm-empty">
              <i class="ti ti-building-off"></i>
              No se encontraron aulas que coincidan con la búsqueda.
            </td>
          </tr>
          <tr v-for="curso in filteredCursos" :key="curso.idCurso" :class="{ 'cfm-row--inactive': !curso.estado }">
            <td><code class="cfm-code">{{ curso.idCurso }}</code></td>
            <td><strong>{{ curso.capacidad }}</strong></td>
            <td>
              <span class="cfm-badge" :class="curso.estado ? 'cfm-badge--active' : 'cfm-badge--inactive'">
                {{ curso.estado ? 'Activa' : 'Inactiva' }}
              </span>
            </td>
            <td><span class="cfm-muted">{{ curso.fechaRegistro || '—' }}</span></td>
            <td>
              <div class="cfm-actions">
                <button class="cfm-btn-action" type="button" @click="openEdit(curso)">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                  Editar
                </button>
                <button
                  v-if="curso.estado"
                  class="uni-btn-action-danger cfm-btn-sm"
                  type="button"
                  :disabled="submitting"
                  @click="confirmAction(curso)"
                >
                  Deshabilitar
                </button>
                <button
                  v-else
                  class="uni-btn-action-success cfm-btn-sm"
                  type="button"
                  :disabled="submitting"
                  @click="toggleEstado(curso)"
                >
                  Habilitar
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal Form: Create / Edit -->
    <Teleport to="body">
      <div v-if="showModal" class="cfm-backdrop" @mousedown.self="closeModal">
        <div class="cfm-modal">
          <div class="cfm-modal-header">
            <div>
              <h4 class="cfm-modal-title">{{ isEditing ? 'Editar Aula Física' : 'Registrar Nueva Aula' }}</h4>
              <p class="cfm-modal-sub">Especifica el código y la capacidad del espacio físico.</p>
            </div>
            <button class="cfm-close-btn" type="button" @click="closeModal">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <form class="cfm-form" @submit.prevent="submitForm">
            <div class="cfm-field">
              <label class="cfm-label">Código de Aula *</label>
              <input
                v-model="form.idCurso"
                type="text"
                class="cfm-input"
                :disabled="isEditing || submitting"
                maxlength="20"
                placeholder="CUR-101 o LAB-201"
                required
                @input="validateFieldIdCurso"
              />
              <span class="cfm-char-counter">{{ (form.idCurso || '').length }}/20</span>
              <small v-if="localErrors.idCurso" class="cfm-field-error">{{ localErrors.idCurso }}</small>
              <small v-else-if="errors.idCurso" class="cfm-field-error">{{ errors.idCurso[0] }}</small>
            </div>

            <div class="cfm-field">
              <label class="cfm-label">Capacidad de Aforo (Personas) *</label>
              <input
                v-model.number="form.capacidad"
                type="number"
                class="cfm-input"
                :disabled="submitting"
                min="1"
                max="40"
                placeholder="Ej. 40"
                required
                @input="validateFieldCapacidad"
              />
              <small v-if="localErrors.capacidad" class="cfm-field-error">{{ localErrors.capacidad }}</small>
              <small v-else-if="errors.capacidad" class="cfm-field-error">{{ errors.capacidad[0] }}</small>
            </div>

            <div class="cfm-modal-actions">
              <button class="cfm-btn-secondary" type="button" :disabled="submitting" @click="closeModal">
                Cancelar
              </button>
              <button class="cfm-btn-primary" type="submit" :disabled="submitting">
                <i v-if="submitting" class="ti ti-loader-quarter ti-spin"></i>
                {{ isEditing ? 'Guardar Cambios' : 'Registrar Aula' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Modal Confirm: Disable -->
    <Teleport to="body">
      <div v-if="confirmTarget" class="cfm-backdrop" @mousedown.self="confirmTarget = null">
        <div class="cfm-modal cfm-modal--sm">
          <div class="cfm-modal-header cfm-modal-header--danger">
            <div>
              <h4 class="cfm-modal-title">¿Deshabilitar Aula?</h4>
              <p class="cfm-modal-sub">El aula física dejará de estar disponible para asignar nuevos cursos.</p>
            </div>
            <button class="cfm-close-btn" type="button" @click="confirmTarget = null">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <div class="cfm-confirm-body">
            <p>¿Estás seguro de que deseas deshabilitar el aula <strong>{{ confirmTarget.idCurso }}</strong>?</p>
          </div>

          <div class="cfm-modal-actions">
            <button class="cfm-btn-secondary" type="button" :disabled="submitting" @click="confirmTarget = null">
              Cancelar
            </button>
            <button class="uni-btn-action-danger" type="button" :disabled="submitting" @click="toggleEstado(confirmTarget)">
              <i v-if="submitting" class="ti ti-loader-quarter ti-spin"></i>
              Sí, Deshabilitar
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.cfm-root {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  width: 100%;
}

/* ── Header ── */
.cfm-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--color-linen, #d0cfca);
  flex-wrap: wrap;
}
.cfm-title {
  margin: 0 0 3px;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a1a1a;
}
.cfm-subtitle {
  margin: 0;
  font-size: 0.8rem;
  color: #5b5c5e;
}
.cfm-btn-create {
  background: var(--color-primary, #624b3c);
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 10px 20px;
  font-size: 12px;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: opacity 0.15s;
}
.cfm-btn-create:hover {
  opacity: 0.9;
}

/* ── Search / Filter Bar ── */
.cfm-search-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
  width: 100%;
}
.cfm-search-input-wrap {
  position: relative;
  flex: 1;
  min-width: 250px;
}
.cfm-search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #8e8d89;
  font-size: 14px;
}
.cfm-search-input {
  width: 100%;
  padding: 8px 12px 8px 34px;
  border: 1px solid #e8e8e5;
  border-radius: 20px;
  font-size: 13px;
  color: #1a1a1a;
  outline: none;
  background: #fdfdfd;
}
.cfm-search-input:focus {
  border-color: var(--color-primary, #624b3c);
  background: #fff;
}
.cfm-filter-wrap {
  display: flex;
  align-items: center;
  gap: 8px;
}
.cfm-filter-label {
  font-size: 12px;
  font-weight: 600;
  color: #5b5c5e;
}
.cfm-filter-select {
  padding: 8px 24px 8px 12px;
  border: 1px solid #e8e8e5;
  border-radius: 20px;
  font-size: 12px;
  background: #fdfdfd;
  color: #1a1a1a;
  outline: none;
}

/* ── Table ── */
.cfm-table-wrap {
  position: relative;
  border: 1px solid #e8e8e5;
  border-radius: 12px;
  overflow: auto;
}
.cfm-loading {
  position: absolute;
  inset: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  background: rgba(255,255,255,0.8);
  font-size: 12px;
  font-weight: 600;
  color: #5b5c5e;
  border-radius: 12px;
}
.cfm-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
  text-align: left;
  transition: opacity 0.2s;
}
.cfm-table--dim {
  opacity: 0.35;
  pointer-events: none;
}
.cfm-table th,
.cfm-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f0f0ee;
  vertical-align: middle;
}
.cfm-table th {
  background: #fafaf9;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #5b5c5e;
}
.cfm-table tbody tr:hover {
  background: #fafaf9;
}
.cfm-table tbody tr:last-child td {
  border-bottom: none;
}
.cfm-row--inactive {
  opacity: 0.5;
}
.cfm-muted {
  color: #5b5c5e;
  font-size: 0.82rem;
}
.cfm-code {
  background: #f0f0ee;
  padding: 2px 6px;
  border-radius: 5px;
  font-family: monospace;
  font-size: 0.8rem;
}
.cfm-empty {
  text-align: center;
  padding: 2.5rem 1rem !important;
  color: #8c9f96;
  font-size: 0.85rem;
}
.cfm-empty i {
  font-size: 2rem;
  opacity: 0.5;
  display: block;
  margin-bottom: 0.4rem;
}

/* ── Badges ── */
.cfm-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.cfm-badge--active {
  background: #ddf0e6;
  color: #1a5235;
}
.cfm-badge--inactive {
  background: #f7e6e6;
  color: #7a2424;
}

/* ── Actions ── */
.cfm-actions {
  display: flex;
  align-items: center;
  gap: 8px;
}
.cfm-btn-action {
  background: none;
  border: 1px solid #c8c8c5;
  border-radius: 15px;
  padding: 5px 12px;
  font-size: 11px;
  font-weight: 600;
  color: #5b5c5e;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
  transition: all 0.15s;
}
.cfm-btn-action:hover {
  background: #f0f0ee;
  color: #1a1a1a;
  border-color: #a8a8a5;
}
.cfm-btn-sm {
  border-radius: 15px;
  padding: 5px 12px;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
}

/* ── Modals & Backdrop ── */
.cfm-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.45);
  backdrop-filter: blur(4px);
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
}
.cfm-modal {
  background: #fff;
  width: 100%;
  max-width: 480px;
  border-radius: 16px;
  box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
  overflow: hidden;
  animation: modalEnter 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.cfm-modal--sm {
  max-width: 400px;
}
@keyframes modalEnter {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.cfm-modal-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid #f0f0ee;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
}
.cfm-modal-header--danger {
  background: #fffdfd;
}
.cfm-modal-title {
  margin: 0 0 3px;
  font-size: 1.05rem;
  font-weight: 700;
  color: #1a1a1a;
}
.cfm-modal-sub {
  margin: 0;
  font-size: 0.75rem;
  color: #5b5c5e;
}
.cfm-close-btn {
  background: none;
  border: none;
  color: #8c8c88;
  cursor: pointer;
  padding: 4px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.15s;
}
.cfm-close-btn:hover {
  background: #f0f0ee;
  color: #1a1a1a;
}

/* ── Forms ── */
.cfm-form {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}
.cfm-field {
  display: flex;
  flex-direction: column;
  gap: 5px;
  position: relative;
}
.cfm-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #5b5c5e;
}
.cfm-input {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid #d0cfca;
  border-radius: 8px;
  font-size: 13px;
  outline: none;
  background: #fafaf9;
}
.cfm-input:focus {
  border-color: var(--color-primary, #624b3c);
  background: #fff;
}
.cfm-input:disabled {
  background: #f0f0ee;
  color: #8e8d89;
  cursor: not-allowed;
  border-color: #e2e2df;
}
.cfm-field-error {
  color: #b22b2b;
  font-size: 11px;
  font-weight: 500;
  margin-top: 2px;
}
.cfm-char-counter {
  position: absolute;
  right: 4px;
  top: 0;
  font-size: 10px;
  color: #8e8d89;
}
.cfm-confirm-body {
  padding: 1.5rem;
  font-size: 13px;
  color: #1a1a1a;
  line-height: 1.5;
}

/* ── Modal Actions ── */
.cfm-modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  padding: 1rem 1.5rem 1.5rem;
  background: #fafaf9;
  border-top: 1px solid #f0f0ee;
}
.cfm-btn-secondary {
  background: #fff;
  border: 1px solid #d0cfca;
  border-radius: 20px;
  padding: 10px 20px;
  font-size: 12px;
  font-weight: 600;
  color: #5b5c5e;
  cursor: pointer;
  transition: background 0.15s;
}
.cfm-btn-secondary:hover {
  background: #f0f0ee;
}
.cfm-btn-primary {
  background: var(--color-primary, #624b3c);
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 10px 20px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: opacity 0.15s;
}
.cfm-btn-primary:hover {
  opacity: 0.9;
}
.cfm-btn-primary:disabled,
.cfm-btn-secondary:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
