<script setup>
import { onBeforeUnmount, onMounted, reactive, ref, watch } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const materias = ref([])
const carreras = ref([])
const materiasActivas = ref([])
const filtros = reactive({
  carrera: '',
  codigo: '',
  semestre: '',
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
let searchTimeout = null

const form = reactive({
  idMateria: '',
  idCarrera: '',
  idMateriaPrevia: '',
  nombre: '',
  semestre: '',
})

const prerequisitosFiltrados = () => {
  if (!form.idCarrera) {
    return materiasActivas.value.filter((materia) => materia.idMateria !== form.idMateria)
  }

  const carreraId = Number(form.idCarrera)

  return materiasActivas.value.filter((materia) => {
    return Number(materia.idCarrera) === carreraId && materia.idMateria !== form.idMateria
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
}

function openCreate() {
  isEditing.value = false
  resetMessages()
  resetForm()
  showModal.value = true
}

function openEdit(materia) {
  isEditing.value = true
  resetMessages()
  form.idMateria = materia.idMateria
  form.idCarrera = String(materia.idCarrera)
  form.idMateriaPrevia = materia.idMateriaPrevia || ''
  form.nombre = materia.nombre
  form.semestre = String(materia.semestre)
  errors.value = {}
  showModal.value = true
}

function closeModal() {
  showModal.value = false
}

function payloadFromResponse(data) {
  return data.data ?? data
}

async function fetchMateriaData() {
  loading.value = true
  resetMessages()

  try {
    const params = {}

    if (filtros.carrera) {
      params.carrera = filtros.carrera
    }

    if (filtros.codigo) {
      params.q = filtros.codigo
    }

    if (filtros.semestre) {
      params.semestre = filtros.semestre
    }

    const [materiasResponse, formDataResponse] = await Promise.all([
      props.api.get('/materias', { params }),
      props.api.get('/materias/form-data'),
    ])

    materias.value = payloadFromResponse(materiasResponse.data).materias || []
    const formData = payloadFromResponse(formDataResponse.data)
    carreras.value = formData.carreras || []
    materiasActivas.value = formData.materias || []
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudieron cargar las materias.'
  } finally {
    loading.value = false
  }
}

async function submitForm() {
  submitting.value = true
  resetMessages()
  errors.value = {}

  try {
    const body = {
      idMateria: form.idMateria,
      idCarrera: Number(form.idCarrera),
      idMateriaPrevia: form.idMateriaPrevia || null,
      nombre: form.nombre,
      semestre: Number(form.semestre),
    }

    const response = isEditing.value
      ? await props.api.put(`/materias/${form.idMateria}`, body)
      : await props.api.post('/materias', body)

    const payload = payloadFromResponse(response.data)
    successMessage.value = response.data.message || 'Materia guardada correctamente.'

    if (isEditing.value) {
      const index = materias.value.findIndex((materia) => materia.idMateria === form.idMateria)
      if (index !== -1) {
        materias.value[index] = payload.materia
      }
    } else {
      materias.value.unshift(payload.materia)
      materiasActivas.value.unshift(payload.materia)
    }

    closeModal()
    resetForm()
  } catch (error) {
    const response = error.response?.data
    if (response?.errors) {
      errors.value = response.errors
      errorMessage.value = 'Por favor, corrige los errores del formulario.'
    } else {
      errorMessage.value = response?.message || 'Ocurrio un error inesperado.'
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

    if (index !== -1) {
      materias.value[index] = payload.materia
    }

    successMessage.value = response.data.message || 'Materia deshabilitada correctamente.'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo deshabilitar la materia.'
  } finally {
    submitting.value = false
    materiaToDisable.value = null
  }
}

function resetFilters() {
  filtros.carrera = ''
  filtros.codigo = ''
  filtros.semestre = ''
}

watch(
  () => [filtros.carrera, filtros.codigo, filtros.semestre],
  () => {
    if (searchTimeout) {
      clearTimeout(searchTimeout)
    }

    searchTimeout = setTimeout(() => {
      fetchMateriaData()
    }, 350)
  }
)

onBeforeUnmount(() => {
  if (searchTimeout) {
    clearTimeout(searchTimeout)
  }
})

onMounted(fetchMateriaData)
</script>

<template>
  <div class="materia-management">
    <div class="header-section">
      <div>
        <h3>Administracion de Materias</h3>
        <p class="subtitle">HU-ADM-05 - Registro, edicion, deshabilitacion y prerrequisitos</p>
      </div>
      <button class="primary" type="button" @click="openCreate">Nueva Materia</button>
    </div>

    <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
    <div v-if="errorMessage" class="alert error">{{ errorMessage }}</div>

    <div class="table-card">
      <div class="table-head">
        <h4>Materias registradas</h4>
        <div class="head-actions">
          <button class="secondary" type="button" @click="resetFilters">Limpiar filtros</button>
        </div>
      </div>

      <div class="filters-grid">
        <label>
          <span>Buscar por carrera</span>
          <select v-model="filtros.carrera">
            <option value="">Todas las carreras</option>
            <option v-for="carrera in carreras" :key="carrera.idCarrera" :value="String(carrera.idCarrera)">
              {{ carrera.nombre }}
            </option>
          </select>
        </label>

        <label>
          <span>Buscar por codigo</span>
          <input
            v-model.trim="filtros.codigo"
            type="text"
            placeholder="Ej: SIS-100"
          />
        </label>

        <label>
          <span>Buscar por semestre</span>
          <input
            v-model.trim="filtros.semestre"
            type="number"
            min="1"
            max="20"
            placeholder="Ej: 3"
          />
        </label>
      </div>

      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Codigo</th>
              <th>Materia</th>
              <th>Carrera</th>
              <th>Semestre</th>
              <th>Prerequisito</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="materias.length === 0">
              <td colspan="7" class="empty-state">No hay materias registradas.</td>
            </tr>
            <tr v-for="materia in materias" :key="materia.idMateria" :class="{ inactive: !materia.estado }">
              <td><code>{{ materia.idMateria }}</code></td>
              <td>
                <strong>{{ materia.nombre }}</strong>
              </td>
              <td>{{ materia.carrera || 'Sin carrera' }}</td>
              <td>{{ materia.semestre }}</td>
              <td>{{ materia.prerrequisito || 'Sin prerrequisito' }}</td>
              <td>
                <span class="status-badge" :class="materia.estado ? 'active' : 'inactive'">
                  {{ materia.estado ? 'Activa' : 'Inactiva' }}
                </span>
              </td>
              <td>
                <div class="actions">
                  <button class="icon-btn" type="button" @click="openEdit(materia)">Editar</button>
                  <button
                    v-if="materia.estado"
                    class="icon-btn danger"
                    type="button"
                    :disabled="submitting"
                    @click="askDisableMateria(materia)"
                  >
                    Deshabilitar
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Teleport to="body">
      <div v-if="showModal" class="backdrop" @mousedown.self="closeModal">
        <div class="modal-card">
          <div class="modal-header">
            <div>
              <h4>{{ isEditing ? 'Editar materia' : 'Registrar materia' }}</h4>
              <p>Configura la carrera, el semestre y el prerrequisito si corresponde.</p>
            </div>
            <button class="close-btn" type="button" @click="closeModal">×</button>
          </div>

          <form class="modal-form" @submit.prevent="submitForm">
            <div class="two-cols">
              <label>
                <span>Codigo *</span>
                <input
                  v-model.trim="form.idMateria"
                  type="text"
                  :disabled="isEditing || submitting"
                  placeholder="Ej: SIS-100"
                  required
                />
                <small v-if="errors.idMateria" class="field-error">{{ errors.idMateria[0] }}</small>
              </label>

              <label>
                <span>Carrera *</span>
                <select v-model="form.idCarrera" :disabled="submitting" required>
                  <option value="" disabled>Seleccione una carrera</option>
                  <option v-for="carrera in carreras" :key="carrera.idCarrera" :value="String(carrera.idCarrera)">
                    {{ carrera.nombre }}
                  </option>
                </select>
                <small v-if="errors.idCarrera" class="field-error">{{ errors.idCarrera[0] }}</small>
              </label>
            </div>

            <div class="two-cols">
              <label>
                <span>Nombre *</span>
                <input v-model.trim="form.nombre" type="text" :disabled="submitting" required />
                <small v-if="errors.nombre" class="field-error">{{ errors.nombre[0] }}</small>
              </label>

              <label>
                <span>Semestre *</span>
                <input
                  v-model="form.semestre"
                  type="number"
                  min="1"
                  max="20"
                  :disabled="submitting"
                  required
                />
                <small v-if="errors.semestre" class="field-error">{{ errors.semestre[0] }}</small>
              </label>
            </div>

            <label>
              <span>Prerrequisito</span>
              <select v-model="form.idMateriaPrevia" :disabled="submitting">
                <option value="">Sin prerrequisito</option>
                <option
                  v-for="materia in prerequisitosFiltrados()"
                  :key="materia.idMateria"
                  :value="materia.idMateria"
                >
                  {{ materia.nombre }} ({{ materia.idMateria }})
                </option>
              </select>
              <small v-if="errors.idMateriaPrevia" class="field-error">
                {{ errors.idMateriaPrevia[0] }}
              </small>
            </label>

            <div class="modal-actions">
              <button class="secondary" type="button" :disabled="submitting" @click="closeModal">
                Cancelar
              </button>
              <button class="primary" type="submit" :disabled="submitting">
                {{ submitting ? 'Guardando...' : 'Guardar materia' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Modal confirmación deshabilitar -->
    <Teleport to="body">
      <div v-if="showConfirmModal" class="backdrop" @mousedown.self="cancelDisable">
        <div class="modal-card confirm-modal">
          <div class="confirm-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
          </div>
          <h4>Deshabilitar materia</h4>
          <p class="confirm-name">{{ materiaToDisable?.nombre }} <code>{{ materiaToDisable?.idMateria }}</code></p>
          <p class="confirm-warning">
            Esta acción <strong>deshabilitará</strong> la materia del sistema. No se eliminará ningún dato
            histórico, pero la materia dejará de estar disponible para nuevas ofertas de cursos.
          </p>
          <div class="confirm-actions">
            <button class="secondary" type="button" :disabled="submitting" @click="cancelDisable">Cancelar</button>
            <button class="danger-btn" type="button" :disabled="submitting" @click="confirmDisableMateria">
              {{ submitting ? 'Deshabilitando...' : 'Confirmar deshabilitar' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.materia-management {
  display: grid;
  gap: 1.25rem;
}

.header-section {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-end;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--panel-border);
}

.header-section h3 {
  margin: 0;
  font-size: 1.5rem;
}

.subtitle {
  margin: 0.25rem 0 0;
  color: var(--muted);
  font-size: 0.9rem;
}

.table-card {
  border: 1px solid rgba(180, 204, 255, 0.08);
  border-radius: 1rem;
  overflow: auto;
  background: rgba(255, 255, 255, 0.02);
}

.table-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.1rem;
  border-bottom: 1px solid rgba(180, 204, 255, 0.08);
}

.table-head h4 {
  margin: 0;
  color: var(--primary);
}

.head-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.filters-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
  padding: 1rem 1.1rem;
  border-bottom: 1px solid rgba(180, 204, 255, 0.08);
}

.table-wrap {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th,
.data-table td {
  padding: 0.9rem 1rem;
  border-bottom: 1px solid rgba(180, 204, 255, 0.05);
  text-align: left;
}

.data-table th {
  color: var(--muted);
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.08em;
}

.data-table tbody tr:hover {
  background: rgba(255, 255, 255, 0.02);
}

.inactive {
  opacity: 0.55;
}

.empty-state {
  text-align: center;
  color: var(--muted);
  padding: 2rem !important;
}

code {
  background: rgba(255, 255, 255, 0.05);
  padding: 0.2rem 0.45rem;
  border-radius: 0.35rem;
}

.status-badge {
  display: inline-flex;
  border-radius: 999px;
  padding: 0.25rem 0.65rem;
  font-size: 0.75rem;
  text-transform: uppercase;
  font-weight: 700;
}

.status-badge.active {
  background: rgba(34, 197, 94, 0.12);
  color: #bbf7d0;
}

.status-badge.inactive {
  background: rgba(239, 68, 68, 0.12);
  color: #fca5a5;
}

.actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.icon-btn {
  border: 1px solid rgba(180, 204, 255, 0.14);
  background: rgba(255, 255, 255, 0.05);
  color: var(--text);
  border-radius: 0.7rem;
  padding: 0.45rem 0.8rem;
  cursor: pointer;
}

.icon-btn.danger {
  border-color: rgba(239, 68, 68, 0.2);
  color: #fca5a5;
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

.modal-card {
  width: min(100%, 44rem);
  background: linear-gradient(180deg, rgba(14, 20, 41, 0.98), rgba(11, 16, 32, 0.96));
  border: 1px solid var(--panel-border);
  border-radius: 1.4rem;
  padding: 1.25rem;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
  margin-bottom: 1rem;
}

.modal-header h4 {
  margin: 0;
  font-size: 1.15rem;
}

.modal-header p {
  margin: 0.25rem 0 0;
  color: var(--muted);
  font-size: 0.9rem;
}

.close-btn {
  border: 0;
  background: rgba(255, 255, 255, 0.06);
  color: var(--text);
  border-radius: 0.7rem;
  padding: 0.35rem 0.65rem;
  cursor: pointer;
}

.modal-form {
  display: grid;
  gap: 1rem;
}

.two-cols {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1rem;
}

label {
  display: grid;
  gap: 0.45rem;
}

label span {
  color: var(--muted);
  font-size: 0.82rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

input,
select {
  width: 100%;
  border: 1px solid rgba(180, 204, 255, 0.14);
  border-radius: 0.85rem;
  background: rgba(6, 10, 23, 0.72);
  color: var(--text);
  padding: 0.9rem 1rem;
  outline: none;
}

.field-error {
  color: #fca5a5;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

@media (max-width: 900px) {
  .header-section,
  .table-head,
  .modal-header {
    flex-direction: column;
    align-items: flex-start;
  }

  .filters-grid {
    grid-template-columns: 1fr;
  }

  .two-cols {
    grid-template-columns: 1fr;
  }
}
.confirm-modal {
  max-width: 28rem;
  text-align: center;
  padding: 2rem 1.75rem;
}

.confirm-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3.5rem;
  height: 3.5rem;
  border-radius: 50%;
  background: rgba(239, 68, 68, 0.12);
  color: #fca5a5;
  margin-bottom: 1rem;
}

.confirm-modal h4 {
  margin: 0 0 0.5rem;
  font-size: 1.15rem;
}

.confirm-name {
  margin: 0 0 1rem;
  font-size: 1rem;
  color: var(--text);
}

.confirm-name code {
  font-size: 0.85rem;
  margin-left: 0.25rem;
}

.confirm-warning {
  color: var(--muted);
  font-size: 0.875rem;
  line-height: 1.55;
  margin: 0 0 1.5rem;
  padding: 0.75rem 1rem;
  background: rgba(239, 68, 68, 0.06);
  border: 1px solid rgba(239, 68, 68, 0.15);
  border-radius: 0.75rem;
  text-align: left;
}

.confirm-actions {
  display: flex;
  justify-content: center;
  gap: 0.75rem;
}

.danger-btn {
  background: rgba(239, 68, 68, 0.15);
  border: 1px solid rgba(239, 68, 68, 0.35);
  color: #fca5a5;
  border-radius: 0.7rem;
  padding: 0.55rem 1.25rem;
  cursor: pointer;
  font-weight: 600;
  transition: background 0.2s;
}

.danger-btn:hover:not(:disabled) {
  background: rgba(239, 68, 68, 0.28);
}

.danger-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>
