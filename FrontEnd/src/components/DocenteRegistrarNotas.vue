<script setup>
import { ref, computed, onMounted } from 'vue'

const props = defineProps({
  user: Object,
  api: {
    type: [Object, Function],
    required: true
  },
  badgeTone: String
})

// ── Estado principal ──────────────────────────────────────────────────────────
const loading         = ref(false)
const guardandoId     = ref(null)
const successMessage  = ref('')
const errorMessage    = ref('')

const cursos      = ref([])
const filas       = ref([])
const cursoActual = ref(null)
const materiaInfo = ref(null)

// ── Vista: 'cursos' | 'notas' ────────────────────────────────────────────────
const view = ref('cursos')

// ── Filtro por semestre ───────────────────────────────────────────────────────
const semestreFiltro = ref('')

const cursosFiltrados = computed(() => {
  let list = cursos.value
  if (semestreFiltro.value !== '') {
    list = list.filter(c => String(c.materia_semestre ?? '') === semestreFiltro.value)
  }
  return list
})

const semestresDisponibles = computed(() => {
  const s = new Set(cursos.value.map(c => c.materia_semestre).filter(v => v != null && v !== ''))
  return [...s].sort((a, b) => {
    const na = Number(a), nb = Number(b)
    if (!isNaN(na) && !isNaN(nb)) return na - nb
    if (isNaN(na) && isNaN(nb)) return String(a).localeCompare(String(b))
    return isNaN(na) ? 1 : -1
  })
})

// ── Helpers ───────────────────────────────────────────────────────────────────
function flash(tipo, msg) {
  if (tipo === 'ok') {
    successMessage.value = msg
    errorMessage.value   = ''
  } else {
    errorMessage.value   = msg
    successMessage.value = ''
  }
  setTimeout(() => { successMessage.value = ''; errorMessage.value = '' }, 4000)
}

function notaClass(nota) {
  if (nota === null || nota === '' || nota === undefined) return 'sin-nota'
  return Number(nota) >= 51 ? 'aprobado' : 'reprobado'
}

// ── Cargar cursos ─────────────────────────────────────────────────────────────
async function cargarCursos() {
  loading.value = true
  try {
    const { data } = await props.api.get('/docente/cursos')
    cursos.value = data.data ?? data
  } catch {
    flash('err', 'No se pudieron cargar tus materias asignadas.')
  } finally {
    loading.value = false
  }
}

// ── Seleccionar curso e ir a vista de notas ──────────────────────────────────
async function seleccionarCurso(curso) {
  cursoActual.value = curso.idCursoMateria
  materiaInfo.value = curso
  view.value = 'notas'
  await cargarEstudiantes()
}

function volver() {
  view.value = 'cursos'
  filas.value = []
  cursoActual.value = null
  materiaInfo.value = null
  successMessage.value = ''
  errorMessage.value = ''
}

// ── Cargar estudiantes + notas ────────────────────────────────────────────────
async function cargarEstudiantes() {
  filas.value = []
  if (!cursoActual.value) return

  loading.value = true
  try {
    const [resE, resN] = await Promise.all([
      props.api.get('/docente/estudiantes', { params: { idCursoMateria: cursoActual.value } }),
      props.api.get('/docente/notas',       { params: { idCursoMateria: cursoActual.value } }),
    ])

    const estudiantes  = resE.data.data ?? resE.data
    const notasMap     = {}
    ;(resN.data.data ?? resN.data).forEach(n => {
      notasMap[n.idInscripcion] = n
    })

    filas.value = estudiantes.map(est => {
      const registro = notasMap[est.idInscripcion] ?? null
      return {
        idInscripcion:  est.idInscripcion,
        nombre:         `${est.apellido1 ?? ''} ${est.apellido2 ?? ''}, ${est.nombre1 ?? ''} ${est.nombre2 ?? ''}`.trim(),
        ci:             est.ci,
        idNota:         registro?.idNota  ?? null,
        nota_original:  registro?.nota    ?? null,
        nota_input:     registro?.nota    ?? '',
        editando:       false,
        error:          '',
      }
    })
  } catch {
    flash('err', 'No se pudieron cargar los datos del curso seleccionado.')
  } finally {
    loading.value = false
  }
}

