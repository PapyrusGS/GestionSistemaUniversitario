<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  user: Object,
  api: Object,
  badgeTone: String
})

// ── Estado principal ─────────────────────────────────────────────────────────
const loading = ref(false)
const errorMessage = ref('')
const cursos = ref([])
const idCursoMateria = ref('')
const rendimiento = ref(null)

// ── Helpers ───────────────────────────────────────────────────────────────────
function resetState() {
  errorMessage.value = ''
  rendimiento.value = null
}

// ── Cargar cursos (reutilizando exactamente el mismo endpoint) ───────────────
async function cargarCursos() {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data } = await props.api.get('/docente/cursos')
    cursos.value = data.data ?? data
  } catch {
    errorMessage.value = 'No se pudieron cargar tus cursos asignados.'
  } finally {
    loading.value = false
  }
}

// ── Cargar rendimiento al cambiar curso ─────────────────────────────────────
async function alCambiarCurso() {
  resetState()
  if (!idCursoMateria.value) return

  loading.value = true
  try {
    const { data } = await props.api.get(`/docente/rendimiento/${idCursoMateria.value}`)
    rendimiento.value = data.data ?? data
  } catch (error) {
    const d = error.response?.data
    errorMessage.value = d?.message || 'Error al cargar el rendimiento académico.'
  } finally {
    loading.value = false
  }
}

// ── Cálculo del porcentaje de aprobación ────────────────────────────────────
const porcentajeAprobados = computed(() => {
  if (!rendimiento.value || rendimiento.value.total_notas === 0) return 0
  return Math.round((rendimiento.value.aprobados / rendimiento.value.total_notas) * 100)
})

const porcentajeReprobados = computed(() => {
  if (!rendimiento.value || rendimiento.value.total_notas === 0) return 0
  return 100 - porcentajeAprobados.value
})

onMounted(cargarCursos)
</script>

<template>
  <div class="fade-in-view">

    <!-- Header -->
    <div class="workspace-topbar">
      <div class="topbar-left">
        <span class="context-path">Docentes / Rendimiento</span>
        <h2>Rendimiento Estudiantil</h2>
        <p class="subtitle-text">Monitoreo de estadísticas y desempeño académico por curso</p>
      </div>
    </div>

    <!-- Alerts globales -->
    <div v-if="errorMessage" class="alert-inline error mb-4">{{ errorMessage }}</div>

    <!-- Selector de Curso -->
    <section class="card-panel mb-4">
      <div class="panel-header">
        <h4>Seleccione un Curso</h4>
      </div>
      <div class="selector-container">
        <label class="full-width">
          <select v-model="idCursoMateria" @change="alCambiarCurso" :disabled="loading">
            <option value="" disabled>Seleccione un curso</option>
            <option v-for="curso in cursos" :key="curso.idCursoMateria" :value="curso.idCursoMateria">
              {{ curso.materia_nombre }} — {{ curso.turno_nombre }}
            </option>
          </select>
        </label>
      </div>
    </section>

    <!-- Estado de carga -->
    <div v-if="loading" class="spinner-container">
      <div class="loading-spinner"></div>
      <span class="loading-text">Procesando información...</span>
    </div>

    <!-- Contenido estadístico si está disponible -->
    <div v-else-if="rendimiento" class="analytics-content">
      
      <!-- Si no hay calificaciones -->
      <div v-if="rendimiento.total_notas === 0" class="alert-inline info mb-4">
        ⚠️ Aún no existen calificaciones registradas para este curso.
      </div>

      <div v-else>
        <!-- Cards Grid -->
        <div class="dashboard-cards-grid mb-4">
          
          <!-- Aprobados (Verde) -->
          <div class="metric-card card-green">
            <div class="metric-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
            </div>
            <div class="metric-info">
              <span class="lbl">Aprobados</span>
              <strong class="val">{{ rendimiento.aprobados }} estudiantes</strong>
            </div>
          </div>

          <!-- Reprobados (Rojo) -->
          <div class="metric-card card-red">
            <div class="metric-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
            </div>
            <div class="metric-info">
              <span class="lbl">Reprobados</span>
              <strong class="val">{{ rendimiento.reprobados }} estudiantes</strong>
            </div>
          </div>

          <!-- Promedio General (Azul) -->
          <div class="metric-card card-blue">
            <div class="metric-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
              </svg>
            </div>
            <div class="metric-info">
              <span class="lbl">Promedio General</span>
              <strong class="val">{{ Number(rendimiento.promedio).toFixed(2) }} pts</strong>
            </div>
          </div>

          <!-- Total Calificaciones (Gris Neutro) -->
          <div class="metric-card card-gray">
            <div class="metric-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
            </div>
            <div class="metric-info">
              <span class="lbl">Notas Registradas</span>
              <strong class="val">{{ rendimiento.total_notas }} registros</strong>
            </div>
          </div>

        </div>

        <!-- Indicador visual (Progress Bar) -->
        <section class="card-panel">
          <div class="panel-header search-style-header">
            <h4>Tasa de Desempeño Académico</h4>
            <span class="sub-badge">{{ porcentajeAprobados }}% Aprobación</span>
          </div>
          <div class="progress-section">
            <div class="progress-legend">
              <span class="legend-item legend-green">Aprobados: {{ porcentajeAprobados }}%</span>
              <span class="legend-item legend-red">Reprobados: {{ porcentajeReprobados }}%</span>
            </div>
            <div class="premium-progress-bar">
              <div class="progress-fill fill-green" :style="{ width: porcentajeAprobados + '%' }"></div>
              <div class="progress-fill fill-red" :style="{ width: porcentajeReprobados + '%' }"></div>
            </div>
          </div>
        </section>

      </div>
    </div>

    <!-- Pantalla vacía inicial -->
    <div v-else class="card-panel text-center-msg">
      <p class="text-muted">Por favor, seleccione un curso de la lista para ver su rendimiento correspondiente.</p>
    </div>

  </div>
