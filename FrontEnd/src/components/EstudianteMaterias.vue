<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const materias = ref([])
const enrollingId = ref(null)
const searchQuery = ref('')

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

// Una materia se considera inactiva solo si el backend manda explícitamente
// estado/activo en false. Si el campo no viene, se asume activa (no se rompe nada
// mientras se confirma el nombre exacto del campo en el backend).
function isMateriaActiva(materia) {
  if (materia.estado === false) return false
  if (materia.activo === false) return false
  if (materia.activa === false) return false
  if (materia?.curso?.estado === false) return false
  return true
}

const materiasActivas = computed(() => materias.value.filter(isMateriaActiva))

const filteredMaterias = computed(() => {
  const query = searchQuery.value.trim().toLowerCase()
  if (!query) return materiasActivas.value
  return materiasActivas.value.filter((materia) => {
    const haystack = [
      materia.materia,
      materia.docente,
      materia.regimen,
      materia.horario,
      materia.turno,
    ].filter(Boolean).join(' ').toLowerCase()
    return haystack.includes(query)
  })
})

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
  <section v-else class="materias-wrap">

    <div class="search-bar">
      <i class="ti ti-search"></i>
      <input
        v-model="searchQuery"
        type="text"
        placeholder="Buscar por materia, docente, régimen u horario..."
      />
      <button v-if="searchQuery" type="button" class="clear-btn" @click="searchQuery = ''" title="Limpiar búsqueda">
        <i class="ti ti-x"></i>
      </button>
    </div>

    <p class="result-count">
      {{ filteredMaterias.length }} materia{{ filteredMaterias.length !== 1 ? 's' : '' }} disponible{{ filteredMaterias.length !== 1 ? 's' : '' }}
    </p>

    <div class="card-grid">
      <article v-for="materia in filteredMaterias" :key="materia.idCursoMateria" class="subject-card">
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
      <p v-if="!filteredMaterias.length && !searchQuery" class="empty">No existen materias disponibles para inscripcion.</p>
      <p v-if="!filteredMaterias.length && searchQuery" class="empty">No se encontraron materias que coincidan con "{{ searchQuery }}".</p>
    </div>

  </section>
</template>

<style scoped>
.materias-wrap { display: flex; flex-direction: column; gap: 1rem; }

/* ── Buscador ── */
.search-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #fff;
  border: 1.5px solid #d0cfca;
  border-radius: 20px;
  padding: 0.65rem 1rem;
  transition: border-color .15s, box-shadow .15s;
}
.search-bar:focus-within { border-color: #697d7b; box-shadow: 0 0 0 3px rgba(105,125,123,.12); }
.search-bar i { font-size: 16px; color: #5b5c5e; flex-shrink: 0; }
.search-bar input {
  flex: 1; border: none; outline: none; background: transparent;
  font-family: inherit; font-size: 13px; color: #1a1a1a;
}
.search-bar input::placeholder { color: #9a9a98; }
.clear-btn {
  background: transparent; border: none; color: #5b5c5e; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
}
.clear-btn:hover { background: #f0f0ee; color: #1a1a1a; }

.result-count { margin: 0; font-size: 12px; font-weight: 600; color: #5b5c5e; }

/* ── Grilla: mínimo 4 columnas en pantallas amplias ── */
.card-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.9rem;
}
@media (max-width: 1280px) { .card-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 980px)  { .card-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 620px)  { .card-grid { grid-template-columns: 1fr; } }

.subject-card {
  display: flex;
  flex-direction: column;
  height: 100%;
  padding: 1.5rem;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.02);
  border: 1.5px solid #d9d8d2;
  transition: border-color .15s, box-shadow .15s;
}
.subject-card:hover {
  border-color: #697d7b;
  box-shadow: 0 10px 30px rgba(0,0,0,0.05);
}

.card-title-row { display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; }
.detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.75rem; margin-top: 1rem; }
.card-title-row h2 { margin: 0.45rem 0 0; font-size: 1.05rem; font-family: 'Playfair Display', serif; color: #1a1a1a; }
.card-title-row > strong { color: #697d7b; font-size: 1.35rem; white-space: nowrap; font-weight: 600; }
.detail-grid div { min-width: 0; padding: 0.75rem; border-radius: 12px; background: #f6f6f4; }
.detail-grid span { font-size: 11px; font-weight: 600; color: #8c9f96; text-transform: uppercase; letter-spacing: 0.05em; }
.detail-grid strong { display: block; margin-top: 0.25rem; overflow-wrap: anywhere; color: #1a1a1a; }
.pill { border-radius: 999px; padding: 0.35rem 0.6rem; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; background: #edf4f2; color: #697d7b; }
.primary {
  margin-top: auto;
  align-self: flex-start;
  border: 0;
  background: #697d7b;
  color: white;
  padding: 8px 14px;
  font-weight: 600;
  border-radius: 20px;
  cursor: pointer;
}
.primary:hover { background: #5b6e6c; }
.primary:disabled { cursor: not-allowed; opacity: 0.55; }
.notice { padding: 0.85rem 1rem; border-radius: 16px; background: #edf4f2; color: #2b3d36; }
.empty { grid-column: 1 / -1; padding: 1rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 16px; color: #5b5c5e; text-align: center; }
</style>