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

const modoDocente = ref('cursos')
const loading     = ref(false)
const errorMessage = ref('')

const cursos               = ref([])
const cursoSeleccionado   = ref(null)
const estudiantesInscritos = ref([])

// Horarios del docente
const schedules = ref([])
const loadingSchedule = ref(false)

// Estado para controlar la barra de búsqueda interactiva
const textoBusqueda = ref('')

// Función auxiliar para normalizar texto (ignorar mayúsculas y tildes)
function normalizarTexto(texto) {
  if (!texto) return ''
  return texto
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .trim()
}

// PROPIEDAD COMPUTADA: Filtra materias inactivas (estado = 0) y aplica el buscador
const cursosFiltrados = computed(() => {
  // 1. Filtrar de forma estricta para dejar SOLO las materias con estado activo (1)
  const activos = cursos.value.filter(curso => {
    // Evaluamos el campo 'estado' proveniente de tus procedimientos almacenados
    const estadoMateria = curso.estado ?? curso.materia_estado ?? null
    if (estadoMateria !== null) {
      return parseInt(estadoMateria) === 1
    }
    return true // Backup por si algún registro carece de la propiedad
  })

  // 2. Aplicar el término de búsqueda por texto si existe
  const termino = normalizarTexto(textoBusqueda.value)
  if (!termino) {
    return activos
  }

  return activos.filter(curso => {
    const nombreMateria = curso.materia_nombre || curso.nombre || curso.materia || ''
    return normalizarTexto(nombreMateria).includes(termino)
  })
})

// Función para seleccionar rápidamente una sugerencia del buscador
function seleccionarMateriaSugerida(nombre) {
  textoBusqueda.value = nombre
}

// Función robusta para transformar cadenas como "1, (07:30-09:20)" en "Lun 07:30-09:20"
function formatearHorario(horarioRaw) {
  if (!horarioRaw) return 'Horario no definido'
  
  const diasMapeo = {
    '1': 'Lun',
    '2': 'Mar',
    '3': 'Mié',
    '4': 'Jue',
    '5': 'Vie',
    '6': 'Sáb',
    '7': 'Dom'
  }

  if (/[a-zA-Z]/.test(horarioRaw) && !/\b\d\b/.test(horarioRaw)) {
    return horarioRaw
  }

  let limpio = horarioRaw.replace(/\s+/g, '');
  const regexGlobal = /(\d),?\(?([^)]+)\)?/g;
  const resultados = [];
  let match;

  while ((match = regexGlobal.exec(limpio)) !== null) {
    const diaNum = match[1];
    let horas = match[2];
    
    if (horas.startsWith(',')) {
      horas = horas.substring(1);
    }

    const diaNombre = diasMapeo[diaNum] || `Día ${diaNum}`;
    resultados.push(`${diaNombre} ${horas}`);
  }

  return resultados.length > 0 ? resultados.join(', ') : horarioRaw;
}

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

// Configuración para grilla de horarios del docente
const timeSlots = [
  { label: '07:30 - 09:20', start: '07:30:00', end: '09:20:00' },
  { label: '09:20 - 11:10', start: '09:20:00', end: '11:10:00' },
  { label: '11:10 - 13:00', start: '11:10:00', end: '13:00:00' },
  { label: '13:30 - 15:10', start: '13:30:00', end: '15:10:00' },
  { label: '15:10 - 16:50', start: '15:10:00', end: '16:50:00' },
  { label: '16:50 - 18:30', start: '16:50:00', end: '18:30:00' }
]

const dayNames = {
  1: 'Lunes',
  2: 'Martes',
  3: 'Miércoles',
  4: 'Jueves',
  5: 'Viernes',
  6: 'Sábado'
}

async function obtenerHorarios() {
  loadingSchedule.value = true
  errorMessage.value = ''
  try {
    const response = await props.api.get('/docente/horario')
    const payload = response.data?.data ?? response.data
    schedules.value = payload.schedules || []
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo cargar el horario del docente.'
  } finally {
    loadingSchedule.value = false
  }
}

function getOccupiedSlot(day, slot) {
  return schedules.value.find(item => {
    if (Number(item.diaSemana) !== day) return false
    return item.horaInicio < slot.end && item.horaFin > slot.start
  })
}

const unmatchedSchedules = computed(() => {
  return schedules.value.filter(item => {
    const matched = timeSlots.some(slot => {
      return Number(item.diaSemana) >= 1 && Number(item.diaSemana) <= 6 &&
             item.horaInicio < slot.end && item.horaFin > slot.start
    })
    return !matched
  })
})

