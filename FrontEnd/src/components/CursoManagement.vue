<script setup>
import { onMounted, reactive, ref, computed } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const cursos = ref([])
const cursosFisicos = ref([])
const materias = ref([])
const allMaterias = ref([])
const carreras = ref([])
const docentes = ref([])
const horarios = ref([])
const periodos = ref([])
const filterCarrera = ref('')

const materiasFiltradas = computed(() => {
  if (!filterCarrera.value) return materias.value
  return materias.value.filter(m => String(m.idCarrera) === String(filterCarrera.value))
})
const loading = ref(false)
const submitting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const errors = ref({})
const showConfirmModal = ref(false)
const cursoToDisable = ref(null)
const showCreateModal = ref(false)

const form = reactive({
  idCurso: '',
  idMateria: '',
  idDocente: '',
  idHorario1: '',
  idHorario2: '',
  idHorario3: '',
  idPeriodo: '',
})

const isMateriaActive = (idMateria) => {
  const m = allMaterias.value.find(x => x.idMateria === idMateria)
  return m ? m.estado : false
}

function payloadFromResponse(data) {
  return data.data ?? data
}

function formatHorarioLabel(horario) {
  if (!horario) return ''
  const days = { 1: 'Lunes', 2: 'Martes', 3: 'Miercoles', 4: 'Jueves', 5: 'Viernes', 6: 'Sabado', 7: 'Domingo' }
  const dayName = days[horario.diaSemana] || `Día ${horario.diaSemana}`
  const start = horario.horaInicio ? horario.horaInicio.substring(0, 5) : ''
  const end = horario.horaFin ? horario.horaFin.substring(0, 5) : ''
  return `${dayName} — ${start} a ${end}`
}

function resetMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

function resetForm() {
  form.idCurso = ''
  form.idMateria = ''
  form.idDocente = ''
  form.idHorario1 = ''
  form.idHorario2 = ''
  form.idHorario3 = ''
  form.idPeriodo = periodos.value.length > 0 ? String(periodos.value[0].idPeriodo) : ''
  filterCarrera.value = ''
  errors.value = {}
}

async function fetchCursoData() {
  loading.value = true
  resetMessages()
  try {
    const [cursosResponse, formDataResponse, materiasResponse] = await Promise.all([
      props.api.get('/cursos'),
      props.api.get('/cursos/form-data'),
      props.api.get('/materias'),
    ])
    cursos.value = payloadFromResponse(cursosResponse.data).cursos || []
    const formData = payloadFromResponse(formDataResponse.data)
    cursosFisicos.value = formData.cursosFisicos || []
    materias.value = formData.materias || []
    carreras.value = formData.carreras || []
    docentes.value = formData.docentes || []
    horarios.value = formData.horarios || []
    periodos.value = formData.periodos || []
    if (periodos.value.length > 0) {
      form.idPeriodo = String(periodos.value[0].idPeriodo)
    }
    allMaterias.value = payloadFromResponse(materiasResponse.data).materias || []
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudieron cargar los cursos.'
  } finally {
    loading.value = false
  }
}

