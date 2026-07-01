<script setup>
import { onBeforeUnmount, onMounted, reactive, ref, watch, computed } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const materias = ref([])
const carreras = ref([])
const allCarreras = ref([])
const materiasActivas = ref([])
const filtros = reactive({
  carrera: '',
  codigo: '',
  semestre: '',
  estado: '',
})
const loading = ref(false)
const submitting = ref(false)
const showModal = ref(false)
const isEditing = ref(false)
const showConfirmModal = ref(false)
const materiaToDisable = ref(null)
const successMessage = ref('')
const errorMessage = ref('')
const errors = ref({})

const filteredMaterias = computed(() => {
  if (filtros.estado === '') {
    return materias.value
  }
  const isTargetActive = filtros.estado === '1'
  return materias.value.filter(m => !!m.estado === isTargetActive)
})

const localErrors = reactive({
  nombre: '',
})

function validateFieldName() {
  if (errors.value.nombre) {
    errors.value.nombre = null
  }
  const val = form.nombre || ''
  if (!val.trim()) {
    localErrors.nombre = 'El nombre de la materia es obligatorio.'
  } else if (val.trim().length < 5) {
    localErrors.nombre = 'El nombre debe tener al menos 5 caracteres.'
  } else if (val.trim().length > 30) {
    localErrors.nombre = 'El nombre no puede exceder los 30 caracteres.'
  } else {
    localErrors.nombre = ''
  }
}

function titleCase(str) {
  if (!str) return ''

  const romanos = /^(i|ii|iii|iv|v|vi|vii|viii|ix|x)$/i

  return str
    .trim()
    .replace(/\s+/g, ' ')
    .split(' ')
    .map(word => {
      if (romanos.test(word)) {
        return word.toUpperCase()
      }

      return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
    })
    .join(' ')
}

function getCareerPrefix(careerName) {
  if (!careerName) return 'MAT'
  const normalized = careerName.normalize("NFD").replace(/[\u0300-\u036f]/g, "")
  const words = normalized.split(/\s+/).map(w => w.toUpperCase())
  const ignoredWords = ["INGENIERIA", "DE", "LA", "EL", "Y", "LOS", "LAS"]
  const filtered = words.filter(w => !ignoredWords.includes(w))
  const targetWord = filtered[0] || words[0] || 'MAT'
  return targetWord.substring(0, 3)
}

function generateNextMateriaId(idCarrera) {
  if (!idCarrera) return ''
  const careerObj = allCarreras.value.find(c => String(c.idCarrera) === String(idCarrera))
  if (!careerObj) return ''
  
  const prefix = getCareerPrefix(careerObj.nombre)
  
  const careerMaterias = [
    ...materias.value,
    ...materiasActivas.value
  ].filter(m => String(m.idCarrera) === String(idCarrera))
  
  let maxNum = 0
  careerMaterias.forEach(m => {
    if (m.idMateria && m.idMateria.startsWith(prefix + '-')) {
      const numPart = m.idMateria.substring(prefix.length + 1)
      const num = parseInt(numPart, 10)
      if (!isNaN(num) && num > maxNum) {
        maxNum = num
      }
    }
  })
  
  const nextNum = maxNum + 1
  const paddedNum = String(nextNum).padStart(3, '0')
  return `${prefix}-${paddedNum}`
}

let searchTimeout = null

const form = reactive({
  idMateria: '',
  idCarrera: '',
  idMateriaPrevia: '',
  nombre: '',
  semestre: '',
})

const draftForm = reactive({
  idMateria: '',
  idCarrera: '',
  idMateriaPrevia: '',
  nombre: '',
  semestre: '',
})

function saveDraft() {
  draftForm.idMateria = form.idMateria
  draftForm.idCarrera = form.idCarrera
  draftForm.idMateriaPrevia = form.idMateriaPrevia
  draftForm.nombre = form.nombre
  draftForm.semestre = form.semestre
}

