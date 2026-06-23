<script setup>
import { ref, computed, onMounted } from 'vue'
import DocenteRegistrarNotas from './DocenteRegistrarNotas.vue'
import DocenteRendimiento from './DocenteRendimiento.vue'
import DocenteReporteNotas from './DocenteReporteNotas.vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: [Object, Function], required: true },
  badgeTone: { type: String, default: 'default' }
})

const emit = defineEmits(['logout'])

const modoDocente = ref('inicio')
const loading = ref(false)
const errorMessage = ref('')

const cursos = ref([])
const cursoSeleccionado = ref(null)
const estudiantesInscritos = ref([])

async function obtenerCursos() {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await props.api.get('/docente/cursos')
    if (response.data && Array.isArray(response.data)) {
      cursos.value = response.data
    } else if (response.data && response.data.data) {
      cursos.value = response.data.data
    } else {
      errorMessage.value = 'El servidor no retornó un listado de cursos válido.'
    }
  } catch (error) {
    console.error(error)
    errorMessage.value = error.response?.data?.message || 'No se pudo conectar con el servidor de asignaturas.'
  } finally {
    loading.value = false
  }
}

async function verDetalleCurso(curso) {
  cursoSeleccionado.value = curso
  loading.value = true
  modoDocente.value = 'detalle'
  errorMessage.value = ''
  const idCursoMateriaReal = curso.idCursoMateria || curso.id_curso_materia || curso.id || null
  try {
    const response = await props.api.get('/docente/estudiantes', {
      params: { idCursoMateria: idCursoMateriaReal }
    })
    if (response.data && Array.isArray(response.data)) {
      estudiantesInscritos.value = response.data
    } else if (response.data && response.data.data) {
      estudiantesInscritos.value = response.data.data
    } else {
      errorMessage.value = 'Error al recuperar el listado de matriculados.'
    }
  } catch (error) {
    console.error(error)
    errorMessage.value = error.response?.data?.message || 'Error de operación al auditar el grupo.'
  } finally {
    loading.value = false
  }
}

const totalEstudiantesMax = computed(() => {
  return cursos.value.reduce((acc, c) => {
    const cupo = c.maxInscritos || c.max_inscritos || c.cupo_maximo || c.cupo || 0
    return acc + (parseInt(cupo) || 0)
  }, 0)
})

function cambiarVista(vista) {
  modoDocente.value = vista
  errorMessage.value = ''
}

onMounted(() => { obtenerCursos() })
</script>

