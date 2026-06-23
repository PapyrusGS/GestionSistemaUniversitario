<script setup>
import { computed, onMounted, ref } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  api: { type: Function, required: true },
})

const emit = defineEmits(['logout'])

const activeView = ref('dashboard')
const loading = ref(false)
const enrollingId = ref(null)
const message = ref('')
const error = ref('')
const dashboard = ref(null)
const materias = ref([])
const inscripciones = ref([])
const notas = ref([])
const historial = ref([])
const malla = ref([])
const reporteTipo = ref('inscripciones')
const reporte = ref(null)


const perfil = computed(() => dashboard.value?.perfil || {})
const resumen = computed(() => dashboard.value?.resumen || {})

const sections = computed(() => [
  {
    id: 'materias',
    title: 'Materias disponibles',
    description: 'Consulta horarios, cupos, docentes y prerrequisitos antes de inscribirte.',
    stat: materias.value.length,
    statLabel: 'ofertadas',
  },
  {
    id: 'inscritas',
    title: 'Carga académica',
    description: 'Revisa tus materias inscritas, horarios, docentes y estado de inscripción.',
    stat: resumen.value.materiasInscritas || 0,
    statLabel: 'activas',
  },
  {
    id: 'notas',
    title: 'Calificaciones',
    description: 'Consulta tus notas finales y el estado obtenido en cada materia.',
    stat: resumen.value.notasRegistradas || 0,
    statLabel: 'notas',
  },
  {
    id: 'historial',
    title: 'Historial académico',
    description: 'Visualiza materias aprobadas, reprobadas, inscritas y pendientes por periodo.',
    stat: (resumen.value.aprobadas || 0) + (resumen.value.reprobadas || 0),
    statLabel: 'cursadas',
  },
  {
    id: 'malla',
    title: 'Malla curricular',
    description: 'Mira tu avance por semestre con estados y notas registradas.',
    stat: malla.value.reduce((total, item) => total + item.materias.length, 0),
    statLabel: 'materias',
  },
  {
    id: 'reportes',
    title: 'Reportes',
    description: 'Genera y exporta tus materias inscritas, notas o historial académico.',
    stat: 3,
    statLabel: 'tipos',
  },
])

const currentSection = computed(() => sections.value.find((section) => section.id === activeView.value))
const approvedPercent = computed(() => {
  const total = malla.value.reduce((count, semester) => count + semester.materias.length, 0)
  if (!total) return 0
  const approved = malla.value.reduce(
    (count, semester) => count + semester.materias.filter((materia) => materia.estadoAcademico === 'Aprobada').length,
    0,
  )
  return Math.round((approved / total) * 100)
})

async function loadAll() {
  loading.value = true
  try {
    const { data: dash } = await props.api.get('/estudiante/dashboard')
    dashboard.value = dash.data ?? dash
    const { data: m } = await props.api.get('/estudiante/materias-disponibles')
    materias.value = m.data ?? m
    const { data: ins } = await props.api.get('/estudiante/inscripciones')
    inscripciones.value = ins.data ?? ins
    const { data: n } = await props.api.get('/estudiante/notas')
    notas.value = n.data ?? n
    const { data: h } = await props.api.get('/estudiante/historial')
    historial.value = h.data ?? h
    const { data: ml } = await props.api.get('/estudiante/malla')
    malla.value = ml.data ?? ml
    message.value = 'Informacion academica actualizada.'
  } catch (e) {
    error.value = e.response?.data?.message || 'No pudimos cargar la informacion academica.'
  } finally {
    loading.value = false
  }
}

async function inscribir(materia) {
  enrollingId.value = materia.idCursoMateria
  try {
    const { data } = await props.api.post('/estudiante/inscribir', { idCursoMateria: materia.idCursoMateria })
    const result = data.data ?? data
    message.value = result.message || 'Inscripcion realizada correctamente.'
    materias.value = materias.value.filter((item) => item.idCursoMateria !== materia.idCursoMateria)
  } catch (e) {
    error.value = e.response?.data?.message || 'No pudimos completar la inscripcion.'
  } finally {
    enrollingId.value = null
  }
}

function openView(view) {
  activeView.value = view
}