async function submitForm() {
  submitting.value = true
  resetMessages()
  errors.value = {}
  try {
    const response = await props.api.post('/cursos', {
      idCurso: form.idCurso,
      idMateria: form.idMateria,
      idDocente: Number(form.idDocente),
      idHorario1: Number(form.idHorario1),
      idHorario2: form.idHorario2 ? Number(form.idHorario2) : null,
      idHorario3: form.idHorario3 ? Number(form.idHorario3) : null,
      idPeriodo: Number(form.idPeriodo),
    })
    const payload = payloadFromResponse(response.data)
    cursos.value.unshift(payload.curso)
    successMessage.value = response.data.message || 'Curso registrado correctamente.'
    resetForm()
    showCreateModal.value = false
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

function askDisableCurso(curso) {
  cursoToDisable.value = curso
  showConfirmModal.value = true
}

function cancelDisableCurso() {
  showConfirmModal.value = false
  cursoToDisable.value = null
}

async function confirmDisableCurso() {
  if (!cursoToDisable.value) return
  const curso = cursoToDisable.value
  showConfirmModal.value = false
  submitting.value = true
  resetMessages()
  try {
    const response = await props.api.delete(`/cursos/${curso.idCursoMateria}`)
    const payload = payloadFromResponse(response.data)
    const index = cursos.value.findIndex((item) => item.idCursoMateria === curso.idCursoMateria)
    if (index !== -1) cursos.value[index] = payload.curso
    successMessage.value = response.data.message || 'Curso deshabilitado correctamente.'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo deshabilitar el curso.'
  } finally {
    submitting.value = false
    cursoToDisable.value = null
  }
}

async function enableCurso(curso) {
  submitting.value = true
  resetMessages()
  try {
    const response = await props.api.patch(`/cursos/${curso.idCursoMateria}/enable`)
    const payload = payloadFromResponse(response.data)
    const index = cursos.value.findIndex((item) => item.idCursoMateria === curso.idCursoMateria)
    if (index !== -1) cursos.value[index] = payload.curso
    successMessage.value = response.data.message || 'Curso habilitado correctamente.'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo habilitar el curso.'
  } finally {
    submitting.value = false
  }
}

onMounted(fetchCursoData)
</script>

<template>
  <div class="cm">

    <!-- Cabecera -->
    <div class="cm-header">
      <div>
        <h3 class="cm-title">Creación de Cursos</h3>
        <p class="cm-subtitle">Materia, docente, horario y régimen académico</p>
      </div>
      <div class="cm-header-actions">
        <button class="uni-btn-action-success" type="button" @click="showCreateModal = true">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Agregar Curso
        </button>
        <button class="cm-btn-secondary" type="button" :disabled="loading" @click="fetchCursoData">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
          {{ loading ? 'Cargando...' : 'Actualizar' }}
        </button>
      </div>
    </div>

    <!-- Alertas -->
    <div v-if="successMessage" class="cm-alert cm-alert--success">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="cm-alert cm-alert--error">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ errorMessage }}
    </div>

    <!-- Tabla -->
    <div class="cm-table-wrap">
      <table class="cm-table">
        <thead>
          <tr>
            <th>Código</th>
            <th>Materia</th>
            <th>Docente</th>
            <th>Horario</th>
            <th>Periodo</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="cursos.length === 0">
            <td colspan="7" class="cm-empty">No hay cursos registrados.</td>
          </tr>
          <tr v-for="curso in cursos" :key="curso.idCursoMateria" :class="{ 'cm-row--inactive': !curso.estado }">
            <td><code class="cm-code">{{ curso.idCurso }}</code></td>
            <td>{{ curso.materia || 'Sin materia' }}</td>
            <td>{{ curso.docente || 'Sin docente' }}</td>
            <td>{{ curso.horarioDetalle || 'Sin horario' }}</td>
            <td>{{ curso.periodo || 'Sin periodo' }}</td>
            <td>
              <span class="cm-badge" :class="curso.estado ? 'cm-badge--active' : 'cm-badge--inactive'">
                {{ curso.estado ? 'Activo' : 'Inactivo' }}
              </span>
            </td>
            <td>
              <button
                v-if="curso.estado"
                class="uni-btn-action-danger cm-btn-sm"
                type="button"
                :disabled="submitting"
                @click="askDisableCurso(curso)"
              >
                Deshabilitar
              </button>
              <button
                v-else
                class="cm-btn-sm"
                :class="isMateriaActive(curso.idMateria) ? 'uni-btn-action-success' : 'uni-btn-action-disabled'"
                type="button"
                :disabled="submitting || !isMateriaActive(curso.idMateria)"
                @click="enableCurso(curso)"
                :title="isMateriaActive(curso.idMateria) ? 'Habilitar curso' : 'No se puede habilitar porque la materia está deshabilitada'"
              >
                Habilitar
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ── Modal: Crear curso ── -->
    <Teleport to="body">
      <div v-if="showCreateModal" class="cm-backdrop" @mousedown.self="showCreateModal = false">
        <div class="cm-modal">

          <div class="cm-modal-header">
            <h4 class="cm-modal-title">Registrar nuevo curso</h4>
            <button class="cm-close-btn" type="button" @click="showCreateModal = false">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <form class="cm-form" @submit.prevent="submitForm">

            <div class="cm-field">
              <label class="cm-label">Curso / Aula *</label>
              <select v-model="form.idCurso" class="cm-select" required>
                <option value="" disabled>Seleccione un curso/aula</option>
                <option v-for="cf in cursosFisicos" :key="cf.idCurso" :value="cf.idCurso">
                  {{ cf.idCurso }} (Capacidad: {{ cf.capacidad }})
                </option>
              </select>
              <small v-if="errors.idCurso" class="cm-field-error">{{ errors.idCurso[0] }}</small>
            </div>

            <div class="cm-field cm-field--filter">
              <label class="cm-label cm-label--filter">Filtrar por carrera</label>
              <select v-model="filterCarrera" class="cm-select cm-select--filter" @change="form.idMateria = ''">
                <option value="">Todas las carreras</option>
                <option v-for="carrera in carreras" :key="carrera.idCarrera" :value="carrera.idCarrera">
                  {{ carrera.nombre }}
                </option>
              </select>
            </div>

            <div class="cm-field">
              <label class="cm-label">Materia *</label>
              <select v-model="form.idMateria" class="cm-select" required>
                <option value="" disabled>{{ filterCarrera ? 'Seleccione una materia de esta carrera' : 'Seleccione una materia' }}</option>
                <option v-for="materia in materiasFiltradas" :key="materia.idMateria" :value="materia.idMateria">
                  {{ materia.nombre }} ({{ materia.idMateria }})
                </option>
              </select>
              <small v-if="errors.idMateria" class="cm-field-error">{{ errors.idMateria[0] }}</small>
            </div>

            <div class="cm-field">
              <label class="cm-label">Docente *</label>
              <select v-model="form.idDocente" class="cm-select" required>
                <option value="" disabled>Seleccione un docente</option>
                <option v-for="docente in docentes" :key="docente.idUsuario" :value="String(docente.idUsuario)">
                  {{ docente.nombreCompleto }} — {{ docente.correo }}
                </option>
              </select>
              <small v-if="errors.idDocente" class="cm-field-error">{{ errors.idDocente[0] }}</small>
            </div>

            <div class="cm-field">
              <label class="cm-label">Horario 1 *</label>
              <select v-model="form.idHorario1" class="cm-select" required>
                <option value="" disabled>Seleccione el horario 1</option>
                <option v-for="horario in horarios" :key="horario.idHorario" :value="String(horario.idHorario)">
                  {{ formatHorarioLabel(horario) }}
                </option>
              </select>
              <small v-if="errors.idHorario1" class="cm-field-error">{{ errors.idHorario1[0] }}</small>
            </div>

            <div class="cm-field">
              <label class="cm-label">Horario 2 <span class="cm-optional">(opcional)</span></label>
              <select v-model="form.idHorario2" class="cm-select">
                <option value="">Ninguno</option>
                <option v-for="horario in horarios" :key="horario.idHorario" :value="String(horario.idHorario)">
                  {{ formatHorarioLabel(horario) }}
                </option>
              </select>
              <small v-if="errors.idHorario2" class="cm-field-error">{{ errors.idHorario2[0] }}</small>
            </div>

            <div class="cm-field">
              <label class="cm-label">Horario 3 <span class="cm-optional">(opcional)</span></label>
              <select v-model="form.idHorario3" class="cm-select">
                <option value="">Ninguno</option>
                <option v-for="horario in horarios" :key="horario.idHorario" :value="String(horario.idHorario)">
                  {{ formatHorarioLabel(horario) }}
                </option>
              </select>
              <small v-if="errors.idHorario3" class="cm-field-error">{{ errors.idHorario3[0] }}</small>
            </div>

            <div class="cm-field">
              <label class="cm-label">Régimen académico *</label>
              <select v-model="form.idPeriodo" class="cm-select cm-select--disabled" disabled required>
                <option value="" disabled>Seleccione un periodo</option>
                <option v-for="periodo in periodos" :key="periodo.idPeriodo" :value="String(periodo.idPeriodo)">
                  {{ periodo.nombre }} (Periodo actual)
                </option>
              </select>
              <small v-if="errors.idPeriodo" class="cm-field-error">{{ errors.idPeriodo[0] }}</small>
            </div>

            <div class="cm-form-actions">
              <button class="cm-btn-secondary" type="button" @click="showCreateModal = false">Cancelar</button>
              <button class="uni-btn-action-success" type="submit" :disabled="submitting">
                {{ submitting ? 'Guardando...' : 'Guardar curso' }}
              </button>
            </div>

          </form>
        </div>
      </div>
    </Teleport>

    <!-- ── Modal: Confirmar deshabilitar ── -->
    <Teleport to="body">
      <div v-if="showConfirmModal" class="cm-backdrop" @mousedown.self="cancelDisableCurso">
        <div class="cm-confirm-modal">

          <div class="cm-confirm-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
          </div>

          <h4 class="cm-confirm-title">Deshabilitar curso</h4>

          <p class="cm-confirm-subject">
            <strong>{{ cursoToDisable?.materia }}</strong>
            <span> — Aula: <code class="cm-code">{{ cursoToDisable?.idCurso }}</code></span>
          </p>

          <p class="cm-confirm-warning">
            Esta acción <strong>deshabilitará</strong> este curso del sistema. Los estudiantes ya
            inscritos y sus notas no se eliminarán, pero el curso dejará de estar disponible
            para nuevas inscripciones.
          </p>

          <div class="cm-confirm-actions">
            <button class="cm-btn-secondary" type="button" :disabled="submitting" @click="cancelDisableCurso">
              Cancelar
            </button>
            <button class="uni-btn-action-danger" type="button" :disabled="submitting" @click="confirmDisableCurso">
              {{ submitting ? 'Deshabilitando...' : 'Confirmar deshabilitar' }}
            </button>
          </div>

        </div>
      </div>
    </Teleport>

  </div>
