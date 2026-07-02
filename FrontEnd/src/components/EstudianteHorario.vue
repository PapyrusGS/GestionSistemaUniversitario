<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api:  { type: [Object, Function], required: true }
})

const schedules = ref([])
const loading = ref(false)
const errorMessage = ref('')

const timeSlots = [
  { label: '07:30 - 09:20', start: '07:30:00', end: '09:20:00' },
  { label: '09:20 - 11:10', start: '09:20:00', end: '11:10:00' },
  { label: '11:10 - 13:00', start: '11:10:00', end: '13:00:00' },
  { label: '13:30 - 15:10', start: '13:30:00', end: '15:10:00' },
  { label: '15:10 - 16:50', start: '15:10:00', end: '16:50:00' },
  { label: '16:50 - 18:30', start: '16:50:00', end: '18:30:00' }
]

const dayNames = {
  1: 'Lunes',
  2: 'Martes',
  3: 'Miércoles',
  4: 'Jueves',
  5: 'Viernes',
  6: 'Sábado'
}

async function cargarHorario() {
  loading.value = true
  errorMessage.value = ''
  try {
    const response = await props.api.get('/estudiante/horario')
    const payload = response.data?.data ?? response.data
    schedules.value = payload.schedules || []
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo cargar el horario del estudiante.'
  } finally {
    loading.value = false
  }
}

function getOccupiedSlot(day, slot) {
  return schedules.value.find(item => {
    if (Number(item.diaSemana) !== day) return false
    return item.horaInicio < slot.end && item.horaFin > slot.start
  })
}

const unmatchedSchedules = computed(() => {
  return schedules.value.filter(item => {
    const matched = timeSlots.some(slot => {
      return Number(item.diaSemana) >= 1 && Number(item.diaSemana) <= 6 &&
             item.horaInicio < slot.end && item.horaFin > slot.start
    })
    return !matched
  })
})

onMounted(cargarHorario)
</script>

<template>
  <div class="std-schedule-card">
    <div class="std-schedule-header">
      <h4 class="std-schedule-title">Mi Horario de Clases</h4>
      <p class="std-schedule-sub">Consulta la distribución semanal de tus materias inscritas en el periodo académico vigente.</p>
    </div>

    <div v-if="errorMessage" class="uni-alert uni-alert--error" style="margin: 1rem 1.5rem;">
      {{ errorMessage }}
    </div>

    <div class="std-schedule-body">
      <!-- Loading state -->
      <div v-if="loading" class="std-schedule-loading">
        <svg class="std-spin" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
        </svg>
        <span style="margin-left: 8px;">Cargando tu calendario académico...</span>
      </div>

      <template v-else>
        <!-- Legend -->
        <div class="std-schedule-legend">
          <div class="std-legend-item">
            <span class="std-legend-color std-legend-color--occupied"></span>
            <span>Materia Inscrita</span>
          </div>
          <div class="std-legend-item">
            <span class="std-legend-color std-legend-color--free"></span>
            <span>Libre / Disponible</span>
          </div>
        </div>

        <!-- Weekly Grid -->
        <div class="std-schedule-grid-wrap">
          <table class="std-schedule-grid-table">
            <thead>
              <tr>
                <th class="std-th-time">Bloque / Hora</th>
                <th v-for="d in [1, 2, 3, 4, 5]" :key="d">{{ dayNames[d] }}</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(slot, slotIdx) in timeSlots" :key="slotIdx">
                <td class="std-td-time">
                  <div class="std-time-label">Bloque {{ slotIdx + 1 }}</div>
                  <div class="std-time-range">{{ slot.label }}</div>
                </td>
                <td v-for="day in [1, 2, 3, 4, 5]" :key="day" class="std-td-slot">
                  <!-- Occupied Card -->
                  <div v-if="getOccupiedSlot(day, slot)" class="std-slot-card">
                    <div class="std-slot-subject" :title="getOccupiedSlot(day, slot).materia">
                      {{ getOccupiedSlot(day, slot).materia }}
                    </div>
                    <div class="std-slot-details">
                      Doc: <span class="std-bold-text" :title="getOccupiedSlot(day, slot).docente">{{ getOccupiedSlot(day, slot).docente }}</span>
                    </div>
                    <div class="std-slot-room">
                      Aula: <code>{{ getOccupiedSlot(day, slot).aula }}</code>
                    </div>
                    <div class="std-slot-career" :title="getOccupiedSlot(day, slot).carrera">
                      {{ getOccupiedSlot(day, slot).carrera }}
                    </div>
                  </div>
                  <!-- Free Cell -->
                  <div v-else class="std-slot-free">
                    <span>Libre</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Custom Unmatched Schedules -->
        <div v-if="unmatchedSchedules.length > 0" class="std-unmatched-section">
          <h5 class="std-unmatched-title">Otros Horarios Registrados (No Estándar)</h5>
          <div class="std-unmatched-list">
            <div v-for="(item, idx) in unmatchedSchedules" :key="idx" class="std-unmatched-item">
              <span class="std-unmatched-day">{{ dayNames[item.diaSemana] || 'Día ' + item.diaSemana }}</span>
              <span class="std-unmatched-time">{{ item.horaInicio.substring(0, 5) }} - {{ item.horaFin.substring(0, 5) }}</span>
              <span class="std-unmatched-details">
                <strong>{{ item.materia }}</strong> — Doc: {{ item.docente }} (Aula: <code>{{ item.aula }}</code>)
              </span>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<style scoped>