function restoreDraft() {
  form.idMateria = draftForm.idMateria
  form.idCarrera = draftForm.idCarrera
  form.idMateriaPrevia = draftForm.idMateriaPrevia
  form.nombre = draftForm.nombre
  form.semestre = draftForm.semestre
}

function clearDraft() {
  draftForm.idMateria = ''
  draftForm.idCarrera = ''
  draftForm.idMateriaPrevia = ''
  draftForm.nombre = ''
  draftForm.semestre = ''
}

function hasDraftData() {
  return draftForm.idCarrera || draftForm.nombre || draftForm.semestre
}

watch(
  () => form.idCarrera,
  (newVal) => {
    if (!isEditing.value) {
      form.idMateria = generateNextMateriaId(newVal)
    }
  }
)

const isCareerActive = (idCarrera) => {
  const c = allCarreras.value.find(x => Number(x.idCarrera) === Number(idCarrera))
  return c ? c.estado : false
}

const prerequisitosFiltrados = () => {
  if (!form.idCarrera || !form.semestre) {
    return []
  }
  if (form.semestre === 'Electiva') {
    const carreraId = Number(form.idCarrera)
    return materiasActivas.value.filter((m) => {
      if (Number(m.idCarrera) !== carreraId) return false
      if (m.idMateria === form.idMateria) return false
      return true
    })
  }
  const carreraId = Number(form.idCarrera)
  const currentSemestre = Number(form.semestre)
  return materiasActivas.value.filter((m) => {
    if (Number(m.idCarrera) !== carreraId) return false
    if (m.idMateria === form.idMateria) return false
    const mSemestre = Number(m.semestre)
    if (isNaN(mSemestre)) return false
    return mSemestre < currentSemestre
  })
}

function resetMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

function resetForm() {
  form.idMateria = ''
  form.idCarrera = ''
  form.idMateriaPrevia = ''
  form.nombre = ''
  form.semestre = ''
  errors.value = {}
  localErrors.nombre = ''
}

function openCreate() {
  isEditing.value = false
  resetMessages()
  if (hasDraftData()) {
    restoreDraft()
  } else {
    resetForm()
  }
  showModal.value = true
}

function openEdit(materia) {
  if (!isEditing.value) {
    saveDraft()
  }
  isEditing.value = true
  resetMessages()
  form.idMateria = materia.idMateria
  form.idCarrera = String(materia.idCarrera)
  form.idMateriaPrevia = materia.idMateriaPrevia || ''
  form.nombre = materia.nombre
  form.semestre = String(materia.semestre)
  errors.value = {}
  localErrors.nombre = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  if (isEditing.value) {
    restoreDraft()
    isEditing.value = false
  } else {
    saveDraft()
  }
}

function payloadFromResponse(data) {
  return data.data ?? data
}

async function fetchMateriaData() {
  loading.value = true
  resetMessages()
  try {
    const params = {}
    if (filtros.carrera)  params.carrera  = filtros.carrera
    if (filtros.codigo)   params.q        = filtros.codigo
    if (filtros.semestre) params.semestre = filtros.semestre

    const [materiasResponse, formDataResponse, carrerasResponse] = await Promise.all([
      props.api.get('/materias', { params }),
      props.api.get('/materias/form-data'),
      props.api.get('/carreras'),
    ])
    materias.value = payloadFromResponse(materiasResponse.data).materias || []
    const formData = payloadFromResponse(formDataResponse.data)
    carreras.value = formData.carreras || []
    materiasActivas.value = formData.materias || []
    allCarreras.value = payloadFromResponse(carrerasResponse.data).carreras || []
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudieron cargar las materias.'
  } finally {
    loading.value = false
  }
}

