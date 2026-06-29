<script setup>
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api:  { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading  = ref(false)
const historial = ref([])

// Filtros
const filterEstado   = ref('')   // aprobada | reprobada | inscrita | pendiente | ''
const filterSemestre = ref('')   // número de semestre | ''
const searchQuery    = ref('')   // texto libre

function statusTone(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado')   return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  if (s === 'inscrita'  || s === 'inscrito')  return 'inscrita'
  return 'pendiente'
}

async function loadHistorial() {
  loading.value = true
  try {
    const { data: res } = await props.api.get('/estudiante/historial')
    const payload = res.data ?? res
    historial.value = payload?.data ?? payload ?? []
  } catch (e) {
    emit('message', { type: 'error', text: e.response?.data?.message || 'No pudimos cargar el historial.' })
  } finally {
    loading.value = false
  }
}

// Semestres disponibles para el selector de filtro
const semestresDisponibles = computed(() =>
  historial.value.map(p => p.semestre).sort((a, b) => a - b)
)

// Historial filtrado
const historialFiltrado = computed(() => {
  return historial.value
    .filter(p => !filterSemestre.value || String(p.semestre) === String(filterSemestre.value))
    .map(p => ({
      ...p,
      materias: p.materias.filter(m => {
        const toneOk    = !filterEstado.value || statusTone(m.estadoAcademico) === filterEstado.value
        const searchOk  = !searchQuery.value  || m.materia.toLowerCase().includes(searchQuery.value.toLowerCase())
        return toneOk && searchOk
      }),
    }))
    .filter(p => p.materias.length > 0)
})

// Estadísticas globales
const stats = computed(() => {
  const all = historial.value.flatMap(p => p.materias)
  return {
    total:     all.length,
    aprobadas: all.filter(m => statusTone(m.estadoAcademico) === 'aprobada').length,
    reprobadas:all.filter(m => statusTone(m.estadoAcademico) === 'reprobada').length,
    inscritas: all.filter(m => statusTone(m.estadoAcademico) === 'inscrita').length,
  }
})

const promedio = computed(() => {
  const notas = historial.value.flatMap(p => p.materias)
    .map(m => Number(m.nota))
    .filter(n => !isNaN(n) && n > 0)
  if (!notas.length) return '—'
  return (notas.reduce((a, b) => a + b, 0) / notas.length).toFixed(1)
})

function clearFilters() {
  filterEstado.value   = ''
  filterSemestre.value = ''
  searchQuery.value    = ''
}

const hayFiltros = computed(() =>
  filterEstado.value || filterSemestre.value || searchQuery.value
)

onMounted(loadHistorial)
</script>

<template>
  <div class="eh-root">

    <!-- ── Encabezado ─────────────────────────────────────────────── -->
    <div class="eh-header">
      <div class="eh-header-left">
        <h3 class="eh-title">Historial Académico</h3>
        <p class="eh-subtitle">Registro completo de tu trayectoria por semestre</p>
      </div>
      <button class="eh-btn-refresh" @click="loadHistorial" :disabled="loading" title="Actualizar">
        <i class="ti" :class="loading ? 'ti-loader-2 eh-spin' : 'ti-refresh'"></i>
      </button>
    </div>

    <!-- ── Tarjetas de resumen ────────────────────────────────────── -->
    <div v-if="!loading && historial.length" class="eh-stats">
      <div class="eh-stat-card">
        <span class="eh-stat-value">{{ stats.total }}</span>
        <span class="eh-stat-label">Total materias</span>
      </div>
      <div class="eh-stat-card eh-stat-card--aprobada">
        <span class="eh-stat-value">{{ stats.aprobadas }}</span>
        <span class="eh-stat-label">Aprobadas</span>
      </div>
      <div class="eh-stat-card eh-stat-card--reprobada">
        <span class="eh-stat-value">{{ stats.reprobadas }}</span>
        <span class="eh-stat-label">Reprobadas</span>
      </div>
      <div class="eh-stat-card eh-stat-card--promedio">
        <span class="eh-stat-value">{{ promedio }}</span>
        <span class="eh-stat-label">Promedio general</span>
      </div>
    </div>

    <!-- ── Barra de filtros ───────────────────────────────────────── -->
    <div v-if="!loading && historial.length" class="eh-filters">

      <!-- Búsqueda -->
      <div class="eh-search-wrap">
        <i class="ti ti-search eh-search-icon"></i>
        <input
          v-model="searchQuery"
          class="eh-search"
          type="text"
          placeholder="Buscar materia..."
        />
        <button v-if="searchQuery" class="eh-search-clear" @click="searchQuery = ''">
          <i class="ti ti-x"></i>
        </button>
      </div>

      <!-- Selector de semestre -->
      <div class="eh-select-wrap">
        <i class="ti ti-calendar eh-select-icon"></i>
        <select v-model="filterSemestre" class="eh-select">
          <option value="">Todos los semestres</option>
          <option v-for="s in semestresDisponibles" :key="s" :value="s">Semestre {{ s }}</option>
        </select>
      </div>

      <!-- Chips de estado -->
      <div class="eh-chips">
        <button
          v-for="op in [
            { value: '',           label: 'Todos'      },
            { value: 'aprobada',   label: 'Aprobadas'  },
            { value: 'reprobada',  label: 'Reprobadas' },
            { value: 'inscrita',   label: 'En curso'   },
            { value: 'pendiente',  label: 'Pendientes' },
          ]"
          :key="op.value"
          class="eh-chip"
          :class="{ 'eh-chip--active': filterEstado === op.value }"
          :data-tone="op.value || 'neutral'"
          @click="filterEstado = op.value"
        >
          {{ op.label }}
        </button>
      </div>

      <!-- Limpiar filtros -->
      <button v-if="hayFiltros" class="eh-btn-clear" @click="clearFilters">
        <i class="ti ti-filter-off"></i> Limpiar
      </button>

    </div>

    <!-- ── Estado de carga ────────────────────────────────────────── -->
    <div v-if="loading" class="eh-loading">
      <i class="ti ti-loader-2 eh-spin"></i>
      <span>Cargando historial académico...</span>
    </div>

    <!-- ── Sin resultados de filtro ──────────────────────────────── -->
    <div v-else-if="!loading && historial.length && !historialFiltrado.length" class="eh-empty">
      <i class="ti ti-filter-search eh-empty-icon"></i>
      <p class="eh-empty-title">Sin resultados</p>
      <p class="eh-empty-sub">Ninguna materia coincide con los filtros aplicados.</p>
      <button class="eh-btn-clear" @click="clearFilters">
        <i class="ti ti-filter-off"></i> Limpiar filtros
      </button>
    </div>

    <!-- ── Historial vacío ────────────────────────────────────────── -->
    <div v-else-if="!loading && !historial.length" class="eh-empty">
      <i class="ti ti-books-off eh-empty-icon"></i>
      <p class="eh-empty-title">Sin historial</p>
      <p class="eh-empty-sub">No existe historial académico registrado.</p>
    </div>

    <!-- ── Timeline de semestres ─────────────────────────────────── -->
    <div v-else class="eh-timeline">
      <div
        v-for="periodo in historialFiltrado"
        :key="periodo.semestre"
        class="eh-periodo"
      >
        <!-- Marcador de línea de tiempo -->
        <div class="eh-marker">
          <div class="eh-marker-dot"></div>
          <div class="eh-marker-line"></div>
        </div>

        <!-- Bloque del período -->
        <div class="eh-block">

          <!-- Cabecera del período -->
          <div class="eh-block-head">
            <div class="eh-block-head-left">
              <span class="eh-periodo-label">Semestre {{ periodo.semestre }}</span>
              <div class="eh-periodo-pills">
                <span class="eh-pill eh-pill--aprobada">
                  {{ periodo.materias.filter(m => statusTone(m.estadoAcademico) === 'aprobada').length }} aprobadas
                </span>
                <span
                  v-if="periodo.materias.filter(m => statusTone(m.estadoAcademico) === 'reprobada').length"
                  class="eh-pill eh-pill--reprobada"
                >
                  {{ periodo.materias.filter(m => statusTone(m.estadoAcademico) === 'reprobada').length }} reprobadas
                </span>
              </div>
            </div>
            <div class="eh-block-head-right">
              <span class="eh-periodo-prom">
                Prom.
                <strong>
                  {{
                    (() => {
                      const ns = periodo.materias.map(m => Number(m.nota)).filter(n => !isNaN(n) && n > 0)
                      return ns.length ? (ns.reduce((a,b)=>a+b,0)/ns.length).toFixed(1) : '—'
                    })()
                  }}
                </strong>
              </span>
            </div>
          </div>

          <!-- Tabla de materias del período -->
          <div class="eh-table-wrap">
            <table class="eh-table">
              <thead>
                <tr>
                  <th>Materia</th>
                  <th class="eh-th-center">Nota</th>
                  <th class="eh-th-center">Estado</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="materia in periodo.materias"
                  :key="materia.materia"
                  class="eh-tr"
                  :data-tone="statusTone(materia.estadoAcademico)"
                >
                  <td class="eh-td-name">
                    <span class="eh-tone-bar"></span>
                    {{ materia.materia }}
                  </td>
                  <td class="eh-td-nota">
                    {{ materia.nota !== null && materia.nota !== undefined ? materia.nota : '—' }}
                  </td>
                  <td class="eh-td-estado">
                    <span class="eh-badge" :data-tone="statusTone(materia.estadoAcademico)">
                      {{ statusTone(materia.estadoAcademico) }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>

  </div>
</template>

<style scoped>
/* ── Raíz ── */
.eh-root {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  width: 100%;
}

/* ── Encabezado ── */
.eh-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding-bottom: 1rem;
  border-bottom: 1px solid #d0cfca;
}
.eh-title   { margin: 0 0 3px; font-size: 1.25rem; font-weight: 700; color: #1a1a1a; }
.eh-subtitle { margin: 0; font-size: 0.8rem; color: #5b5c5e; }
.eh-btn-refresh {
  display: flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border-radius: 50%;
  border: 1.5px solid #d0cfca; background: #fff;
  color: #5b5c5e; font-size: 15px; cursor: pointer;
  transition: background 0.15s, color 0.15s;
  flex-shrink: 0;
}
.eh-btn-refresh:hover:not(:disabled) { background: #f4f4f2; color: #1a1a1a; }
.eh-btn-refresh:disabled { opacity: 0.5; }

/* ── Tarjetas de resumen ── */
.eh-stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.75rem;
}
@media (max-width: 600px) { .eh-stats { grid-template-columns: repeat(2, 1fr); } }

.eh-stat-card {
  background: #fafaf9;
  border: 1px solid #e8e8e5;
  border-radius: 12px;
  padding: 0.9rem 1rem;
  display: flex; flex-direction: column; gap: 2px;
}
.eh-stat-card--aprobada  { border-left: 3px solid #4e9e6b; }
.eh-stat-card--reprobada { border-left: 3px solid #b85c5c; }
.eh-stat-card--promedio  { border-left: 3px solid #4e615e; }
.eh-stat-value { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; line-height: 1; }
.eh-stat-label { font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.07em; color: #5b5c5e; }

/* ── Filtros ── */
.eh-filters {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 0.6rem;
  padding: 1rem 1.25rem;
  background: #fafaf9;
  border: 1px solid #e8e8e5;
  border-radius: 12px;
}

/* Búsqueda */
.eh-search-wrap {
  position: relative;
  display: flex; align-items: center;
  flex: 1; min-width: 160px; max-width: 260px;
}
.eh-search-icon { position: absolute; left: 10px; font-size: 14px; color: #8c9f96; pointer-events: none; }
.eh-search {
  width: 100%;
  background: #fff;
  border: 1.5px solid #d0cfca;
  border-radius: 20px;
  padding: 7px 30px 7px 30px;
  font: inherit; font-size: 12px;
  color: #1a1a1a; outline: none;
  transition: border-color 0.15s;
}
.eh-search:focus { border-color: #4e615e; }
.eh-search::placeholder { color: #a0a0a0; }
.eh-search-clear {
  position: absolute; right: 8px;
  background: transparent; border: none; color: #8c9f96;
  cursor: pointer; font-size: 12px; display: flex; align-items: center;
}
.eh-search-clear:hover { color: #1a1a1a; }

/* Select semestre */
.eh-select-wrap {
  position: relative;
  display: flex; align-items: center;
}
.eh-select-icon { position: absolute; left: 10px; font-size: 14px; color: #8c9f96; pointer-events: none; }
.eh-select {
  background: #fff;
  border: 1.5px solid #d0cfca;
  border-radius: 20px;
  padding: 7px 12px 7px 30px;
  font: inherit; font-size: 12px; font-weight: 600;
  color: #1a1a1a; outline: none; cursor: pointer;
  appearance: none;
  transition: border-color 0.15s;
}
.eh-select:focus { border-color: #4e615e; }

/* Chips de estado */
.eh-chips { display: flex; gap: 5px; flex-wrap: wrap; }
.eh-chip {
  padding: 5px 11px;
  border-radius: 20px;
  border: 1.5px solid #d0cfca;
  background: #fff;
  font: inherit; font-size: 11px; font-weight: 600;
  color: #5b5c5e; cursor: pointer;
  transition: background 0.15s, border-color 0.15s, color 0.15s;
}
.eh-chip:hover { background: #f4f4f2; color: #1a1a1a; }
.eh-chip--active                            { background: #f4f4f2; border-color: #4e615e; color: #1a1a1a; }
.eh-chip--active[data-tone="aprobada"]      { background: #edf7f1; border-color: #4e9e6b; color: #1a5235; }
.eh-chip--active[data-tone="reprobada"]     { background: #faf0f0; border-color: #b85c5c; color: #7a2424; }
.eh-chip--active[data-tone="inscrita"]      { background: #eef3fb; border-color: #4a7aad; color: #1e3a5f; }
.eh-chip--active[data-tone="pendiente"]     { background: #f6f6f4; border-color: #a0a0a0; color: #3a3a3a; }

/* Limpiar filtros */
.eh-btn-clear {
  display: inline-flex; align-items: center; gap: 5px;
  background: transparent; border: 1.5px solid #dca6a6;
  border-radius: 20px; padding: 6px 12px;
  font: inherit; font-size: 11px; font-weight: 600;
  color: #7a2424; cursor: pointer;
  transition: background 0.15s;
}
.eh-btn-clear:hover { background: #faf0f0; }

/* ── Estados globales ── */
.eh-loading {
  display: flex; align-items: center; justify-content: center; gap: 0.6rem;
  padding: 2rem; background: #edf4f2; border-radius: 12px;
  font-size: 0.85rem; font-weight: 600; color: #2b3d36;
}
.eh-loading i { font-size: 1.1rem; }

.eh-empty {
  display: flex; flex-direction: column; align-items: center; gap: 0.5rem;
  padding: 3rem 2rem;
  border: 2px dashed #d0cfca; border-radius: 14px;
  text-align: center;
}
.eh-empty-icon  { font-size: 2.5rem; color: #bfb09b; }
.eh-empty-title { margin: 0; font-size: 1rem; font-weight: 700; color: #5b5c5e; }
.eh-empty-sub   { margin: 0; font-size: 0.82rem; color: #8c9f96; }

/* ── Timeline ── */
.eh-timeline {
  display: flex;
  flex-direction: column;
  gap: 0;
  padding-left: 1.5rem;
}

.eh-periodo {
  position: relative;
  display: flex;
  gap: 1.25rem;
  padding-bottom: 2rem;
}
.eh-periodo:last-child { padding-bottom: 0; }

/* Marcador lateral */
.eh-marker {
  position: absolute;
  left: -1.5rem;
  top: 0;
  display: flex; flex-direction: column; align-items: center;
  width: 16px;
}
.eh-marker-dot {
  width: 14px; height: 14px;
  background: #fff;
  border: 3px solid #4e615e;
  border-radius: 50%;
  flex-shrink: 0;
  z-index: 2;
  margin-top: 1.1rem;
}
.eh-marker-line {
  width: 2px;
  flex: 1;
  background: #d0cfca;
  margin-top: 4px;
}
.eh-periodo:last-child .eh-marker-line { display: none; }

/* Bloque por período */
.eh-block {
  flex: 1;
  background: #fff;
  border: 1px solid #e8e8e5;
  border-radius: 14px;
  overflow: hidden;
}

/* Cabecera del bloque */
.eh-block-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.9rem 1.25rem;
  background: #fafaf9;
  border-bottom: 1px solid #e8e8e5;
  gap: 1rem;
  flex-wrap: wrap;
}
.eh-block-head-left  { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
.eh-block-head-right { flex-shrink: 0; }

.eh-periodo-label {
  font-family: 'Playfair Display', serif;
  font-size: 1rem;
  font-weight: 700;
  color: #1a1a1a;
}
.eh-periodo-pills { display: flex; gap: 5px; }
.eh-pill {
  padding: 2px 9px;
  border-radius: 20px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}
.eh-pill--aprobada  { background: #ddf0e6; color: #1a5235; }
.eh-pill--reprobada { background: #faf0f0; color: #7a2424; }

.eh-periodo-prom {
  font-size: 11px;
  font-weight: 600;
  color: #5b5c5e;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.eh-periodo-prom strong {
  color: #1a1a1a;
  font-size: 1rem;
  font-family: monospace;
}

/* ── Tabla interna ── */
.eh-table-wrap { overflow-x: auto; }
.eh-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
  text-align: left;
}
.eh-table th {
  padding: 0.55rem 1.25rem;
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #8c9f96;
  border-bottom: 1px solid #f0f0ee;
  background: #fdfdfd;
}
.eh-th-center { text-align: center; }

.eh-tr {
  transition: background 0.12s;
}
.eh-tr:hover { background: #fafaf9; }
.eh-tr:not(:last-child) td { border-bottom: 1px solid #f5f5f3; }

/* Barra de color lateral en la celda de nombre */
.eh-td-name {
  padding: 0.7rem 1.25rem;
  font-weight: 500;
  color: #1a1a1a;
  display: flex; align-items: center; gap: 10px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  max-width: 300px;
}
.eh-tone-bar {
  width: 4px; height: 16px; border-radius: 3px; flex-shrink: 0;
}
.eh-tr[data-tone="aprobada"]  .eh-tone-bar { background: #4e9e6b; }
.eh-tr[data-tone="reprobada"] .eh-tone-bar { background: #b85c5c; }
.eh-tr[data-tone="inscrita"]  .eh-tone-bar { background: #4a7aad; }
.eh-tr[data-tone="pendiente"] .eh-tone-bar { background: #c0c0c0; }

.eh-td-nota {
  padding: 0.7rem 1.25rem;
  text-align: center;
  font-family: monospace;
  font-weight: 700;
  font-size: 0.95rem;
  color: #1a1a1a;
}
.eh-td-estado {
  padding: 0.7rem 1.25rem;
  text-align: center;
}

/* Badges de estado */
.eh-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 6px;
  font-size: 9px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  min-width: 75px;
  text-align: center;
}
.eh-badge[data-tone="aprobada"]  { background: #edf7f1; color: #1a5235; }
.eh-badge[data-tone="reprobada"] { background: #faf0f0; color: #7a2424; }
.eh-badge[data-tone="inscrita"]  { background: #eef3fb; color: #1e3a5f; }
.eh-badge[data-tone="pendiente"] { background: #f6f6f4; color: #5b5c5e; }

/* ── Spin ── */
.eh-spin { animation: eh-rotate 0.8s linear infinite; }
@keyframes eh-rotate { to { transform: rotate(360deg); } }
</style>