.std-schedule-card {
  background: var(--color-white, #ffffff);
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 14px;
  overflow: hidden;
  animation: fadeIn 0.2s ease-out;
  flex-shrink: 0;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}

.std-schedule-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.std-schedule-title {
  margin: 0;
  font-family: 'Playfair Display', serif;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-black, #000000);
}

.std-schedule-sub {
  margin: 2px 0 0 0;
  font-size: 11px;
  color: var(--uni-muted, #777777);
}

.std-schedule-body {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.std-schedule-loading {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  padding: 4rem;
  color: var(--uni-muted, #777777);
  font-size: 13px;
}

.std-spin {
  animation: stdSpinAnimation 0.8s linear infinite;
}
@keyframes stdSpinAnimation {
  from { transform: rotate(0deg); }
  to   { transform: rotate(360deg); }
}

.std-schedule-legend {
  display: flex;
  gap: 1.25rem;
  margin-bottom: 0.25rem;
}

.std-legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
  color: var(--uni-muted, #777777);
}

.std-legend-color {
  width: 14px;
  height: 14px;
  border-radius: 4px;
  border: 1px solid rgba(0, 0, 0, 0.08);
}

.std-legend-color--occupied {
  background: #edf4f2;
  border-color: var(--color-mint-light);
}

.std-legend-color--free {
  background: #fafaf9;
  border-color: #e8e8e5;
}

.std-schedule-grid-wrap {
  border: 1px solid #e8e8e5;
  border-radius: 12px;
  overflow-x: auto;
}

.std-schedule-grid-table {
  width: 100%;
  border-collapse: collapse;
  min-width: 900px;
}

.std-schedule-grid-table th {
  background: #fafaf9;
  text-align: center;
  padding: 10px 8px;
  font-size: 10px;
  font-weight: 700;
  color: var(--uni-muted, #777777);
  border-bottom: 2px solid var(--color-linen);
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.std-th-time {
  width: 140px;
}

.std-schedule-grid-table td {
  border-bottom: 1px solid #e8e8e5;
  border-right: 1px solid #e8e8e5;
  padding: 8px;
  vertical-align: top;
}

.std-schedule-grid-table td:last-child {
  border-right: none;
}

.std-td-time {
  background: #fafaf9;
  text-align: center;
  border-right: 2px solid var(--color-linen) !important;
  vertical-align: middle !important;
  padding: 12px 8px !important;
}

.std-time-label {
  font-size: 10px;
  font-weight: 700;
  color: #8c9f96;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.std-time-range {
  font-size: 12px;
  font-weight: 600;
  color: var(--color-black, #000000);
  margin-top: 3px;
}

.std-td-slot {
  width: 17%;
  height: 110px;
  background: #fdfdfd;
}

.std-slot-card {
  background: #edf4f2;
  border-left: 4px solid var(--color-mint-dark);
  border-radius: 8px;
  padding: 8px 10px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  height: 100%;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
  text-align: left;
}

.std-slot-subject {
  font-size: 11px;
  font-weight: 700;
  color: #2b3d36;
  line-height: 1.35;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}

.std-slot-details {
  font-size: 9.5px;
  font-weight: 500;
  color: var(--uni-muted, #777777);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.std-bold-text {
  font-weight: 600;
  color: var(--color-black, #000000);
}

.std-slot-room {
  font-size: 9.5px;
  font-weight: 600;
  color: #1a1a1a;
}
.std-slot-room code {
  background: #d0cfca;
  padding: 1px 4px;
  border-radius: 4px;
  font-family: monospace;
}

.std-slot-career {
  font-size: 9px;
  font-weight: 600;
  color: var(--color-mint-light);
  text-transform: uppercase;
  letter-spacing: 0.02em;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  margin-top: auto;
}

.std-slot-free {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
  border: 1px dashed #e8e8e5;
  border-radius: 8px;
  color: #c0c0bc;
  font-size: 11px;
  font-weight: 500;
  background: #fafaf9;
}

/* ── Horarios no estándar ── */
.std-unmatched-section {
  border-top: 1px solid var(--color-linen);
  padding-top: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.std-unmatched-title {
  margin: 0;
  font-size: 12px;
  font-weight: 700;
  color: var(--color-black, #000000);
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.std-unmatched-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.std-unmatched-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  background: #fafaf9;
  border: 1px solid #e8e8e5;
  border-radius: 8px;
  font-size: 12px;
}

.std-unmatched-day {
  font-weight: 700;
  color: var(--color-mint-dark);
  min-width: 80px;
}

.std-unmatched-time {
  font-weight: 600;
  color: #1a1a1a;
  background: #f0f0ee;
  padding: 2px 8px;
  border-radius: 6px;
  font-family: monospace;
  font-size: 11px;
}

.std-unmatched-details {
  color: var(--uni-muted, #777777);
}
</style>