// ── Guardar nota ──────────────────────────────────────────────────────────────
async function guardarNota(fila) {
  fila.error = ''
  const val = fila.nota_input
  if (val === '' || val === null || val === undefined) {
    fila.error = 'La nota es requerida.'
    return
  }
  const n = Number(val)
  if (isNaN(n) || n < 0 || n > 100) {
    fila.error = 'Debe estar entre 0 y 100.'
    return
  }

  guardandoId.value = fila.idInscripcion
  try {
    if (fila.idNota) {
      await props.api.put(`/docente/notas/${fila.idNota}`, { nota: n })
    } else {
      const res = await props.api.post('/docente/notas', {
        estudiante_materia_id: fila.idInscripcion,
        nota: n
      })
      fila.idNota = res.data.data?.nota?.idNota ?? res.data.nota?.idNota ?? null
    }
    fila.nota_original = n
    fila.editando      = false
    flash('ok', `Nota de ${fila.nombre.split(',')[0]} guardada correctamente.`)
  } catch (err) {
    fila.error = err.response?.data?.message || 'Error al guardar la nota.'
  } finally {
    guardandoId.value = null
  }
}

function cancelarEdicion(fila) {
  fila.nota_input = fila.nota_original ?? ''
  fila.editando   = false
  fila.error      = ''
}

// ── Stats ─────────────────────────────────────────────────────────────────────
const stats = computed(() => {
  const conNota   = filas.value.filter(f => f.nota_original !== null)
  const aprobados = conNota.filter(f => Number(f.nota_original) >= 51)
  const promedio  = conNota.length
    ? (conNota.reduce((s, f) => s + Number(f.nota_original), 0) / conNota.length).toFixed(1)
    : '—'
  return {
    total:      filas.value.length,
    conNota:    conNota.length,
    aprobados:  aprobados.length,
    reprobados: conNota.length - aprobados.length,
    promedio,
  }
})

onMounted(cargarCursos)
</script>

