<script setup>
import { ref, reactive, onMounted } from 'vue'

const props = defineProps({
  user: Object,
  api: {
    type: [Object, Function],
    required: true
  },
  badgeTone: String
})

// ── Estado principal ─────────────────────────────────────────────────────────
const loading    = ref(false)
const submitting = ref(false)
const successMessage = ref('')
const errorMessage   = ref('')
const errors = ref({})

const cursos          = ref([])
const estudiantes     = ref([])
const notasRegistradas = ref([])

const form = reactive({
  idCursoMateria:      '',
  estudiante_materia_id: '',
  nota: ''
})

// ── Estado del modal Editar ───────────────────────────────────────────────────
const modalVisible   = ref(false)
const guardando      = ref(false)
const modalError     = ref('')
const modalNota      = ref('')
const modalNotaError = ref('')
const notaSeleccionada = ref(null)   // fila completa que se está editando

// ── Helpers ───────────────────────────────────────────────────────────────────
function resetMessages() {
  successMessage.value = ''
  errorMessage.value   = ''
}

async function recargarNotas() {
  if (!form.idCursoMateria) return
  const res = await props.api.get('/docente/notas', {
    params: { idCursoMateria: form.idCursoMateria }
  })
  notasRegistradas.value = res.data.data ?? res.data
}

// ── Cargar cursos ─────────────────────────────────────────────────────────────
async function cargarCursos() {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data } = await props.api.get('/docente/cursos')
    cursos.value = data.data ?? data
  } catch {
    errorMessage.value = 'No se pudieron cargar tus cursos asignados.'
  } finally {
    loading.value = false
  }
}

// ── Cambio de curso ───────────────────────────────────────────────────────────
async function alCambiarCurso() {
  form.estudiante_materia_id = ''
  form.nota   = ''
  estudiantes.value  = []
  errors.value       = {}
  resetMessages()

  if (!form.idCursoMateria) return

  console.log('Curso ID seleccionado:', form.idCursoMateria) // ← ¿llega aquí?
  console.log('API object:', props.api)                       // ← ¿existe la api?

  loading.value = true
  try {
    console.log('Iniciando requests...')                      // ← ¿entra al try?
    const [resE, resN] = await Promise.all([
      props.api.get('/docente/estudiantes', { params: { idCursoMateria: form.idCursoMateria } }),
      props.api.get('/docente/notas',       { params: { idCursoMateria: form.idCursoMateria } }),
    ])
    console.log('Estudiantes:', resE.data)
    console.log('Notas:', resN.data)
    estudiantes.value      = resE.data.data ?? resE.data
    notasRegistradas.value = resN.data.data ?? resN.data
  } catch (error) {
    console.error('Error completo:', error)
    console.error('Respuesta:', error.response?.data)
    errorMessage.value = 'No se pudieron cargar los datos del curso seleccionado.'
  } finally {
    loading.value = false
  }
}

// ── Validación formulario registro ────────────────────────────────────────────
function validarFormulario() {
  const e = {}
  if (!form.idCursoMateria)        e.idCursoMateria        = 'El curso es requerido.'
  if (!form.estudiante_materia_id) e.estudiante_materia_id = 'El estudiante es requerido.'
  if (form.nota === '' || form.nota === null) {
    e.nota = 'La nota es requerida.'
  } else {
    const n = Number(form.nota)
    if (isNaN(n) || n < 0 || n > 100) e.nota = 'La nota debe estar entre 0 y 100.'
  }
  errors.value = e
  return Object.keys(e).length === 0
}

// ── Registrar nota ────────────────────────────────────────────────────────────
async function registrarNota() {
  resetMessages()
  if (!validarFormulario()) return

  submitting.value = true
  try {
    const response = await props.api.post('/docente/notas', {
      estudiante_materia_id: Number(form.estudiante_materia_id),
      nota: Number(form.nota)
    })
    successMessage.value           = response.data.message || 'Calificación registrada con éxito.'
    form.estudiante_materia_id = ''
    form.nota                  = ''
    await recargarNotas()
  } catch (error) {
    const d = error.response?.data
    errorMessage.value = d?.message || 'Error al registrar la calificación.'
    if (d?.errors) errors.value = d.errors
  } finally {
    submitting.value = false
  }
}

// ── Modal: Abrir ──────────────────────────────────────────────────────────────
function abrirModalEditar(nota) {
  notaSeleccionada.value = nota
  modalNota.value        = Number(nota.nota)
  modalError.value       = ''
  modalNotaError.value   = ''
  modalVisible.value     = true
}

