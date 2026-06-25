<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const historial = ref([])

// HU-EST-08 Escenario 1: Normalización estricta para identificar los estados requeridos
function statusTone(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado') return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  if (s === 'inscrita' || s === 'inscrito') return 'inscrita'
  return 'pendiente'
}

// HU-EST-08 Escenario 1 y 2: Carga los registros del estudiante agrupados por periodo académico
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
  <div v-if="loading" class="notice">Cargando historial académico...</div>
  
  <div v-else class="historial-timeline">
    <div v-for="periodo in historial" :key="periodo.semestre" class="periodo-wrapper">
      
      <div class="periodo-marker">
        <div class="marker-dot"></div>
      </div>

      <article class="periodo-block">
        <div class="periodo-head">
          <span class="periodo-tag">Semestre {{ periodo.semestre }}</span>
          <span class="periodo-stats">
            {{ periodo.materias.filter(m => statusTone(m.estadoAcademico) === 'aprobada').length }} aprobadas
            · {{ periodo.materias.filter(m => statusTone(m.estadoAcademico) === 'reprobada').length }} reprobadas
          </span>
        </div>
        
        <div class="periodo-body">
          <div 
            v-for="materia in periodo.materias" 
            :key="materia.materia" 
            class="materia-row" 
            :data-tone="statusTone(materia.estadoAcademico)"
          >
            <span class="materia-name">{{ materia.materia }}</span>
            
            <div class="materia-meta">
              <span class="materia-score">
                {{ materia.nota !== null && materia.nota !== undefined ? materia.nota : '—' }}
              </span>
              
              <span class="materia-badge">{{ statusTone(materia.estadoAcademico) }}</span>
            </div>
          </div>
        </div>
      </article>

    </div>
    
    <p v-if="!historial.length" class="empty">No existe historial académico para mostrar.</p>
  </div>
</template>

<style scoped>
/* Contenedor Línea de Tiempo Activa */
.historial-timeline { 
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 2rem; 
  padding-left: 1.5rem;
  /* Línea vertical de fondo */
  background: linear-gradient(to bottom, rgba(0,0,0,0.06) 95%, transparent 100%);
  background-size: 2px 100%;
  background-repeat: no-repeat;
  background-position: 7px 10px;
}

/* Envoltorio por bloque de período */
.periodo-wrapper {
  position: relative;
  display: flex;
  align-items: flex-start;
}

/* Marcador de línea de tiempo */
.periodo-marker {
  position: absolute;
  left: -1.5rem;
  top: 1.25rem;
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2;
}

.marker-dot {
  width: 10px;
  height: 10px;
  background: #ffffff;
  border: 3px solid #8c9f96;
  border-radius: 50%;
}

/* Bloque del período (Modo lista compacta) */
.periodo-block { 
  flex: 1;
  background: #ffffff; 
  border-radius: 12px; 
  border: 1px solid rgba(0,0,0,0.06); 
  box-shadow: 0 2px 8px rgba(0,0,0,0.01);
  overflow: hidden; 
}

.periodo-head { 
  padding: 0.85rem 1.25rem; 
  background: #fdfdfd; 
  display: flex; 
  justify-content: space-between; 
  align-items: center; 
  border-bottom: 1px solid rgba(0,0,0,0.05); 
  flex-wrap: wrap; 
  gap: 0.5rem; 
}

.periodo-tag { 
  font-family: 'Playfair Display', serif; 
  font-size: 1rem; 
  font-weight: 700; 
  color: #1a1a1a; 
}

.periodo-stats { 
  font-size: 11px; 
  font-weight: 600; 
  color: #8c9f96; 
  text-transform: uppercase; 
  letter-spacing: 0.04em; 
}

.periodo-body { 
  padding: 0.25rem 0; 
  display: flex;
  flex-direction: column;
}

/* Fila de registro académico (Estilo bitácora / tabla) */
.materia-row { 
  display: flex; 
  justify-content: space-between;
  align-items: center; 
  padding: 0.75rem 1.25rem; 
  gap: 1rem;
  border-bottom: 1px solid rgba(0,0,0,0.03);
}

.materia-row:last-child {
  border-bottom: none;
}

.materia-row:hover {
  background: #fbfbfa;
}

.materia-name { 
  font-size: 0.85rem; 
  font-weight: 500; 
  color: #1a1a1a; 
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.materia-meta {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  flex-shrink: 0;
}

.materia-score { 
  font-family: monospace;
  font-weight: 700; 
  font-size: 0.95rem; 
  color: #1a1a1a; 
  width: 2rem; 
  text-align: right; 
}

/* Píldoras sólidas a la derecha */
.materia-badge { 
  font-size: 9px; 
  font-weight: 700; 
  text-transform: uppercase; 
  letter-spacing: 0.05em; 
  padding: 0.2rem 0.6rem; 
  border-radius: 6px; 
  text-align: center;
  min-width: 80px;
}

/* Estilos de Insignias según el estado */
.materia-row[data-tone='aprobada'] .materia-badge { background: #edf4f2; color: #2b3d36; }
.materia-row[data-tone='reprobada'] .materia-badge { background: #faf0f0; color: #7a2424; }
.materia-row[data-tone='inscrita'] .materia-badge { background: #eef3f4; color: #4a6b6c; }
.materia-row[data-tone='pendiente'] .materia-badge { background: #f6f6f4; color: #7a7a7a; }

/* Estados globales (Cargando / Vacío) */
.notice { padding: 1rem; border-radius: 12px; background: #edf4f2; color: #2b3d36; text-align: center; font-size: 0.9rem; }
.empty { padding: 2rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 12px; color: #5b5c5e; text-align: center; font-size: 0.9rem; }
</style>