function cambiarVista(vista) {
  modoDocente.value = vista
  errorMessage.value = ''
  if (vista === 'horarios') {
    obtenerHorarios()
  }
}

onMounted(obtenerCursos)
</script>

<template>
  <div class="dashboard-docente-main-wrapper">
    
    <div class="uni-top-nav-row">
      <div class="uni-tab-bar">
        <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'cursos' || modoDocente === 'detalle' }" @click="cambiarVista('cursos')">
          <i class="ti ti-book"></i>Cursos
        </button>
        <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'horarios' }" @click="cambiarVista('horarios')">
          <i class="ti ti-calendar"></i>Horarios
        </button>
        <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'registrar_notas' }" @click="cambiarVista('registrar_notas')">
          <i class="ti ti-edit"></i>Calificaciones
        </button>
        <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'rendimiento' }" @click="cambiarVista('rendimiento')">
          <i class="ti ti-chart-bar"></i>Rendimiento
        </button>
        <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoDocente === 'reporte_notas' }" @click="cambiarVista('reporte_notas')">
          <i class="ti ti-file-report"></i>Reportes
        </button>
      </div>

      <div v-if="modoDocente === 'cursos'" class="search-inline-container">
        <div class="search-input-wrapper">
          <i class="ti ti-search search-icon-embed"></i>
          <input 
            v-model="textoBusqueda" 
            type="text" 
            class="doc-search-input" 
            placeholder="Buscar materia por nombre..."
          />
          <button v-if="textoBusqueda" @click="textoBusqueda = ''" class="clear-search-btn">
            <i class="ti ti-x"></i>
          </button>
        </div>
      </div>
    </div>

    <div v-if="modoDocente === 'cursos' && textoBusqueda && {cursosFiltrados}.length > 0" class="search-predictions-row-box">
      <div class="search-predictions-box">
        <span class="prediction-label">Sugerencias:</span>
        <div class="prediction-tags">
          <span 
            v-for="curso in cursosFiltrados.slice(0, 3)" 
            :key="'sug-' + (curso.idCursoMateria || curso.id)"
            @click="seleccionarMateriaSugerida(curso.materia_nombre || curso.nombre || curso.materia)"
            class="prediction-tag-item"
          >
            {{ curso.materia_nombre || curso.nombre || curso.materia }}
          </span>
        </div>
      </div>
    </div>

    <div v-if="errorMessage" class="uni-alert uni-alert--error" style="margin: 1rem 1.5rem 0 1.5rem;">{{ errorMessage }}</div>

    <div class="uni-section-body">

      <template v-if="modoDocente === 'cursos'">
        <div class="doc-cards-container">
          
          <div v-for="curso in cursosFiltrados" :key="curso.idCursoMateria || curso.id_curso_materia || curso.id" class="doc-materia-card-box">
            
            <div class="doc-materia-card-header">
              <h4>{{ curso.materia_nombre || curso.nombre || curso.materia }}</h4>
            </div>

            <div class="doc-materia-card-body">
              <div class="doc-materia-row">
                <span class="doc-materia-label-text">Horario:</span>
                <span class="doc-badge">
                  {{ formatearHorario(curso.turno_nombre || curso.turno || curso.horario) }}
                </span>
              </div>
              <div class="doc-materia-row">
                <span class="doc-materia-label-text">Inicio de Gestión:</span>
                <span class="doc-materia-value-text">{{ curso.fechaInicio || curso.fecha_inicio || 'N/A' }}</span>
              </div>
              <div class="doc-materia-row">
                <span class="doc-materia-label-text">Conclusión de Gestión:</span>
                <span class="doc-materia-value-text">{{ curso.fechaFin || curso.fecha_fin || 'N/A' }}</span>
              </div>

              <div class="doc-materia-capacity-block">
                <span class="doc-capacity-title">CAPACIDAD DE LA MATERIA</span>
                <div class="doc-capacity-counter">
                  <strong class="doc-counter-total">
                    {{ curso.alumnos_count ?? 0 }} / {{ curso.maxInscritos || curso.max_inscritos || curso.cupo_maximo || curso.cupo || 0 }}
                  </strong>
                  <span class="doc-counter-label">Estudiantes</span>
                </div>
              </div>
            </div>

            <div class="doc-materia-card-footer">
              <button class="doc-action-btn doc-action-btn--full" @click="verDetalleCurso(curso)">Ver Alumnos</button>
            </div>

          </div>

          <div v-if="!cursosFiltrados.length && !loading" class="doc-empty-box">
            <i class="ti ti-box-off"></i> {{ textoBusqueda ? `No se encontraron materias activas con "${textoBusqueda}".` : 'No tiene materias asignadas o activas en esta gestión.' }}
          </div>
        </div>
      </template>

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

      <template v-else-if="modoDocente === 'horarios'">
        <div class="doc-table-card">
          <div class="doc-table-header">
            <h4 style="font-family:'Playfair Display', serif; font-size:1.25rem; font-weight:700; color:var(--color-black);">Mi Horario Semanal</h4>
            <p style="font-size:11px; color:var(--uni-muted); margin:2px 0 0 0;">Visualiza la distribución semanal de tus asignaturas en sus respectivos bloques y aulas físicas.</p>
          </div>

          <div class="doc-schedule-body">
            <!-- Loading indicator -->
            <div v-if="loadingSchedule" class="doc-schedule-loading">
              <svg class="doc-spin" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
              <span style="margin-left: 8px;">Cargando tu horario académico...</span>
            </div>

            <template v-else>
              <!-- Info del Periodo -->
              <div class="doc-schedule-legend">
                <div class="doc-legend-item">
                  <span class="doc-legend-color doc-legend-color--occupied"></span>
                  <span>Clase Asignada</span>
                </div>
                <div class="doc-legend-item">
                  <span class="doc-legend-color doc-legend-color--free"></span>
                  <span>Libre / Disponible</span>
                </div>
              </div>

              <!-- Grilla de Horario -->
              <div class="doc-schedule-grid-wrap">
                <table class="doc-schedule-grid-table">
                  <thead>
                    <tr>
                      <th class="doc-th-time">Bloque / Hora</th>
                      <th v-for="d in [1, 2, 3, 4, 5]" :key="d">{{ dayNames[d] }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(slot, slotIdx) in timeSlots" :key="slotIdx">
                      <td class="doc-td-time">
                        <div class="doc-time-label">Bloque {{ slotIdx + 1 }}</div>
                        <div class="doc-time-range">{{ slot.label }}</div>
                      </td>
                      <td v-for="day in [1, 2, 3, 4, 5]" :key="day" class="doc-td-slot">
                        <!-- Celda de ocupación -->
                        <div v-if="getOccupiedSlot(day, slot)" class="doc-slot-card">
                          <div class="doc-slot-subject" :title="getOccupiedSlot(day, slot).materia">
                            {{ getOccupiedSlot(day, slot).materia }}
                          </div>
                          <div class="doc-slot-room">
                            Aula: <code>{{ getOccupiedSlot(day, slot).aula }}</code>
                          </div>
                          <div class="doc-slot-career" :title="getOccupiedSlot(day, slot).carrera">
                            {{ getOccupiedSlot(day, slot).carrera }}
                          </div>
                          <div class="doc-slot-period">
                            {{ getOccupiedSlot(day, slot).periodo }}
                          </div>
                        </div>
                        <div v-else class="doc-slot-free">
                          <span>Libre</span>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- Horarios no estándar / personalizados -->
              <div v-if="unmatchedSchedules.length > 0" class="doc-unmatched-section">
                <h5 class="doc-unmatched-title">Otros Horarios de Clase (No Estándar)</h5>
                <div class="doc-unmatched-list">
                  <div v-for="(item, idx) in unmatchedSchedules" :key="idx" class="doc-unmatched-item">
                    <span class="doc-unmatched-day">{{ dayNames[item.diaSemana] || 'Día ' + item.diaSemana }}</span>
                    <span class="doc-unmatched-time">{{ item.horaInicio.substring(0, 5) }} - {{ item.horaFin.substring(0, 5) }}</span>
                    <span class="doc-unmatched-details">
                      <strong>{{ item.materia }}</strong> — Aula: <code>{{ item.aula }}</code> ({{ item.carrera }})
                    </span>
                  </div>
                </div>
              </div>

            </template>
          </div>
        </div>
      </template>

      <DocenteRegistrarNotas v-else-if="modoDocente === 'registrar_notas'" :user="user" :api="api" :badgeTone="badgeTone" />
      <DocenteRendimiento    v-else-if="modoDocente === 'rendimiento'"     :user="user" :api="api" :badgeTone="badgeTone" />
      <DocenteReporteNotas   v-else-if="modoDocente === 'reporte_notas'"   :user="user" :api="api" :badgeTone="badgeTone" />

    </div>
  </div>
</template>

<style scoped>
/* Transiciones */
.uni-section-body > * { animation: fadeIn .2s ease-out; }

.mb-4 { margin-bottom: 1.5rem; }
.txt-right { text-align: right; }
.font-medium { font-weight: 500; }
.font-mono { font-family: monospace; font-size: 0.85rem; color: var(--color-mint-dark) !important; }
.text-muted { color: var(--uni-muted); }
.text-cyan { color: var(--color-mint-dark); }
.primary-cell { color: var(--color-black) !important; font-weight: 600; }

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* DISEÑO DE LA CABECERA COMPARTIDA (Navegación + Buscador alineado) */
.uni-top-nav-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08); 
  padding-bottom: 0.25rem;
  margin: 0 1.5rem;
}