// ── Modal: Cerrar ─────────────────────────────────────────────────────────────
function cerrarModal() {
  modalVisible.value   = false
  notaSeleccionada.value = null
  modalNota.value      = ''
  modalError.value     = ''
  modalNotaError.value = ''
}

// ── Modal: Guardar cambios ────────────────────────────────────────────────────
async function guardarEdicion() {
  modalNotaError.value = ''
  modalError.value     = ''

  // Validación local
  const n = Number(modalNota.value)
  if (modalNota.value === '' || isNaN(n)) {
    modalNotaError.value = 'La nota es requerida.'
    return
  }
  if (n < 0 || n > 100) {
    modalNotaError.value = 'La nota debe estar entre 0 y 100.'
    return
  }

  guardando.value = true
  try {
    const response = await props.api.put(
      `/docente/notas/${notaSeleccionada.value.idNota}`,
      { nota: n }
    )
    successMessage.value = response.data.message || 'Calificación actualizada correctamente.'
    cerrarModal()
    await recargarNotas()
  } catch (error) {
    const d = error.response?.data
    modalError.value = d?.message || 'Error al actualizar la calificación.'
  } finally {
    guardando.value = false
  }
}

onMounted(cargarCursos)
</script>

<template>
  <div class="fade-in-view">

    <!-- Header -->
    <div class="workspace-topbar">
      <div class="topbar-left">
        <span class="context-path">Docentes / Calificaciones</span>
        <h2>Registrar Calificaciones</h2>
        <p class="subtitle-text">Gestión de notas académicas de estudiantes</p>
      </div>
    </div>

    <!-- Alerts globales -->
    <div v-if="successMessage" class="alert-inline success mb-4">{{ successMessage }}</div>
    <div v-if="errorMessage"   class="alert-inline error   mb-4">{{ errorMessage }}</div>

    <div class="registration-grid">

      <!-- ── Formulario Registro ── -->
      <section class="card-panel">
        <div class="panel-header">
          <h4>Formulario de Registro</h4>
        </div>
        <form @submit.prevent="registrarNota" class="form-grid">

          <label>
            <span>Curso y Materia *</span>
            <select v-model="form.idCursoMateria" @change="alCambiarCurso"
                    :disabled="submitting || loading">
              <option value="" disabled>Seleccione un curso</option>
              <option v-for="curso in cursos" :key="curso.idCursoMateria"
                      :value="curso.idCursoMateria">
                {{ curso.materia_nombre }} — {{ curso.turno_nombre }}
              </option>
            </select>
            <small v-if="errors.idCursoMateria" class="field-error">{{ errors.idCursoMateria }}</small>
          </label>

          <label>
            <span>Estudiante *</span>
            <select v-model="form.estudiante_materia_id"
                    :disabled="!form.idCursoMateria || submitting || loading">
              <option value="" disabled>Seleccione un estudiante</option>
              <option v-for="est in estudiantes" :key="est.idInscripcion"
                      :value="est.idInscripcion">
                {{ est.apellido1 }} {{ est.apellido2 }}, {{ est.nombre1 }} (CI: {{ est.ci }})
              </option>
            </select>
            <small v-if="errors.estudiante_materia_id" class="field-error">
              {{ errors.estudiante_materia_id }}
            </small>
          </label>

          <label>
            <span>Calificación (0 – 100) *</span>
            <input v-model.number="form.nota" type="number" min="0" max="100"
                   step="0.01" placeholder="Ej. 85"
                   :disabled="!form.estudiante_materia_id || submitting || loading" />
            <small v-if="errors.nota" class="field-error">{{ errors.nota }}</small>
          </label>

          <div class="form-actions">
            <button type="submit" class="primary-btn"
                    :disabled="!form.estudiante_materia_id || submitting || loading">
              <span v-if="submitting">Registrando...</span>
              <span v-else>Registrar Nota</span>
            </button>
          </div>
        </form>
      </section>

      <!-- ── Tabla de Calificaciones ── -->
      <section class="table-card-wrapper">
        <div class="table-card-header">
          <h4>Calificaciones Registradas</h4>
        </div>

        <div v-if="loading" class="spinner-container">
          <div class="loading-spinner"></div>
          <span class="loading-text">Cargando información...</span>
        </div>

        <div v-else class="table-responsive">
          <table class="workspace-table">
            <thead>
              <tr>
                <th>Estudiante</th>
                <th>Materia</th>
                <th class="txt-center">Nota</th>
                <th class="txt-center">Estado</th>
                <th class="txt-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="nota in notasRegistradas" :key="nota.idNota">
                <td class="primary-cell font-medium">{{ nota.estudiante_nombre }}</td>
                <td class="text-muted">{{ nota.materia_nombre }}</td>
                <td class="font-medium txt-center font-mono">{{ Number(nota.nota).toFixed(1) }}</td>
                <td class="txt-center">
                  <span class="badge-state"
                        :class="Number(nota.nota) >= 51 ? 'approved' : 'failed'">
                    {{ Number(nota.nota) >= 51 ? 'Aprobado' : 'Reprobado' }}
                  </span>
                </td>
                <td class="txt-center">
                  <button class="action-row-btn" @click="abrirModalEditar(nota)">
                    ✏️ Editar
                  </button>
                </td>
              </tr>

              <tr v-if="!form.idCursoMateria">
                <td colspan="5" class="empty-table-msg">
                  Seleccione un curso para ver las calificaciones registradas.
                </td>
              </tr>
              <tr v-else-if="notasRegistradas.length === 0">
                <td colspan="5" class="empty-table-msg">
                  No hay notas registradas para este curso aún.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <!-- ═══════════════════════════════════════════════════════════ -->
    <!--  MODAL: Editar Calificación                               -->
    <!-- ═══════════════════════════════════════════════════════════ -->
    <Teleport to="body">
      <Transition name="modal-fade">
        <div v-if="modalVisible" class="modal-overlay" @mousedown.self="cerrarModal">
          <div class="modal-card" role="dialog" aria-modal="true"
               aria-labelledby="modal-title">

            <!-- Cabecera modal -->
            <div class="modal-header">
              <h3 id="modal-title">Editar Calificación</h3>
              <button class="modal-close-btn" @click="cerrarModal" aria-label="Cerrar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="18" y1="6" x2="6" y2="18"></line>
                  <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
              </button>
            </div>

            <!-- Info del registro -->
            <div class="modal-info-grid">
              <div class="modal-info-row">
                <span class="modal-info-label">Estudiante</span>
                <span class="modal-info-value">{{ notaSeleccionada?.estudiante_nombre }}</span>
              </div>
              <div class="modal-info-row">
                <span class="modal-info-label">Materia</span>
                <span class="modal-info-value">{{ notaSeleccionada?.materia_nombre }}</span>
              </div>
              <div class="modal-info-row">
                <span class="modal-info-label">Nota actual</span>
                <span class="modal-info-value accent">{{ Number(notaSeleccionada?.nota).toFixed(1) }}</span>
              </div>
            </div>

            <hr class="modal-divider" />

            <!-- Formulario edición -->
            <form @submit.prevent="guardarEdicion" class="modal-form">
              <label>
                <span>Nueva nota (0 – 100) *</span>
                <input v-model.number="modalNota" type="number" min="0" max="100"
                       step="0.01" placeholder="Ej. 78"
                       :disabled="guardando"
                       autofocus />
                <small v-if="modalNotaError" class="field-error">{{ modalNotaError }}</small>
              </label>

              <!-- Error del backend -->
              <div v-if="modalError" class="alert-inline error" style="margin-top:0.5rem;">
                {{ modalError }}
              </div>

              <!-- Botones -->
              <div class="modal-actions">
                <button type="button" class="secondary-btn" @click="cerrarModal"
                        :disabled="guardando">
                  Cancelar
                </button>
                <button type="submit" class="primary-btn" :disabled="guardando">
                  <span v-if="guardando" class="btn-spinner"></span>
                  <span v-if="guardando">Guardando...</span>
                  <span v-else>Guardar Cambios</span>
                </button>
              </div>
            </form>

          </div>
        </div>
      </Transition>
    </Teleport>

  </div>