async function submitForm() {
  validateFieldName()
  if (localErrors.nombre) {
    errorMessage.value = 'Por favor, corrige los errores del formulario.'
    return
  }

  form.nombre = titleCase(form.nombre)

  submitting.value = true
  resetMessages()
  errors.value = {}
  try {
    const body = {
      idMateria: form.idMateria,
      idCarrera: Number(form.idCarrera),
      idMateriaPrevia: form.idMateriaPrevia || null,
      nombre: form.nombre,
      semestre: form.semestre,
    }
    const response = isEditing.value
      ? await props.api.put(`/materias/${form.idMateria}`, body)
      : await props.api.post('/materias', body)

    const payload = payloadFromResponse(response.data)
    successMessage.value = response.data.message || 'Materia guardada correctamente.'

    if (isEditing.value) {
      const index = materias.value.findIndex((m) => m.idMateria === form.idMateria)
      if (index !== -1) materias.value[index] = payload.materia
    } else {
      materias.value.unshift(payload.materia)
      materiasActivas.value.unshift(payload.materia)
    }
    clearDraft()
    resetForm()
    showModal.value = false
  } catch (error) {
    const response = error.response?.data
    if (response?.errors) {
      errors.value = response.errors
      errorMessage.value = 'Por favor, corrige los errores del formulario.'
    } else {
      errorMessage.value = response?.message || 'Ocurrió un error inesperado.'
    }
  } finally {
    submitting.value = false
  }
}

function askDisableMateria(materia) {
  materiaToDisable.value = materia
  showConfirmModal.value = true
}

function cancelDisable() {
  showConfirmModal.value = false
  materiaToDisable.value = null
}

async function confirmDisableMateria() {
  if (!materiaToDisable.value) return
  const materia = materiaToDisable.value
  showConfirmModal.value = false
  submitting.value = true
  resetMessages()
  try {
    const response = await props.api.delete(`/materias/${materia.idMateria}`)
    const payload = payloadFromResponse(response.data)
    const index = materias.value.findIndex((item) => item.idMateria === materia.idMateria)
    if (index !== -1) materias.value[index] = payload.materia
    successMessage.value = response.data.message || 'Materia deshabilitada correctamente.'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo deshabilitar la materia.'
  } finally {
    submitting.value = false
    materiaToDisable.value = null
  }
}

async function enableMateria(materia) {
  submitting.value = true
  resetMessages()
  try {
    const response = await props.api.patch(`/materias/${materia.idMateria}/enable`)
    const payload = payloadFromResponse(response.data)
    const index = materias.value.findIndex((item) => item.idMateria === materia.idMateria)
    if (index !== -1) materias.value[index] = payload.materia
    successMessage.value = response.data.message || 'Materia habilitada correctamente.'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo habilitar la materia.'
  } finally {
    submitting.value = false
  }
}

function resetFilters() {
  filtros.carrera = ''
  filtros.codigo = ''
  filtros.semestre = ''
  filtros.estado = ''
}

watch(
  () => [filtros.carrera, filtros.codigo, filtros.semestre],
  () => {
    if (searchTimeout) clearTimeout(searchTimeout)
    searchTimeout = setTimeout(() => fetchMateriaData(), 350)
  }
)

onBeforeUnmount(() => {
  if (searchTimeout) clearTimeout(searchTimeout)
})

onMounted(fetchMateriaData)
</script>

