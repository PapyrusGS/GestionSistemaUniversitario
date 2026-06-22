<script setup>
import { onMounted, reactive, ref } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const cursos = ref([])
const cursosFisicos = ref([])
const materias = ref([])
const docentes = ref([])
const horarios = ref([])
const periodos = ref([])
const loading = ref(false)
const submitting = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const errors = ref({})

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

async function disableCurso(curso) {
  if (!confirm(`¿Deseas deshabilitar el curso de la materia ${curso.materia} en el aula ${curso.idCurso}?`)) {
    return
  }

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

          <label>
            <span>Materia *</span>
            <select v-model="form.idMateria" required>
              <option value="" disabled>Seleccione una materia</option>
              <option v-for="materia in materias" :key="materia.idMateria" :value="materia.idMateria">
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
                      @click="disableCurso(curso)"
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
</style>