.uni-tab-bar {
  display: flex;
  gap: 0.5rem;
  border-bottom: none !important;
  padding-bottom: 0;
}

/* Buscador Empotrado Estilizado */
.search-inline-container {
  width: 100%;
  max-width: 290px;
}

.search-input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.search-icon-embed {
  position: absolute;
  left: 0.85rem;
  color: #a0aec0;
  font-size: 0.95rem;
  pointer-events: none;
}

.doc-search-input {
  width: 100%;
  padding: 0.5rem 2rem 0.5rem 2.2rem;
  font-size: 0.85rem;
  color: #1a202c;
  background-color: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.12);
  border-radius: 8px;
  outline: none;
  transition: all 0.2s ease;
}

.doc-search-input:focus {
  border-color: #0891b2;
  box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.12);
}

.clear-search-btn {
  position: absolute;
  right: 0.6rem;
  background: transparent;
  border: none;
  color: #a0aec0;
  cursor: pointer;
  display: flex;
  align-items: center;
  border-radius: 50%;
  padding: 0.2rem;
}

.clear-search-btn:hover {
  background: rgba(0, 0, 0, 0.05);
  color: #4a5568;
}

/* Burbujas de sugerencia */
.search-predictions-row-box {
  margin: 0.4rem 1.5rem 0 1.5rem;
  display: flex;
  justify-content: flex-end;
}