</template>

<style scoped>
.subtitle-text { font-size: 0.9rem; color: #6b7280; margin-top: 0.2rem; }
.mb-4          { margin-bottom: 1.5rem; }

.registration-grid {
  display: grid;
  grid-template-columns: 1fr 2fr;
  gap: 1.75rem;
  margin-top: 1rem;
}
@media (max-width: 1024px) { .registration-grid { grid-template-columns: 1fr; } }

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

.form-grid { display: flex; flex-direction: column; gap: 1.25rem; }

label { display: flex; flex-direction: column; font-size: 0.85rem; color: #6b7280; font-weight: 600; }
label span { margin-bottom: 0.35rem; }

select, input {
  background: var(--color-white);
  border: 1px solid rgba(0, 0, 0, 0.1);
  color: var(--color-black);
  padding: 0.75rem 1rem;
  border-radius: 8px;
  width: 100%;
  font-size: 0.9rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
select:focus, input:focus {
  outline: none;
  border-color: #38bdf8;
  box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.15);
}
select:disabled, input:disabled { opacity: 0.5; cursor: not-allowed; }

.field-error { color: #ef4444; font-size: 0.75rem; margin-top: 0.35rem; font-weight: 500; }

.form-actions { margin-top: 0.5rem; }

.primary-btn {
  background: #38bdf8;
  color: #0f172a;
  border: none;
  width: 100%;
  padding: 0.8rem 1.5rem;
  border-radius: 8px;
  font-weight: 700;
  font-size: 0.9rem;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  transition: all 0.2s ease;
}
.primary-btn:hover:not(:disabled) { background: #0ea5e9; transform: translateY(-1px); }
.primary-btn:disabled             { opacity: 0.6; cursor: not-allowed; }

.secondary-btn {
  background: #f3f4f6;
  border: 1px solid rgba(0, 0, 0, 0.08);
  color: #374151;
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.secondary-btn:hover:not(:disabled) { background: #e5e7eb; }
.secondary-btn:disabled             { opacity: 0.5; cursor: not-allowed; }

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
.font-mono    { font-family: monospace; font-size: 0.9rem; }
.text-muted   { color: #6b7280; }
.txt-center   { text-align: center; }

.badge-state {
  padding: 0.25rem 0.6rem;
  border-radius: 6px;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  display: inline-block;
}
.badge-state.approved { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
.badge-state.failed   { background: rgba(239, 68, 68, 0.1);  color: #ef4444; }

.action-row-btn {
  background: transparent;
  border: 1px solid rgba(0, 0, 0, 0.1);
  color: #6b7280;
  padding: 0.4rem 0.85rem;
  border-radius: 6px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}
.action-row-btn:hover { background: var(--color-linen); color: var(--color-black); border-color: var(--color-mint-dark); }

.empty-table-msg { text-align: center; color: #6b7280; padding: 3rem !important; }

.spinner-container {
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  padding: 3rem; gap: 1rem;
}
.loading-spinner {
  width: 32px; height: 32px;
  border: 3px solid rgba(56, 189, 248, 0.1);
  border-radius: 50%;
  border-top-color: #38bdf8;
  animation: spin 1s ease-in-out infinite;
}
.loading-text { font-size: 0.85rem; color: #6b7280; }

.btn-spinner {
  width: 14px; height: 14px;
  border: 2px solid rgba(15, 23, 42, 0.3);
  border-radius: 50%;
  border-top-color: #0f172a;
  animation: spin 0.7s linear infinite;
  display: inline-block;
}

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

.fade-in-view { animation: fadeIn 0.25s ease-out; }
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ═══════════════════════════════════════════════════════════════════ */
/*  MODAL                                                             */
/* ═══════════════════════════════════════════════════════════════════ */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 1rem;
}

.modal-card {
  background: var(--color-white);
  border: 1px solid rgba(0, 0, 0, 0.08);
  border-radius: 16px;
  width: 100%;
  max-width: 480px;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  background: transparent;
}
.modal-header h3 {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--color-black);
}
.modal-close-btn {
  background: transparent;
  border: none;
  color: #6b7280;
  cursor: pointer;
  padding: 0.25rem;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: color 0.2s;
}
.modal-close-btn:hover { color: var(--color-black); }
.modal-close-btn svg   { width: 20px; height: 20px; }

.modal-info-grid {
  display: flex;
  flex-direction: column;
  gap: 0.65rem;
  padding: 1.25rem 1.5rem 0;
}
.modal-info-row {
  display: flex;
  align-items: baseline;
  gap: 0.5rem;
}
.modal-info-label {
  font-size: 0.78rem;
  color: #6b7280;
  text-transform: uppercase;
  font-weight: 700;
  letter-spacing: 0.04em;
  min-width: 90px;
}
.modal-info-value {
  font-size: 0.9rem;
  color: #4b5563;
  font-weight: 500;
}
.modal-info-value.accent {
  color: #38bdf8;
  font-family: monospace;
  font-size: 1rem;
  font-weight: 700;
}

.modal-divider {
  border: 0;
  border-top: 1px solid rgba(0, 0, 0, 0.05);
  margin: 1.25rem 0 0;
}

.modal-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1.25rem 1.5rem 1.5rem;
}
.modal-form label { color: #6b7280; }

.modal-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: flex-end;
  margin-top: 0.5rem;
}
.modal-actions .primary-btn { width: auto; }

.modal-fade-enter-active,
.modal-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}
.modal-fade-enter-from,
.modal-fade-leave-to {
  opacity: 0;
  transform: scale(0.97);
}
</style>
