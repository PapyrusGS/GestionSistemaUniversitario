<script setup>
import { onMounted, reactive, ref } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

// ─── State ────────────────────────────────────────────────────────────────────
const carreras       = ref([])
const loading        = ref(false)
const submitting     = ref(false)
const showModal      = ref(false)
const isEditing      = ref(false)
const confirmTarget  = ref(null)   // carrera to be disabled (confirm dialog)
const successMessage = ref('')
const errorMessage   = ref('')
const errors         = ref({})

const form = reactive({
  idCarrera:   null,
  nombre:      '',
  descripcion: '',
})

// ─── API calls ────────────────────────────────────────────────────────────────
async function fetchCarreras() {
  loading.value = true
  clearMessages()
  try {
    const { data } = await props.api.get('/carreras')
    carreras.value = data.carreras
  } catch {
    errorMessage.value = 'No se pudieron cargar las carreras.'
  } finally {
    loading.value = false
  }
}

async function submitForm() {
  submitting.value = true
  clearMessages()
  errors.value = {}

  try {
    let data
    if (isEditing.value) {
      ;({ data } = await props.api.put(`/carreras/${form.idCarrera}`, {
        nombre:      form.nombre,
        descripcion: form.descripcion,
      }))
      const idx = carreras.value.findIndex(c => c.idCarrera === form.idCarrera)
      if (idx !== -1) carreras.value[idx] = data.carrera
    } else {
      ;({ data } = await props.api.post('/carreras', {
        nombre:      form.nombre,
        descripcion: form.descripcion,
      }))
      carreras.value.push(data.carrera)
    }

    successMessage.value = data.message
    closeModal()
  } catch (error) {
    const response = error.response?.data
    if (response?.errors) {
      errors.value = response.errors
      errorMessage.value = 'Por favor, corrige los errores del formulario.'
    } else {
      errorMessage.value = response?.message || 'Ocurrió un error inesperado.'
    }
  } finally {
    submitting.value = false
  }
}

async function confirmDisable() {
  if (!confirmTarget.value) return
  submitting.value = true
  clearMessages()

  try {
    const { data } = await props.api.delete(`/carreras/${confirmTarget.value.idCarrera}`)
    const idx = carreras.value.findIndex(c => c.idCarrera === confirmTarget.value.idCarrera)
    if (idx !== -1) carreras.value[idx] = data.carrera
    successMessage.value = data.message
  } catch (error) {
    errorMessage.value =
      error.response?.data?.message || 'No se pudo deshabilitar la carrera.'
  } finally {
    submitting.value = false
    confirmTarget.value = null
  }
}

// ─── Modal helpers ────────────────────────────────────────────────────────────
function openCreate() {
  isEditing.value    = false
  form.idCarrera     = null
  form.nombre        = ''
  form.descripcion   = ''
  errors.value       = {}
  showModal.value    = true
}

function openEdit(carrera) {
  isEditing.value    = true
  form.idCarrera     = carrera.idCarrera
  form.nombre        = carrera.nombre
  form.descripcion   = carrera.descripcion ?? ''
  errors.value       = {}
  showModal.value    = true
}

function closeModal() {
  showModal.value = false
}

function requestDisable(carrera) {
  confirmTarget.value = carrera
}

function cancelDisable() {
  confirmTarget.value = null
}

function clearMessages() {
  successMessage.value = ''
  errorMessage.value   = ''
}

onMounted(fetchCarreras)
</script>

