<script setup>
import { ref, computed, onMounted } from 'vue'
import DocenteRegistrarNotas from './DocenteRegistrarNotas.vue'
import DocenteRendimiento from './DocenteRendimiento.vue'
import DocenteReporteNotas from './DocenteReporteNotas.vue'


// Propiedades flexibles para mitigar las advertencias del bundler de Vue
const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  api: {
    type: [Object, Function],
    required: true
  },
  badgeTone: {
    type: String,
    default: 'default'
  }
})

const emit = defineEmits(['logout'])

// Estados de control de la UI
const modoDocente = ref('inicio') // 'inicio' | 'cursos' | 'detalle'
const loading = ref(false)
const errorMessage = ref('')

// Almacenamiento de respuestas de la API
const cursos = ref([])
const cursoSeleccionado = ref(null)
const estudiantesInscritos = ref([])

/**
 * HU-DOC-02: Recuperar materias asignadas al docente autenticado
 */
async function obtenerCursos() {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await props.api.get('/docente/cursos')
    if (response.data && response.data.status === 'success') {
      cursos.value = response.data.data
    } else {
      errorMessage.value = response.data.message || 'Error inesperado al cargar las materias asignadas.'
    }
  } catch (error) {
    console.error(error)
    errorMessage.value = error.response?.data?.message || 'No se pudo conectar con el servidor de asignaturas.'
  } finally {
    loading.value = false
  }
}

/**
 * HU-DOC-03: Recuperar estudiantes inscritos en el grupo académico seleccionado
 */
async function verDetalleCurso(curso) {
  cursoSeleccionado.value = curso
  loading.value = true
  modoDocente.value = 'detalle'
  errorMessage.value = ''
  try {
    const response = await props.api.get(`/cursos-materias/${curso.idCursoMateria}/estudiantes`)
    if (response.data && response.data.status === 'success') {
      estudiantesInscritos.value = response.data.data
    } else {
      errorMessage.value = response.data.message || 'Error al recuperar el listado de matriculados.'
    }
  } catch (error) {
    console.error(error)
    errorMessage.value = error.response?.data?.message || 'Error de operación al auditar el grupo.'
  } finally {
    loading.value = false
  }
}

/**
 * Cálculo dinámico de capacidad global instalada
 */
const totalEstudiantesMax = computed(() => {
  return cursos.value.reduce((acc, c) => acc + (parseInt(c.maxInscritos) || 0), 0)
})

function cambiarVista(vista) {
  modoDocente.value = vista
  errorMessage.value = ''
}

onMounted(() => {
  obtenerCursos()
})
</script>

