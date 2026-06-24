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
const carreras = ref([])
const docentes = ref([])
const horarios = ref([])
const periodos = ref([])
const filterCarrera = ref('')

// Materias filtradas por carrera seleccionada (solo filtro visual, no se envía al backend)
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

const form = reactive({
  idCurso: '',
  idMateria: '',
  idDocente: '',
  idHorario1: '',
  idHorario2: '',
  idPeriodo: '',
})

function payloadFromResponse(data) {
  return data.data ?? data
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
  form.idPeriodo = periodos.value.length > 0 ? String(periodos.value[0].idPeriodo) : ''
  filterCarrera.value = ''
  errors.value = {}
}

async function fetchCursoData() {
  loading.value = true
  resetMessages()

  try {
    const [cursosResponse, formDataResponse] = await Promise.all([
      props.api.get('/cursos'),
      props.api.get('/cursos/form-data'),
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

  // Frontend Validation for Schedules
  const h1 = horarios.value.find(h => String(h.idHorario) === String(form.idHorario1))
  const h2 = horarios.value.find(h => String(h.idHorario) === String(form.idHorario2))

  if (h1 && h2) {
    if (String(h1.idHorario) === String(h2.idHorario)) {
      errors.value.idHorario2 = ['Los dos horarios seleccionados deben ser diferentes.']
      errorMessage.value = 'Por favor, corrige los errores del formulario.'
      submitting.value = false
      return
    }
    if (h1.diaSemana === h2.diaSemana) {
      const t1End = h1.horaFin.substring(0, 5)
      const t1Start = h1.horaInicio.substring(0, 5)
      const t2End = h2.horaFin.substring(0, 5)
      const t2Start = h2.horaInicio.substring(0, 5)

      if (t1End !== t2Start && t2End !== t1Start) {
        errors.value.idHorario2 = ['Los horarios del mismo día deben ser bloques continuos.']
        errorMessage.value = 'Por favor, corrige los errores del formulario.'
        submitting.value = false
        return
      }
    }
  }

  try {
    const response = await props.api.post('/cursos', {
      idCurso: form.idCurso,
      idMateria: form.idMateria,
      idDocente: Number(form.idDocente),
      idHorario1: Number(form.idHorario1),
      idHorario2: Number(form.idHorario2),
      idPeriodo: Number(form.idPeriodo),
    })

    const payload = payloadFromResponse(response.data)
    cursos.value.unshift(payload.curso)
    successMessage.value = response.data.message || 'Curso registrado correctamente.'
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

    if (index !== -1) {
      cursos.value[index] = payload.curso
    }

    successMessage.value = response.data.message || 'Curso deshabilitado correctamente.'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo deshabilitar el curso.'
  } finally {
    submitting.value = false
    cursoToDisable.value = null
  }
}

onMounted(fetchCursoData)
</script>

<template>
  <div class="curso-management">
    <div class="header-section">
      <div>
        <h3>Creacion de Cursos</h3>
        <p class="subtitle">HU-ADM-06 - Materia, docente, horario y regimen academico</p>
      </div>
      <button class="primary" type="button" :disabled="loading" @click="fetchCursoData">
        {{ loading ? 'Cargando...' : 'Actualizar' }}
      </button>
    </div>

    <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
    <div v-if="errorMessage" class="alert error">{{ errorMessage }}</div>

    <div class="content-grid">
      <section class="card-panel">
        <h4>Registrar curso</h4>
        <form class="course-form" @submit.prevent="submitForm">
          <label>
            <span>Curso / Aula *</span>
            <select v-model="form.idCurso" required>
              <option value="" disabled>Seleccione un curso/aula</option>
              <option v-for="cf in cursosFisicos" :key="cf.idCurso" :value="cf.idCurso">
                {{ cf.idCurso }} (Capacidad: {{ cf.capacidad }})
              </option>
            </select>
            <small v-if="errors.idCurso" class="field-error">{{ errors.idCurso[0] }}</small>
          </label>

          <!-- Filtro por carrera: solo filtra el listado de materias, no se guarda -->
          <label class="filter-label">
            <span>Filtrar por carrera</span>
            <select v-model="filterCarrera" @change="form.idMateria = ''">
              <option value="">Todas las carreras</option>
              <option v-for="carrera in carreras" :key="carrera.idCarrera" :value="carrera.idCarrera">
                {{ carrera.nombre }}
              </option>
            </select>
          </label>

          <label>
            <span>Materia *</span>
            <select v-model="form.idMateria" required>
              <option value="" disabled>{{ filterCarrera ? 'Seleccione una materia de esta carrera' : 'Seleccione una materia' }}</option>
              <option v-for="materia in materiasFiltradas" :key="materia.idMateria" :value="materia.idMateria">
                {{ materia.nombre }} ({{ materia.idMateria }})
              </option>
            </select>
            <small v-if="errors.idMateria" class="field-error">{{ errors.idMateria[0] }}</small>
          </label>

          <label>
            <span>Docente *</span>
            <select v-model="form.idDocente" required>
              <option value="" disabled>Seleccione un docente</option>
              <option v-for="docente in docentes" :key="docente.idUsuario" :value="String(docente.idUsuario)">
                {{ docente.nombreCompleto }} - {{ docente.correo }}
              </option>
            </select>
            <small v-if="errors.idDocente" class="field-error">{{ errors.idDocente[0] }}</small>
          </label>

          <label>
            <span>Horario 1 *</span>
            <select v-model="form.idHorario1" required>
              <option value="" disabled>Seleccione el horario 1</option>
              <option v-for="horario in horarios" :key="horario.idHorario" :value="String(horario.idHorario)">
                {{ horario.descripcion }}
              </option>
            </select>
            <small v-if="errors.idHorario1" class="field-error">{{ errors.idHorario1[0] }}</small>
          </label>

          <label>
            <span>Horario 2 *</span>
            <select v-model="form.idHorario2" required>
              <option value="" disabled>Seleccione el horario 2</option>
              <option v-for="horario in horarios" :key="horario.idHorario" :value="String(horario.idHorario)">
                {{ horario.descripcion }}
              </option>
            </select>
            <small v-if="errors.idHorario2" class="field-error">{{ errors.idHorario2[0] }}</small>
          </label>

          <label>
            <span>Regimen academico *</span>
            <select v-model="form.idPeriodo" disabled required>
              <option value="" disabled>Seleccione un periodo</option>
              <option v-for="periodo in periodos" :key="periodo.idPeriodo" :value="String(periodo.idPeriodo)">
                {{ periodo.nombre }} (Periodo actual)
              </option>
            </select>
            <small v-if="errors.idPeriodo" class="field-error">{{ errors.idPeriodo[0] }}</small>
          </label>

      

          <div class="form-actions">
            <button class="secondary" type="button" @click="resetForm">Limpiar</button>
            <button class="primary" type="submit" :disabled="submitting">
              {{ submitting ? 'Guardando...' : 'Guardar curso' }}
            </button>
          </div>
        </form>
      </section>

      <section class="card-panel">
        <div class="table-head">
          <h4>Cursos creados</h4>
        </div>

        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th>Codigo</th>
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
                <td colspan="7" class="empty-state">No hay cursos registrados.</td>
              </tr>
              <tr v-for="curso in cursos" :key="curso.idCursoMateria" :class="{ inactive: !curso.estado }">
                <td><code>{{ curso.idCurso }}</code></td>
                <td>{{ curso.materia || 'Sin materia' }}</td>
                <td>{{ curso.docente || 'Sin docente' }}</td>
                <td>{{ curso.horarioDetalle || 'Sin horario' }}</td>
                <td>{{ curso.periodo || 'Sin periodo' }}</td>
                <td>
                  <span class="status-badge" :class="curso.estado ? 'active' : 'inactive'">
                    {{ curso.estado ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>
                <td>
                  <div class="actions">
                    <button
                      v-if="curso.estado"
                      class="icon-btn danger"
                      type="button"
                      :disabled="submitting"
                      @click="askDisableCurso(curso)"
                    >
                      Deshabilitar
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- Modal confirmación deshabilitar curso -->
    <Teleport to="body">
      <div v-if="showConfirmModal" class="backdrop" @mousedown.self="cancelDisableCurso">
        <div class="confirm-modal">
          <div class="confirm-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
              <line x1="12" y1="9" x2="12" y2="13"/>
              <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
          </div>
          <h4>Deshabilitar curso</h4>
          <p class="confirm-subject">
            <strong>{{ cursoToDisable?.materia }}</strong>
            <span> &mdash; Aula: <code>{{ cursoToDisable?.idCurso }}</code></span>
          </p>
          <p class="confirm-warning">
            Esta acción <strong>deshabilitará</strong> este curso del sistema. Los estudiantes ya
            inscritos y sus notas no se eliminarán, pero el curso dejará de estar disponible
            para nuevas inscripciones.
          </p>
          <div class="confirm-actions">
            <button class="secondary" type="button" :disabled="submitting" @click="cancelDisableCurso">Cancelar</button>
            <button class="danger-btn" type="button" :disabled="submitting" @click="confirmDisableCurso">
              {{ submitting ? 'Deshabilitando...' : 'Confirmar deshabilitar' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.curso-management {
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

.header-section h3,
.card-panel h4 {
  margin: 0;
}

.subtitle {
  margin: 0.25rem 0 0;
  color: var(--muted);
  font-size: 0.9rem;
}

.content-grid {
  display: grid;
  grid-template-columns: 0.95fr 1.05fr;
  gap: 1rem;
}

.card-panel {
  border: 1px solid rgba(180, 204, 255, 0.08);
  border-radius: 1rem;
  background: rgba(255, 255, 255, 0.02);
  padding: 1rem;
}

.course-form {
  display: grid;
  gap: 1rem;
  margin-top: 1rem;
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

/* Etiqueta de filtro: no es un campo del formulario, solo ayuda visual */
.filter-label span {
  color: #0284c7;
  font-style: italic;
}
.filter-label select {
  border-color: rgba(125, 211, 252, 0.2);
  background: rgba(56, 189, 248, 0.04);
}
.filter-label select:focus {
  border-color: rgba(125, 211, 252, 0.55);
  box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
}
.filter-hint {
  font-size: 0.72rem;
  color: var(--muted);
  font-style: italic;
  margin-top: 0.1rem;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

.table-wrap {
  overflow-x: auto;
  margin-top: 1rem;
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

.field-error {
  color: #fca5a5;
}

@media (max-width: 980px) {
  .header-section,
  .content-grid {
    grid-template-columns: 1fr;
    flex-direction: column;
    align-items: flex-start;
  }
}

.inactive {
  opacity: 0.55;
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

/* ---- Confirm Modal ---- */
.backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: grid;
  place-items: center;
  padding: 1rem;
  z-index: 40;
}

.confirm-modal {
  width: min(100%, 28rem);
  background: linear-gradient(180deg, rgba(14, 20, 41, 0.98), rgba(11, 16, 32, 0.96));
  border: 1px solid var(--panel-border, rgba(180, 204, 255, 0.12));
  border-radius: 1.4rem;
  padding: 2rem 1.75rem;
  text-align: center;
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
  color: var(--text, #e2e8f0);
}

.confirm-subject {
  margin: 0 0 1rem;
  font-size: 0.95rem;
  color: var(--text, #e2e8f0);
}

.confirm-subject code {
  background: rgba(255,255,255,0.06);
  padding: 0.15rem 0.4rem;
  border-radius: 0.35rem;
  font-size: 0.85rem;
}

.confirm-warning {
  color: var(--muted, #94a3b8);
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