<template>
  <div class="carrera-management">

    <!-- Header -->
    <div class="cm-header">
      <div>
        <h3>Administración de Carreras</h3>
        <p class="cm-subtitle">HU-ADM-04 · Borrado lógico habilitado</p>
      </div>
      <button class="btn-primary" @click="openCreate">
        <span class="btn-icon">＋</span> Nueva Carrera
      </button>
    </div>

    <!-- Alerts -->
    <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
    <div v-if="errorMessage"   class="alert error">{{ errorMessage }}</div>

    <!-- Table -->
    <div class="table-wrapper">
      <div v-if="loading" class="loading-overlay">
        <div class="spinner"></div>
        <span>Cargando...</span>
      </div>

      <table class="cm-table" :class="{ dimmed: loading }">
        <thead>
          <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Estado</th>
            <th>Registro</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!loading && carreras.length === 0">
            <td colspan="6" class="empty-row">No hay carreras registradas.</td>
          </tr>
          <tr
            v-for="c in carreras"
            :key="c.idCarrera"
            :class="{ 'row-disabled': !c.estado }"
          >
            <td><code>{{ c.idCarrera }}</code></td>
            <td>
              <span class="carrera-name">{{ c.nombre }}</span>
            </td>
            <td class="text-muted">{{ c.descripcion || '—' }}</td>
            <td>
              <span class="status-badge" :class="c.estado ? 'badge-active' : 'badge-inactive'">
                {{ c.estado ? 'Activa' : 'Deshabilitada' }}
              </span>
            </td>
            <td class="text-muted">{{ c.fechaRegistro }}</td>
            <td>
              <div class="action-group">
                <button class="btn-action btn-edit" @click="openEdit(c)" title="Editar carrera">
                  ✏️
                </button>
                <button
                  v-if="c.estado"
                  class="btn-action btn-disable"
                  :disabled="submitting"
                  @click="requestDisable(c)"
                  title="Deshabilitar carrera"
                >
                  🚫
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ── Create / Edit Modal ──────────────────────────────────────────────── -->
    <Teleport to="body">
      <div v-if="showModal" class="modal-backdrop" @mousedown.self="closeModal">
        <div class="modal-card" role="dialog" aria-modal="true">
          <div class="modal-header">
            <h4>{{ isEditing ? 'Editar Carrera' : 'Registrar Nueva Carrera' }}</h4>
            <button class="modal-close" @click="closeModal" aria-label="Cerrar">✕</button>
          </div>

          <div v-if="errorMessage" class="alert error alert-sm">{{ errorMessage }}</div>

          <form class="modal-form" @submit.prevent="submitForm">
            <label>
              <span>Nombre de la Carrera <em>*</em></span>
              <input
                v-model.trim="form.nombre"
                type="text"
                required
                :disabled="submitting"
                placeholder="Ej: Ingeniería en Sistemas"
              />
              <span v-if="errors.nombre" class="field-error">{{ errors.nombre[0] }}</span>
            </label>

            <label>
              <span>Descripción</span>
              <textarea
                v-model.trim="form.descripcion"
                :disabled="submitting"
                rows="3"
                placeholder="Descripción opcional de la carrera..."
              ></textarea>
              <span v-if="errors.descripcion" class="field-error">{{ errors.descripcion[0] }}</span>
            </label>

            <div class="modal-actions">
              <button type="button" class="btn-secondary" @click="closeModal" :disabled="submitting">
                Cancelar
              </button>
              <button type="submit" class="btn-primary" :disabled="submitting">
                <span v-if="submitting">Guardando...</span>
                <span v-else>{{ isEditing ? 'Guardar Cambios' : 'Crear Carrera' }}</span>
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- ── Confirm Disable Dialog ───────────────────────────────────────────── -->
    <Teleport to="body">
      <div v-if="confirmTarget" class="modal-backdrop" @mousedown.self="cancelDisable">
        <div class="modal-card confirm-card" role="alertdialog" aria-modal="true">
          <div class="confirm-icon">⚠️</div>
          <h4>¿Deshabilitar esta carrera?</h4>
          <p class="confirm-detail">
            La carrera <strong>{{ confirmTarget.nombre }}</strong> quedará marcada como inactiva.<br />
            <span class="text-muted">Los registros históricos relacionados serán conservados.</span>
          </p>
          <div class="modal-actions">
            <button class="btn-secondary" @click="cancelDisable" :disabled="submitting">
              Cancelar
            </button>
            <button class="btn-danger" @click="confirmDisable" :disabled="submitting">
              <span v-if="submitting">Deshabilitando...</span>
              <span v-else>Sí, deshabilitar</span>
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<style scoped>
/* ── Layout ─────────────────────────────────────────────────────────────── */
.carrera-management {
  display: grid;
  gap: 1.25rem;
  width: 100%;
}