<template>
  <div class="mm">

    <!-- Cabecera -->
    <div class="mm-header">
      <div>
        <h3 class="mm-title">Administración de Materias</h3>
        <p class="mm-subtitle">Registro, edición, deshabilitación y prerrequisitos</p>
      </div>
      <button class="uni-btn-action-success" type="button" @click="openCreate">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Nueva Materia
      </button>
    </div>

    <!-- Alertas -->
    <div v-if="successMessage" class="mm-alert mm-alert--success">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="mm-alert mm-alert--error">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ errorMessage }}
    </div>

    <!-- Tabla card -->
    <div class="mm-card">

      <div class="mm-card-head">
        <span class="mm-card-label">Materias registradas</span>
        <button class="mm-btn-secondary" type="button" @click="resetFilters">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
          Limpiar filtros
        </button>
      </div>

      <!-- Filtros -->
      <div class="mm-filters">
        <div class="mm-filter-group">
          <label class="mm-label">Carrera</label>
          <select v-model="filtros.carrera" class="mm-select">
            <option value="">Todas las carreras</option>
            <option v-for="carrera in carreras" :key="carrera.idCarrera" :value="String(carrera.idCarrera)">
              {{ carrera.nombre }}
            </option>
          </select>
        </div>
        <div class="mm-filter-group">
          <label class="mm-label">Código</label>
          <input v-model.trim="filtros.codigo" type="text" class="mm-input" placeholder="Ej: SIS-100" />
        </div>
        <div class="mm-filter-group">
          <label class="mm-label">Semestre</label>
          <select v-model="filtros.semestre" class="mm-select">
            <option value="">Todos los semestres</option>
            <option v-for="i in 10" :key="i" :value="String(i)">{{ i }}º Semestre</option>
            <option value="Electiva">Electiva</option>
          </select>
        </div>
        <div class="mm-filter-group">
          <label class="mm-label">Estado</label>
          <select v-model="filtros.estado" class="mm-select">
            <option value="">Todos los estados</option>
            <option value="1">Activas</option>
            <option value="0">Inactivas</option>
          </select>
        </div>
      </div>

      <!-- Tabla -->
      <div class="mm-table-wrap">
        <table class="mm-table">
          <thead>
            <tr>
              <th>Código</th>
              <th>Materia</th>
              <th>Carrera</th>
              <th>Semestre</th>
              <th>Prerrequisito</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="filteredMaterias.length === 0">
              <td colspan="7" class="mm-empty">No hay materias registradas.</td>
            </tr>
            <tr v-for="materia in filteredMaterias" :key="materia.idMateria" :class="{ 'mm-row--inactive': !materia.estado }">
              <td><code class="mm-code">{{ materia.idMateria }}</code></td>
              <td><strong>{{ materia.nombre }}</strong></td>
              <td>{{ materia.carrera || 'Sin carrera' }}</td>
              <td>{{ materia.semestre }}</td>
              <td>{{ materia.prerrequisito || '—' }}</td>
              <td>
                <span class="mm-badge" :class="materia.estado ? 'mm-badge--active' : 'mm-badge--inactive'">
                  {{ materia.estado ? 'Activa' : 'Inactiva' }}
                </span>
              </td>
              <td>
                <div class="mm-actions">
                  <button class="mm-btn-action" type="button" @click="openEdit(materia)">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Editar
                  </button>
                  <button
                    v-if="materia.estado"
                    class="uni-btn-action-danger mm-btn-sm"
                    type="button"
                    :disabled="submitting"
                    @click="askDisableMateria(materia)"
                  >
                    Deshabilitar
                  </button>
                  <button
                    v-else
                    class="mm-btn-sm"
                    :class="isCareerActive(materia.idCarrera) ? 'uni-btn-action-success' : 'uni-btn-action-disabled'"
                    type="button"
                    :disabled="submitting || !isCareerActive(materia.idCarrera)"
                    @click="enableMateria(materia)"
                    :title="isCareerActive(materia.idCarrera) ? 'Habilitar materia' : 'No se puede habilitar porque la carrera está deshabilitada'"
                  >
                    Habilitar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal: Crear / Editar -->
    <Teleport to="body">
      <div v-if="showModal" class="mm-backdrop" @mousedown.self="closeModal">
        <div class="mm-modal">

          <div class="mm-modal-header">
            <div>
              <h4 class="mm-modal-title">{{ isEditing ? 'Editar materia' : 'Registrar materia' }}</h4>
              <p class="mm-modal-sub">Configura la carrera, el semestre y el prerrequisito si corresponde.</p>
            </div>
            <button class="mm-close-btn" type="button" @click="closeModal">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <form class="mm-form" @submit.prevent="submitForm">

            <div class="mm-two-cols">
              <div class="mm-field">
                <label class="mm-label">Carrera *</label>
                <select v-model="form.idCarrera" class="mm-select" :disabled="isEditing || submitting" required>
                  <option value="" disabled>Seleccione una carrera</option>
                  <option v-for="carrera in carreras" :key="carrera.idCarrera" :value="String(carrera.idCarrera)">
                    {{ carrera.nombre }}
                  </option>
                </select>
                <small v-if="errors.idCarrera" class="mm-field-error">{{ errors.idCarrera[0] }}</small>
              </div>
              <div class="mm-field">
                <label class="mm-label">Código *</label>
                <input v-model="form.idMateria" type="text" class="mm-input" disabled placeholder="Autogenerado" required />
                <small v-if="errors.idMateria" class="mm-field-error">{{ errors.idMateria[0] }}</small>
              </div>
            </div>

            <div class="mm-two-cols">
              <div class="mm-field">
                <label class="mm-label">Nombre *</label>
                <input v-model="form.nombre" type="text" class="mm-input" :disabled="submitting" maxlength="30" required @input="form.nombre = $event.target.value.replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '')" />
                <small v-if="localErrors.nombre" class="mm-field-error">{{ localErrors.nombre }}</small>
                <small v-else-if="errors.nombre" class="mm-field-error">{{ errors.nombre[0] }}</small>
              </div>
              <div class="mm-field">
                <label class="mm-label">Semestre *</label>
                <select v-model="form.semestre" class="mm-select" :disabled="submitting" required>
                  <option value="" disabled>Seleccione un semestre</option>
                  <option v-for="i in 10" :key="i" :value="String(i)">{{ i }}º Semestre</option>
                  <option value="Electiva">Electiva</option>
                </select>
                <small v-if="errors.semestre" class="mm-field-error">{{ errors.semestre[0] }}</small>
              </div>
            </div>

            <div class="mm-field">
              <label class="mm-label">Prerrequisito <span class="mm-optional">(opcional)</span></label>
              <select v-model="form.idMateriaPrevia" class="mm-select" :disabled="submitting">
                <option value="">Sin prerrequisito</option>
                <option v-for="materia in prerequisitosFiltrados()" :key="materia.idMateria" :value="materia.idMateria">
                  {{ materia.nombre }} ({{ materia.idMateria }})
                </option>
              </select>
              <small v-if="errors.idMateriaPrevia" class="mm-field-error">{{ errors.idMateriaPrevia[0] }}</small>
            </div>

            <div class="mm-form-actions">
              <button class="mm-btn-secondary" type="button" :disabled="submitting" @click="closeModal">Cancelar</button>
              <button class="uni-btn-action-success" type="submit" :disabled="submitting">
                {{ submitting ? 'Guardando...' : 'Guardar materia' }}
              </button>
            </div>

          </form>
        </div>
      </div>
    </Teleport>

    <!-- Modal: Confirmar deshabilitar -->
    <Teleport to="body">
      <div v-if="showConfirmModal" class="mm-backdrop" @mousedown.self="cancelDisable">
        <div class="mm-confirm-modal">

          <div class="mm-confirm-icon">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
          </div>

          <h4 class="mm-confirm-title">Deshabilitar materia</h4>

          <p class="mm-confirm-subject">
            {{ materiaToDisable?.nombre }}
            <code class="mm-code">{{ materiaToDisable?.idMateria }}</code>
          </p>

          <p class="mm-confirm-warning">
            Esta acción <strong>deshabilitará</strong> la materia del sistema. No se eliminará ningún dato
            histórico, pero la materia dejará de estar disponible para nuevas ofertas de cursos.
          </p>

          <div class="mm-confirm-actions">
            <button class="mm-btn-secondary" type="button" :disabled="submitting" @click="cancelDisable">Cancelar</button>
            <button class="uni-btn-action-danger" type="button" :disabled="submitting" @click="confirmDisableMateria">
              {{ submitting ? 'Deshabilitando...' : 'Confirmar deshabilitar' }}
            </button>
          </div>

        </div>
      </div>
    </Teleport>

  </div>