async function generarReporte() {
  loading.value = true
  try {
    const { data } = await props.api.post('/estudiante/reporte', { tipo: reporteTipo.value })
    reporte.value = data.data ?? data
  } catch (e) {
    error.value = e.response?.data?.message || 'No pudimos generar el reporte.'
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
  win.document.write('h1 { font-size: 1.4rem; }')
  win.document.write('table { width: 100%; border-collapse: collapse; margin-top: 1rem; }')
  win.document.write('th, td { text-align: left; padding: 0.5rem; border-bottom: 1px solid #d0cfca; }')
  win.document.write('</style></head><body>')
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

function displayValue(value) {
  if (value === null || value === undefined || value === '') return 'Sin dato'
  return value
}

function statusTone(status) {
  if (!status) return 'pendiente'
  const s = String(status).toLowerCase().trim()
  if (s === 'aprobada' || s === 'aprobado') return 'aprobada'
  if (s === 'reprobada' || s === 'reprobado') return 'reprobada'
  if (s === 'inscrita' || s === 'inscrito') return 'inscrita'
  return 'pendiente'
}

onMounted(loadAll)
</script>

<template>
  <main class="student-app">
    <aside class="sidebar">
      <div class="brand-block">
        <span class="eyebrow">Estudiante</span>
        <strong>{{ perfil.nombreCompleto || user.nombreCompleto }}</strong>
        <small>{{ perfil.carrera || 'Carrera no asignada' }}</small>
      </div>

      <nav class="side-menu" aria-label="Menu estudiante">
        <button type="button" :class="{ active: activeView === 'dashboard' }" @click="openView('dashboard')">
          Panel
        </button>
        <button v-for="section in sections" :key="section.id" type="button" :class="{ active: activeView === section.id }" @click="openView(section.id)">
          {{ section.title }}
        </button>
      </nav>

      <button class="logout-button" type="button" @click="emit('logout')">Cerrar sesion</button>
    </aside>

    <section class="workspace">
      <header class="page-header">
        <div>
          <button v-if="activeView !== 'dashboard'" class="back-link" type="button" @click="openView('dashboard')">
            Volver al panel
          </button>
          <span class="eyebrow">{{ activeView === 'dashboard' ? 'Resumen academico' : currentSection?.statLabel }}</span>
          <h1>{{ activeView === 'dashboard' ? 'Panel del estudiante' : currentSection?.title }}</h1>
          <p>{{ activeView === 'dashboard' ? `${perfil.modalidad || 'Modalidad pendiente'} · ${perfil.correo || user.correo}` : currentSection?.description }}</p>
        </div>
        <button class="refresh-button" type="button" :disabled="loading" @click="loadAll">
          {{ loading ? 'Actualizando...' : 'Actualizar' }}
        </button>
      </header>

      <div v-if="message" class="notice success">{{ message }}</div>
      <div v-if="error" class="notice error">{{ error }}</div>
      <div v-if="loading" class="notice">Cargando informacion academica...</div>

      <section v-if="activeView === 'dashboard'" class="dashboard-view">
        <div class="summary-grid">
          <article>
            <span>Materias inscritas</span>
            <strong>{{ resumen.materiasInscritas || 0 }}</strong>
          </article>
          <article>
            <span>Notas registradas</span>
            <strong>{{ resumen.notasRegistradas || 0 }}</strong>
          </article>
          <article>
            <span>Aprobadas</span>
            <strong>{{ resumen.aprobadas || 0 }}</strong>
          </article>
          <article>
            <span>Avance de malla</span>
            <strong>{{ approvedPercent }}%</strong>
          </article>
        </div>

        <div class="menu-grid">
          <button v-for="section in sections" :key="section.id" class="menu-card" type="button" @click="openView(section.id)">
            <span>{{ section.stat }} {{ section.statLabel }}</span>
            <strong>{{ section.title }}</strong>
            <small>{{ section.description }}</small>
          </button>
        </div>
      </section>

      <section v-if="activeView === 'materias'" class="card-grid">
        <article v-for="materia in materias" :key="materia.idCursoMateria" class="subject-card">
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
        <p v-if="!materias.length && !loading" class="empty">No existen materias disponibles para inscripcion.</p>
      </section>

      <section v-if="activeView === 'inscritas'" class="stack-list">
        <article v-for="item in inscripciones.filter(Boolean)" :key="item.idInscripcion" class="list-row">
          <div>
            <span class="pill">{{ item.estado }}</span>
            <h2>{{ item.materia }}</h2>
          </div>
          <div class="row-meta">
            <span>{{ item.docente }}</span>
            <strong>{{ item.horario || item.turno }}</strong>
          </div>
        </article>
        <p v-if="!inscripciones.length && !loading" class="empty">No tienes materias inscritas activas.</p>
      </section>

      <section v-if="activeView === 'notas'" class="grade-grid">
        <article v-for="nota in notas" :key="`${nota.materia}-${nota.fechaRegistro}`" class="grade-card">
          <div>
            <span class="status" :data-tone="statusTone(nota.estadoAcademico)">{{ nota.estadoAcademico }}</span>
            <h2>{{ nota.materia }}</h2>
          </div>
          <strong>{{ nota.nota }}</strong>
        </article>
        <p v-if="!notas.length && !loading" class="empty">Aun no existen calificaciones registradas.</p>
      </section>

      <section v-if="activeView === 'historial'" class="semester-list">
        <article v-for="periodo in historial" :key="periodo.semestre" class="semester">
          <h2>Semestre {{ periodo.semestre }}</h2>
          <div v-for="materia in periodo.materias" :key="materia.materia" class="semester-row">
            <span>{{ materia.materia }}</span>
            <strong>{{ materia.nota ?? 'Sin nota' }}</strong>
            <em :data-tone="statusTone(materia.estadoAcademico)">{{ materia.estadoAcademico }}</em>
          </div>
        </article>
        <p v-if="!historial.length && !loading" class="empty">No existe historial academico para mostrar.</p>
      </section>

      <section v-if="activeView === 'malla'" class="curriculum">
        <article v-for="semestre in malla" :key="semestre.semestre" class="semester">
          <h2>Semestre {{ semestre.semestre }}</h2>
          <div class="chip-grid">
            <span v-for="materia in semestre.materias" :key="materia.idMateria" class="subject-chip" :data-tone="statusTone(materia.estadoAcademico)">
              {{ materia.materia }}
              <small>{{ materia.nota ?? materia.estadoAcademico }}</small>
            </span>
          </div>
        </article>
        <p v-if="!malla.length && !loading" class="empty">No hay malla curricular asociada al estudiante.</p>
      </section>

      <section v-if="activeView === 'reportes'" class="report-panel">
        <div class="report-actions">
          <select v-model="reporteTipo">
            <option value="inscripciones">Materias inscritas</option>
            <option value="notas">Notas</option>
            <option value="historial">Historial academico</option>
          </select>
          <button class="primary" type="button" @click="generarReporte">Generar</button>
          <button class="secondary" type="button" :disabled="!reporte?.data?.length" @click="exportCsv">Excel CSV</button>
          <button class="secondary" type="button" :disabled="!reporte?.data?.length" @click="printReport">PDF</button>
        </div>

        <div v-if="reporte?.data?.length" class="report-table">
          <header>
            <span class="eyebrow">Reporte generado</span>
            <h2>{{ reporte.tipo }}</h2>
            <p>{{ reporte.estudiante }} · {{ reporte.generadoEn }}</p>
          </header>
          <div v-for="(row, index) in reporte.data" :key="index" class="report-row">
            <div v-for="(value, key) in row" :key="key">
              <span>{{ key }}</span>
              <strong>{{ displayValue(value) }}</strong>
            </div>
          </div>
        </div>
        <p v-else class="empty">Selecciona un reporte para visualizarlo y exportarlo.</p>
      </section>
    </section>
  </main>
</template>

<style scoped>
.student-app {
  display: grid;
  grid-template-columns: 280px 1fr;
  min-height: 100vh;
  background: #f6f6f4;
}

.sidebar {
  position: sticky;
  top: 0;
  display: flex;
  flex-direction: column;
  height: 100vh;
  padding: 2rem;
  background: #d0cfca;
  border-right: 1px solid rgba(0, 0, 0, 0.06);
}

.brand-block {
  display: grid;
  gap: 0.35rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  text-align: center;
}

.brand-block strong {
  font-size: 1.05rem;
  color: #1a1a1a;
}

.brand-block small {
  color: #5b5c5e;
}

.eyebrow {
  font-size: 11px;
  font-weight: 600;
  color: #8c9f96;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.side-menu {
  display: grid;
  gap: 0.4rem;
  margin-top: 1rem;
}

button, select {
  min-height: 2.55rem;
  border-radius: 20px;
  font: inherit;
  font-family: 'Montserrat', ui-sans-serif, system-ui, sans-serif;
}

button {
  cursor: pointer;
}

.side-menu button,
.logout-button,
.refresh-button,
.back-link,
.secondary {
  border: 1px solid transparent;
  background: transparent;
  color: #5b5c5e;
  font-weight: 600;
  font-size: 12px;
}

.side-menu button {
  width: 100%;
  padding: 8px 14px;
  text-align: left;
  font-weight: 600;
  border-radius: 20px;
}

.side-menu button:hover {
  background: rgba(0, 0, 0, 0.04);
}

.side-menu button.active {
  background: #bfb09b;
  color: #1a1a1a;
}

.logout-button {
  margin-top: auto;
  padding: 8px 14px;
  font-weight: 600;
  text-align: center;
}

.logout-button:hover {
  background: rgba(0, 0, 0, 0.04);
}

.workspace {
  min-width: 0;
  padding: 2rem;
  display: flex;
  flex-direction: column;
}

.page-header {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
  margin-bottom: 1rem;
}

h1, h2, p {
  margin: 0;
}

h1 {
  margin-top: 0.25rem;
  font-family: 'Playfair Display', serif;
  font-size: 1.6rem;
}

.page-header p {
  margin-top: 0.35rem;
  color: #5b5c5e;
}

.refresh-button,
.back-link,
.secondary {
  padding: 8px 14px;
  font-weight: 600;
}

.refresh-button:hover,
.back-link:hover,
.secondary:hover {
  background: #d0cfca;
}

.back-link {
  margin-bottom: 0.65rem;
}

.primary {
  border: 0;
  background: #697d7b;
  color: white;
  padding: 8px 14px;
  font-weight: 600;
  border-radius: 20px;
}

.primary:hover {
  background: #5b6e6c;
}

button:disabled {
  cursor: not-allowed;
  opacity: 0.55;
}

.notice {
  margin-bottom: 1rem;
  padding: 0.85rem 1rem;
  border-radius: 16px;
  background: #edf4f2;
  color: #2b3d36;
}

.notice.success {
  background: #edf4f2;
  color: #2b3d36;
}

.notice.error {
  background: #faf0f0;
  color: #7a2424;
}

.dashboard-view,
.semester-list,
.curriculum,
.stack-list,
.report-panel {
  display: grid;
  gap: 1rem;
}

.summary-grid {
  display: grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap: 0.8rem;
}

.summary-grid article,
.menu-card,
.subject-card,
.list-row,
.grade-card,
.semester,
.report-panel,
.report-table {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.02);
  border: 1px solid rgba(0,0,0,0.05);
}

.summary-grid article {
  padding: 1.5rem;
}

.summary-grid span {
  font-size: 12px;
  font-weight: 600;
  color: #8c9f96;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.summary-grid strong {
  display: block;
  margin-top: 0.35rem;
  font-size: 1.7rem;
  color: #1a1a1a;
}

.menu-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(245px, 1fr));
  gap: 0.9rem;
}