.cm-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
  padding-bottom: 0.75rem;
  border-bottom: 1px solid var(--panel-border);
}

.cm-header h3 {
  margin: 0;
  font-size: 1.6rem;
  color: var(--text);
}

.cm-subtitle {
  margin: 0.2rem 0 0;
  font-size: 0.82rem;
  color: var(--muted);
}

/* ── Table ──────────────────────────────────────────────────────────────── */
.table-wrapper {
  position: relative;
  border-radius: 1rem;
  border: 1px solid rgba(180, 204, 255, 0.08);
  overflow: hidden;
}

.loading-overlay {
  position: absolute;
  inset: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  background: rgba(11, 16, 32, 0.7);
  backdrop-filter: blur(4px);
  color: var(--muted);
  font-size: 0.9rem;
}

.spinner {
  width: 1.4rem;
  height: 1.4rem;
  border: 2px solid rgba(125, 211, 252, 0.3);
  border-top-color: var(--primary);
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

@keyframes spin { to { transform: rotate(360deg); } }

.cm-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
  font-size: 0.88rem;
  transition: opacity 0.25s ease;
}

.cm-table.dimmed { opacity: 0.4; pointer-events: none; }

.cm-table th,
.cm-table td {
  padding: 0.85rem 1rem;
  border-bottom: 1px solid rgba(180, 204, 255, 0.05);
  vertical-align: middle;
}

.cm-table th {
  background: rgba(255, 255, 255, 0.02);
  color: var(--muted);
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.72rem;
  letter-spacing: 0.08em;
  white-space: nowrap;
}

.cm-table tbody tr {
  transition: background 0.15s ease;
}

.cm-table tbody tr:hover {
  background: rgba(255, 255, 255, 0.02);
}

.row-disabled {
  opacity: 0.55;
}

.carrera-name {
  font-weight: 500;
  color: var(--text);
}

.text-muted { color: var(--muted); font-size: 0.84rem; }

.empty-row {
  text-align: center;
  color: var(--muted);
  padding: 2.5rem !important;
}

code {
  background: rgba(255, 255, 255, 0.05);
  padding: 0.15rem 0.4rem;
  border-radius: 0.3rem;
  font-family: monospace;
  font-size: 0.8rem;
}

/* ── Badges ─────────────────────────────────────────────────────────────── */
.status-badge {
  display: inline-block;
  padding: 0.25rem 0.65rem;
  border-radius: 999px;
  font-size: 0.74rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  white-space: nowrap;
}

.badge-active {
  color: #bbf7d0;
  background: rgba(34, 197, 94, 0.12);
  border: 1px solid rgba(34, 197, 94, 0.24);
}

.badge-inactive {
  color: #fca5a5;
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.26);
}

/* ── Action buttons ─────────────────────────────────────────────────────── */
.action-group {
  display: flex;
  gap: 0.4rem;
  align-items: center;
}

.btn-action {
  border: none;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 0.5rem;
  padding: 0.35rem 0.55rem;
  cursor: pointer;
  font-size: 1rem;
  line-height: 1;
  transition: background 0.18s ease, transform 0.15s ease;
}

.btn-action:hover { background: rgba(255, 255, 255, 0.1); transform: translateY(-1px); }
.btn-action:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

/* ── Shared buttons ─────────────────────────────────────────────────────── */
.btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  border: none;
  border-radius: 0.85rem;
  padding: 0.7rem 1.1rem;
  font-weight: 700;
  font-size: 0.88rem;
  cursor: pointer;
  color: #02131e;
  background: linear-gradient(135deg, #67e8f9, #7dd3fc 50%, #38bdf8);
  box-shadow: 0 8px 28px rgba(56, 189, 248, 0.24);
  transition: transform 0.18s ease, opacity 0.18s ease;
}

.btn-primary:hover   { transform: translateY(-1px); }
.btn-primary:disabled { opacity: 0.6; cursor: wait; transform: none; }