<template>
  <div class="premium-dashboard-container">
    
    <aside class="sidebar-navigation">
      <div class="sidebar-header">
        <div class="brand-dot"></div>
        <span class="brand-txt">PANEL DOCENTE</span>
      </div>
      
      <nav class="sidebar-menu">
        <button :class="{ active: modoDocente === 'inicio' }" @click="cambiarVista('inicio')">
          <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="7" height="7"></rect>
            <rect x="14" y="3" width="7" height="7"></rect>
            <rect x="14" y="14" width="7" height="7"></rect>
            <rect x="3" y="14" width="7" height="7"></rect>
          </svg>
          Vista General
        </button>
        <button :class="{ active: modoDocente === 'cursos' || modoDocente === 'detalle' }" @click="cambiarVista('cursos')">
          <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
          </svg>
          Cursos Asignados
        </button>
        <button :class="{ active: modoDocente === 'registrar_notas' }" @click="cambiarVista('registrar_notas')">
          <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg>
          Registrar Calificaciones
        </button>
        <button :class="{ active: modoDocente === 'rendimiento' }" @click="cambiarVista('rendimiento')">
          <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"></path><path d="M12 20V4"></path><path d="M6 20v-6"></path></svg>
          Ver Rendimiento
        </button>
        <button :class="{ active: modoDocente === 'reporte_notas' }" @click="cambiarVista('reporte_notas')">
          <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line></svg>
          Reporte de Notas
        </button>
      </nav>


      <div class="sidebar-footer">
        <button class="btn-logout-minimal" @click="emit('logout')">
          <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          Cerrar Sesión
        </button>
      </div>
    </aside>

    <main class="main-workspace">
      <header class="workspace-topbar">
        <div class="topbar-left">
          <span class="context-path">Módulos Académicos / Gestión</span>
          <h2>Panel Control Universitario</h2>
        </div>
        <div v-if="errorMessage" class="alert-inline error">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          {{ errorMessage }}
        </div>
        <div v-if="loading" class="alert-inline loading">
          <div class="spinner-mini"></div>
          Sincronizando con la Base de Datos...
        </div>
      </header>

      <section class="workspace-body">
        
        <div v-if="modoDocente === 'inicio'" class="fade-in-view">
          <div class="welcome-banner">
            <h3>¡Bienvenido al Sistema de Gestión, {{ user?.nombre1 }}!</h3>
            <p>Desde este entorno integrado puedes auditar en tiempo real tus listas de estudiantes matriculados, validar cupos asignados y revisar los horarios vigentes provistos por el departamento de sistemas.</p>
          </div>

          <h4 class="block-title">Resumen de Carga Horaria</h4>
          <div class="dashboard-cards-grid">
            <div class="metric-card">
              <div class="metric-icon m-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                </svg>
              </div>
              <div class="metric-info">
                <span class="lbl">Materias Asignadas</span>
                <strong class="val">{{ cursos.length }} Asignaturas</strong>
              </div>
            </div>

            <div class="metric-card">
              <div class="metric-icon m-purple">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <div class="metric-info">
                <span class="lbl">Capacidad Global Alumnos</span>
                <strong class="val">{{ totalEstudiantesMax }} Cupos Reservados</strong>
              </div>
            </div>
          </div>
        </div>

        <div v-else-if="modoDocente === 'cursos'" class="fade-in-view">
          <div class="table-card-wrapper">
            <div class="table-card-header">
              <h4>Asignaturas y Distribución de Horarios Académicos</h4>
            </div>
            <div class="table-responsive">
              <table class="workspace-table">
                <thead>
                  <tr>
                    <th>Nombre de la Asignatura</th>
                    <th>Horario Asignado</th>
                    <th>Inicio de Gestión</th>
                    <th>Conclusión de Gestión</th>
                    <th>Cupo Máximo</th>
                    <th class="txt-right">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="curso in cursos" :key="curso.idCursoMateria">
                    <td class="primary-cell">{{ curso.materia_nombre }}</td>
                    <td><span class="badge-tag-turno">{{ curso.turno_nombre }}</span></td>
                    <td class="text-muted">{{ curso.fechaInicio }}</td>
                    <td class="text-muted">{{ curso.fechaFin }}</td>
                    <td class="font-medium text-cyan">{{ curso.maxInscritos }} Estudiantes</td>
                    <td class="txt-right">
                      <button class="action-row-btn" @click="verDetalleCurso(curso)">
                        Ver Alumnos
                      </button>
                    </td>
                  </tr>
                  <tr v-if="cursos.length === 0 && !loading">
                    <td colspan="6" class="empty-table-msg">No se encontraron registros de materias vinculadas a su cuenta de docente.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div v-else-if="modoDocente === 'detalle'" class="fade-in-view">
          <div class="table-card-wrapper">
            <div class="table-card-header search-style-header">
              <button class="action-back-btn" @click="cambiarVista('cursos')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="19" y1="12" x2="5" y2="12"></line>
                  <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Regresar a Materias
              </button>
              <div class="header-title-group">
                <h4>{{ cursoSeleccionado?.materia_nombre }}</h4>
                <span class="sub-badge">{{ cursoSeleccionado?.turno_nombre }}</span>
              </div>
            </div>
            <div class="table-responsive">
              <table class="workspace-table">
                <thead>
                  <tr>
                    <th>Documento de Identidad (CI)</th>
                    <th>Estudiante Matriculado</th>
                    <th>Correo Electrónico Institucional</th>
                    <th>Fecha de Inscripción</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="alumno in estudiantesInscritos" :key="alumno.id_estudiante">
                    <td class="primary-cell font-mono">{{ alumno.ci }}</td>
                    <td class="font-medium">
                      {{ alumno.apellido1 }} {{ alumno.apellido2 || '' }}, {{ alumno.nombre1 }} {{ alumno.nombre2 || '' }}
                    </td>
                    <td class="text-muted">{{ alumno.correo }}</td>
                    <td class="text-muted">{{ alumno.fecha_inscripcion }}</td>
                  </tr>
                  <tr v-if="estudiantesInscritos.length === 0 && !loading">
                    <td colspan="4" class="empty-table-msg">Actualmente no existen alumnos matriculados en este grupo académico.</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div v-else-if="modoDocente === 'registrar_notas'" class="fade-in-view">
          <DocenteRegistrarNotas :user="user" :api="api" :badgeTone="badgeTone" />
        </div>

        <div v-else-if="modoDocente === 'rendimiento'" class="fade-in-view">
          <DocenteRendimiento :user="user" :api="api" :badgeTone="badgeTone" />
        </div>

        <div v-else-if="modoDocente === 'reporte_notas'" class="fade-in-view">
          <DocenteReporteNotas :user="user" :api="api" :badgeTone="badgeTone" />
        </div>



      </section>
    </main>

    <aside class="sidebar-profile-panel">
      <div class="profile-card-minimal">
        <div class="avatar-placeholder">
          {{ user?.nombre1?.charAt(0) }}{{ user?.apellido1?.charAt(0) }}
        </div>
        <div class="profile-meta">
          <h5>{{ user?.nombre1 }} {{ user?.apellido1 }}</h5>
          <span class="user-role-tag" :data-tone="badgeTone">{{ user?.rol || 'Docente' }}</span>
        </div>
      </div>

      <hr class="separator-line" />

      <div class="quick-status-widget">
        <h6>Metadata de Sesión</h6>
        <div class="widget-row">
          <span class="label">Correo institucional</span>
          <span class="value text-ellipsis" :title="user?.correo">{{ user?.correo }}</span>
        </div>
        <div class="widget-row">
          <span class="label">Cédula de Identidad</span>
          <span class="value">{{ user?.ci }}</span>
        </div>
        <div class="widget-row">
          <span class="label">Estado de Cuenta</span>
          <span class="value text-success">✔ Conexión Segura</span>
        </div>
      </div>
    </aside>

  </div>
