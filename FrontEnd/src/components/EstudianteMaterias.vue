<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const materias = ref([])
const enrollingId = ref(null)

async function loadMaterias() {
  loading.value = true
  try {
    const { data: res } = await props.api.get('/estudiante/materias-disponibles')
    const payload = res.data ?? res
    materias.value = payload?.data ?? payload ?? []
  } catch (e) {
    emit('message', { type: 'error', text: e.response?.data?.message || 'No pudimos cargar las materias.' })
  } finally {
    loading.value = false
  }
}

async function inscribir(materia) {
  enrollingId.value = materia.idCursoMateria
  try {
    const { data: res } = await props.api.post('/estudiante/inscribir', { idCursoMateria: materia.idCursoMateria })
    const result = res.data ?? res
    emit('message', { type: 'success', text: result.message || 'Inscripcion realizada correctamente.' })
    materias.value = materias.value.filter((item) => item.idCursoMateria !== materia.idCursoMateria)
  } catch (e) {
    emit('message', { type: 'error', text: e.response?.data?.message || 'No pudimos completar la inscripcion.' })
  } finally {
    enrollingId.value = null
  }
}

onMounted(loadMaterias)
</script>

<template>
  <div v-if="loading" class="notice">Cargando materias disponibles...</div>
  <section v-else class="card-grid">
    <article v-for="materia in materias" :key="materia.idCursoMateria" class="subject-card">
      <div class="card-title-row">
        <div>
          <span class="pill">{{ materia.regimen }}</span>
          <h2>{{ materia.materia }}</h2>
        </div>
        <strong>{{ materia.cuposDisponibles }} cupos</strong>
      </div>
      <div class="detail-grid">
        <div><span>Docente</span><strong>{{ materia.docente }}</strong></div>
        <div><span>Horario</span><strong>{{ materia.horario || materia.turno }}</strong></div>
        <div><span>Prerrequisito</span><strong>{{ materia.prerrequisito || 'Sin prerrequisito' }}</strong></div>
        <div><span>Cupo total</span><strong>{{ materia.cupoTotal }}</strong></div>
      </div>
      <button class="primary" type="button" :disabled="!materia.puedeInscribirse || enrollingId === materia.idCursoMateria" @click="inscribir(materia)">
        {{ enrollingId === materia.idCursoMateria ? 'Inscribiendo...' : 'Inscribirme' }}
      </button>
    </article>
    <p v-if="!materias.length" class="empty">No existen materias disponibles para inscripcion.</p>
  </section>
</template>

<style scoped>
.card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(310px, 1fr)); gap: 0.9rem; }
.subject-card { display: grid; gap: 1rem; padding: 1.5rem; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05); }
.card-title-row { display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; }
.card-title-row h2 { margin: 0.45rem 0 0; font-size: 1.05rem; font-family: 'Playfair Display', serif; color: #1a1a1a; }
.card-title-row > strong { color: #697d7b; font-size: 1.35rem; white-space: nowrap; font-weight: 600; }
.detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; }
.detail-grid div { min-width: 0; padding: 0.75rem; border-radius: 12px; background: #f6f6f4; }
.detail-grid span { font-size: 11px; font-weight: 600; color: #8c9f96; text-transform: uppercase; letter-spacing: 0.05em; }
.detail-grid strong { display: block; margin-top: 0.25rem; overflow-wrap: anywhere; color: #1a1a1a; }
.pill { border-radius: 999px; padding: 0.35rem 0.6rem; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; background: #edf4f2; color: #697d7b; }
.primary { border: 0; background: #697d7b; color: white; padding: 8px 14px; font-weight: 600; border-radius: 20px; cursor: pointer; }
.primary:hover { background: #5b6e6c; }
.primary:disabled { cursor: not-allowed; opacity: 0.55; }
.notice { padding: 0.85rem 1rem; border-radius: 16px; background: #edf4f2; color: #2b3d36; }
.empty { padding: 1rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 16px; color: #5b5c5e; text-align: center; }
@media (max-width: 700px) { .card-title-row { flex-direction: column; } .detail-grid { grid-template-columns: 1fr; } }
</style>
