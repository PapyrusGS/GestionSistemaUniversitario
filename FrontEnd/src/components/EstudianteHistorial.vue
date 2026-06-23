<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const historial = ref([])

function statusTone(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado') return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  if (s === 'inscrita' || s === 'inscrito') return 'inscrita'
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

onMounted(loadHistorial)
</script>

<template>
  <div v-if="loading" class="notice">Cargando historial academico...</div>
  <div v-else class="historial-timeline">
    <article v-for="periodo in historial" :key="periodo.semestre" class="periodo-block">
      <div class="periodo-head">
        <span class="periodo-tag">Semestre {{ periodo.semestre }}</span>
        <span class="periodo-stats">
          {{ periodo.materias.filter(m => statusTone(m.estadoAcademico) === 'aprobada').length }} aprobadas
          · {{ periodo.materias.filter(m => statusTone(m.estadoAcademico) === 'reprobada').length }} reprobadas
        </span>
      </div>
      <div class="periodo-body">
        <div v-for="materia in periodo.materias" :key="materia.materia" class="materia-row" :data-tone="statusTone(materia.estadoAcademico)">
          <span class="materia-name">{{ materia.materia }}</span>
          <span class="materia-score">{{ materia.nota ?? '—' }}</span>
          <span class="materia-badge">{{ materia.estadoAcademico }}</span>
        </div>
      </div>
    </article>
    <p v-if="!historial.length" class="empty">No existe historial academico para mostrar.</p>
  </div>
</template>

<style scoped>
.historial-timeline { display: grid; gap: 1rem; }
.periodo-block { background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; }
.periodo-head { padding: 1rem 1.25rem; background: #f6f6f4; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.05); flex-wrap: wrap; gap: 0.5rem; }
.periodo-tag { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 700; color: #1a1a1a; }
.periodo-stats { font-size: 11px; font-weight: 600; color: #8c9f96; text-transform: uppercase; letter-spacing: 0.04em; }
.periodo-body { padding: 0.5rem; display: grid; gap: 0.35rem; }
.materia-row { display: grid; grid-template-columns: 1fr auto auto; gap: 1rem; align-items: center; padding: 0.6rem 0.75rem; border-radius: 12px; }
.materia-name { font-size: 0.85rem; font-weight: 500; color: #1a1a1a; }
.materia-score { font-weight: 700; font-size: 0.9rem; color: #1a1a1a; min-width: 2rem; text-align: center; }
.materia-badge { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; padding: 0.25rem 0.5rem; border-radius: 999px; background: rgba(255,255,255,0.6); }
.materia-row[data-tone='aprobada'] { background: #edf4f2; } .materia-row[data-tone='aprobada'] .materia-badge { color: #2b3d36; }
.materia-row[data-tone='reprobada'] { background: #faf0f0; } .materia-row[data-tone='reprobada'] .materia-badge { color: #7a2424; }
.materia-row[data-tone='inscrita'] { background: #edf4f2; } .materia-row[data-tone='inscrita'] .materia-badge { color: #697d7b; }
.materia-row[data-tone='pendiente'] { background: #f6f6f4; } .materia-row[data-tone='pendiente'] .materia-badge { color: #5b5c5e; }
.notice { padding: 0.85rem 1rem; border-radius: 16px; background: #edf4f2; color: #2b3d36; }
.empty { padding: 1rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 16px; color: #5b5c5e; text-align: center; }
</style>