</template>

<style scoped>
/* ── Wrapper ── */
.cm {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  color: var(--uni-text);
}

/* ── Cabecera ── */
.cm-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
  flex-wrap: wrap;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--color-linen);
}

.cm-title {
  margin: 0 0 2px;
  font-size: 1rem;
  font-weight: 700;
  color: var(--uni-text);
}

.cm-subtitle {
  margin: 0;
  font-size: 11px;
  color: var(--uni-muted);
}

.cm-header-actions {
  display: flex;
  gap: 0.6rem;
  flex-wrap: wrap;
}

/* ── Botón secundario local ── */
.cm-btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  border: 1.5px solid var(--color-linen);
  border-radius: 20px;
  color: var(--uni-muted);
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  font-family: inherit;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.cm-btn-secondary:hover:not(:disabled) {
  background: var(--color-linen);
  color: var(--color-black);
}
.cm-btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Alertas ── */
.cm-alert {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 1rem;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid;
}
.cm-alert--success {
  background: var(--uni-success-bg);
  border-color: var(--uni-success-border);
  color: var(--uni-success-text);
}
.cm-alert--error {
  background: var(--uni-error-bg);
  border-color: var(--uni-error-border);
  color: var(--uni-error-text);
}

/* ── Tabla ── */
.cm-table-wrap {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 12px;
  overflow: hidden;
  overflow-x: auto;
}