.menu-card {
  display: grid;
  gap: 0.55rem;
  min-height: 9rem;
  padding: 1.5rem;
  text-align: left;
  cursor: pointer;
  border-radius: 16px;
}

.menu-card:hover {
  border-color: #bfb09b;
}

.menu-card span {
  width: fit-content;
  border-radius: 999px;
  background: #edf4f2;
  color: #697d7b;
  padding: 0.35rem 0.65rem;
  font-size: 11px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.menu-card strong {
  font-size: 1.1rem;
  color: #1a1a1a;
}

.menu-card small {
  color: #5b5c5e;
}

.card-grid,
.grade-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(310px, 1fr));
  gap: 0.9rem;
}

.subject-card,
.grade-card {
  display: grid;
  gap: 1rem;
  padding: 1.5rem;
}

.card-title-row,
.list-row,
.grade-card {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
}

.card-title-row h2,
.list-row h2,
.grade-card h2,
.semester h2 {
  margin-top: 0.45rem;
  font-size: 1.05rem;
  font-family: 'Playfair Display', serif;
  color: #1a1a1a;
}

.card-title-row > strong,
.grade-card > strong {
  color: #697d7b;
  font-size: 1.35rem;
  white-space: nowrap;
  font-weight: 600;
}

.detail-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.75rem;
}

