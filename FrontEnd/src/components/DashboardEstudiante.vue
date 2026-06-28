<script setup>
import { ref, computed, onMounted } from 'vue'
import EstudianteInscripciones from './EstudianteInscripciones.vue'
import EstudianteNotas from './EstudianteNotas.vue'
import EstudianteMalla from './EstudianteMalla.vue'
import EstudianteReportes from './EstudianteReportes.vue'

const props = defineProps({
  user: { type: Object, required: true },
  api:  { type: [Object, Function], required: true },
  badgeTone: { type: String, default: 'default' }
})

const modoEstudiante = ref('resumen')
const loading        = ref(false)
const errorMessage   = ref('')

// Estados de datos
const dashboardData  = ref(null)
const materias       = ref([])

const perfil  = computed(() => dashboardData.value?.perfil || {})
const resumen = computed(() => dashboardData.value?.resumen || {})

// Calcular porcentaje de avance de la malla
const approvedPercent = computed(() => {
  if (!dashboardData.value?.malla) return 0
  const total = dashboardData.value.malla.reduce((count, sem) => count + sem.materias.length, 0)
  if (!total) return 0
  const approved = dashboardData.value.malla.reduce(
    (count, sem) => count + sem.materias.filter((m) => m.estadoAcademico === 'Aprobada' || m.estadoAcademico === 'Aprobado').length,
    0
  )
  return Math.round((approved / total) * 100)
})

async function cargarInformacionBase() {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data: dash } = await props.api.get('/estudiante/dashboard')
    dashboardData.value = dash.data ?? dash
    
    const { data: m } = await props.api.get('/estudiante/materias-disponibles')
    materias.value = m.data ?? m
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No pudimos cargar la información académica.'
  } finally {
    loading.value = false
  }
}

function cambiarVista(vista) {
  modoEstudiante.value = vista
  errorMessage.value = ''
}

// Lógica para formatear los textos de los estados de cupos o prerequisitos
function displayValue(value) {
  return value === null || value === undefined || value === '' ? 'Sin dato' : value
}

onMounted(cargarInformacionBase)
</script>

<template>
  <div class="uni-tab-bar">
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoEstudiante === 'resumen' }" @click="cambiarVista('resumen')">
      <i class="ti ti-layout-dashboard"></i>Panel
    </button>
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoEstudiante === 'inscripciones' }" @click="cambiarVista('inscripciones')">
      <i class="ti ti-checklist"></i>Carga Académica
    </button>
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoEstudiante === 'notas' }" @click="cambiarVista('notas')">
      <i class="ti ti-school"></i>Calificaciones
    </button>
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoEstudiante === 'malla' }" @click="cambiarVista('malla')">
      <i class="ti ti-git-fork"></i>Malla Curricular
    </button>
    <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': modoEstudiante === 'reportes' }" @click="cambiarVista('reportes')">
      <i class="ti ti-file-report"></i>Reportes
    </button>
  </div>

  <div v-if="errorMessage" class="uni-alert uni-alert--error" style="margin: 0 1.5rem;">{{ errorMessage }}</div>

  <div class="uni-section-body">
    
    <template v-if="modoEstudiante === 'resumen'">
      <div class="est-summary-grid">
        <article class="est-stat-card">
          <span class="est-stat-label">Materias Inscritas</span>
          <strong class="est-stat-value">{{ resumen.materiasInscritas || 0 }}</strong>
        </article>
        <article class="est-stat-card">
          <span class="est-stat-label">Notas Registradas</span>
          <strong class="est-stat-value">{{ resumen.notasRegistradas || 0 }}</strong>
        </article>
        <article class="est-stat-card">
          <span class="est-stat-label">Materias Aprobadas</span>
          <strong class="est-stat-value">{{ resumen.aprobadas || 0 }}</strong>
        </article>
        <article class="est-stat-card">
          <span class="est-stat-label">Avance de Carrera</span>
          <strong class="est-stat-value">{{ approvedPercent }}%</strong>
        </article>
      </div>

      <h3 class="est-section-title">Oferta Académica Disponible</h3>
      <div class="est-cards-container">
        <div v-for="materia in materias" :key="materia.idCursoMateria" class="est-materia-card-box">
          <div class="est-materia-card-header">
            <h4>{{ materia.materia }}</h4>
            <span class="est-regimen-pill">{{ materia.regimen || 'Regular' }}</span>
          </div>

          <div class="est-materia-card-body">
            <div class="est-materia-row">
              <span class="est-materia-label-text">Docente:</span>
              <span class="est-materia-value-text">{{ displayValue(materia.docente) }}</span>
            </div>
            <div class="est-materia-row">
              <span class="est-materia-label-text">Horario / Turno:</span>
              <span class="est-badge">{{ materia.horario || materia.turno || 'No definido' }}</span>
            </div>
            <div class="est-materia-row">
              <span class="est-materia-label-text">Prerrequisito:</span>
              <span class="est-materia-value-text" :style="{ color: !materia.prerrequisito ? '#777' : 'inherit' }">
                {{ materia.prerrequisito || 'Ninguno' }}
              </span>
            </div>

            <div class="est-materia-capacity-block">
              <span class="est-capacity-title">Cupos del Curso</span>
              <div class="est-capacity-counter">
                <strong class="est-counter-total">{{ materia.cuposDisponibles }} / {{ materia.cupoTotal }}</strong>
                <span class="est-counter-label">disponibles</span>
              </div>
            </div>
          </div>

          <div class="est-materia-card-footer">
            <button 
              class="est-action-btn est-action-btn--full" 
              :disabled="!materia.puedeInscribirse"
              @click="cambiarVista('inscripciones')"
            >
              {{ materia.puedeInscribirse ? 'Gestionar Inscripción' : 'No Habilitado' }}
            </button>
          </div>
        </div>

        <div v-if="!materias.length && !loading" class="est-empty-box">
          <i class="ti ti-box-off"></i> No existen materias disponibles para tu plan en este periodo.
        </div>
      </div>
    </template>

    <EstudianteInscripciones v-else-if="modoEstudiante === 'inscripciones'" :user="user" :api="api" :badgeTone="badgeTone" @refresh="cargarInformacionBase" />
    <EstudianteNotas         v-else-if="modoEstudiante === 'notas'"         :user="user" :api="api" :badgeTone="badgeTone" />
    <EstudianteMalla         v-else-if="modoEstudiante === 'malla'"         :user="user" :api="api" :badgeTone="badgeTone" :mallaInitial="dashboardData?.malla" />
    <EstudianteReportes      v-else-if="modoEstudiante === 'reportes'"      :user="user" :api="api" :badgeTone="badgeTone" />

  </div>