.cm-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
  white-space: nowrap;
}

.cm-table thead tr {
  background: #fafafa;
  border-bottom: 1px solid var(--color-linen);
}

.cm-table th {
  padding: 0.75rem 1rem;
  text-align: left;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--uni-muted);
}

.cm-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid rgba(0,0,0,.04);
  color: var(--uni-text);
}

.cm-table tbody tr:hover { background: #f7f7f5; }
.cm-table tbody tr:last-child td { border-bottom: none; }
.cm-row--inactive { opacity: 0.5; }

.cm-empty {
  text-align: center;
  color: var(--uni-muted);
  padding: 2rem !important;
  font-size: 12px;
}

/* ── Code chip ── */
.cm-code {
  background: var(--color-linen);
  color: var(--uni-text);
  padding: 2px 8px;
  border-radius: 20px;
  font-size: 11px;
  font-family: ui-monospace, monospace;
}

/* ── Badges estado ── */
.cm-badge {
  display: inline-flex;
  border-radius: 999px;
  padding: 3px 10px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.cm-badge--active {
  background: var(--uni-success-bg);
  color: var(--uni-success-text);
}
.cm-badge--inactive {
  background: var(--uni-error-bg);
  color: var(--uni-error-text);
}

/* ── Botón sm en tabla ── */
.cm-btn-sm {
  padding: 5px 12px;
  font-size: 11px;
}

/* ── Backdrop ── */
.cm-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.35);
  display: grid;
  place-items: center;
  padding: 1rem;
  z-index: 40;
}