.detail-grid div,
.report-row div {
  min-width: 0;
  padding: 0.75rem;
  border-radius: 12px;
  background: #f6f6f4;
}

.detail-grid span,
.report-row span {
  font-size: 11px;
  font-weight: 600;
  color: #8c9f96;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.detail-grid strong,
.report-row strong {
  display: block;
  margin-top: 0.25rem;
  overflow-wrap: anywhere;
  color: #1a1a1a;
}

.pill,
.status,
.semester-row em {
  border-radius: 999px;
  padding: 0.35rem 0.6rem;
  font-size: 11px;
  font-style: normal;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.03em;
}

.pill {
  background: #edf4f2;
  color: #697d7b;
}

.row-meta {
  display: grid;
  gap: 0.3rem;
  min-width: 16rem;
  text-align: right;
}

.row-meta span {
  color: #5b5c5e;
}

.semester {
  padding: 1.5rem;
}

.semester-row {
  display: grid;
  grid-template-columns: 1fr auto auto;
  gap: 0.75rem;
  align-items: center;
  padding: 0.7rem 0;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.semester-row:last-child {
  border-bottom: 0;
}

.chip-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
  gap: 0.6rem;
  margin-top: 0.8rem;
}

.subject-chip {
  display: grid;
  gap: 0.25rem;
  min-height: 4.4rem;
  align-content: center;
  border-radius: 16px;
  padding: 0.7rem;
  font-weight: 600;
  background: #f6f6f4;
}

.subject-chip small {
  color: #5b5c5e;
}

[data-tone='aprobada'] {
  background: #edf4f2;
  color: #2b3d36;
}

[data-tone='reprobada'] {
  background: #faf0f0;
  color: #7a2424;
}

[data-tone='inscrita'] {
  background: #edf4f2;
  color: #697d7b;
}

[data-tone='pendiente'] {
  background: #f6f6f4;
  color: #5b5c5e;
}

.report-panel {
  padding: 1.5rem;
}

.report-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.6rem;
}

