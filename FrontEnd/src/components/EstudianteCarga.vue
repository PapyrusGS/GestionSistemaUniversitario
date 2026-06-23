<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const inscripciones = ref([])

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
  <div v-else>
    <p class="carga-desc">Materias en las que estás inscrito actualmente. Aquí puedes consultar tus horarios, docentes y el estado de cada inscripción.</p>
    <div class="carga-list">
      <article v-for="item in inscripciones.filter(Boolean)" :key="item.idInscripcion" class="carga-card">
        <div class="carga-top">
          <span class="pill">{{ item.estado }}</span>
          <h2>{{ item.materia }}</h2>
        </div>
        <div class="carga-meta">
          <div class="carga-meta-item">
            <span>Docente</span>
            <strong>{{ item.docente }}</strong>
          </div>
          <div class="carga-meta-item">
            <span>Horario</span>
            <strong>{{ item.horario || item.turno }}</strong>
          </div>
        </div>
      </article>
      <p v-if="!inscripciones.length" class="empty">No tienes materias inscritas activas.</p>
    </div>
  </div>
</template>

<style scoped>
.carga-desc { font-size: 0.85rem; color: #5b5c5e; margin: 0 0 1rem; }
.carga-list { display: grid; gap: 0.9rem; }
.carga-card { padding: 1.25rem; background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.02); display: grid; gap: 1rem; }
.carga-top { display: flex; align-items: center; gap: 0.75rem; }
.carga-top h2 { margin: 0; font-size: 1.05rem; font-family: 'Playfair Display', serif; color: #1a1a1a; }
.pill { border-radius: 999px; padding: 0.35rem 0.6rem; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; background: #edf4f2; color: #697d7b; white-space: nowrap; }
.carga-meta { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.carga-meta-item { min-width: 0; padding: 0.65rem 0.85rem; border-radius: 12px; background: #f6f6f4; flex: 1; }
.carga-meta-item span { font-size: 11px; font-weight: 600; color: #8c9f96; text-transform: uppercase; letter-spacing: 0.05em; }
.carga-meta-item strong { display: block; margin-top: 0.2rem; color: #1a1a1a; }
.notice { padding: 0.85rem 1rem; border-radius: 16px; background: #edf4f2; color: #2b3d36; }
.empty { padding: 1rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 16px; color: #5b5c5e; text-align: center; }
</style>