</template>

<style scoped>
.mm {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  color: var(--uni-text);
}

/* Cabecera */
.mm-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
  flex-wrap: wrap;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--color-linen);
}
.mm-title  { margin: 0 0 2px; font-size: 1rem; font-weight: 700; }
.mm-subtitle { margin: 0; font-size: 11px; color: var(--uni-muted); }

/* Alertas */
.mm-alert {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 1rem;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid;
}
.mm-alert--success { background: var(--uni-success-bg); border-color: var(--uni-success-border); color: var(--uni-success-text); }
.mm-alert--error   { background: var(--uni-error-bg);   border-color: var(--uni-error-border);   color: var(--uni-error-text); }

/* Card tabla */
.mm-card {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 12px;
  overflow: hidden;
}
.mm-card-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--color-linen);
}
.mm-card-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--uni-muted);
}

/* Filtros */
.mm-filters {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.75rem;
  padding: 0.85rem 1rem;
  border-bottom: 1px solid var(--color-linen);
  background: #fafafa;
}
@media (max-width: 700px) { .mm-filters { grid-template-columns: 1fr; } }

.mm-filter-group { display: flex; flex-direction: column; gap: 0.3rem; }

/* Inputs y selects */
.mm-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--uni-muted);
}
.mm-input,
.mm-select {
  width: 100%;
  background: var(--color-white);
  border: 1.5px solid var(--color-linen);
  color: var(--uni-text);
  padding: 0.52rem 0.85rem;
  border-radius: 20px;
  font-size: 12px;
  font-family: inherit;
  outline: none;
  appearance: none;
  transition: border-color 0.2s;
}
.mm-input:focus, .mm-select:focus { border-color: var(--color-mint-dark); }
.mm-input:disabled, .mm-select:disabled { opacity: 0.55; cursor: not-allowed; }

