<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const inscripciones = ref([])
const searchQuery = ref('')

// La fecha puede llegar con distintos nombres según el backend;
// se usa el primero que exista (ajustar aquí si el campo real es otro).
function getFecha(item) {
  return item.fecha || item.fechaInscripcion || item.fecha_inscripcion || item.fechaAvance || null
}

function formatFecha(value) {
  if (!value) return 'Sin fecha registrada'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleDateString('es-BO', { year: 'numeric', month: 'short', day: '2-digit' })
}

const inscripcionesActivas = computed(() => {
  return inscripciones.value
    .filter(Boolean)
    .filter(item => {
      const estadoActivo = String(item.estado).toLowerCase() === 'activa'
      if (!estadoActivo) return false
      const q = searchQuery.value.toLowerCase()
      if (!q) return true
      return (
        (item.materia && item.materia.toLowerCase().includes(q)) ||
        (item.docente && item.docente.toLowerCase().includes(q)) ||
        (item.horario && item.horario.toLowerCase().includes(q)) ||
        (item.turno && item.turno.toLowerCase().includes(q))
      )
    })
})

async function loadInscripciones() {
  loading.value = true
  try {
    const { data: res } = await props.api.get('/estudiante/inscripciones')
    const payload = res.data ?? res
    inscripciones.value = payload?.data ?? payload ?? []
  } catch (e) {
    emit('message', { type: 'error', text: e.response?.data?.message || 'No pudimos cargar las inscripciones.' })
  } finally {
    loading.value = false
  }
}

onMounted(loadInscripciones)
</script>

<template>
  <div v-if="loading" class="notice">Cargando carga academica...</div>
  <div v-else class="carga-wrap">
    <p class="carga-desc">Materias en las que estás inscrito actualmente. Aquí puedes consultar tus horarios, docentes y el estado de cada inscripción.</p>

    <div class="carga-search-wrap">
      <i class="ti ti-search carga-search-icon"></i>
      <input
        v-model="searchQuery"
        class="carga-search"
        type="text"
        placeholder="Buscar por materia, docente u horario..."
      />
      <button v-if="searchQuery" class="carga-search-clear" @click="searchQuery = ''">
        <i class="ti ti-x"></i>
      </button>
    </div>

    <p class="carga-count">
      {{ inscripcionesActivas.length }} materia{{ inscripcionesActivas.length !== 1 ? 's' : '' }} inscrita{{ inscripcionesActivas.length !== 1 ? 's' : '' }}
    </p>

    <!-- Lista compacta, sin grilla -->
    <div class="carga-list">
      <article v-for="item in inscripcionesActivas" :key="item.idInscripcion" class="carga-row">
        <div class="carga-row-main">
          <span class="pill">{{ item.estado }}</span>
          <h2>{{ item.materia }}</h2>
        </div>
        <div class="carga-row-meta">
          <div class="carga-meta-item">
            <i class="ti ti-user"></i>
            <span>{{ item.docente || 'Sin asignar' }}</span>
          </div>
          <div class="carga-meta-item">
            <i class="ti ti-clock"></i>
            <span>{{ item.horario || item.turno || 'Sin horario' }}</span>
          </div>
          <div class="carga-meta-item">
            <i class="ti ti-calendar"></i>
            <span>{{ formatFecha(getFecha(item)) }}</span>
          </div>
        </div>
      </article>
      <p v-if="!inscripcionesActivas.length && !searchQuery" class="empty">No tienes materias inscritas activas.</p>
      <p v-if="!inscripcionesActivas.length && searchQuery" class="empty">No se encontraron resultados para "{{ searchQuery }}".</p>
    </div>
  </div>
</template>

<style scoped>
.carga-wrap { display: flex; flex-direction: column; gap: 0.6rem; }
.carga-desc { font-size: 0.82rem; color: #5b5c5e; margin: 0 0 0.25rem; }

.carga-search-wrap {
  position: relative; display: flex; align-items: center;
  max-width: 360px;
}
.carga-search-icon { position: absolute; left: 12px; font-size: 14px; color: #8c9f96; pointer-events: none; }
.carga-search {
  width: 100%; background: #fff;
  border: 1.5px solid #d0cfca; border-radius: 20px;
  padding: 8px 32px 8px 34px;
  font: inherit; font-size: 12px; color: #1a1a1a; outline: none;
  transition: border-color 0.15s;
}
.carga-search:focus { border-color: #4e615e; }
.carga-search::placeholder { color: #a0a0a0; }
.carga-search-clear {
  position: absolute; right: 10px;
  background: transparent; border: none; color: #8c9f96;
  cursor: pointer; font-size: 12px; display: flex; align-items: center;
}
.carga-search-clear:hover { color: #1a1a1a; }

.carga-count { margin: 0; font-size: 11px; font-weight: 600; color: #8c9f96; }

/* ── Lista compacta (NO grilla) ── */
.carga-list { display: flex; flex-direction: column; gap: 0.5rem; }

.carga-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  flex-wrap: wrap;
  padding: 0.75rem 1.1rem;
  background: #fff;
  border-radius: 12px;
  border: 1px solid rgba(0,0,0,0.05);
  box-shadow: 0 3px 10px rgba(0,0,0,0.02);
}

.carga-row-main { display: flex; align-items: center; gap: 0.65rem; min-width: 180px; }
.carga-row-main h2 { margin: 0; font-size: 0.95rem; font-family: 'Playfair Display', serif; color: #1a1a1a; }

.pill { border-radius: 999px; padding: 0.3rem 0.55rem; font-size: 10px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; background: #edf4f2; color: #697d7b; white-space: nowrap; flex-shrink: 0; }

.carga-row-meta { display: flex; gap: 1.25rem; flex-wrap: wrap; }
.carga-meta-item { display: flex; align-items: center; gap: 6px; font-size: 12px; color: #1a1a1a; white-space: nowrap; }
.carga-meta-item i { font-size: 14px; color: #8c9f96; }

.notice { padding: 0.85rem 1rem; border-radius: 16px; background: #edf4f2; color: #2b3d36; }
.empty { padding: 1rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 16px; color: #5b5c5e; text-align: center; }

@media (max-width: 600px) {
  .carga-row { flex-direction: column; align-items: flex-start; }
  .carga-row-meta { gap: 0.75rem; }
}
</style>