/* ── Modal crear curso ── */
.cm-modal {
  width: min(100%, 34rem);
  background: var(--color-white);
  border: 1px solid var(--color-linen);
  border-radius: 16px;
  padding: 1.75rem;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 16px 40px rgba(0,0,0,.12);
}

.cm-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid var(--color-linen);
  padding-bottom: 0.75rem;
  margin-bottom: 1.25rem;
}

.cm-modal-title {
  margin: 0;
  font-size: 1rem;
  font-weight: 700;
  color: var(--uni-text);
  font-family: 'Playfair Display', serif;
}

.cm-close-btn {
  background: transparent;
  border: none;
  color: var(--uni-muted);
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  display: flex;
  transition: color 0.15s;
}
.cm-close-btn:hover { color: var(--uni-text); }

/* ── Formulario ── */
.cm-form {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.cm-field {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.cm-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--uni-muted);
}

.cm-label--filter {
  color: var(--color-mint-dark);
}

.cm-optional {
  font-weight: 400;
  text-transform: none;
  letter-spacing: 0;
  color: var(--uni-muted);
  font-size: 10px;
}

.cm-select {
  width: 100%;
  background: var(--color-white);
  border: 1.5px solid var(--color-linen);
  color: var(--uni-text);
  padding: 0.55rem 0.85rem;
  border-radius: 20px;
  font-size: 12px;
  font-family: inherit;
  outline: none;
  appearance: none;
  cursor: pointer;
  transition: border-color 0.2s;
}
.cm-select:focus { border-color: var(--color-mint-dark); }

.cm-select--filter {
  border-color: var(--color-mint-light);
  background: var(--uni-success-bg);
}
.cm-select--filter:focus { border-color: var(--color-mint-dark); }

.cm-select--disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.cm-field-error {
  font-size: 11px;
  color: var(--uni-error-text);
}

.cm-form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.6rem;
  padding-top: 0.5rem;
  border-top: 1px solid var(--color-linen);
  margin-top: 0.25rem;
}

/* ── Modal confirmar ── */
.cm-confirm-modal {
  width: min(100%, 26rem);
  background: var(--color-white);
  border: 1px solid var(--color-linen);
  border-radius: 16px;
  padding: 2rem 1.75rem;
  text-align: center;
  box-shadow: 0 16px 40px rgba(0,0,0,.12);
}

.cm-confirm-icon {
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

.cm-confirm-title {
  margin: 0 0 0.5rem;
  font-size: 1rem;
  font-weight: 700;
  color: var(--uni-text);
  font-family: 'Playfair Display', serif;
}

.cm-confirm-subject {
  margin: 0 0 1rem;
  font-size: 13px;
  color: var(--uni-text);
}

.cm-confirm-warning {
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

.cm-confirm-actions {
  display: flex;
  justify-content: center;
  gap: 0.6rem;
}
.cm-row--inactive {
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