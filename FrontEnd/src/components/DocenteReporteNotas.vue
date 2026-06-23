<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
  user: Object,
  api: Object,
  badgeTone: String
})

// ── Estado principal ─────────────────────────────────────────────────────────
const loading = ref(false)
const exportingPdf = ref(false)
const exportingExcel = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const cursos = ref([])
const idCursoMateria = ref('')
const reportData = ref(null)

// ── Helpers ───────────────────────────────────────────────────────────────────
function resetMessages() {
  errorMessage.value = ''
  successMessage.value = ''
}

// ── Cargar cursos (reutilizando el endpoint estándar) ─────────────────────────
async function cargarCursos() {
  loading.value = true
  resetMessages()
  try {
    const { data } = await props.api.get('/docente/cursos')
    cursos.value = data.data ?? data
  } catch {
    errorMessage.value = 'No se pudieron cargar tus cursos asignados.'
  } finally {
    loading.value = false
  }
}

// ── Obtener reporte JSON ──────────────────────────────────────────────────────
async function generarReporte() {
  if (!idCursoMateria.value) {
    errorMessage.value = 'Debe seleccionar un curso primero.'
    return
  }

  loading.value = true
  resetMessages()
  reportData.value = null

  try {
    const { data } = await props.api.get(`/docente/reportes/notas/${idCursoMateria.value}`)
    reportData.value = data.data ?? data
    if (reportData.value?.rows?.length === 0) {
      successMessage.value = 'No hay datos disponibles para este curso.'
    } else {
      successMessage.value = 'Reporte académico generado con éxito.'
    }
  } catch (error) {
    const d = error.response?.data
    errorMessage.value = d?.message || 'Error al obtener el reporte del curso.'
  } finally {
    loading.value = false
  }
}