<template>
  <div class="uni-admin-shell">

    <aside class="uni-admin-sidebar">
      <div>
        <div class="uni-brand">
          <i class="ti ti-building-community"></i>
          Universidad
        </div>
        <div class="uni-hero uni-hero--sm">
          <h1>Panel del<br><em>docente</em></h1>
          <p>Sistema académico integrado.</p>
        </div>
      </div>
      <div>
        <div class="uni-foot">
          <span class="uni-dot"></span>
          Conexión segura
        </div>
        <div class="uni-sidebar-actions">
          <button class="uni-sidebar-logout" :disabled="loading" @click="emit('logout')">
            <i class="ti ti-logout"></i>
            {{ loading ? 'Cerrando...' : 'Cerrar sesión' }}
          </button>
        </div>
      </div>
    </aside>

    <main class="uni-admin-main">
      <div class="uni-dashboard-card">

        <div class="uni-dashboard-head">
          <div>
            <span class="uni-eyebrow">Sesión activa</span>
            <h2 class="uni-dashboard-name">{{ user?.nombre1 || user?.nombre }} {{ user?.apellido1 || user?.paterno }}</h2>
          </div>
          <div class="uni-dashboard-actions">
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'inicio' }" @click="cambiarVista('inicio')">
              <i class="ti ti-home" aria-hidden="true"></i>Inicio
            </button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'cursos' || modoDocente === 'detalle' }" @click="cambiarVista('cursos')">
              <i class="ti ti-book" aria-hidden="true"></i>Cursos
            </button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'registrar_notas' }" @click="cambiarVista('registrar_notas')">
              <i class="ti ti-edit" aria-hidden="true"></i>Calificaciones
            </button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'rendimiento' }" @click="cambiarVista('rendimiento')">
              <i class="ti ti-chart-bar" aria-hidden="true"></i>Rendimiento
            </button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'reporte_notas' }" @click="cambiarVista('reporte_notas')">
              <i class="ti ti-file-report" aria-hidden="true"></i>Reportes
            </button>
            <span class="uni-role-badge" :data-tone="badgeTone">{{ user?.rol || 'Docente' }}</span>
          </div>
        </div>

        <div v-if="errorMessage" class="uni-alert uni-alert--error mb-4">{{ errorMessage }}</div>

        <!-- INICIO -->
        <div v-if="modoDocente === 'inicio'" class="uni-section-body fade-in-view">

          <div class="welcome-card">
            <h3>¡Bienvenido, {{ user?.nombre1 || user?.nombre }}!</h3>
            <p>Desde este panel puedes gestionar tus cursos, registrar calificaciones y generar reportes académicos.</p>
          </div>

          <div class="docente-cards-grid">
            <div class="metric-card-docente">
              <div class="docente-icon purple-icon">
                <i class="ti ti-book"></i>
              </div>
              <div class="metric-data">
                <span class="metric-label">Materias Asignadas</span>
                <strong class="metric-value">{{ cursos.length }} Asignaturas</strong>
              </div>
            </div>
            <div class="metric-card-docente">
              <div class="docente-icon cyan-icon">
                <i class="ti ti-users"></i>
              </div>
              <div class="metric-data">
                <span class="metric-label">Capacidad Global</span>
                <strong class="metric-value">{{ totalEstudiantesMax }} Cupos</strong>
              </div>
            </div>
          </div>
        </div>

        <!-- CURSOS -->
        <div v-else-if="modoDocente === 'cursos'" class="uni-section-body fade-in-view">
          <div class="table-card-wrapper-docente">
            <div class="table-card-header-docente">
              <h4>Asignaturas y Distribución de Horarios</h4>
            </div>
            <div class="table-responsive">
              <table class="docente-table">
                <thead>
                  <tr>
                    <th>Asignatura</th>
                    <th>Horario</th>
                    <th>Inicio</th>
                    <th>Fin</th>
                    <th>Cupo</th>
                    <th class="txt-right">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="curso in cursos" :key="curso.idCursoMateria || curso.id_curso_materia || curso.id">
                    <td class="primary-cell">{{ curso.materia_nombre || curso.nombre || curso.materia }}</td>
                    <td><span class="docente-badge">{{ curso.turno_nombre || curso.turno || curso.horario }}</span></td>
                    <td class="text-muted">{{ curso.fechaInicio || curso.fecha_inicio || 'N/A' }}</td>
                    <td class="text-muted">{{ curso.fechaFin || curso.fecha_fin || 'N/A' }}</td>
                    <td class="font-medium text-cyan">{{ curso.maxInscritos || curso.max_inscritos || curso.cupo_maximo || curso.cupo || 0 }}</td>
                    <td class="txt-right">
                      <button class="docente-action-btn" @click="verDetalleCurso(curso)">Ver Alumnos</button>
                    </td>
                  </tr>
                  <tr v-if="cursos.length === 0 && !loading">
                    <td colspan="6" class="empty-table-msg">No hay materias asignadas.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- DETALLE CURSO -->
        <div v-else-if="modoDocente === 'detalle'" class="uni-section-body fade-in-view">
          <div class="table-card-wrapper-docente">
            <div class="table-card-header-docente flex-header-docente">
              <button class="back-btn-docente" @click="cambiarVista('cursos')">
                <i class="ti ti-arrow-left"></i> Regresar
              </button>
              <h4>{{ cursoSeleccionado?.materia_nombre || cursoSeleccionado?.nombre || cursoSeleccionado?.materia }}</h4>
            </div>
            <div class="table-responsive">
              <table class="docente-table">
                <thead>
                  <tr>
                    <th>CI</th>
                    <th>Estudiante</th>
                    <th>Correo</th>
                    <th>Fecha Inscripción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="alumno in estudiantesInscritos" :key="alumno.id_estudiante || alumno.id_inscripcion || alumno.id">
                    <td class="primary-cell font-mono">{{ alumno.ci || alumno.documento || 'N/A' }}</td>
                    <td class="font-medium">{{ alumno.apellido1 || '' }} {{ alumno.apellido2 || '' }}, {{ alumno.nombre1 || '' }}</td>
                    <td class="text-muted">{{ alumno.correo || alumno.email || 'Sin correo' }}</td>
                    <td class="text-muted">{{ alumno.fecha_inscripcion || 'N/A' }}</td>
                  </tr>
                  <tr v-if="estudiantesInscritos.length === 0 && !loading">
                    <td colspan="4" class="empty-table-msg">No hay alumnos matriculados.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- SUB-COMPONENTES -->
        <div v-else-if="modoDocente === 'registrar_notas'" class="uni-section-body">
          <DocenteRegistrarNotas :user="user" :api="api" :badgeTone="badgeTone" />
        </div>
        <div v-else-if="modoDocente === 'rendimiento'" class="uni-section-body">
          <DocenteRendimiento :user="user" :api="api" :badgeTone="badgeTone" />
        </div>
        <div v-else-if="modoDocente === 'reporte_notas'" class="uni-section-body">
          <DocenteReporteNotas :user="user" :api="api" :badgeTone="badgeTone" />
        </div>

      </div>
    </main>

  </div>