</template>

<style scoped>
/* Animación idéntica al docente */
.uni-section-body > * { animation: fadeIn .2s ease-out; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* GRID DE METRICAS PRINCIPALES */
.est-summary-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.est-stat-card {
  background: var(--color-white, #ffffff);
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 14px;
  padding: 1.5rem;
}

.est-stat-label {
  font-size: 0.72rem;
  font-weight: 700;
  color: var(--uni-muted, #777777);
  text-transform: uppercase;
  letter-spacing: .05em;
  display: block;
}

.est-stat-value {
  display: block;
  margin-top: 0.5rem;
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--color-black, #000000);
}

.est-section-title {
  font-size: 1.1rem;
  font-weight: 600;
  margin: 1.5rem 0 1rem 0;
  color: var(--color-black, #000000);
}

/* CUADRÍCULA DE MATERIAS DISPONIBLES */
.est-cards-container {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.5rem;
}

.est-materia-card-box {
  background: var(--color-white, #ffffff);
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 14px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: transform 0.2s, box-shadow 0.2s;
}

.est-materia-card-box:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(0,0,0,.04);
}

.est-materia-card-header {
  padding: 1.1rem 1.5rem;
  background: #fafafa;
  border-bottom: 1px solid rgba(0,0,0,.04);
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.5rem;
}

.est-materia-card-header h4 {
  font-size: .95rem;
  font-weight: 600;
  margin: 0;
  color: var(--color-black, #000000);
  line-height: 1.3;
}

.est-regimen-pill {
  background: rgba(0, 0, 0, 0.05);
  padding: 0.2rem 0.5rem;
  font-size: 0.68rem;
  font-weight: 600;
  border-radius: 4px;
  color: #555;
}

.est-materia-card-body {
  padding: 1.25rem 1.5rem;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
}

.est-materia-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.85rem;
}

.est-materia-label-text {
  color: var(--uni-muted, #777777);
}

.est-materia-value-text {
  font-weight: 500;
  color: var(--color-black, #000000);
  text-align: right;
}

.est-materia-capacity-block {
  background: #fdfdfd;
  border: 1px dashed rgba(0,0,0,.08);
  border-radius: 10px;
  padding: 0.85rem;
  margin-top: 0.5rem;
  text-align: center;
}

.est-capacity-title {
  font-size: 0.68rem;
  color: var(--uni-muted, #777777);
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .04em;
  display: block;
  margin-bottom: 0.25rem;
}

.est-capacity-counter {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.4rem;
}

.est-counter-total {
  font-size: 1.1rem;
  font-weight: 700;
  color: #a16207; /* Tono ocre/café para diferenciar la estética del estudiante */
}

.est-counter-label {
  font-size: 0.8rem;
  color: var(--uni-muted, #777777);
}

.est-materia-card-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid rgba(0,0,0,.03);
  background: #fafafa;
}

.est-action-btn--full {
  width: 100%;
  text-align: center;
}

.est-empty-box {
  grid-column: 1 / -1;
  text-align: center;
  color: var(--uni-muted);
  padding: 4rem 1.5rem;
  background: #fafafa;
  border-radius: 14px;
  border: 1px dashed rgba(0,0,0,.05);
}

.est-badge {
  background: rgba(161, 98, 7, 0.08);
  color: #a16207;
  padding: .35rem .75rem;
  border-radius: 6px;
  font-size: .78rem;
  font-weight: 600;
}

.est-action-btn {
  background: transparent;
  border: 1px solid rgba(0,0,0,.12);
  color: var(--uni-muted);
  padding: .45rem .9rem;
  border-radius: 6px;
  font-size: .78rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
}

.est-action-btn:hover:not(:disabled) {
  background: #fafafa;
  color: var(--color-black);
  border-color: #a16207;
}

.est-action-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}
</style>