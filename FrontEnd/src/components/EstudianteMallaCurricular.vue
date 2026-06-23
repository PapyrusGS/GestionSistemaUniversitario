<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const malla = ref([])

function statusTone(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado') return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  if (s === 'inscrita' || s === 'inscrito') return 'inscrita'
  return 'pendiente'
}

async function loadMalla() {
  loading.value = true
  try {
    const { data: res } = await props.api.get('/estudiante/malla')
    const payload = res.data ?? res
    malla.value = payload?.data ?? payload ?? []
  } catch (e) {
    emit('message', { type: 'error', text: e.response?.data?.message || 'No pudimos cargar la malla curricular.' })
  } finally {
    loading.value = false
  }
}

onMounted(loadMalla)
</script>

<template>
  <div v-if="loading" class="notice">Cargando malla curricular...</div>
  <div v-else class="malla-grid">
    <article v-for="semestre in malla" :key="semestre.semestre" class="semestre-card">
      <div class="semestre-header">
        <span class="semestre-num">Semestre {{ semestre.semestre }}</span>
        <span class="semestre-count">{{ semestre.materias.length }} materias</span>
      </div>
      <div class="materias-list">
        <div v-for="materia in semestre.materias" :key="materia.idMateria" class="materia-item" :data-tone="statusTone(materia.estadoAcademico)">
          <div class="materia-info">
            <span class="materia-nombre">{{ materia.materia }}</span>
            <span class="materia-nota" v-if="materia.nota !== null && materia.nota !== undefined">{{ materia.nota }}</span>
          </div>
          <span class="materia-estado">{{ materia.estadoAcademico }}</span>
        </div>
      </div>
    </article>
    <p v-if="!malla.length" class="empty">No hay malla curricular asociada al estudiante.</p>
  </div>
</template>

<style scoped>
.malla-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1rem; }
.semestre-card { background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.02); overflow: hidden; }
.semestre-header { padding: 1rem 1.25rem; background: #f6f6f4; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(0,0,0,0.05); }
.semestre-num { font-family: 'Playfair Display', serif; font-size: 1.1rem; font-weight: 700; color: #1a1a1a; }
.semestre-count { font-size: 11px; font-weight: 600; color: #8c9f96; text-transform: uppercase; letter-spacing: 0.05em; }
.materias-list { padding: 0.5rem; display: grid; gap: 0.4rem; }
.materia-item { display: flex; justify-content: space-between; align-items: center; padding: 0.65rem 0.75rem; border-radius: 12px; gap: 0.5rem; }
.materia-info { display: flex; align-items: center; gap: 0.5rem; min-width: 0; }
.materia-nombre { font-size: 0.85rem; font-weight: 500; color: #1a1a1a; }
.materia-nota { font-size: 0.8rem; font-weight: 700; color: inherit; background: rgba(255,255,255,0.6); padding: 0.15rem 0.5rem; border-radius: 6px; }
.materia-estado { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap; padding: 0.25rem 0.5rem; border-radius: 999px; background: rgba(255,255,255,0.6); }
.materia-item[data-tone='aprobada'] { background: #edf4f2; } .materia-item[data-tone='aprobada'] .materia-estado { color: #2b3d36; }
.materia-item[data-tone='reprobada'] { background: #faf0f0; } .materia-item[data-tone='reprobada'] .materia-estado { color: #7a2424; }
.materia-item[data-tone='inscrita'] { background: #edf4f2; } .materia-item[data-tone='inscrita'] .materia-estado { color: #697d7b; }
.materia-item[data-tone='pendiente'] { background: #f6f6f4; } .materia-item[data-tone='pendiente'] .materia-estado { color: #5b5c5e; }
.notice { padding: 0.85rem 1rem; border-radius: 16px; background: #edf4f2; color: #2b3d36; }
.empty { padding: 1rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 16px; color: #5b5c5e; text-align: center; }
</style>