.search-predictions-box {
  display: flex;
  align-items: center;
  gap: 0.4rem;
}

.prediction-label {
  font-size: 0.68rem;
  color: #718096;
  font-weight: 700;
  text-transform: uppercase;
}

.prediction-tags {
  display: flex;
  gap: 0.35rem;
}

.prediction-tag-item {
  font-size: 0.72rem;
  background: #edf2f7;
  color: #2d3748;
  padding: 0.15rem 0.55rem;
  border-radius: 12px;
  cursor: pointer;
  font-weight: 500;
  transition: all 0.15s;
}

.prediction-tag-item:hover {
  background: rgba(6, 182, 212, 0.1);
  color: #0891b2;
}

/* CONTENEDOR Y TARJETAS */
.doc-cards-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-top: .5rem;
}

.doc-materia-card-box {
  background: var(--color-white, #ffffff);
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 14px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.doc-materia-card-box:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0,0,0,.04);
}

.doc-materia-card-header {
  padding: 1.1rem 1.5rem;
  background: #fafafa;
  border-bottom: 1px solid rgba(0,0,0,.04);
}

.doc-materia-card-header h4 {
  font-size: .95rem;
  font-weight: 600;
  margin: 0;
  color: var(--color-black, #000000);
}

.doc-materia-card-body {
  padding: 1.25rem 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.doc-materia-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.85rem;
}

.doc-materia-label-text {
  color: var(--uni-muted, #777777);
}

.doc-materia-value-text {
  font-weight: 500;
  color: var(--color-black, #000000);
}

.doc-materia-capacity-block {
  background: #fdfdfd;
  border: 1px dashed rgba(0,0,0,.08);
  border-radius: 10px;
  padding: 0.85rem;
  margin-top: 0.5rem;
  text-align: center;
}

.doc-capacity-title {
  font-size: 0.68rem;
  color: var(--uni-muted, #777777);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .04em;
  display: block;
  margin-bottom: 0.25rem;
}

.doc-capacity-counter {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
}

.doc-counter-total {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-mint-dark, #0891b2);
}

.doc-counter-label {
  font-size: 0.8rem;
  color: var(--uni-muted, #777777);
}

.doc-materia-card-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid rgba(0,0,0,.03);
  background: #fafafa;
}

.doc-action-btn--full {
  width: 100%;
  text-align: center;
}

.doc-empty-box {
  grid-column: 1 / -1;
  text-align: center;
  color: var(--uni-muted);
  padding: 4rem 1.5rem;
  background: #fafafa;
  border-radius: 14px;
  border: 1px dashed rgba(0,0,0,.05);
}

/* TABLA DE DETALLES */
.doc-table-card {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 14px;
  overflow: hidden;
  flex-shrink: 0;
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
.doc-cell-muted { color: var(--uni-muted); }
.doc-empty { text-align: center; color: var(--uni-muted); padding: 3rem !important; }

.doc-badge {
  background: rgba(6,182,212,.08); color: #0891b2;
  padding: .35rem .75rem; border-radius: 6px;
  font-size: .78rem; font-weight: 600;
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

/* ── Estilos de Horario para el Docente ── */
.doc-schedule-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.doc-schedule-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 4rem;
  color: var(--uni-muted);
  font-size: 13px;
}

.doc-spin {
  animation: docSpin 0.8s linear infinite;
}
@keyframes docSpin {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.doc-schedule-legend {
  display: flex;
  gap: 1.25rem;
  margin-bottom: 0.25rem;
}

.doc-legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
  color: var(--uni-muted);
}

.doc-legend-color {
  width: 14px;
  height: 14px;
  border-radius: 4px;
  border: 1px solid rgba(0, 0, 0, 0.08);
}

.doc-legend-color--occupied {
  background: #edf4f2;
  border-color: var(--color-mint-light);
}

.doc-legend-color--free {
  background: #fafaf9;
  border-color: #e8e8e5;
}

.doc-schedule-grid-wrap {
  border: 1px solid #e8e8e5;
  border-radius: 12px;
  overflow-x: auto;
}

.doc-schedule-grid-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 900px;
}

.doc-schedule-grid-table th {
  background: #fafaf9;
  text-align: center;
  padding: 10px 8px;
  font-size: 10px;
  font-weight: 700;
  color: var(--uni-muted);
  border-bottom: 2px solid var(--color-linen);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.doc-th-time {
  width: 140px;
}

.doc-schedule-grid-table td {
  border-bottom: 1px solid #e8e8e5;
  border-right: 1px solid #e8e8e5;
  padding: 8px;
  vertical-align: top;
}

.doc-schedule-grid-table td:last-child {
  border-right: none;
}

.doc-td-time {
  background: #fafaf9;
  text-align: center;
  border-right: 2px solid var(--color-linen) !important;
  vertical-align: middle !important;
  padding: 12px 8px !important;
}

.doc-time-label {
  font-size: 10px;
  font-weight: 700;
  color: #8c9f96;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.doc-time-range {
  font-size: 12px;
  font-weight: 600;
  color: var(--color-black);
  margin-top: 3px;
}

.doc-td-slot {
  width: 17%;
  height: 100px;
  background: #fdfdfd;
}

.doc-slot-card {
  background: #edf4f2;
  border-left: 4px solid var(--color-mint-dark);
  border-radius: 8px;
  padding: 8px 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  height: 100%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  text-align: left;
}

.doc-slot-subject {
  font-size: 11px;
  font-weight: 700;
  color: #2b3d36;
  line-height: 1.35;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.doc-slot-room {
  font-size: 10px;
  font-weight: 600;
  color: #1a1a1a;
}
.doc-slot-room code {
  background: #d0cfca;
  padding: 1px 4px;
  border-radius: 4px;
  font-family: monospace;
}

.doc-slot-career {
  font-size: 9px;
  font-weight: 600;
  color: var(--color-mint-light);
  text-transform: uppercase;
  letter-spacing: 0.02em;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.doc-slot-period {
  font-size: 8px;
  font-weight: 500;
  color: #8c8c88;
  margin-top: auto;
}

.doc-slot-free {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  border: 1px dashed #e8e8e5;
  border-radius: 8px;
  color: #c0c0bc;
  font-size: 11px;
  font-weight: 500;
  background: #fafaf9;
}

/* ── Horarios no estándar ── */
.doc-unmatched-section {
  border-top: 1px solid var(--color-linen);
  padding-top: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.doc-unmatched-title {
  margin: 0;
  font-size: 12px;
  font-weight: 700;
  color: var(--color-black);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.doc-unmatched-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.doc-unmatched-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  background: #fafaf9;
  border: 1px solid #e8e8e5;
  border-radius: 8px;
  font-size: 12px;
}

.doc-unmatched-day {
  font-weight: 700;
  color: var(--color-mint-dark);
  min-width: 80px;
}

.doc-unmatched-time {
  font-weight: 600;
  color: #1a1a1a;
  background: #f0f0ee;
  padding: 2px 8px;
  border-radius: 6px;
  font-family: monospace;
  font-size: 11px;
}

.doc-unmatched-details {
  color: var(--uni-muted);
}
</style>