<script setup>
import { ref } from 'vue'
function getReportTitle() {
  const titles = {
    inscripciones: 'Reporte de Materias Inscritas',
    notas: 'Reporte de Calificaciones',
    historial: 'Historial Académico'
  }

  return titles[reporteTipo.value] || 'Reporte Académico'
}
const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['message'])

const loading = ref(false)
const reporteTipo = ref('inscripciones')
const reportePeriodo = ref('todos')
const reporte = ref(null)

function isIdKey(key) {
  const k = key.toLowerCase()

  return (
    k === 'id' ||
    k.startsWith('id') ||
    k.endsWith('id') ||
    k.includes('_id')
  )
}

function formatDate(value) {
  if (!value) return 'Sin dato'

  const date = new Date(value)

  if (isNaN(date)) return value

  return date.toLocaleString('es-BO', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

function dataKeys(row) {
  return Object.keys(row).filter(k => !isIdKey(k))
}

function filteredEntries(row) {
  return Object.entries(row).filter(([k]) => !isIdKey(k))
}

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
  const keys = dataKeys(reporte.value.data[0])
  let csv = keys.join(',') + '\n'
  for (const row of reporte.value.data) {
    csv += keys.map((key) => {
      let value = row[key]

      if (key.toLowerCase().includes('fecha')) {
        value = formatDate(value)
      }

      return `"${value ?? ''}"`
    }).join(',') + '\n'
  }
  const blob = new Blob([csv], { type: 'text/csv' })
  const url = URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `reporte-${reporteTipo.value}.csv`
  a.click()
  URL.revokeObjectURL(url)
}

function downloadPdf() {
  if (!reporte.value?.data?.length) return

  const win = window.open('', '_blank')

  if (!win) return

  const titulo = getReportTitle()

  const keys = dataKeys(reporte.value.data[0])

  win.document.write(`
    <html>
      <head>
        <title>${titulo}</title>

        <style>
          body{
            font-family: Arial, sans-serif;
            padding:40px;
            color:#333;
          }

          .header{
            margin-bottom:30px;
          }

          h1{
            margin:0;
            font-size:24px;
          }

          .meta{
            color:#666;
            margin-top:10px;
            font-size:14px;
          }

          table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
          }

          th{
            background:#697d7b;
            color:white;
            padding:10px;
            text-align:left;
          }

          td{
            padding:10px;
            border:1px solid #ddd;
          }

          .footer{
            margin-top:30px;
            font-size:12px;
            color:#777;
          }
        </style>
      </head>

      <body>

        <div class="header">
          <h1>${titulo}</h1>

          <div class="meta">
            <p><strong>Estudiante:</strong> ${reporte.value.estudiante}</p>
            <p><strong>Periodo:</strong> ${reportePeriodo.value}</p>
            <p><strong>Generado:</strong> ${reporte.value.generadoEn}</p>
          </div>
        </div>

        <table>
          <thead>
            <tr>
              ${keys.map(key => `<th>${key}</th>`).join('')}
            </tr>
          </thead>

          <tbody>
            ${reporte.value.data.map(row => `
              <tr>
                ${keys.map(key =>
                  `<td>${row[key] ?? 'Sin dato'}</td>`
                ).join('')}
              </tr>
            `).join('')}
          </tbody>
        </table>

        <div class="footer">
          Documento generado por el Sistema Académico.
        </div>

      </body>
    </html>
  `)

  win.document.close()

  setTimeout(() => {
    win.print()
  }, 500)
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
          <button class="secondary" @click="exportCsv">
            Exportar CSV
          </button>

          <button
            class="secondary"
            @click="downloadPdf"
          >
            Descargar PDF
          </button>
        </div>
      </div>
      <div class="reporte-table">
        <div v-for="(row, index) in reporte.data" :key="index" class="reporte-row">
          <div v-for="[key, value] in filteredEntries(row)" :key="key">
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