.mm-optional {
  font-weight: 400;
  text-transform: none;
  letter-spacing: 0;
  font-size: 10px;
  color: var(--uni-muted);
}

/* Tabla */
/* Tabla */
.mm-table-wrap { overflow-x: auto; }
.mm-table { width: 100%; border-collapse: collapse; font-size: 12px; table-layout: auto; }
.mm-table thead tr { background: #fafafa; border-bottom: 1px solid var(--color-linen); }
.mm-table th {
  padding: 0.6rem 0.75rem;
  text-align: left;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--uni-muted);
  white-space: nowrap;
}
.mm-table td {
  padding: 0.6rem 0.75rem;
  border-bottom: 1px solid rgba(0,0,0,.04);
  color: var(--uni-text);
  white-space: nowrap;
}
.mm-table td:nth-child(2),
.mm-table td:nth-child(3),
.mm-table td:nth-child(5) {
  white-space: normal;
  max-width: 220px;
}
.mm-table tbody tr:hover { background: #f7f7f5; }
.mm-table tbody tr:last-child td { border-bottom: none; }
.mm-row--inactive { opacity: 0.5; }
.mm-empty { text-align: center; color: var(--uni-muted); padding: 2rem !important; font-size: 12px; }

/* Code chip */
.mm-code {
  background: var(--color-linen);
  color: var(--uni-text);
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 11px;
  font-family: ui-monospace, monospace;
}

/* Badges */
.mm-badge { display: inline-flex; border-radius: 999px; padding: 3px 10px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; }
.mm-badge--active   { background: var(--uni-success-bg); color: var(--uni-success-text); }
.mm-badge--inactive { background: var(--uni-error-bg);   color: var(--uni-error-text); }

/* Acciones tabla */
.mm-actions { display: flex; gap: 0.4rem; flex-wrap: wrap; }
.mm-btn-action {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: transparent;
  border: 1.5px solid var(--color-linen);
  border-radius: 20px;
  color: var(--uni-muted);
  padding: 5px 12px;
  font-size: 11px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.mm-btn-action:hover { background: var(--color-linen); color: var(--color-black); }
.mm-btn-sm { padding: 5px 12px; font-size: 11px; }

/* Botón secundario */
.mm-btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  background: transparent;
  border: 1.5px solid var(--color-linen);
  border-radius: 20px;
  color: var(--uni-muted);
  padding: 7px 14px;
  font-size: 11px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.mm-btn-secondary:hover:not(:disabled) { background: var(--color-linen); color: var(--color-black); }
.mm-btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

/* Backdrop */
.mm-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.32);
  display: grid;
  place-items: center;
  padding: 1rem;
  z-index: 40;
}

