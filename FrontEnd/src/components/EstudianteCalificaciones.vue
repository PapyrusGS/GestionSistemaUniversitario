<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const notas = ref([])

// ── Filters ────────────────────────────────────────────────────────────────
const busqueda = ref('')
const semestreFiltro = ref('')
const periodoFiltro = ref('')

const semestresDisponibles = computed(() => {
  const s = new Set(notas.value.map(n => n.semestre).filter(v => v != null))
  return [...s].sort((a, b) => a - b)
})

const periodosDisponibles = computed(() => {
  const s = new Set(notas.value.map(n => n.periodo).filter(v => v != null && v !== ''))
  return [...s].sort((a, b) => {
    const numA = parseInt(a, 10)
    const numB = parseInt(b, 10)
    if (!isNaN(numA) && !isNaN(numB)) return numB - numA
    return String(a).localeCompare(String(b))
  })
})

const notasFiltradas = computed(() => {
  let list = notas.value

  if (busqueda.value.trim()) {
    const q = busqueda.value.trim().toLowerCase()
    list = list.filter(n =>
      n.materia?.toLowerCase().includes(q) ||
      n.docente?.toLowerCase().includes(q) ||
      n.periodo?.toLowerCase().includes(q)
    )
  }

  if (semestreFiltro.value !== '') {
    list = list.filter(n => n.semestre === Number(semestreFiltro.value))
  }

  if (periodoFiltro.value !== '') {
    list = list.filter(n => n.periodo === periodoFiltro.value)
  }

  return list
})

// ── Helpers ────────────────────────────────────────────────────────────────
function estadoClase(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado') return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  return 'pendiente'
}

function formatFecha(fecha) {
  if (!fecha) return '—'
  try {
    const d = new Date(fecha + 'T00:00:00')
    if (isNaN(d.getTime())) return fecha
    return d.toLocaleDateString('es-BO', { day: '2-digit', month: 'short', year: 'numeric' })
  } catch {
    return fecha
  }
}

// ── Load ───────────────────────────────────────────────────────────────────
async function loadNotas() {
  loading.value = true
  try {
    const { data: res } = await props.api.get('/estudiante/notas')
    const payload = res.data ?? res
    notas.value = payload?.data ?? payload ?? []
  } catch (e) {
    emit('message', { type: 'error', text: e.response?.data?.message || 'No pudimos cargar las calificaciones.' })
  } finally {
    loading.value = false
  }
}

onMounted(loadNotas)
</script>

<template>
  <div class="ec-root">

    <!-- Header -->
    <div class="ec-header">
      <h2>Mis Calificaciones</h2>
      <span class="ec-count">{{ notasFiltradas.length }} de {{ notas.length }} materia{{ notas.length !== 1 ? 's' : '' }}</span>
    </div>

    <!-- Filters row -->
    <div class="ec-filters">
      <div class="ec-search-wrap">
        <svg class="ec-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input
          v-model="busqueda"
          class="ec-search"
          type="text"
          placeholder="Buscar por materia, docente, periodo..."
        />
      </div>
      <select v-model="semestreFiltro" class="ec-select">
        <option value="">Todos los semestres</option>
        <option v-for="s in semestresDisponibles" :key="s" :value="String(s)">Semestre {{ s }}</option>
      </select>
      <select v-model="periodoFiltro" class="ec-select">
        <option value="">Todos los periodos</option>
        <option v-for="p in periodosDisponibles" :key="p" :value="p">{{ p }}</option>
      </select>
    </div>

    <!-- Grades -->
    <div v-if="loading" class="ec-loading">Cargando calificaciones...</div>

    <div v-else-if="notasFiltradas.length === 0" class="ec-empty">
      <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" class="ec-empty-icon"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
      <p v-if="busqueda || semestreFiltro || periodoFiltro">No hay resultados con los filtros actuales.</p>
      <p v-else>Aun no existen calificaciones registradas.</p>
    </div>

    <div v-else class="ec-grid">
      <div
        v-for="nota in notasFiltradas"
        :key="nota.idMateria + '-' + (nota.fechaRegistro || '')"
        class="ec-card"
      >
        <div class="ec-card-top">
          <span class="ec-materia">{{ nota.materia }}</span>
          <span class="ec-nota" :class="estadoClase(nota.estadoAcademico)">{{ Number(nota.nota).toFixed(1) }}</span>
        </div>
        <span class="ec-badge" :class="estadoClase(nota.estadoAcademico)">{{ nota.estadoAcademico }}</span>
        <div class="ec-meta">
          <span class="ec-meta-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            {{ nota.docente || '—' }}
          </span>
          <span class="ec-meta-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            {{ formatFecha(nota.fechaRegistro) }}
          </span>
          <span class="ec-meta-item">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a10 10 0 1010 10 10 10 0 00-10-10z"/><polyline points="12 6 12 12 16 14"/></svg>
            {{ nota.periodo }} &middot; Semestre {{ nota.semestre }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.ec-root {
  animation: ecFadeIn 0.25s ease-out;
}
@keyframes ecFadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: none; }
}