</template>

<style scoped>
/* ESTRUCTURA GENERAL DE 3 COLUMNAS OSCURAS */
.premium-dashboard-container {
  display: grid;
  grid-template-columns: 240px 1fr 280px;
  width: 100%;
  min-height: 100vh;
  background: #0b0f19;
  color: #f1f5f9;
  font-family: system-ui, -apple-system, sans-serif;
  box-sizing: border-box;
}

/* COLUMNA 1: SIDEBAR DE NAVEGACIÓN TRASLÚCIDA */
.sidebar-navigation {
  background: rgba(15, 23, 42, 0.6);
  border-right: 1px solid rgba(255, 255, 255, 0.05);
  padding: 2.5rem 1.25rem;
  display: flex;
  flex-direction: column;
}
.sidebar-header {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  margin-bottom: 2.5rem;
  padding-left: 0.5rem;
}
.brand-dot {
  width: 12px;
  height: 12px;
  background: #38bdf8;
  border-radius: 50%;
  box-shadow: 0 0 12px #38bdf8;
}
.brand-txt {
  font-weight: 800;
  font-size: 0.9rem;
  letter-spacing: 0.15em;
  color: #f8fafc;
}
.sidebar-menu {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.sidebar-menu button {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.8rem 1rem;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #94a3b8;
  font-size: 0.9rem;
  font-weight: 600;
  text-align: left;
  cursor: pointer;
  transition: all 0.2s ease;
}
.sidebar-menu button:hover {
  color: #f1f5f9;
  background: rgba(255, 255, 255, 0.03);
}
.sidebar-menu button.active {
  color: #38bdf8;
  background: rgba(56, 189, 248, 0.08);
}
.menu-icon {
  width: 18px;
  height: 18px;
}
.sidebar-footer {
  margin-top: auto;
}
.btn-logout-minimal {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  width: 100%;
  padding: 0.8rem 1rem;
  background: transparent;
  border: none;
  border-radius: 8px;
  color: #ef4444;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  opacity: 0.8;
  transition: all 0.2s;
}
.btn-logout-minimal:hover {
  background: rgba(239, 68, 68, 0.05);
  opacity: 1;
}

/* COLUMNA 2: MAIN WORKSPACE CENTRAL */
.main-workspace {
  padding: 2.5rem 3rem;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  background: #0f172a;
}
.workspace-topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2.5rem;
}
.context-path {
  font-size: 0.75rem;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
.workspace-topbar h2 {
  font-size: 1.75rem;
  font-weight: 700;
  margin-top: 0.25rem;
  color: #f8fafc;
}

/* CARDS DE BIENVENIDA E INDICADORES */
.welcome-banner {
  background: linear-gradient(135deg, rgba(30, 41, 59, 0.6), rgba(15, 23, 42, 0.4));
  border: 1px solid rgba(255, 255, 255, 0.05);
  padding: 2rem;
  border-radius: 12px;
  margin-bottom: 2.5rem;
}
.welcome-banner h3 {
  font-size: 1.25rem;
  margin-bottom: 0.5rem;
  color: #38bdf8;
}
.welcome-banner p {
  color: #94a3b8;
  font-size: 0.95rem;
  line-height: 1.6;
}
.block-title {
  font-size: 0.85rem;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 1.25rem;
}
.dashboard-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 1.5rem;
}
.metric-card {
  background: rgba(30, 41, 59, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.06);
  padding: 1.5rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1.25rem;
}
.metric-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.metric-icon svg { width: 24px; height: 24px; }
.m-blue { background: rgba(56, 189, 248, 0.08); color: #38bdf8; }
.m-purple { background: rgba(168, 85, 247, 0.08); color: #a855f7; }
.metric-info { display: flex; flex-direction: column; }
.metric-info .lbl { font-size: 0.75rem; color: #64748b; text-transform: uppercase; }
.metric-info .val { font-size: 1.2rem; font-weight: 700; color: #f1f5f9; margin-top: 0.2rem; }

/* CORPORATIVO DE TABLAS */
.table-card-wrapper {
  background: rgba(15, 23, 42, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 14px;
  overflow: hidden;
}
.table-card-header {
  padding: 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  background: rgba(255, 255, 255, 0.01);
}
.table-card-header h4 { font-size: 1.05rem; font-weight: 600; color: #f1f5f9; }
.table-responsive { overflow-x: auto; }

.workspace-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
  text-align: left;
}
.workspace-table th {
  padding: 1.1rem 1.5rem;
  background: rgba(15, 23, 42, 0.6);
  color: #64748b;
  font-size: 0.75rem;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.05em;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
.workspace-table td {
  padding: 1.2rem 1.5rem;
  border-bottom: 1px solid rgba(255, 255, 255, 0.03);
  color: #cbd5e1;
}
.workspace-table tr:hover td {
  background: rgba(255, 255, 255, 0.015);
}
.primary-cell { color: #f8fafc !important; font-weight: 600; }
.font-medium { font-weight: 500; }
.font-mono { font-family: monospace; font-size: 0.85rem; color: #38bdf8 !important; }
.text-muted { color: #64748b; }
.text-cyan { color: #38bdf8; }
.txt-right { text-align: right; }

.badge-tag-turno {
  background: rgba(56, 189, 248, 0.08);
  color: #38bdf8;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
}
.action-row-btn {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #e2e8f0;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.action-row-btn:hover {
  background: #f1f5f9;
  color: #0f172a;
  border-color: #f1f5f9;
}
.empty-table-msg { text-align: center; color: #64748b; padding: 4rem !important; font-size: 0.95rem; }

/* HEADER DETALLES / VOLVER */
.search-style-header {
  display: flex;
  align-items: center;
  gap: 2rem;
}
.action-back-btn {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: transparent;
  border: none;
  color: #94a3b8;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  padding: 0;
}
.action-back-btn svg { width: 16px; height: 16px; transition: transform 0.2s; }
.action-back-btn:hover { color: #38bdf8; }
.action-back-btn:hover svg { transform: translateX(-2px); }
.header-title-group { display: flex; align-items: center; gap: 0.75rem; }
.sub-badge { font-size: 0.7rem; background: rgba(168, 85, 247, 0.15); color: #c084fc; padding: 0.2rem 0.5rem; border-radius: 4px; font-weight: 700; text-transform: uppercase; }

/* COLUMNA 3: SIDEBAR DERECHO DE PERFIL */
.sidebar-profile-panel {
  background: rgba(15, 23, 42, 0.4);
  border-left: 1px solid rgba(255, 255, 255, 0.05);
  padding: 2.5rem 1.5rem;
  display: flex;
  flex-direction: column;
}
.profile-card-minimal {
  display: flex;
  align-items: center;
  gap: 1rem;
}
.avatar-placeholder {
  width: 48px;
  height: 48px;
  background: linear-gradient(135deg, #38bdf8, #818cf8);
  color: #0f172a;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 1rem;
}
.profile-meta h5 { font-size: 0.95rem; font-weight: 600; color: #f8fafc; margin: 0; }
.user-role-tag { font-size: 0.7rem; color: #38bdf8; font-weight: 700; text-transform: uppercase; background: rgba(56, 189, 248, 0.1); padding: 0.2rem 0.5rem; border-radius: 4px; }
.separator-line { border: 0; border-top: 1px solid rgba(255, 255, 255, 0.05); margin: 2rem 0; }

.quick-status-widget h6 { font-size: 0.8rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1.25rem; margin-top: 0; }
.widget-row { display: flex; flex-direction: column; gap: 0.25rem; margin-bottom: 1.25rem; }
.widget-row .label { font-size: 0.75rem; color: #475569; text-transform: uppercase; font-weight: 600; }
.widget-row .value { font-size: 0.85rem; color: #cbd5e1; font-weight: 500; }
.text-ellipsis { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.text-success { color: #10b981 !important; }

/* ANIMACIONES Y FEEDBACK */
.fade-in-view {
  animation: fadeIn 0.25s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to { opacity: 1; transform: translateY(0); }
}
.alert-inline { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; }
.alert-inline.error { background: rgba(239, 68, 68, 0.08); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.15); }
.alert-inline.loading { background: rgba(56, 189, 248, 0.08); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.15); }

.spinner-mini {
  width: 14px;
  height: 14px;
  border: 2px solid rgba(56, 189, 248, 0.2);
  border-top-color: #38bdf8;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>