// ── Descarga de Archivos Binarios (Blob) ──────────────────────────────────────
async function exportarReporte(tipo) {
  if (!idCursoMateria.value) return

  const isPdf = tipo === 'pdf'
  if (isPdf) exportingPdf.value = true
  else exportingExcel.value = true

  resetMessages()

  try {
    const response = await props.api.get(`/docente/reportes/notas/${idCursoMateria.value}/${tipo}`, {
      responseType: 'blob'
    })

    // Leer encabezado Content-Disposition para nombre del archivo
    const disposition = response.headers['content-disposition']
    let filename = `reporte_notas_curso_${idCursoMateria.value}.${isPdf ? 'pdf' : 'csv'}`
    
    if (disposition && disposition.indexOf('attachment') !== -1) {
      const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/
      const matches = filenameRegex.exec(disposition)
      if (matches != null && matches[1]) { 
        filename = matches[1].replace(/['"]/g, '')
      }
    }

    // Crear Blob y disparar descarga nativa directa sin abrir pestañas nuevas
    const blob = new Blob([response.data], { type: response.headers['content-type'] })
    const link = document.createElement('a')
    link.href = window.URL.createObjectURL(blob)
    link.download = filename
    document.body.appendChild(link)
    link.click()
    
    // Liberación de recursos de memoria
    document.body.removeChild(link)
    window.URL.revokeObjectURL(link.href)

    successMessage.value = `Archivo ${isPdf ? 'PDF' : 'CSV (Excel)'} descargado correctamente.`
  } catch (error) {
    errorMessage.value = `Error al exportar el reporte a ${isPdf ? 'PDF' : 'Excel'}.`
  } finally {
    if (isPdf) exportingPdf.value = false
    else exportingExcel.value = false
  }
}

onMounted(cargarCursos)
</script>

<template>
  <div class="fade-in-view">

    <!-- Header -->
    <div class="workspace-topbar">
      <div class="topbar-left">
        <span class="context-path">Docentes / Reportes</span>
        <h2>Reporte de Notas por Curso</h2>
        <p class="subtitle-text">Generación y exportación de reportes académicos oficiales</p>
      </div>
    </div>

    <!-- Alerts -->
    <div v-if="successMessage" class="alert-inline success mb-4">{{ successMessage }}</div>
    <div v-if="errorMessage"   class="alert-inline error mb-4">{{ errorMessage }}</div>

    <!-- Filtros de Generación -->
    <section class="card-panel mb-4">
      <div class="panel-header">
        <h4>Panel de Control del Reporte</h4>
      </div>
      <div class="generation-bar">
        <div class="selector-field">
          <label>
            <span>Seleccione Curso *</span>
            <select v-model="idCursoMateria" :disabled="loading || exportingPdf || exportingExcel">
              <option value="" disabled>Seleccione un curso asignado</option>
              <option v-for="curso in cursos" :key="curso.idCursoMateria" :value="curso.idCursoMateria">
                {{ curso.materia_nombre }} — {{ curso.turno_nombre }}
              </option>
            </select>
          </label>
        </div>
        <button class="primary-btn shrink-btn" @click="generarReporte"
                :disabled="!idCursoMateria || loading || exportingPdf || exportingExcel">
          <span v-if="loading">Generando...</span>
          <span v-else>📊 Generar Reporte</span>
        </button>
      </div>
    </section>

    <!-- Indicador de Carga -->
    <div v-if="loading" class="spinner-container">
      <div class="loading-spinner"></div>
      <span class="loading-text">Cargando reporte de notas...</span>
    </div>

    <!-- Resultados del Reporte -->
    <div v-else-if="reportData" class="fade-in-view">

      <!-- Cards de Resumen Académico -->
      <div class="dashboard-cards-grid mb-4">
        
        <!-- Total Estudiantes -->
        <div class="metric-card card-blue">
          <div class="metric-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
            </svg>
          </div>
          <div class="metric-info">
            <span class="lbl">Total Estudiantes</span>
            <strong class="val">{{ reportData.summary.total_estudiantes }} alumnos</strong>
          </div>
        </div>

        <!-- Evaluados / Con Calificación -->
        <div class="metric-card card-green">
          <div class="metric-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
          </div>
          <div class="metric-info">
            <span class="lbl">Evaluados</span>
            <strong class="val">{{ reportData.summary.total_con_nota }} calificados</strong>
          </div>
        </div>

        <!-- Promedio General -->
        <div class="metric-card card-purple">
          <div class="metric-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </div>
          <div class="metric-info">
            <span class="lbl">Promedio General</span>
            <strong class="val">{{ Number(reportData.summary.promedio_general).toFixed(2) }} pts</strong>
          </div>
        </div>

      </div>

      <!-- Tabla de Estudiantes e Inscripciones -->
      <section class="table-card-wrapper mb-4">
        <div class="table-card-header flex-header">
          <h4>Listado de Calificaciones</h4>
          
          <!-- Botones de Exportación Real -->
          <div class="export-actions" v-if="reportData.rows.length > 0">
            <button class="export-btn btn-pdf" @click="exportarReporte('pdf')" :disabled="exportingPdf || exportingExcel">
              <span v-if="exportingPdf">Generando PDF...</span>
              <span v-else>📄 Exportar PDF</span>
            </button>
            <button class="export-btn btn-excel" @click="exportarReporte('excel')" :disabled="exportingPdf || exportingExcel">
              <span v-if="exportingExcel">Generando Excel...</span>
              <span v-else>🟢 Exportar Excel</span>
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="workspace-table">
            <thead>
              <tr>
                <th style="width: 15%;">ID Inscripción</th>
                <th>Estudiante</th>
                <th class="txt-center" style="width: 15%;">Calificación</th>
                <th class="txt-center" style="width: 25%;">Estado</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="row in reportData.rows" :key="row.idInscripcion">
                <td class="font-mono">{{ row.idInscripcion }}</td>
                <td class="primary-cell font-medium">{{ row.nombreCompleto }}</td>
                <td class="font-medium txt-center font-mono">
                  {{ row.nota !== null ? Number(row.nota).toFixed(1) : '-' }}
                </td>
                <td class="txt-center">
                  <span class="badge-state" :class="row.estadoAcademico.toLowerCase().replace(' ', '-')">
                    {{ row.estadoAcademico }}
                  </span>
                </td>
              </tr>
              <tr v-if="reportData.rows.length === 0">
                <td colspan="4" class="empty-table-msg">
                  ⚠️ No existen estudiantes o calificaciones para visualizar en este curso.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

    </div>

    <!-- Pantalla Inicial de Espera -->
    <div v-else class="card-panel text-center-msg">
      <p class="text-muted">Por favor, seleccione un curso de la barra superior y pulse "Generar Reporte".</p>
    </div>

  </div>
</template>

<style scoped>
.subtitle-text { font-size: 0.9rem; color: #6b7280; margin-top: 0.2rem; }
.mb-4 { margin-bottom: 1.5rem; }

.card-panel {
  background: #fafafa;
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 12px;
  padding: 1.5rem;
  height: fit-content;
}
.panel-header {
  margin-bottom: 1.25rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  padding-bottom: 0.75rem;
}
.panel-header h4 { font-size: 1rem; font-weight: 600; color: var(--color-black); }

.generation-bar {
  display: flex;
  align-items: flex-end;
  gap: 1.5rem;
  width: 100%;
}
@media (max-width: 768px) {
  .generation-bar { flex-direction: column; align-items: stretch; }
}

.selector-field {
  flex: 1;
}

label { display: flex; flex-direction: column; font-size: 0.85rem; color: #6b7280; font-weight: 600; }
label span { margin-bottom: 0.35rem; }

select {
  background: var(--color-white);
  border: 1px solid rgba(0, 0, 0, 0.1);
  color: var(--color-black);
  padding: 0.75rem 1rem;
  border-radius: 8px;
  width: 100%;
  font-size: 0.9rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
select:focus {
  outline: none;
  border-color: #38bdf8;
  box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.15);
}
select:disabled { opacity: 0.5; cursor: not-allowed; }

.primary-btn {
  background: #38bdf8;
  color: #0f172a;
  border: none;
  padding: 0.8rem 2rem;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s ease;
  height: 44px;
}
.primary-btn:hover:not(:disabled) { background: #0ea5e9; transform: translateY(-1px); }
.primary-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.dashboard-cards-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
  gap: 1.25rem;
}

.metric-card {
  padding: 1.25rem;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 1rem;
}

.metric-icon {
  width: 44px;
  height: 44px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.04);
}
.metric-icon svg { width: 22px; height: 22px; }

.metric-info { display: flex; flex-direction: column; }
.metric-info .lbl { font-size: 0.75rem; text-transform: uppercase; opacity: 0.8; }
.metric-info .val { font-size: 1.15rem; font-weight: 700; margin-top: 0.1rem; }

.card-blue { background: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.15); color: #38bdf8; }
.card-blue .metric-info .val { color: var(--color-black); }

.card-green { background: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.15); color: #4ade80; }
.card-green .metric-info .val { color: var(--color-black); }

.card-purple { background: rgba(168, 85, 247, 0.08); border: 1px solid rgba(168, 85, 247, 0.15); color: #c084fc; }
.card-purple .metric-info .val { color: var(--color-black); }

.table-card-wrapper {
  background: var(--color-white);
  border: 1px solid rgba(0, 0, 0, 0.06);
  border-radius: 14px;
  overflow: hidden;
}
.table-card-header {
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  background: transparent;
}
.table-card-header h4 { font-size: 1rem; font-weight: 600; color: var(--color-black); }

.flex-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
@media (max-width: 600px) {
  .flex-header { flex-direction: column; align-items: flex-start; gap: 0.75rem; }
}

.export-actions {
  display: flex;
  gap: 0.75rem;
}

.export-btn {
  border: none;
  padding: 0.45rem 0.9rem;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-pdf {
  background: rgba(239, 68, 68, 0.1);
  color: #f87171;
  border: 1px solid rgba(239, 68, 68, 0.2);
}
.btn-pdf:hover:not(:disabled) { background: #ef4444; color: #fff; }

.btn-excel {
  background: rgba(34, 197, 94, 0.1);
  color: #4ade80;
  border: 1px solid rgba(34, 197, 94, 0.2);
}
.btn-excel:hover:not(:disabled) { background: #22c55e; color: #fff; }

.export-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.table-responsive { overflow-x: auto; }

.workspace-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
  text-align: left;
}
.workspace-table th {
  padding: 1rem 1.5rem;
  background: #fafafa;
  color: #6b7280;
  font-size: 0.75rem;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}
.workspace-table td {
  padding: 1.1rem 1.5rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.03);
  color: #4b5563;
}
.workspace-table tr:hover td { background: #fafafa; }

.primary-cell { color: var(--color-black) !important; }
.font-medium  { font-weight: 500; }
.font-mono    { font-family: monospace; font-size: 0.85rem; color: #6b7280; }
.txt-center   { text-align: center; }

.badge-state {
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  display: inline-block;
}
.badge-state.aprobado { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.badge-state.reprobado { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
.badge-state.sin-registro { background: rgba(148, 163, 184, 0.1); color: #6b7280; }

.empty-table-msg { text-align: center; color: #6b7280; padding: 3rem !important; }

.spinner-container {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 4rem; gap: 1rem;
}
.loading-spinner {
  width: 32px; height: 32px;
  border: 3px solid rgba(56, 189, 248, 0.1);
  border-radius: 50%;
  border-top-color: #38bdf8;
  animation: spin 1s ease-in-out infinite;
}
.loading-text { font-size: 0.85rem; color: #6b7280; }
@keyframes spin { to { transform: rotate(360deg); } }

.alert-inline {
  padding: 0.75rem 1rem;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
}
.alert-inline.success {
  background: rgba(34, 197, 94, 0.1);
  color: #22c55e;
  border: 1px solid rgba(34, 197, 94, 0.2);
}
.alert-inline.error {
  background: rgba(239, 68, 68, 0.1);
  color: #ef4444;
  border: 1px solid rgba(239, 68, 68, 0.2);
}

.text-center-msg {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 4rem 2rem;
}
.text-muted { color: #6b7280; font-size: 0.9rem; text-align: center; }

.fade-in-view { animation: fadeIn 0.25s ease-out; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}
</style>