.btn-secondary {
  border: 1px solid rgba(180, 204, 255, 0.18);
  border-radius: 0.85rem;
  padding: 0.7rem 1.1rem;
  font-weight: 600;
  font-size: 0.88rem;
  cursor: pointer;
  color: var(--text);
  background: rgba(255, 255, 255, 0.06);
  transition: background 0.18s ease, transform 0.15s ease;
}

.btn-secondary:hover    { background: rgba(255, 255, 255, 0.1); transform: translateY(-1px); }
.btn-secondary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

.btn-danger {
  border: none;
  border-radius: 0.85rem;
  padding: 0.7rem 1.2rem;
  font-weight: 700;
  font-size: 0.88rem;
  cursor: pointer;
  color: #fff;
  background: linear-gradient(135deg, #ef4444, #f87171);
  box-shadow: 0 8px 24px rgba(239, 68, 68, 0.28);
  transition: transform 0.18s ease, opacity 0.18s ease;
}

.btn-danger:hover    { transform: translateY(-1px); }
.btn-danger:disabled { opacity: 0.6; cursor: wait; transform: none; }

/* ── Modal ──────────────────────────────────────────────────────────────── */
.modal-backdrop {
  position: fixed;
  inset: 0;
  z-index: 9000;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(6px);
  animation: fade-in 0.18s ease;
}

@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }

.modal-card {
  width: min(92vw, 32rem);
  background: linear-gradient(180deg, rgba(14, 20, 41, 0.98), rgba(11, 16, 32, 0.96));
  border: 1px solid var(--panel-border);
  border-radius: 1.5rem;
  padding: 1.75rem;
  box-shadow: 0 40px 100px rgba(3, 8, 20, 0.65);
  animation: slide-up 0.22s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes slide-up {
  from { transform: translateY(24px); opacity: 0; }
  to   { transform: translateY(0);    opacity: 1; }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.25rem;
}

.modal-header h4 {
  margin: 0;
  font-size: 1.15rem;
  color: var(--primary);
}

.modal-close {
  border: none;
  background: rgba(255, 255, 255, 0.05);
  border-radius: 0.5rem;
  padding: 0.35rem 0.55rem;
  cursor: pointer;
  color: var(--muted);
  font-size: 0.9rem;
  transition: background 0.15s ease;
}

.modal-close:hover { background: rgba(255, 255, 255, 0.1); color: var(--text); }

.modal-form {
  display: grid;
  gap: 1rem;
}

.modal-form label {
  display: grid;
  gap: 0.5rem;
}

.modal-form span {
  font-size: 0.8rem;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: 0.1em;
}

.modal-form em {
  color: var(--danger);
  font-style: normal;
}

.modal-form input,
.modal-form textarea {
  width: 100%;
  border: 1px solid rgba(180, 204, 255, 0.14);
  border-radius: 0.8rem;
  background: rgba(6, 10, 23, 0.72);
  color: var(--text);
  padding: 0.85rem 1rem;
  font: inherit;
  outline: none;
  resize: vertical;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.modal-form input:focus,
.modal-form textarea:focus {
  border-color: rgba(125, 211, 252, 0.7);
  box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.12);
}

.field-error {
  font-size: 0.75rem;
  color: var(--danger);
  margin-top: 0.1rem;
}

.modal-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
  margin-top: 0.5rem;
}

/* ── Confirm dialog ─────────────────────────────────────────────────────── */
.confirm-card {
  text-align: center;
  max-width: 26rem;
}

.confirm-icon {
  font-size: 2.4rem;
  margin-bottom: 0.75rem;
}

.confirm-card h4 {
  margin: 0 0 0.6rem;
  font-size: 1.2rem;
  color: var(--text);
}

.confirm-detail {
  margin: 0 0 1.25rem;
  font-size: 0.9rem;
  line-height: 1.65;
  color: var(--muted);
}

.confirm-detail strong {
  color: var(--text);
}

.confirm-card .modal-actions {
  justify-content: center;
}

/* ── Alert inline (modal) ───────────────────────────────────────────────── */
.alert.alert-sm {
  padding: 0.6rem 0.85rem;
  font-size: 0.82rem;
  margin-bottom: 0.75rem;
}
</style>
