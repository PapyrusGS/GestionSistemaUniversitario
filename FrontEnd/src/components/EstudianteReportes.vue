<script setup>
import { ref } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const reporteTipo = ref('inscripciones')
const reportePeriodo = ref('todos')
const reporte = ref(null)

function displayValue(value) {
  if (value === null || value === undefined || value === '') return 'Sin dato'
  return value
}

async function generarReporte() {
  loading.value = true
  try {
    const body = { tipo: reporteTipo.value }
    if (reportePeriodo.value !== 'todos') {
      body.periodo = reportePeriodo.value
    }
    const { data: res } = await props.api.post('/estudiante/reporte', body)
    const payload = res.data ?? res
    reporte.value = payload
  } catch (e) {
    emit('message', { type: 'error', text: e.response?.data?.message || 'No pudimos generar el reporte.' })
  } finally {
    loading.value = false
  }
}

function exportCsv() {
  if (!reporte.value?.data?.length) return
  let csv = ''
  const keys = Object.keys(reporte.value.data[0])
  csv += keys.join(',') + '\n'
  for (const row of reporte.value.data) {
    csv += keys.map((key) => `"${row[key] ?? ''}"`).join(',') + '\n'
  }
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `reporte-${reporteTipo.value}.csv`
  a.click()
  URL.revokeObjectURL(url)
}

function printReport() {
  if (!reporte.value?.data?.length) return
  const win = window.open('', '_blank')
  if (!win) return
  win.document.write('<html><head><title>Reporte</title><style>')
  win.document.write('body { font-family: Montserrat, sans-serif; padding: 2rem; }')
  win.document.write('h1 { font-size: 1.4rem; font-family: "Playfair Display", serif; }')
  win.document.write('table { width: 100%; border-collapse: collapse; margin-top: 1rem; }')
  win.document.write('th, td { text-align: left; padding: 0.5rem; border-bottom: 1px solid #d0cfca; }')
  win.document.write('</style></head></head><body>')
  win.document.write(`<h1>${reporte.value.tipo}</h1>`)
  win.document.write(`<p>${reporte.value.estudiante} &middot; ${reporte.value.generadoEn}</p>`)
  win.document.write('<table><thead><tr>')
  const keys = Object.keys(reporte.value.data[0])
  for (const key of keys) {
    win.document.write(`<th>${key}</th>`)
  }
  win.document.write('</tr></thead><tbody>')
  for (const row of reporte.value.data) {
    win.document.write('<tr>')
    for (const key of keys) {
      win.document.write(`<td>${row[key] ?? ''}</td>`)
    }
    win.document.write('</tr>')
  }
  win.document.write('</tbody></table>')
  win.document.write('</body></html>')
  win.document.close()
  win.print()
}
</script>

<template>
  <div class="reportes-shell">
    <div class="reportes-filters">
      <div class="filter-group">
        <label>Tipo de reporte</label>
        <select v-model="reporteTipo">
          <option value="inscripciones">Materias inscritas</option>
          <option value="notas">Calificaciones</option>
          <option value="historial">Historial academico</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Periodo</label>
        <select v-model="reportePeriodo">
          <option value="todos">Todos los periodos</option>
          <option value="2026-1">2026-1</option>
          <option value="2026-2">2026-2</option>
          <option value="2025-1">2025-1</option>
          <option value="2025-2">2025-2</option>
        </select>
      </div>
      <button class="primary" type="button" @click="generarReporte" :disabled="loading">
        {{ loading ? 'Generando...' : 'Generar reporte' }}
      </button>
    </div>

    <div v-if="reporte?.data?.length" class="reporte-result">
      <div class="reporte-head">
        <div>
          <span class="eyebrow">Reporte generado</span>
          <h2>{{ reporte.tipo }}</h2>
          <p>{{ reporte.estudiante }} · {{ reporte.generadoEn }}</p>
        </div>
        <div class="reporte-actions">
          <button class="secondary" @click="exportCsv">Excel CSV</button>
          <button class="secondary" @click="printReport">PDF</button>
        </div>
      </div>
      <div class="reporte-table">
        <div v-for="(row, index) in reporte.data" :key="index" class="reporte-row">
          <div v-for="(value, key) in row" :key="key">
            <span>{{ key }}</span>
            <strong>{{ displayValue(value) }}</strong>
          </div>
        </div>
      </div>
    </div>
    <p v-else-if="!loading" class="empty">Selecciona un reporte para visualizarlo y exportarlo.</p>
  </div>
</template>

<style scoped>
.reportes-shell { display: grid; gap: 1rem; }
.reportes-filters { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; padding: 1.25rem; background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
.filter-group { display: grid; gap: 0.3rem; }
.filter-group label { font-size: 11px; font-weight: 600; color: #8c9f96; text-transform: uppercase; letter-spacing: 0.05em; }
.filter-group select { border: 1px solid rgba(0,0,0,0.05); background: #f6f6f4; padding: 0.5rem 0.8rem; border-radius: 12px; color: #1a1a1a; font: inherit; font-size: 0.85rem; min-height: 2.55rem; min-width: 160px; }
.primary { border: 0; background: #697d7b; color: white; padding: 0.7rem 1.2rem; font-weight: 600; border-radius: 20px; cursor: pointer; font-size: 0.85rem; }
.primary:hover { background: #5b6e6c; }
.primary:disabled { cursor: not-allowed; opacity: 0.55; }
.secondary { border: 1px solid transparent; background: transparent; color: #5b5c5e; font-weight: 600; font-size: 12px; padding: 8px 14px; border-radius: 20px; cursor: pointer; }
.secondary:hover { background: #d0cfca; }
.secondary:disabled { cursor: not-allowed; opacity: 0.55; }
.eyebrow { font-size: 11px; font-weight: 600; color: #8c9f96; text-transform: uppercase; letter-spacing: 0.08em; }
.reporte-result { display: grid; gap: 1rem; }
.reporte-head { display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; padding: 1.25rem; background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
.reporte-head h2 { margin: 0.25rem 0 0; font-family: 'Playfair Display', serif; color: #1a1a1a; }
.reporte-head p { color: #5b5c5e; margin-top: 0.15rem; font-size: 0.85rem; }
.reporte-actions { display: flex; gap: 0.5rem; }
.reporte-table { display: grid; gap: 0.5rem; }
.reporte-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 0.5rem; padding: 1rem; background: #fff; border-radius: 16px; border: 1px solid rgba(0,0,0,0.05); box-shadow: 0 10px 30px rgba(0,0,0,0.02); }
.reporte-row div { min-width: 0; padding: 0.65rem; border-radius: 12px; background: #f6f6f4; }
.reporte-row span { font-size: 11px; font-weight: 600; color: #8c9f96; text-transform: uppercase; letter-spacing: 0.05em; }
.reporte-row strong { display: block; margin-top: 0.2rem; overflow-wrap: anywhere; color: #1a1a1a; }
.empty { padding: 1rem; border: 1px dashed rgba(0,0,0,0.1); border-radius: 16px; color: #5b5c5e; text-align: center; }
</style>
