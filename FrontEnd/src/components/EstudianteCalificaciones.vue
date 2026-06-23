<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const notas = ref([])

function statusTone(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado') return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  if (s === 'inscrita' || s === 'inscrito') return 'inscrita'
  return 'pendiente'
}

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
  <div v-if="loading" class="notice">Cargando calificaciones...</div>
  <section v-else class="grade-grid">
    <article v-for="nota in notas" :key="`${nota.materia}-${nota.fechaRegistro}`" class="grade-card">
      <div>
        <span class="status" :data-tone="statusTone(nota.estadoAcademico)">{{ nota.estadoAcademico }}</span>
        <h2>{{ nota.materia }}</h2>
      </div>
      <strong>{{ nota.nota }}</strong>
    </article>
    <p v-if="!notas.length" class="empty">Aun no existen calificaciones registradas.</p>
  </section>
</template>

<style scoped>
.grade-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 0.9rem; }
.grade-card { display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; padding: 1.5rem; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05); }
.grade-card h2 { margin: 0.45rem 0 0; font-size: 1.05rem; font-family: 'Playfair Display', serif; color: #1a1a1a; }
.grade-card > strong { color: #697d7b; font-size: 1.35rem; white-space: nowrap; font-weight: 600; }
.status { border-radius: 999px; padding: 0.35rem 0.6rem; font-size: 11px; font-style: normal; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; }
[data-tone='aprobada'] { background: #edf4f2; color: #2b3d36; }
[data-tone='reprobada'] { background: #faf0f0; color: #7a2424; }
[data-tone='inscrita'] { background: #edf4f2; color: #697d7b; }
[data-tone='pendiente'] { background: #f6f6f4; color: #5b5c5e; }
.notice { padding: 0.85rem 1rem; border-radius: 16px; background: #edf4f2; color: #2b3d36; }
.empty { padding: 1rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 16px; color: #5b5c5e; text-align: center; }
@media (max-width: 700px) { .grade-card { flex-direction: column; } }
</style>