<template>
  <div class="drn-root">

    <!-- Header -->
    <div class="workspace-topbar">
      <div class="topbar-left">
        <span class="context-path">Docentes / Calificaciones</span>
        <h2>Registrar Calificaciones</h2>
        <p class="subtitle-text" v-if="view === 'cursos'">Selecciona un curso para registrar las notas de los estudiantes inscritos</p>
        <p class="subtitle-text" v-else>Gestiona las notas de los estudiantes del curso seleccionado</p>
      </div>
    </div>

    <!-- Alerts -->
    <transition name="alert-slide">
      <div v-if="successMessage" class="alert-inline success mb-4">{{ successMessage }}</div>
    </transition>
    <transition name="alert-slide">
      <div v-if="errorMessage" class="alert-inline error mb-4">{{ errorMessage }}</div>
    </transition>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- VISTA: LISTA DE CURSOS                                        -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <template v-if="view === 'cursos'">

      <!-- Filtro por semestre -->
      <div class="drn-filters">
        <div class="drn-filter-group">
          <label class="drn-filter-label">Semestre</label>
          <select v-model="semestreFiltro" class="drn-filter-select">
            <option value="">Todos los semestres</option>
            <option v-for="s in semestresDisponibles" :key="s" :value="String(s)">
              Semestre {{ s }}
            </option>
          </select>
        </div>
        <span class="drn-count">{{ cursosFiltrados.length }} curso{{ cursosFiltrados.length !== 1 ? 's' : '' }}</span>
      </div>

      <!-- Lista de cursos -->
      <div v-if="loading" class="spinner-container">
        <div class="loading-spinner"></div>
        <span class="loading-text">Cargando cursos...</span>
      </div>

      <div v-else-if="cursosFiltrados.length === 0" class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" class="empty-icon"><path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/></svg>
        <p>No hay cursos asignados para los filtros seleccionados.</p>
      </div>

      <div v-else class="drn-course-list">
        <div
          v-for="curso in cursosFiltrados"
          :key="curso.idCursoMateria"
          class="drn-course-card"
        >
          <div class="drn-course-info">
            <span class="drn-course-name">{{ curso.materia_nombre }}</span>
            <span class="drn-course-meta">{{ curso.turno_nombre }} &middot; Semestre {{ curso.materia_semestre }}</span>
          </div>
          <button class="drn-btn-action" type="button" @click="seleccionarCurso(curso)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
            Asignar Notas
          </button>
        </div>
      </div>
    </template>

    <!-- ═══════════════════════════════════════════════════════════════ -->
    <!-- VISTA: REGISTRO DE NOTAS                                      -->
    <!-- ═══════════════════════════════════════════════════════════════ -->
    <template v-if="view === 'notas'">

      <!-- Volver -->
      <button class="drn-btn-back" type="button" @click="volver" :disabled="loading">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Volver a cursos
      </button>

      <!-- Stats -->
      <transition name="fade-up">
        <div v-if="filas.length > 0 && !loading" class="stats-bar">
          <div class="stat-pill">
            <span class="stat-num">{{ stats.total }}</span>
            <span class="stat-label">Inscritos</span>
          </div>
          <div class="stat-pill">
            <span class="stat-num">{{ stats.conNota }}</span>
            <span class="stat-label">Con nota</span>
          </div>
          <div class="stat-pill green">
            <span class="stat-num">{{ stats.aprobados }}</span>
            <span class="stat-label">Aprobados</span>
          </div>
          <div class="stat-pill red">
            <span class="stat-num">{{ stats.reprobados }}</span>
            <span class="stat-label">Reprobados</span>
          </div>
          <div class="stat-pill blue">
            <span class="stat-num">{{ stats.promedio }}</span>
            <span class="stat-label">Promedio</span>
          </div>
        </div>
      </transition>

      <!-- Spinner -->
      <div v-if="loading" class="spinner-container">
        <div class="loading-spinner"></div>
        <span class="loading-text">Cargando estudiantes...</span>
      </div>

      <!-- Tabla -->
      <transition name="fade-up">
        <div v-if="!loading" class="table-card-wrapper">
          <div class="table-card-header">
            <h4>
              Estudiantes inscritos
              <span v-if="materiaInfo" class="materia-badge">{{ materiaInfo.materia_nombre }}</span>
            </h4>
            <span class="total-badge">{{ filas.length }} estudiante{{ filas.length !== 1 ? 's' : '' }}</span>
          </div>

          <div v-if="filas.length === 0" class="empty-table-msg">
            No hay estudiantes inscritos en esta materia.
          </div>

          <div v-else class="table-responsive">
            <table class="workspace-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Estudiante</th>
                  <th class="txt-center">CI</th>
                  <th class="txt-center">Nota</th>
                  <th class="txt-center">Estado</th>
                  <th class="txt-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(fila, idx) in filas" :key="fila.idInscripcion"
                    :class="{ 'row-editing': fila.editando }">

                  <td class="txt-center text-muted num-col">{{ idx + 1 }}</td>
                  <td class="primary-cell font-medium">{{ fila.nombre }}</td>
                  <td class="txt-center text-muted font-mono">{{ fila.ci }}</td>

                  <td class="txt-center nota-col">
                    <div v-if="fila.editando" class="nota-input-wrap">
                      <input
                        v-model.number="fila.nota_input"
                        type="number" min="0" max="100" step="0.01"
                        placeholder="0–100"
                        class="nota-input"
                        :disabled="guardandoId === fila.idInscripcion"
                        @keyup.enter="guardarNota(fila)"
                        @keyup.escape="cancelarEdicion(fila)"
                        autofocus
                      />
                      <small v-if="fila.error" class="field-error">{{ fila.error }}</small>
                    </div>
                    <span v-else class="nota-badge" :class="notaClass(fila.nota_original)">
                      {{ fila.nota_original !== null ? Number(fila.nota_original).toFixed(1) : '—' }}
                    </span>
                  </td>

                  <td class="txt-center">
                    <span class="badge-state" :class="notaClass(fila.nota_original)">
                      <template v-if="fila.nota_original === null">Sin nota</template>
                      <template v-else-if="Number(fila.nota_original) >= 51">Aprobado</template>
                      <template v-else>Reprobado</template>
                    </span>
                  </td>

                  <td class="txt-center">
                    <template v-if="!fila.editando">
                      <button class="action-row-btn" @click="fila.editando = true; fila.error = ''">
                        <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-2.207 2.207L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                        {{ fila.nota_original !== null ? 'Editar' : 'Agregar' }}
                      </button>
                    </template>
                    <template v-else>
                      <div class="action-edit-group">
                        <button class="save-btn" @click="guardarNota(fila)"
                                :disabled="guardandoId === fila.idInscripcion">
                          <span v-if="guardandoId === fila.idInscripcion" class="btn-spinner"></span>
                          <svg v-else width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                          Guardar
                        </button>
                        <button class="cancel-btn" @click="cancelarEdicion(fila)"
                                :disabled="guardandoId === fila.idInscripcion">
                          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                      </div>
                    </template>
                  </td>

                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </transition>
    </template>

  </div>