</template>

<style scoped>
.mb-4 { margin-bottom: 1.5rem; }
.txt-right { text-align: right; }
.font-medium { font-weight: 500; }
.font-mono { font-family: monospace; font-size: 0.85rem; color: var(--color-mint-dark) !important; }
.text-muted { color: var(--uni-muted); }
.text-cyan { color: var(--color-mint-dark); }
.primary-cell { color: var(--color-black) !important; font-weight: 600; }

.fade-in-view { animation: fadeIn 0.25s ease-out; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ── Welcome ──────────────────────────────────── */
.welcome-card {
  background: var(--color-linen);
  padding: 1.5rem 2rem;
  border-radius: 12px;
  margin-bottom: 1.5rem;
}
.welcome-card h3 {
  font-family: 'Playfair Display', serif;
  font-size: 1.25rem;
  margin: 0 0 0.4rem;
  color: var(--color-black);
}
.welcome-card p {
  color: var(--uni-muted);
  font-size: 0.9rem;
  line-height: 1.6;
  margin: 0;
}

/* ── Metric cards ─────────────────────────────── */
.docente-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 1.25rem;
}
.metric-card-docente {
  background: #fafafa;
  border: 1px solid rgba(0, 0, 0, 0.06);
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1.25rem;
}
.docente-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
}
.purple-icon {
  background: rgba(168, 85, 247, 0.08);
  color: #a855f7;
}
.cyan-icon {
  background: rgba(6, 182, 212, 0.08);
  color: #0891b2;
}
.metric-data {
  display: flex;
  flex-direction: column;
}
.metric-label {
  font-size: 0.75rem;
  color: var(--uni-muted);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.metric-value {
  font-size: 1.15rem;
  font-weight: 700;
  margin-top: 0.2rem;
  color: var(--color-black);
}

/* ── Table cards ──────────────────────────────── */
.table-card-wrapper-docente {
  background: var(--color-white);
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 14px;
  overflow: hidden;
}
.table-card-header-docente {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}
.table-card-header-docente h4 {
  font-size: 1rem;
  font-weight: 600;
  margin: 0;
}
.table-responsive { overflow-x: auto; }

.docente-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
  text-align: left;
}
.docente-table th {
  padding: 1rem 1.5rem;
  background: #fafafa;
  color: var(--uni-muted);
  font-size: 0.75rem;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}
.docente-table td {
  padding: 1.1rem 1.5rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.03);
  color: var(--uni-text);
}
.docente-table tr:hover td {
  background: #fafafa;
}

.docente-badge {
  background: rgba(6, 182, 212, 0.08);
  color: #0891b2;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
}
.docente-action-btn {
  background: transparent;
  border: 1px solid rgba(0, 0, 0, 0.12);
  color: var(--uni-muted);
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.docente-action-btn:hover {
  background: var(--color-linen);
  color: var(--color-black);
  border-color: var(--color-mint-dark);
}
.empty-table-msg {
  text-align: center;
  color: var(--uni-muted);
  padding: 3rem !important;
}

.flex-header-docente {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.back-btn-docente {
  display: flex;
  align-items: center;
  gap: 0.4rem;
  background: transparent;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 8px;
  color: var(--uni-muted);
  font-size: 0.8rem;
  font-weight: 600;
  padding: 0.4rem 0.8rem;
  cursor: pointer;
  transition: all 0.2s;
}
.back-btn-docente:hover {
  background: var(--color-linen);
  color: var(--color-black);
}
.loading-bar {
  padding: 0.5rem 0;
  font-size: 0.85rem;
  color: var(--uni-muted);
  font-weight: 600;
}
</style>
