<script setup>
import { ref, computed, onMounted } from 'vue'
import DocenteRegistrarNotas from './DocenteRegistrarNotas.vue'
import DocenteRendimiento from './DocenteRendimiento.vue'
import DocenteReporteNotas from './DocenteReporteNotas.vue'

const props = defineProps({
  user: { type: Object, required: true },
  api:  { type: [Object, Function], required: true },
  badgeTone: { type: String, default: 'default' }
})

const modoDocente = ref('inicio')
const loading     = ref(false)
const errorMessage = ref('')

const cursos              = ref([])
const cursoSeleccionado   = ref(null)
const estudiantesInscritos = ref([])

async function obtenerCursos() {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await props.api.get('/docente/cursos')
    cursos.value = response.data?.data ?? (Array.isArray(response.data) ? response.data : [])
    if (!cursos.value.length && !Array.isArray(response.data) && !response.data?.data) {
      errorMessage.value = 'El servidor no retornó un listado de cursos válido.'
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo conectar con el servidor de asignaturas.'
  } finally {
    loading.value = false
  }
}

async function verDetalleCurso(curso) {
  cursoSeleccionado.value = curso
  modoDocente.value = 'detalle'
  loading.value = true
  errorMessage.value = ''
  const id = curso.idCursoMateria || curso.id_curso_materia || curso.id || null
  try {
    const response = await props.api.get('/docente/estudiantes', { params: { idCursoMateria: id } })
    estudiantesInscritos.value = response.data?.data ?? (Array.isArray(response.data) ? response.data : [])
    if (!estudiantesInscritos.value.length && !Array.isArray(response.data) && !response.data?.data) {
      errorMessage.value = 'Error al recuperar el listado de matriculados.'
    }
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Error al auditar el grupo.'
  } finally {
    loading.value = false
  }
}

const totalEstudiantesMax = computed(() =>
  cursos.value.reduce((acc, c) => acc + (parseInt(c.maxInscritos || c.max_inscritos || c.cupo_maximo || c.cupo || 0) || 0), 0)
)

function cambiarVista(vista) {
  modoDocente.value = vista
  errorMessage.value = ''
}

onMounted(obtenerCursos)
</script>

<template>
  <!-- Tab bar -->
  <div class="uni-tab-bar">
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'cursos' || modoDocente === 'detalle' }" @click="cambiarVista('cursos')">
      <i class="ti ti-book"></i>Cursos
    </button>
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'registrar_notas' }" @click="cambiarVista('registrar_notas')">
      <i class="ti ti-edit"></i>Calificaciones
    </button>
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'rendimiento' }"     @click="cambiarVista('rendimiento')">
      <i class="ti ti-chart-bar"></i>Rendimiento
    </button>
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'reporte_notas' }"   @click="cambiarVista('reporte_notas')">
      <i class="ti ti-file-report"></i>Reportes
    </button>
  </div>

  <div v-if="errorMessage" class="uni-alert uni-alert--error" style="margin: 0 1.5rem;">{{ errorMessage }}</div>

  <!-- Contenido -->
  <div class="uni-section-body">

    <!-- INICIO -->
    <template v-if="modoDocente === 'inicio'">
      <div class="doc-welcome">
        <h3>¡Bienvenido, {{ user?.nombre1 || user?.nombre }}!</h3>
        <p>Desde este panel puedes gestionar tus cursos, registrar calificaciones y generar reportes académicos.</p>
      </div>
      <div class="doc-metric-grid">
        <div class="doc-metric-card">
          <div class="doc-icon doc-icon--purple"><i class="ti ti-book"></i></div>
          <div>
            <span class="doc-metric-label">Materias Asignadas</span>
            <strong class="doc-metric-value">{{ cursos.length }} Asignaturas</strong>
          </div>
        </div>
        <div class="doc-metric-card">
          <div class="doc-icon doc-icon--cyan"><i class="ti ti-users"></i></div>
          <div>
            <span class="doc-metric-label">Capacidad Global</span>
            <strong class="doc-metric-value">{{ totalEstudiantesMax }} Cupos</strong>
          </div>
        </div>
      </div>
    </template>

    <!-- CURSOS -->
    <template v-else-if="modoDocente === 'cursos'">
      <div class="doc-table-card">
        <div class="doc-table-header">
          <h4>Asignaturas y Distribución de Horarios</h4>
        </div>
        <div class="doc-table-scroll">
          <table class="doc-table">
            <thead>
              <tr>
                <th>Asignatura</th><th>Horario</th><th>Inicio</th><th>Fin</th><th>Cupo</th><th style="text-align:right">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="curso in cursos" :key="curso.idCursoMateria || curso.id_curso_materia || curso.id">
                <td class="doc-cell-primary">{{ curso.materia_nombre || curso.nombre || curso.materia }}</td>
                <td><span class="doc-badge">{{ curso.turno_nombre || curso.turno || curso.horario }}</span></td>
                <td class="doc-cell-muted">{{ curso.fechaInicio || curso.fecha_inicio || 'N/A' }}</td>
                <td class="doc-cell-muted">{{ curso.fechaFin || curso.fecha_fin || 'N/A' }}</td>
                <td style="color:var(--color-mint-dark);font-weight:600;">{{ curso.maxInscritos || curso.max_inscritos || curso.cupo_maximo || curso.cupo || 0 }}</td>
                <td style="text-align:right">
                  <button class="doc-action-btn" @click="verDetalleCurso(curso)">Ver Alumnos</button>
                </td>
              </tr>
              <tr v-if="!cursos.length && !loading">
                <td colspan="6" class="doc-empty">No hay materias asignadas.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- DETALLE CURSO -->
    <template v-else-if="modoDocente === 'detalle'">
      <div class="doc-table-card">
        <div class="doc-table-header doc-table-header--flex">
          <button class="doc-back-btn" @click="cambiarVista('cursos')">
            <i class="ti ti-arrow-left"></i> Regresar
          </button>
          <h4>{{ cursoSeleccionado?.materia_nombre || cursoSeleccionado?.nombre || cursoSeleccionado?.materia }}</h4>
        </div>
        <div class="doc-table-scroll">
          <table class="doc-table">
            <thead>
              <tr><th>CI</th><th>Estudiante</th><th>Correo</th><th>Fecha Inscripción</th></tr>
            </thead>
            <tbody>
              <tr v-for="alumno in estudiantesInscritos" :key="alumno.id_estudiante || alumno.id_inscripcion || alumno.id">
                <td style="font-family:monospace;font-size:.85rem;color:var(--color-mint-dark);font-weight:600;">{{ alumno.ci || alumno.documento || 'N/A' }}</td>
                <td style="font-weight:500;">{{ alumno.apellido1 || '' }} {{ alumno.apellido2 || '' }}, {{ alumno.nombre1 || '' }}</td>
                <td class="doc-cell-muted">{{ alumno.correo || alumno.email || 'Sin correo' }}</td>
                <td class="doc-cell-muted">{{ alumno.fecha_inscripcion || 'N/A' }}</td>
              </tr>
              <tr v-if="!estudiantesInscritos.length && !loading">
                <td colspan="4" class="doc-empty">No hay alumnos matriculados.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- SUB-COMPONENTES -->
    <DocenteRegistrarNotas v-else-if="modoDocente === 'registrar_notas'" :user="user" :api="api" :badgeTone="badgeTone" />
    <DocenteRendimiento    v-else-if="modoDocente === 'rendimiento'"     :user="user" :api="api" :badgeTone="badgeTone" />
    <DocenteReporteNotas   v-else-if="modoDocente === 'reporte_notas'"   :user="user" :api="api" :badgeTone="badgeTone" />

  </div>