/* ── Header ──────────────────────────────────────────────────────────────── */
.ec-header {
  display: flex;
  align-items: baseline;
  gap: 0.75rem;
  margin-bottom: 0.75rem;
}
.ec-header h2 {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--color-black);
  margin: 0;
}
.ec-count {
  font-size: 0.75rem;
  color: #9ca3af;
  font-weight: 600;
}

/* ── Filters ─────────────────────────────────────────────────────────────── */
.ec-filters {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
  flex-wrap: wrap;
  align-items: center;
}
.ec-search-wrap {
  position: relative;
  flex: 1 1 220px;
  min-width: 160px;
}
.ec-search-icon {
  position: absolute;
  left: 0.6rem;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  pointer-events: none;
}
.ec-search {
  width: 100%;
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,0.1);
  border-radius: 8px;
  padding: 0.45rem 0.75rem 0.45rem 2rem;
  font-size: 0.82rem;
  color: var(--color-black);
  outline: none;
  transition: border-color 0.2s;
}
.ec-search:focus {
  border-color: #38bdf8;
  box-shadow: 0 0 0 2px rgba(56,189,248,0.12);
}
.ec-select {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,0.1);
  border-radius: 8px;
  padding: 0.45rem 2rem 0.45rem 0.65rem;
  font-size: 0.8rem;
  color: var(--color-black);
  outline: none;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.4rem center;
  background-size: 0.9rem;
  cursor: pointer;
  min-width: 130px;
  flex: 0 0 auto;
}
.ec-select:focus {
  border-color: #38bdf8;
  box-shadow: 0 0 0 2px rgba(56,189,248,0.12);
}

/* ── Grid ────────────────────────────────────────────────────────────────── */
.ec-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 0.55rem;
}
.ec-card {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,0.06);
  border-radius: 10px;
  padding: 0.75rem 0.85rem;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.ec-card:hover {
  border-color: rgba(56,189,248,0.2);
  box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
.ec-card-top {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 0.5rem;
}
.ec-materia {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--color-black);
  line-height: 1.3;
}
.ec-nota {
  font-size: 1rem;
  font-weight: 700;
  font-family: monospace;
  white-space: nowrap;
  padding: 0.05rem 0.4rem;
  border-radius: 5px;
}
.ec-nota.aprobada  { color: #22c55e; background: rgba(34,197,94,0.07); }
.ec-nota.reprobada { color: #ef4444; background: rgba(239,68,68,0.07); }
.ec-nota.pendiente { color: #9ca3af; background: #f3f4f6; }

.ec-badge {
  display: inline-block;
  align-self: flex-start;
  font-size: 0.62rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  padding: 0.12rem 0.45rem;
  border-radius: 999px;
}
.ec-badge.aprobada  { background: rgba(34,197,94,0.1);  color: #22c55e; }
.ec-badge.reprobada { background: rgba(239,68,68,0.1);  color: #ef4444; }
.ec-badge.pendiente { background: #f3f4f6; color: #9ca3af; }

.ec-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem 0.7rem;
  margin-top: 0.2rem;
}
.ec-meta-item {
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.7rem;
  color: #6b7280;
}
.ec-meta-item svg {
  opacity: 0.5;
  flex-shrink: 0;
}

/* ── States ──────────────────────────────────────────────────────────────── */
.ec-loading {
  padding: 0.7rem 1rem;
  border-radius: 8px;
  background: #f3f4f6;
  color: #6b7280;
  font-size: 0.85rem;
}
.ec-empty {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1.5rem;
  gap: 0.7rem;
  color: #9ca3af;
  border: 1px dashed rgba(0,0,0,0.08);
  border-radius: 12px;
}
.ec-empty-icon { opacity: 0.35; }
.ec-empty p { font-size: 0.85rem; text-align: center; max-width: 280px; }
</style>
