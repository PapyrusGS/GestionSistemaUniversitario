<script setup>
import { ref, computed, onMounted } from 'vue'
const exportingPdf = ref(false)
const exportingCsv = ref(false)
const periodosList = ref([])

onMounted(async () => {
  try {
    const { data: res } = await props.api.get('/estudiante/periodos')
    periodosList.value = res.data ?? res
  } catch (e) {
    console.error('Error cargando periodos:', e)
  }
})

const availablePeriodos = computed(() => {
  if (!reporte.value?.data) return []
  const periods = new Set()
  const extract = (obj) => {
    if (!obj || typeof obj !== 'object') return
    Object.entries(obj).forEach(([key, val]) => {
      const k = key.toLowerCase()
      if (PERIODO_KEYS.some(pk => k.includes(pk)) && val) {
        periods.add(String(val).trim())
      } else if (Array.isArray(val)) {
        val.forEach(item => extract(item))
      }
    })
  }
  reporte.value.data.forEach(row => extract(row))
  return Array.from(periods).sort((a, b) => normalizePeriodo(a).localeCompare(normalizePeriodo(b)))
})

const filteredReportData = computed(() => {
  if (!reporte.value?.data) return []
  if (reportePeriodo.value === 'todos') return reporte.value.data
  return filtrarPorPeriodo(reporte.value.data)
})

function normalizePeriodo(str) {
  if (!str) return ''
  let s = String(str).toUpperCase().trim()
  
  let match1 = s.match(/^(I|II|1|2)\s*[-\/]?\s*(20\d{2})$/)
  if (match1) {
    let sem = (match1[1] === 'II' || match1[1] === '2') ? '2' : '1'
    return `${match1[2]}-${sem}`
  }
  
  let match2 = s.match(/^(20\d{2})\s*[-\/]?\s*(I|II|1|2)$/)
  if (match2) {
    let sem = (match2[2] === 'II' || match2[2] === '2') ? '2' : '1'
    return `${match2[1]}-${sem}`
  }
  
  return s.toLowerCase()
}

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

// Campos donde puede venir el periodo/gestión académica dentro de una fila.
const PERIODO_KEYS = ['periodo', 'gestion', 'periodoacademico', 'gestionacademica', 'periodogestion']

// Revisa si una fila (o materia anidada) corresponde al periodo seleccionado,
// comparando por IGUALDAD EXACTA contra campos explícitos de periodo/gestión
// (en vez de "incluye", que generaba falsos positivos con ids, notas o fechas
// que casualmente contenían los mismos dígitos que el periodo).
function rowMatchesPeriodo(row, periodo) {
  if (!row || typeof row !== 'object') return false

  const targetPeriodoNorm = normalizePeriodo(periodo)

  const directMatch = Object.entries(row).some(([key, val]) => {
    if (val === null || val === undefined) return false
    const k = key.toLowerCase()
    if (!PERIODO_KEYS.some(pk => k.includes(pk))) return false
    return normalizePeriodo(val) === targetPeriodoNorm
  })
  if (directMatch) return true

  // El historial académico viene agrupado por semestre, con las materias
  // anidadas en un arreglo (`materias`). Si el periodo no está en el nivel
  // superior, lo buscamos dentro de cada materia anidada.
  return Object.values(row).some(val => {
    if (Array.isArray(val)) {
      return val.some(item => rowMatchesPeriodo(item, periodo))
    }
    return false
  })
}

function filtrarPorPeriodo(data) {
  if (reportePeriodo.value === 'todos' || !data?.length) return data
  const periodo = reportePeriodo.value

  return data
    .map(row => {
      // Si la fila trae materias anidadas (caso del historial agrupado por
      // semestre), filtramos también ese arreglo interno para no arrastrar
      // materias de otros periodos dentro del mismo grupo.
      if (Array.isArray(row.materias)) {
        const materiasDelPeriodo = row.materias.filter(m => rowMatchesPeriodo(m, periodo))
        const grupoCoincide = rowMatchesPeriodo({ ...row, materias: undefined }, periodo)

        if (materiasDelPeriodo.length) {
          return { ...row, materias: materiasDelPeriodo }
        }
        return grupoCoincide ? row : null
      }

      return rowMatchesPeriodo(row, periodo) ? row : null
    })
    .filter(Boolean)
}