</template>

<style scoped>
.subtitle-text { font-size: 0.9rem; color: #6b7280; margin-top: 0.2rem; }
.mb-4 { margin-bottom: 1.5rem; }

.card-panel {
  background: #fafafa;
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 12px;
  padding: 1.5rem;
  height: fit-content;
}
.panel-header {
  margin-bottom: 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  padding-bottom: 0.75rem;
}
.panel-header h4 { font-size: 1rem; font-weight: 600; color: var(--color-black); }

.selector-container {
  display: flex;
  gap: 1rem;
  width: 100%;
}
.full-width { width: 100%; }

select {
  background: var(--color-white);
  border: 1px solid rgba(0, 0, 0, 0.1);
  color: var(--color-black);
  padding: 0.75rem 1rem;
  border-radius: 8px;
  width: 100%;
  font-size: 0.9rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
select:focus {
  outline: none;
  border-color: #38bdf8;
  box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.15);
}
select:disabled { opacity: 0.5; cursor: not-allowed; }

.dashboard-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 1.25rem;
}

.metric-card {
  padding: 1.5rem 1.25rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
  transition: transform 0.2s, box-shadow 0.2s;
}
.metric-card:hover {
  transform: translateY(-2px);
}

.metric-icon {
  width: 46px;
  height: 46px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.06);
}
.metric-icon svg { width: 22px; height: 22px; }

.metric-info { display: flex; flex-direction: column; }
.metric-info .lbl { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.8; }
.metric-info .val { font-size: 1.2rem; font-weight: 700; margin-top: 0.15rem; }

.card-green {
  background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(34, 197, 94, 0.05));
  border: 1px solid rgba(34, 197, 94, 0.2);
  color: #4ade80;
}
.card-green .metric-icon { color: #4ade80; background: rgba(34, 197, 94, 0.1); }
.card-green .metric-info .val { color: var(--color-black); }

.card-red {
  background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.05));
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #f87171;
}
.card-red .metric-icon { color: #f87171; background: rgba(239, 68, 68, 0.1); }
.card-red .metric-info .val { color: var(--color-black); }

.card-blue {
  background: linear-gradient(135deg, rgba(56, 189, 248, 0.15), rgba(56, 189, 248, 0.05));
  border: 1px solid rgba(56, 189, 248, 0.2);
  color: #38bdf8;
}
.card-blue .metric-icon { color: #38bdf8; background: rgba(56, 189, 248, 0.1); }
.card-blue .metric-info .val { color: var(--color-black); }

.card-gray {
  background: linear-gradient(135deg, rgba(148, 163, 184, 0.15), rgba(148, 163, 184, 0.05));
  border: 1px solid rgba(148, 163, 184, 0.2);
  color: #cbd5e1;
}
.card-gray .metric-icon { color: #cbd5e1; background: rgba(148, 163, 184, 0.1); }
.card-gray .metric-info .val { color: var(--color-black); }

.search-style-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.sub-badge {
  font-size: 0.75rem;
  background: rgba(34, 197, 94, 0.15);
  color: #4ade80;
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-weight: 700;
  border: 1px solid rgba(34, 197, 94, 0.1);
}

.progress-section {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 0.5rem;
}
.progress-legend {
  display: flex;
  gap: 1.5rem;
}
.legend-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.8rem;
  font-weight: 600;
}
.legend-item::before {
  content: '';
  display: inline-block;
  width: 8px;
  height: 8px;
  border-radius: 50%;
}
.legend-green { color: #4ade80; }
.legend-green::before { background: #22c55e; }
.legend-red { color: #f87171; }
.legend-red::before { background: #ef4444; }

.premium-progress-bar {
  display: flex;
  height: 10px;
  background: #e5e7eb;
  border-radius: 9999px;
  overflow: hidden;
  border: 1px solid rgba(0, 0, 0, 0.04);
}
.progress-fill {
  height: 100%;
  transition: width 0.4s ease-out;
}
.fill-green { background: #22c55e; }
.fill-red { background: #ef4444; }

.spinner-container {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 4rem; gap: 1rem;
}
.loading-spinner {
  width: 32px; height: 32px;
  border: 3px solid rgba(56, 189, 248, 0.1);
  border-radius: 50%;
  border-top-color: #38bdf8;
  animation: spin 1s ease-in-out infinite;
}
.loading-text { font-size: 0.85rem; color: #6b7280; }
@keyframes spin { to { transform: rotate(360deg); } }

.alert-inline {
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
}
.alert-inline.error {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
}
.alert-inline.info {
  background: rgba(56, 189, 248, 0.08);
  color: #38bdf8;
  border: 1px solid rgba(56, 189, 248, 0.15);
}

.text-center-msg {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 4rem 2rem;
}
.text-muted { color: #6b7280; font-size: 0.9rem; text-align: center; }

.fade-in-view { animation: fadeIn 0.25s ease-out; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