/* Modal crear/editar */
.mm-modal {
  width: min(100%, 42rem);
  background: var(--color-white);
  border: 1px solid var(--color-linen);
  border-radius: 16px;
  padding: 1.75rem;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 16px 40px rgba(0,0,0,.12);
}
.mm-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 1rem;
  border-bottom: 1px solid var(--color-linen);
  padding-bottom: 0.75rem;
  margin-bottom: 1.25rem;
}
.mm-modal-title { margin: 0 0 2px; font-size: 1rem; font-weight: 700; font-family: 'Playfair Display', serif; color: var(--uni-text); }
.mm-modal-sub   { margin: 0; font-size: 11px; color: var(--uni-muted); }
.mm-close-btn {
  background: transparent;
  border: none;
  color: var(--uni-muted);
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  display: flex;
  flex-shrink: 0;
  transition: color 0.15s;
}
.mm-close-btn:hover { color: var(--uni-text); }

/* Formulario */
.mm-form { display: flex; flex-direction: column; gap: 0.85rem; }
.mm-two-cols { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.85rem; }
@media (max-width: 560px) { .mm-two-cols { grid-template-columns: 1fr; } }
.mm-field { display: flex; flex-direction: column; gap: 0.3rem; }
.mm-field-error { font-size: 11px; color: var(--uni-error-text); }
.mm-form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.6rem;
  padding-top: 0.5rem;
  border-top: 1px solid var(--color-linen);
}

/* Modal confirmar */
.mm-confirm-modal {
  width: min(100%, 26rem);
  background: var(--color-white);
  border: 1px solid var(--color-linen);
  border-radius: 16px;
  padding: 2rem 1.75rem;
  text-align: center;
  box-shadow: 0 16px 40px rgba(0,0,0,.12);
}
.mm-confirm-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 3rem;
  border-radius: 50%;
  background: var(--uni-error-bg);
  color: var(--uni-error-text);
  margin-bottom: 1rem;
}
.mm-confirm-title {
  margin: 0 0 0.5rem;
  font-size: 1rem;
  font-weight: 700;
  font-family: 'Playfair Display', serif;
  color: var(--uni-text);
}
.mm-confirm-subject {
  margin: 0 0 1rem;
  font-size: 13px;
  color: var(--uni-text);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
  flex-wrap: wrap;
}
.mm-confirm-warning {
  color: var(--uni-muted);
  font-size: 12px;
  line-height: 1.6;
  margin: 0 0 1.5rem;
  padding: 0.75rem 1rem;
  background: var(--uni-error-bg);
  border: 1px solid var(--uni-error-border);
  border-radius: 10px;
  text-align: left;
}
.mm-confirm-actions { display: flex; justify-content: center; gap: 0.6rem; }
.mm-row--inactive {
  opacity: 0.5;
}
.uni-btn-action-disabled {
  background: #e8e8e5 !important;
  color: #a0a0a0 !important;
  border: 1px solid #d0cfca !important;
  cursor: not-allowed !important;
  opacity: 0.6;
}
</style>