async function generarReporte() {
  loading.value = true
  reporte.value = null
  try {
    const params = { tipo: reporteTipo.value }
    const { data: res } = await props.api.post('/estudiante/reporte', params)
    const raw = res.data ?? res
    reporte.value = raw
  } catch (e) {
    emit('message', { type: 'error', text: e.response?.data?.message || 'No pudimos generar el reporte.' })
  } finally {
    loading.value = false
  }
}

async function exportarReporte(tipo) {
  const isPdf = tipo === 'pdf'
  if (isPdf) exportingPdf.value = true
  else exportingCsv.value = true

  try {
    const params = { tipo: reporteTipo.value }
    if (reportePeriodo.value !== 'todos') params.periodo = reportePeriodo.value

    // El backend recibe el tipo de export como query param o en el body
    const response = await props.api.post(
      `/estudiante/reporte/${tipo}`,
      params,
      { responseType: 'blob' }
    )

    // Leer nombre del header o usar fallback
    const disposition = response.headers['content-disposition']
    let filename = `reporte-${reporteTipo.value}.${isPdf ? 'pdf' : 'csv'}`
    if (disposition?.indexOf('attachment') !== -1) {
      const match = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/.exec(disposition)
      if (match?.[1]) filename = match[1].replace(/['"]/g, '')
    }

    // Disparar descarga
    const blob = new Blob([response.data], { type: response.headers['content-type'] })
    const link = document.createElement('a')
    link.href = window.URL.createObjectURL(blob)
    link.download = filename
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    window.URL.revokeObjectURL(link.href)

  } catch {
    emit('message', { type: 'error', text: `Error al exportar el reporte a ${isPdf ? 'PDF' : 'CSV'}.` })
  } finally {
    if (isPdf) exportingPdf.value = false
    else exportingCsv.value = false
  }
}

function exportCsv() {
  const dataToExport = filteredReportData.value
  if (!dataToExport.length) return
  const keys = dataKeys(dataToExport[0])
  let csv = keys.join(',') + '\n'
  for (const row of dataToExport) {
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
  const dataToPrint = filteredReportData.value
  if (!dataToPrint.length) return

  const win = window.open('', '_blank')

  if (!win) return

  const titulo = getReportTitle()

  const keys = dataKeys(dataToPrint[0])

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
            ${dataToPrint.map(row => `
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
        <select v-model="reporteTipo" @change="reportePeriodo = 'todos'" :disabled="loading">
          <option value="inscripciones">Materias inscritas</option>
          <option value="notas">Calificaciones</option>
          <option value="historial">Historial academico</option>
        </select>
      </div>
      <div class="filter-group">
        <label>Periodo</label>
        <select v-model="reportePeriodo" :disabled="loading">
          <option value="todos">Todos los periodos</option>
          <option v-for="p in periodosList" :key="p.idPeriodo" :value="p.nombre">
            {{ p.nombre }}
          </option>
        </select>
      </div>
      <button class="primary" type="button" @click="generarReporte" :disabled="loading">
        {{ loading ? 'Generando...' : 'Generar reporte' }}
      </button>
    </div>

    <div v-if="reporte" class="reporte-result">
      <div class="reporte-head">
        <div>
          <span class="eyebrow">Reporte generado</span>
          <h2>{{ reporte.tipo }}</h2>
          <p>{{ reporte.estudiante }} · {{ reporte.generadoEn }}</p>
        </div>
        <div class="reporte-actions">
          <button class="secondary" @click="exportarReporte('csv')"
                  :disabled="exportingCsv || exportingPdf">
            {{ exportingCsv ? 'Generando...' : 'Exportar CSV' }}
          </button>
          <button class="secondary" @click="exportarReporte('pdf')"
                  :disabled="exportingPdf || exportingCsv">
            {{ exportingPdf ? 'Generando PDF...' : 'Descargar PDF' }}
          </button>
        </div>
      </div>
      <div class="reporte-table" v-if="filteredReportData.length">
        <div v-for="(row, index) in filteredReportData" :key="index" class="reporte-row">
          <div v-for="[key, value] in filteredEntries(row)" :key="key">
            <span>{{ key }}</span>
            <strong>{{ displayValue(value) }}</strong>
          </div>
        </div>
      </div>
      <p v-else class="empty">No se encontraron registros para el periodo seleccionado.</p>
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