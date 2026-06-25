<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const malla = ref([])

// HU-EST-09 Escenario 2: Normalización estricta de estados para cumplir el criterio de aceptación
function statusTone(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado') return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  if (s === 'inscrita' || s === 'inscrito') return 'inscrita'
  return 'pendiente'
}

// HU-EST-09 Escenario 1: Carga y organización por semestre desde el servicio API de la carrera
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

function statusIcon(status) {
  const tone = statusTone(status)
  if (tone === 'aprobada') return 'ph ph-check-circle'
  if (tone === 'reprobada') return 'ph ph-x-circle'
  if (tone === 'inscrita') return 'ph ph-pencil-simple'
  return 'ph ph-clock'
}

onMounted(loadMalla)
</script>

<template>
  <div v-if="loading" class="notice">Cargando malla curricular...</div>
  
  <!-- HU-EST-09 Escenario 1: Vista de materias organizada en semestres -->
  <div v-else class="malla-grid">
    <article v-for="semestre in malla" :key="semestre.semestre" class="semestre-card">
      <div class="semestre-header">
        <span class="semestre-num">
          Semestre {{ semestre.semestre }}
        </span>

        <span class="semestre-count">
          {{
            semestre.materias.filter(
              m => statusTone(m.estadoAcademico) === 'aprobada'
            ).length
          }}
          /
          {{ semestre.materias.length }} completadas
        </span>
      </div>
      
      <div class="materias-list">
        <div
          v-for="materia in semestre.materias"
          :key="materia.idMateria"
          class="materia-item"
          :data-tone="statusTone(materia.estadoAcademico)"
        >
          <div class="materia-info">
            <i
              :class="statusIcon(materia.estadoAcademico)"
              class="materia-icon"
            />

            <div class="materia-content">
              <span class="materia-nombre" :title="materia.materia">
                {{ materia.materia }}
              </span>

              <!-- HU-EST-09 Escenario 2: Texto homologado para identificar el estado de forma limpia -->
              <span class="materia-estado">
                {{ statusTone(materia.estadoAcademico) }}
              </span>
            </div>
          </div>

          <!-- HU-EST-09 Escenario 3: Muestra la calificación final registrada si existe -->
          <span
            v-if="materia.nota !== null && materia.nota !== undefined"
            class="materia-nota"
          >
            {{ materia.nota }}
          </span>
        </div>
      </div>
    </article>
    <p v-if="!malla.length" class="empty">No hay malla curricular asociada al estudiante.</p>
  </div>
</template>

<style scoped>
/* Grid Principal */
.malla-grid { 
  display: grid; 
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); 
  gap: 1.5rem; 
  align-items: start;
}

/* Contenedor del Semestre */
.semestre-card { 
  background: #fdfdfd; 
  border-radius: 16px; 
  border: 1px solid rgba(0,0,0,0.08); 
  box-shadow: 0 4px 12px rgba(0,0,0,0.01); 
  overflow: hidden; 
}

.semestre-header { 
  padding: 1rem 1.25rem; 
  background: #f6f6f4; 
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
  border-bottom: 1px solid rgba(0,0,0,0.06); 
}

.semestre-num { 
  font-family: 'Playfair Display', serif; 
  font-size: 1.1rem; 
  font-weight: 700; 
  color: #1a1a1a; 
}

.semestre-count { 
  font-size: 11px; 
  font-weight: 600; 
  color: #8c9f96; 
  text-transform: uppercase; 
  letter-spacing: 0.05em; 
}

/* Lista de Materias */
.materias-list { 
  padding: 1rem; 
  display: grid; 
  gap: 0.75rem; 
}

/* Tarjeta Individual de Materia (Card) */
.materia-item { 
  position: relative;
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
  padding: 0.85rem 1rem 0.85rem 1.25rem; 
  background: #ffffff;
  border-radius: 12px; 
  border: 1px solid rgba(0, 0, 0, 0.06);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
  gap: 0.75rem; 
  overflow: hidden;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.materia-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.04);
}

/* Barra lateral de estado */
.materia-item::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0;
  bottom: 0;
  width: 5px;
}

.materia-info { 
  display: flex; 
  align-items: center; 
  gap: 0.75rem; 
  min-width: 0; 
}

.materia-content { 
  display: flex; 
  flex-direction: column; 
  gap: 2px; 
  min-width: 0;
}

.materia-nombre { 
  font-size: 0.85rem; 
  font-weight: 600; 
  color: #1a1a1a; 
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.materia-icon { 
  font-size: 1.25rem; 
  flex-shrink: 0;
}

.materia-estado { 
  font-size: 10px; 
  font-weight: 700; 
  text-transform: uppercase; 
  letter-spacing: .04em; 
}

.materia-nota { 
  font-size: .8rem; 
  font-weight: 700; 
  min-width: 36px; 
  text-align: center; 
  background: #f6f6f4; 
  padding: .25rem .5rem; 
  border-radius: 6px; 
  border: 1px solid rgba(0,0,0,0.04);
  color: #1a1a1a;
  flex-shrink: 0;
}

/* Variaciones por Estado (Bordes laterales, Iconos y Textos) */
.materia-item[data-tone='aprobada']::before { background: #2b3d36; }
.materia-item[data-tone='aprobada'] .materia-icon { color: #2b3d36; }
.materia-item[data-tone='aprobada'] .materia-estado { color: #2b3d36; }

.materia-item[data-tone='reprobada']::before { background: #7a2424; }
.materia-item[data-tone='reprobada'] .materia-icon { color: #7a2424; }
.materia-item[data-tone='reprobada'] .materia-estado { color: #7a2424; }

.materia-item[data-tone='inscrita']::before { background: #4a6b6c; }
.materia-item[data-tone='inscrita'] .materia-icon { color: #4a6b6c; }
.materia-item[data-tone='inscrita'] .materia-estado { color: #4a6b6c; }

.materia-item[data-tone='pendiente']::before { background: #b5b5b5; }
.materia-item[data-tone='pendiente'] .materia-icon { color: #8c8c8c; }
.materia-item[data-tone='pendiente'] .materia-estado { color: #8c8c8c; }

/* Estados globales (Cargando / Vacío) */
.notice { padding: 1rem 1.25rem; border-radius: 16px; background: #edf4f2; color: #2b3d36; font-size: 0.9rem; text-align: center; }
.empty { padding: 2rem; border: 1px dashed rgba(0,0,0,0.12); border-radius: 16px; color: #5b5c5e; text-align: center; font-size: 0.9rem; }
</style>