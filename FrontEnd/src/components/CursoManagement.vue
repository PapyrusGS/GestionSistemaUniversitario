<script setup>
import { onMounted, reactive, ref } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const cursos = ref([])
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
  idMateria: '',
  idDocente: '',
  idHorario: '',
  idPeriodo: '',
  maxInscritos: '',
})

function payloadFromResponse(data) {
  return data.data ?? data
}

function resetMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

function resetForm() {
  form.idMateria = ''
  form.idDocente = ''
  form.idHorario = ''
  form.idPeriodo = ''
  form.maxInscritos = ''
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
    materias.value = formData.materias || []
    docentes.value = formData.docentes || []
    horarios.value = formData.horarios || []
    periodos.value = formData.periodos || []
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
      idMateria: form.idMateria,
      idDocente: Number(form.idDocente),
      idHorario: Number(form.idHorario),
      idPeriodo: Number(form.idPeriodo),
      maxInscritos: Number(form.maxInscritos),
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

onMounted(fetchCursoData)
</script>

<template>
  <div class="curso-management">
    <div class="header-section">
      <div>
        <h3>Creacion de Cursos</h3>
        <p class="subtitle">HU-ADM-06 - Materia, docente, horario, cupo y regimen academico</p>
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
            <span>Horario *</span>
            <select v-model="form.idHorario" required>
              <option value="" disabled>Seleccione un horario</option>
              <option v-for="horario in horarios" :key="horario.idHorario" :value="String(horario.idHorario)">
                {{ horario.descripcion }}
              </option>
            </select>
            <small v-if="errors.idHorario" class="field-error">{{ errors.idHorario[0] }}</small>
          </label>

          <label>
            <span>Regimen academico *</span>
            <select v-model="form.idPeriodo" required>
              <option value="" disabled>Seleccione un periodo</option>
              <option v-for="periodo in periodos" :key="periodo.idPeriodo" :value="String(periodo.idPeriodo)">
                {{ periodo.nombre }}
              </option>
            </select>
            <small v-if="errors.idPeriodo" class="field-error">{{ errors.idPeriodo[0] }}</small>
          </label>

          <label>
            <span>Cupo *</span>
            <input v-model.trim="form.maxInscritos" type="number" min="1" required />
            <small v-if="errors.maxInscritos" class="field-error">{{ errors.maxInscritos[0] }}</small>
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
                <th>Cupo</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="cursos.length === 0">
                <td colspan="7" class="empty-state">No hay cursos registrados.</td>
              </tr>
              <tr v-for="curso in cursos" :key="curso.idCursoMateria">
                <td><code>{{ curso.idCurso }}</code></td>
                <td>{{ curso.materia || 'Sin materia' }}</td>
                <td>{{ curso.docente || 'Sin docente' }}</td>
                <td>{{ curso.horarioDetalle || 'Sin horario' }}</td>
                <td>{{ curso.periodo || 'Sin periodo' }}</td>
                <td>{{ curso.maxInscritos }}</td>
                <td>
                  <span class="status-badge" :class="curso.estado ? 'active' : 'inactive'">
                    {{ curso.estado ? 'Activo' : 'Inactivo' }}
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
</style>
