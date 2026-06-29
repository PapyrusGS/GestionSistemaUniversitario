<script setup>
import { ref, onMounted, computed } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api:  { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const malla   = ref([])

const progreso = computed(() => {
  if (!malla.value.length) return { pct: 0, aprobadas: 0, total: 0 }
  const total = malla.value.reduce((c, s) => c + s.materias.length, 0)
  const aprobadas = malla.value.reduce(
    (c, s) => c + s.materias.filter(m => statusTone(m.estadoAcademico) === 'aprobada').length, 0
  )
  return {
    pct: total ? Math.round((aprobadas / total) * 100) : 0,
    aprobadas,
    total,
  }
})

function statusTone(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado')   return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  if (s === 'inscrita'  || s === 'inscrito')  return 'inscrita'
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


function toneConfig(tone) {
  return {
    aprobada:  { icon: 'ti-circle-check',  label: 'Aprobada',  cls: 'mc-tone--aprobada'  },
    reprobada: { icon: 'ti-circle-x',      label: 'Reprobada', cls: 'mc-tone--reprobada' },
    inscrita:  { icon: 'ti-pencil',        label: 'En curso',  cls: 'mc-tone--inscrita'  },
    pendiente: { icon: 'ti-clock',         label: 'Pendiente', cls: 'mc-tone--pendiente' },
  }[tone] ?? { icon: 'ti-clock', label: 'Pendiente', cls: 'mc-tone--pendiente' }
}

onMounted(loadMalla)
</script>

<template>
  <div class="mc-root">

    <!-- ── Encabezado ── -->
    <div class="mc-header">
      <div>
        <h3 class="mc-title">Malla Curricular</h3>
        <p class="mc-subtitle">Vista global de tu plan de estudios y avance por semestre</p>
      </div>
      <button class="mc-btn-refresh" @click="loadMalla" :disabled="loading" title="Actualizar">
        <i class="ti" :class="loading ? 'ti-loader-2 mc-spin' : 'ti-refresh'"></i>
      </button>
    </div>

      <!-- Leyenda -->
      <div class="mc-legend">
        <span class="mc-legend-item mc-legend--aprobada"><i class="ti ti-circle-check"></i> Aprobada</span>
        <span class="mc-legend-item mc-legend--reprobada"><i class="ti ti-circle-x"></i> Reprobada</span>
        <span class="mc-legend-item mc-legend--inscrita"><i class="ti ti-pencil"></i> En curso</span>
        <span class="mc-legend-item mc-legend--pendiente"><i class="ti ti-clock"></i> Pendiente</span>
      </div>
    </div>

    <!-- ── Cargando ── -->
    <div v-if="loading" class="mc-loading">
      <i class="ti ti-loader-2 mc-spin"></i>
      <span>Cargando malla curricular...</span>
    </div>

    <!-- ── Vacío ── -->
    <div v-else-if="!malla.length" class="mc-empty">
      <i class="ti ti-layout-grid-remove mc-empty-icon"></i>
      <p class="mc-empty-title">Sin malla curricular</p>
      <p class="mc-empty-sub">No hay malla asociada a tu carrera.</p>
    </div>

    <!-- ── Grid de semestres ── -->
    <div v-else class="mc-grid">
      <article v-for="semestre in malla" :key="semestre.semestre" class="mc-semestre">

        <!-- Cabecera del semestre -->
        <div class="mc-sem-head">
          <div class="mc-sem-num">
            <span class="mc-sem-num-label">{{ semestre.semestre }}</span>
          </div>
          <div class="mc-sem-info">
            <span class="mc-sem-title">Semestre {{ semestre.semestre }}</span>
            <span class="mc-sem-count">
              {{ semestre.materias.filter(m => statusTone(m.estadoAcademico) === 'aprobada').length }}
              /
              {{ semestre.materias.length }} materias
            </span>
          </div>
          <!-- Mini barra de progreso del semestre -->
          <div class="mc-sem-mini-bar-wrap">
            <div
              class="mc-sem-mini-bar"
              :style="{
                width: semestre.materias.length
                  ? Math.round(semestre.materias.filter(m => statusTone(m.estadoAcademico) === 'aprobada').length / semestre.materias.length * 100) + '%'
                  : '0%'
              }"
            ></div>
          </div>
        </div>

        <!-- Lista de materias -->
        <div class="mc-materias">
          <div
            v-for="materia in semestre.materias"
            :key="materia.idMateria"
            class="mc-materia"
            :data-tone="statusTone(materia.estadoAcademico)"
          >
            <div class="mc-materia-icon-wrap" :class="toneConfig(statusTone(materia.estadoAcademico)).cls">
              <i class="ti" :class="toneConfig(statusTone(materia.estadoAcademico)).icon"></i>
            </div>
            <div class="mc-materia-body">
              <span class="mc-materia-nombre" :title="materia.materia">{{ materia.materia }}</span>
              <span class="mc-materia-estado" :class="toneConfig(statusTone(materia.estadoAcademico)).cls">
                {{ toneConfig(statusTone(materia.estadoAcademico)).label }}
              </span>
            </div>
            <span
              v-if="materia.nota !== null && materia.nota !== undefined"
              class="mc-materia-nota"
              :data-tone="statusTone(materia.estadoAcademico)"
            >
              {{ materia.nota }}
            </span>
          </div>
        </div>

      </article>
    </div>
</template>

<style scoped>
/* ── Raíz ── */
.mc-root {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  width: 100%;
}

/* ── Encabezado ── */
.mc-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding-bottom: 1rem;
  border-bottom: 1px solid #d0cfca;
}
.mc-title    { margin: 0 0 3px; font-size: 1.25rem; font-weight: 700; color: #1a1a1a; }
.mc-subtitle { margin: 0; font-size: 0.8rem; color: #5b5c5e; }
.mc-btn-refresh {
  display: flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border-radius: 50%;
  border: 1.5px solid #d0cfca; background: #fff;
  color: #5b5c5e; font-size: 15px; cursor: pointer;
  transition: background 0.15s, color 0.15s; flex-shrink: 0;
}
.mc-btn-refresh:hover:not(:disabled) { background: #f4f4f2; color: #1a1a1a; }
.mc-btn-refresh:disabled { opacity: 0.5; }

/* ── Tarjeta de progreso ── */
.mc-progress-card {
  background: #fff;
  border: 1px solid #e8e8e5;
  border-radius: 14px;
  padding: 1.25rem 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}
.mc-progress-top {
  display: flex; justify-content: space-between; align-items: center;
}
.mc-progress-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #5b5c5e; }
.mc-progress-pct   { font-size: 1.4rem; font-weight: 700; color: #4e615e; font-family: monospace; }
.mc-progress-bar-wrap {
  height: 8px; background: #e8e8e5; border-radius: 999px; overflow: hidden;
}
.mc-progress-bar {
  height: 100%; background: #4e615e; border-radius: 999px;
  transition: width 0.6s ease;
}
.mc-progress-bottom { font-size: 11px; color: #8c9f96; }

/* Leyenda */
.mc-legend {
  display: flex; gap: 1rem; flex-wrap: wrap;
  padding-top: 0.6rem;
  border-top: 1px solid #f0f0ee;
}
.mc-legend-item {
  display: flex; align-items: center; gap: 5px;
  font-size: 11px; font-weight: 600;
}
.mc-legend--aprobada  { color: #1a5235; }
.mc-legend--reprobada { color: #7a2424; }
.mc-legend--inscrita  { color: #1e3a5f; }
.mc-legend--pendiente { color: #5b5c5e; }

/* ── Estado carga / vacío ── */
.mc-loading {
  display: flex; align-items: center; justify-content: center; gap: 0.6rem;
  padding: 2rem; background: #edf4f2; border-radius: 12px;
  font-size: 0.85rem; font-weight: 600; color: #2b3d36;
}
.mc-empty {
  display: flex; flex-direction: column; align-items: center; gap: 0.5rem;
  padding: 3rem 2rem;
  border: 2px dashed #d0cfca; border-radius: 14px; text-align: center;
}
.mc-empty-icon  { font-size: 2.5rem; color: #bfb09b; }
.mc-empty-title { margin: 0; font-size: 1rem; font-weight: 700; color: #5b5c5e; }
.mc-empty-sub   { margin: 0; font-size: 0.82rem; color: #8c9f96; }

/* ── Grid de semestres ── */
.mc-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
  gap: 1.25rem;
  align-items: start;
}

/* ── Tarjeta de semestre ── */
.mc-semestre {
  background: #fff;
  border: 1px solid #e8e8e5;
  border-radius: 14px;
  overflow: hidden;
}

.mc-sem-head {
  display: grid;
  grid-template-columns: auto 1fr auto;
  align-items: center;
  gap: 0.75rem;
  padding: 0.9rem 1.1rem;
  background: #fafaf9;
  border-bottom: 1px solid #e8e8e5;
}

/* Número de semestre como círculo */
.mc-sem-num {
  width: 34px; height: 34px;
  background: #4e615e;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.mc-sem-num-label {
  font-size: 13px; font-weight: 700; color: #fff; line-height: 1;
}

.mc-sem-info { display: flex; flex-direction: column; gap: 1px; }
.mc-sem-title { font-weight: 700; font-size: 0.88rem; color: #1a1a1a; }
.mc-sem-count { font-size: 10px; color: #8c9f96; font-weight: 600; }

/* Mini barra del semestre */
.mc-sem-mini-bar-wrap {
  width: 50px; height: 5px;
  background: #e8e8e5; border-radius: 999px; overflow: hidden;
}
.mc-sem-mini-bar {
  height: 100%; background: #4e615e; border-radius: 999px;
  transition: width 0.5s ease;
}

/* ── Lista de materias ── */
.mc-materias {
  padding: 0.75rem;
  display: flex; flex-direction: column; gap: 0.5rem;
}

.mc-materia {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  padding: 0.65rem 0.75rem;
  background: #fafaf9;
  border-radius: 10px;
  border: 1px solid #f0f0ee;
  transition: background 0.12s;
}
.mc-materia:hover { background: #f4f4f2; }

/* Ícono de estado */
.mc-materia-icon-wrap {
  width: 28px; height: 28px; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; flex-shrink: 0;
}
.mc-tone--aprobada  { background: #ddf0e6; color: #1a5235; }
.mc-tone--reprobada { background: #faf0f0; color: #7a2424; }
.mc-tone--inscrita  { background: #eef3fb; color: #1e3a5f; }
.mc-tone--pendiente { background: #f6f6f4; color: #5b5c5e; }

.mc-materia-body {
  display: flex; flex-direction: column; gap: 1px; flex: 1; min-width: 0;
}
.mc-materia-nombre {
  font-size: 0.82rem; font-weight: 600; color: #1a1a1a;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.mc-materia-estado {
  font-size: 9px; font-weight: 700;
  text-transform: uppercase; letter-spacing: 0.06em;
}

/* Nota */
.mc-materia-nota {
  font-size: 0.8rem; font-weight: 700; font-family: monospace;
  min-width: 32px; text-align: center;
  padding: 3px 6px; border-radius: 6px;
  background: #f0f0ee; color: #1a1a1a;
  border: 1px solid #e8e8e5;
  flex-shrink: 0;
}
.mc-materia-nota[data-tone="aprobada"]  { background: #ddf0e6; color: #1a5235; border-color: #8ec9a2; }
.mc-materia-nota[data-tone="reprobada"] { background: #faf0f0; color: #7a2424; border-color: #dca6a6; }

/* ── Spin ── */
.mc-spin { animation: mc-rotate 0.8s linear infinite; }
@keyframes mc-rotate { to { transform: rotate(360deg); } }
</style>