select {
  border: 1px solid rgba(0,0,0,0.05);
  background: #ffffff;
  padding: 0 0.8rem;
  border-radius: 20px;
  color: #1a1a1a;
}

.report-table {
  padding: 1.5rem;
}

.report-table header {
  margin-bottom: 1rem;
}

.report-table header p {
  color: #5b5c5e;
}

.report-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
  gap: 0.6rem;
  padding: 0.8rem 0;
  border-top: 1px solid rgba(0,0,0,0.05);
}

.empty {
  padding: 1rem;
  border: 1px dashed rgba(0,0,0,0.1);
  border-radius: 16px;
  color: #5b5c5e;
  text-align: center;
}

@media (max-width: 900px) {
  .student-app {
    grid-template-columns: 1fr;
  }

  .sidebar {
    position: static;
    height: auto;
  }

  .side-menu {
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  }
}

@media (max-width: 700px) {
  .workspace {
    padding: 1rem;
  }

  .page-header,
  .card-title-row,
  .list-row,
  .grade-card {
    flex-direction: column;
  }

  .summary-grid,
  .detail-grid {
    grid-template-columns: 1fr;
  }

  .row-meta {
    min-width: 0;
    text-align: left;
  }

  .semester-row {
    grid-template-columns: 1fr;
  }
}

@media print {
  .sidebar,
  .page-header,
  .notice,
  .report-actions {
    display: none;
  }

  .student-app {
    display: block;
    background: #ffffff;
    color: #000000;
  }

  .workspace {
    padding: 0;
  }
}
</style>