</template>

<style scoped>
/* Fade animación */
.uni-section-body > * { animation: fadeIn .2s ease-out; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Welcome */
.doc-welcome {
  background: var(--color-linen);
  padding: 1.5rem 2rem;
  border-radius: 12px;
}
.doc-welcome h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.2rem;
  margin: 0 0 .35rem;
}
.doc-welcome p { color: var(--uni-muted); font-size: .875rem; line-height: 1.6; margin: 0; }

/* Metric grid */
.doc-metric-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 1.25rem;
}
.doc-metric-card {
  background: #fafafa;
  border: 1px solid rgba(0,0,0,.06);
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1.25rem;
}
.doc-icon {
  width: 48px; height: 48px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center; font-size: 1.4rem; flex-shrink: 0;
}
.doc-icon--purple { background: rgba(168,85,247,.08); color: #a855f7; }
.doc-icon--cyan   { background: rgba(6,182,212,.08);  color: #0891b2; }
.doc-metric-label { font-size: .72rem; color: var(--uni-muted); text-transform: uppercase; letter-spacing: .04em; display: block; }
.doc-metric-value { font-size: 1.1rem; font-weight: 700; color: var(--color-black); display: block; margin-top: .15rem; }

/* Table card */
.doc-table-card {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 14px;
  overflow: hidden;
}
.doc-table-header {
  padding: 1.1rem 1.5rem;
  border-bottom: 1px solid rgba(0,0,0,.05);
}
.doc-table-header h4 { font-size: .95rem; font-weight: 600; margin: 0; }
.doc-table-header--flex { display: flex; align-items: center; gap: 1rem; }
.doc-table-scroll { overflow-x: auto; }

.doc-table { width: 100%; border-collapse: collapse; font-size: .875rem; text-align: left; }
.doc-table th {
  padding: .9rem 1.5rem;
  background: #fafafa;
  color: var(--uni-muted);
  font-size: .7rem;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: .04em;
  border-bottom: 1px solid rgba(0,0,0,.05);
}
.doc-table td { padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,.03); }
.doc-table tr:hover td { background: #fafafa; }
.doc-cell-primary { font-weight: 600; color: var(--color-black); }
.doc-cell-muted   { color: var(--uni-muted); }
.doc-empty        { text-align: center; color: var(--uni-muted); padding: 3rem !important; }

.doc-badge {
  background: rgba(6,182,212,.08); color: #0891b2;
  padding: .25rem .6rem; border-radius: 6px;
  font-size: .7rem; font-weight: 700; text-transform: uppercase;
}
.doc-action-btn {
  background: transparent; border: 1px solid rgba(0,0,0,.12);
  color: var(--uni-muted); padding: .45rem .9rem;
  border-radius: 6px; font-size: .78rem; font-weight: 600;
  cursor: pointer; transition: all .2s;
}
.doc-action-btn:hover { background: var(--color-linen); color: var(--color-black); border-color: var(--color-mint-dark); }

.doc-back-btn {
  display: flex; align-items: center; gap: .4rem;
  background: transparent; border: 1px solid rgba(0,0,0,.1);
  border-radius: 8px; color: var(--uni-muted);
  font-size: .78rem; font-weight: 600;
  padding: .4rem .8rem; cursor: pointer; transition: all .2s; white-space: nowrap;
}
.doc-back-btn:hover { background: var(--color-linen); color: var(--color-black); }
</style>