</template>

<style scoped>
/* ── Base ─────────────────────────────────────────────────────────────────── */
.drn-root { animation: fadeIn 0.25s ease-out; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: none; } }
.subtitle-text { font-size: 0.88rem; color: #6b7280; margin-top: 0.2rem; }
.mb-4          { margin-bottom: 1.25rem; }

/* ── Filters ──────────────────────────────────────────────────────────────── */
.drn-filters {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-top: 1rem;
  margin-bottom: 1.25rem;
  flex-wrap: wrap;
}
.drn-filter-group {
  display: flex;
  align-items: center;
  gap: 0.6rem;
}
.drn-filter-label {
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  font-weight: 700;
  color: #6b7280;
}
.drn-filter-select {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,0.12);
  border-radius: 8px;
  padding: 0.5rem 2rem 0.5rem 0.75rem;
  font-size: 0.85rem;
  color: var(--color-black);
  outline: none;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 0.5rem center;
  background-size: 1rem;
  cursor: pointer;
}
.drn-filter-select:focus { border-color: #38bdf8; box-shadow: 0 0 0 2px rgba(56,189,248,0.15); }
.drn-count {
  font-size: 0.8rem;
  font-weight: 600;
  color: #6b7280;
  background: #f3f4f6;
  padding: 0.3rem 0.7rem;
  border-radius: 6px;
}

/* ── Course list ──────────────────────────────────────────────────────────── */
.drn-course-list {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}
.drn-course-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,0.07);
  border-radius: 12px;
  padding: 1rem 1.25rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.drn-course-card:hover {
  border-color: rgba(56,189,248,0.3);
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.drn-course-info {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}
.drn-course-name {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--color-black);
}
.drn-course-meta {
  font-size: 0.78rem;
  color: #6b7280;
}
.drn-btn-action {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: #4e615e;
  color: #fff;
  border: none;
  border-radius: 8px;
  padding: 0.5rem 1rem;
  font-size: 0.78rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
  white-space: nowrap;
}
.drn-btn-action:hover { background: #3b4a48; }

/* ── Back button ──────────────────────────────────────────────────────────── */
.drn-btn-back {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  background: transparent;
  border: 1px solid rgba(0,0,0,0.1);
  color: #6b7280;
  border-radius: 8px;
  padding: 0.45rem 0.9rem;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s;
  margin-bottom: 1rem;
}
.drn-btn-back:hover { background: #f3f4f6; color: var(--color-black); border-color: #38bdf8; }
.drn-btn-back:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Stats bar ────────────────────────────────────────────────────────────── */
.stats-bar {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  margin-bottom: 1.25rem;
}
.stat-pill {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.1rem;
  background: #fafafa;
  border: 1px solid rgba(0,0,0,0.07);
  border-radius: 10px;
  padding: 0.6rem 1.1rem;
  min-width: 80px;
}
.stat-num   { font-size: 1.3rem; font-weight: 700; color: var(--color-black); }
.stat-label { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; font-weight: 600; }
.stat-pill.green .stat-num { color: #22c55e; }
.stat-pill.red   .stat-num { color: #ef4444; }
.stat-pill.blue  .stat-num { color: #38bdf8; }

/* ── Empty state ─────────────────────────────────────────────────────────── */
.empty-state {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 5rem 2rem; gap: 1rem; color: #9ca3af;
}
.empty-icon { opacity: 0.4; }
.empty-state p { font-size: 0.9rem; text-align: center; max-width: 320px; }

/* ── Table wrapper ───────────────────────────────────────────────────────── */
.table-card-wrapper {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,0.06);
  border-radius: 14px;
  overflow: hidden;
}
.table-card-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 1.15rem 1.5rem;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}
.table-card-header h4 { font-size: 1rem; font-weight: 600; color: var(--color-black); display: flex; align-items: center; gap: 0.6rem; }
.materia-badge {
  font-size: 0.75rem; font-weight: 600; background: rgba(56,189,248,0.1);
  color: #0ea5e9; padding: 0.2rem 0.6rem; border-radius: 6px;
}
.total-badge {
  font-size: 0.75rem; font-weight: 600; color: #6b7280;
  background: #f3f4f6; padding: 0.25rem 0.65rem; border-radius: 6px;
}
.table-responsive { overflow-x: auto; }
.empty-table-msg { text-align: center; color: #9ca3af; padding: 3rem; }

/* ── Table ───────────────────────────────────────────────────────────────── */
.workspace-table { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
.workspace-table th {
  padding: 0.85rem 1.25rem;
  background: #fafafa; color: #6b7280;
  font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700;
  border-bottom: 1px solid rgba(0,0,0,0.06);
}
.workspace-table td {
  padding: 0.9rem 1.25rem;
  border-bottom: 1px solid rgba(0,0,0,0.04);
  color: #4b5563;
  vertical-align: middle;
}
.workspace-table tr:hover td { background: #fafafa; }
.workspace-table tr.row-editing td { background: rgba(56,189,248,0.03); }
.workspace-table tr:last-child td { border-bottom: none; }

.primary-cell { color: var(--color-black) !important; }
.font-medium  { font-weight: 500; }
.font-mono    { font-family: monospace; }
.text-muted   { color: #6b7280; }
.txt-center   { text-align: center; }
.num-col      { width: 2.5rem; }
.nota-col     { width: 9rem; }

/* ── Nota badge ──────────────────────────────────────────────────────────── */
.nota-badge {
  display: inline-block;
  font-size: 1rem; font-weight: 700; font-family: monospace;
  padding: 0.2rem 0.6rem; border-radius: 6px;
}
.nota-badge.aprobado { color: #22c55e; background: rgba(34,197,94,0.08); }
.nota-badge.reprobado { color: #ef4444; background: rgba(239,68,68,0.08); }
.nota-badge.sin-nota { color: #9ca3af; background: #f3f4f6; font-size: 0.85rem; }

/* ── Badge estado ────────────────────────────────────────────────────────── */
.badge-state {
  display: inline-block; padding: 0.2rem 0.6rem;
  border-radius: 6px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
}
.badge-state.aprobado  { background: rgba(34,197,94,0.1);  color: #22c55e; }
.badge-state.reprobado { background: rgba(239,68,68,0.1);  color: #ef4444; }
.badge-state.sin-nota  { background: #f3f4f6; color: #9ca3af; }

/* ── Nota input inline ───────────────────────────────────────────────────── */
.nota-input-wrap { display: flex; flex-direction: column; align-items: center; gap: 0.25rem; }
.nota-input {
  width: 90px; text-align: center;
  background: var(--color-white);
  border: 1.5px solid #38bdf8;
  border-radius: 7px;
  padding: 0.45rem 0.6rem;
  font-size: 0.9rem; font-weight: 600;
  color: var(--color-black);
  box-shadow: 0 0 0 3px rgba(56,189,248,0.12);
  transition: all 0.2s;
}
.nota-input:focus { outline: none; border-color: #0ea5e9; box-shadow: 0 0 0 3px rgba(14,165,233,0.2); }
.nota-input:disabled { opacity: 0.5; }
.field-error { color: #ef4444; font-size: 0.72rem; font-weight: 500; }

/* ── Action buttons ──────────────────────────────────────────────────────── */
.action-row-btn {
  display: inline-flex; align-items: center; gap: 0.35rem;
  background: transparent;
  border: 1px solid rgba(0,0,0,0.1);
  color: #6b7280; padding: 0.38rem 0.75rem;
  border-radius: 6px; font-size: 0.78rem; font-weight: 600;
  cursor: pointer; transition: all 0.2s;
}
.action-row-btn svg { width: 13px; height: 13px; }
.action-row-btn:hover { background: var(--color-linen); color: var(--color-black); border-color: #38bdf8; }

.action-edit-group { display: flex; align-items: center; gap: 0.4rem; justify-content: center; }

.save-btn {
  display: inline-flex; align-items: center; gap: 0.3rem;
  background: #22c55e; color: #fff;
  border: none; border-radius: 6px;
  padding: 0.38rem 0.75rem; font-size: 0.78rem; font-weight: 700;
  cursor: pointer; transition: all 0.2s;
}
.save-btn:hover:not(:disabled) { background: #16a34a; }
.save-btn:disabled { opacity: 0.6; cursor: not-allowed; }

.cancel-btn {
  background: #f3f4f6; border: 1px solid rgba(0,0,0,0.1);
  color: #6b7280; border-radius: 6px;
  padding: 0.38rem 0.6rem; font-size: 0.8rem; font-weight: 700;
  cursor: pointer; transition: all 0.2s;
}
.cancel-btn:hover:not(:disabled) { background: #e5e7eb; color: #ef4444; }
.cancel-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Alerts ──────────────────────────────────────────────────────────────── */
.alert-inline {
  padding: 0.7rem 1rem; border-radius: 8px;
  font-size: 0.85rem; font-weight: 600;
}
.alert-inline.success { background: rgba(34,197,94,0.1);  color: #22c55e; border: 1px solid rgba(34,197,94,0.2); }
.alert-inline.error   { background: rgba(239,68,68,0.1);  color: #ef4444; border: 1px solid rgba(239,68,68,0.2); }

/* ── Spinner ─────────────────────────────────────────────────────────────── */
.spinner-container {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  padding: 4rem; gap: 1rem;
}
.loading-spinner {
  width: 32px; height: 32px;
  border: 3px solid rgba(56,189,248,0.1);
  border-radius: 50%; border-top-color: #38bdf8;
  animation: spin 1s ease-in-out infinite;
}
.loading-text { font-size: 0.85rem; color: #6b7280; }

.btn-spinner {
  width: 13px; height: 13px;
  border: 2px solid rgba(255,255,255,0.4);
  border-radius: 50%; border-top-color: #fff;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}

@keyframes spin { to { transform: rotate(360deg); } }

/* ── Transitions ─────────────────────────────────────────────────────────── */
.fade-up-enter-active { transition: opacity 0.25s ease, transform 0.25s ease; }
.fade-up-enter-from  { opacity: 0; transform: translateY(8px); }

.alert-slide-enter-active, .alert-slide-leave-active { transition: all 0.25s ease; }
.alert-slide-enter-from, .alert-slide-leave-to { opacity: 0; transform: translateY(-6px); }
</style>
