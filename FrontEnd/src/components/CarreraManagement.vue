<script setup>
import { onMounted, reactive, ref } from 'vue'

const props = defineProps({
  api: {
    type: Object,
    required: true,
  },
})

const carreras       = ref([])
const loading        = ref(false)
const submitting     = ref(false)
const showModal      = ref(false)
const isEditing      = ref(false)
const confirmTarget  = ref(null)
const successMessage = ref('')
const errorMessage   = ref('')
const errors         = ref({})

const form = reactive({
  idCarrera:   null,
  nombre:      '',
  descripcion: '',
})

async function fetchCarreras() {
  loading.value = true
  clearMessages()
  try {
    const { data } = await props.api.get('/carreras')
    carreras.value = (data.data ?? data).carreras
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
      const payload = data.data ?? data
      if (idx !== -1) carreras.value[idx] = payload.carrera
    } else {
      ;({ data } = await props.api.post('/carreras', {
        nombre:      form.nombre,
        descripcion: form.descripcion,
      }))
      const payload = data.data ?? data
      carreras.value.push(payload.carrera)
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
    const payload = data.data ?? data
    const idx = carreras.value.findIndex(c => c.idCarrera === confirmTarget.value.idCarrera)
    if (idx !== -1) carreras.value[idx] = payload.carrera
    successMessage.value = data.message
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo deshabilitar la carrera.'
  } finally {
    submitting.value = false
    confirmTarget.value = null
  }
}

async function enableCarrera(carrera) {
  submitting.value = true
  clearMessages()

  try {
    const { data } = await props.api.patch(`/carreras/${carrera.idCarrera}/enable`)
    const payload = data.data ?? data
    const idx = carreras.value.findIndex(c => c.idCarrera === carrera.idCarrera)
    if (idx !== -1) carreras.value[idx] = payload.carrera
    successMessage.value = data.message
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'No se pudo habilitar la carrera.'
  } finally {
    submitting.value = false
  }
}

function openCreate() {
  isEditing.value  = false
  form.idCarrera   = null
  form.nombre      = ''
  form.descripcion = ''
  errors.value     = {}
  clearMessages()
  showModal.value  = true
}

function openEdit(carrera) {
  isEditing.value  = true
  form.idCarrera   = carrera.idCarrera
  form.nombre      = carrera.nombre
  form.descripcion = carrera.descripcion ?? ''
  errors.value     = {}
  clearMessages()
  showModal.value  = true
}

function closeModal() { showModal.value = false }
function requestDisable(carrera) { confirmTarget.value = carrera }
function cancelDisable() { confirmTarget.value = null }
function clearMessages() { successMessage.value = ''; errorMessage.value = '' }

onMounted(fetchCarreras)
</script>

<template>
  <div class="cm-root">

    <!-- Header -->
    <div class="cm-header">
      <div>
        <h3 class="cm-title">Administración de Carreras</h3>
        <p class="cm-subtitle">Registro, edición y baja lógica de carreras académicas</p>
      </div>
      <button class="cm-btn-primary" @click="openCreate">
        <i class="ti ti-plus"></i> Nueva Carrera
      </button>
    </div>

    <!-- Alertas -->
    <div v-if="successMessage" class="uni-alert uni-alert--success">
      <i class="ti ti-circle-check"></i> {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="uni-alert uni-alert--error">
      <i class="ti ti-alert-circle"></i> {{ errorMessage }}
    </div>

    <!-- Tabla -->
    <div class="cm-table-wrap">
      <!-- Loading overlay -->
      <div v-if="loading" class="cm-loading">
        <i class="ti ti-loader-2 cm-spin"></i>
        <span>Cargando carreras...</span>
      </div>

      <table class="cm-table" :class="{ 'cm-table--dim': loading }">
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
            <td colspan="6" class="cm-empty">
              <i class="ti ti-building-off"></i>
              <span>No hay carreras registradas.</span>
            </td>
          </tr>
          <tr v-for="c in carreras" :key="c.idCarrera" :class="{ 'cm-row--inactive': !c.estado }">
            <td><code class="cm-code">{{ c.idCarrera }}</code></td>
            <td><strong class="cm-carrera-name">{{ c.nombre }}</strong></td>
            <td class="cm-muted">{{ c.descripcion || '—' }}</td>
            <td>
              <span class="cm-badge" :class="c.estado ? 'cm-badge--active' : 'cm-badge--inactive'">
                {{ c.estado ? 'Activa' : 'Deshabilitada' }}
              </span>
            </td>
            <td class="cm-muted">{{ c.fechaRegistro }}</td>
            <td>
              <div class="cm-actions">
                <button class="cm-btn-icon" @click="openEdit(c)" title="Editar carrera">
                  <i class="ti ti-pencil"></i>
                </button>
                <button
                  v-if="c.estado"
                  class="cm-btn-icon cm-btn-icon--danger"
                  :disabled="submitting"
                  @click="requestDisable(c)"
                  title="Deshabilitar carrera"
                >
                  <i class="ti ti-ban"></i>
                </button>
                <button
                  v-else
                  class="cm-btn-icon cm-btn-icon--success"
                  :disabled="submitting"
                  @click="enableCarrera(c)"
                  title="Habilitar carrera"
                >
                  <i class="ti ti-circle-check"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Modal: crear / editar -->
    <Teleport to="body">
      <div v-if="showModal" class="cm-backdrop" @mousedown.self="closeModal">
        <div class="cm-modal" role="dialog" aria-modal="true">

          <div class="cm-modal-header">
            <h4>{{ isEditing ? 'Editar Carrera' : 'Registrar Nueva Carrera' }}</h4>
            <button class="cm-close" @click="closeModal" aria-label="Cerrar">
              <i class="ti ti-x"></i>
            </button>
          </div>

          <div v-if="errorMessage" class="uni-alert uni-alert--error cm-alert-sm">
            <i class="ti ti-alert-circle"></i> {{ errorMessage }}
          </div>

          <form class="cm-form" @submit.prevent="submitForm">
            <label class="cm-field">
              <span>Nombre de la Carrera <em>*</em></span>
              <input
                v-model.trim="form.nombre"
                type="text"
                required
                :disabled="submitting"
                placeholder="Ej: Ingeniería en Sistemas"
              />
              <span v-if="errors.nombre" class="cm-field-error">{{ errors.nombre[0] }}</span>
            </label>

            <label class="cm-field">
              <span>Descripción</span>
              <textarea
                v-model.trim="form.descripcion"
                :disabled="submitting"
                rows="3"
                placeholder="Descripción opcional de la carrera..."
              ></textarea>
              <span v-if="errors.descripcion" class="cm-field-error">{{ errors.descripcion[0] }}</span>
            </label>

            <div class="cm-form-actions">
              <button type="button" class="cm-btn-ghost" @click="closeModal" :disabled="submitting">
                Cancelar
              </button>
              <button type="submit" class="cm-btn-primary" :disabled="submitting">
                <i class="ti" :class="submitting ? 'ti-loader-2 cm-spin' : (isEditing ? 'ti-device-floppy' : 'ti-plus')"></i>
                {{ submitting ? 'Guardando...' : (isEditing ? 'Guardar Cambios' : 'Crear Carrera') }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </Teleport>

    <!-- Modal: confirmar deshabilitar -->
    <Teleport to="body">
      <div v-if="confirmTarget" class="cm-backdrop" @mousedown.self="cancelDisable">
        <div class="cm-modal cm-modal--confirm" role="alertdialog" aria-modal="true">
          <div class="cm-confirm-icon">
            <i class="ti ti-alert-triangle"></i>
          </div>
          <h4 class="cm-confirm-title">¿Deshabilitar esta carrera?</h4>
          <p class="cm-confirm-body">
            La carrera <strong>{{ confirmTarget.nombre }}</strong> quedará marcada como inactiva.
            <br />
            <span class="cm-muted">Los registros históricos relacionados serán conservados.</span>
          </p>
          <div class="cm-form-actions cm-form-actions--center">
            <button class="cm-btn-ghost" @click="cancelDisable" :disabled="submitting">
              Cancelar
            </button>
            <button class="cm-btn-danger" @click="confirmDisable" :disabled="submitting">
              <i class="ti" :class="submitting ? 'ti-loader-2 cm-spin' : 'ti-ban'"></i>
              {{ submitting ? 'Deshabilitando...' : 'Sí, deshabilitar' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<style scoped>
/* ── Raíz ── */
.cm-root {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  width: 100%;
}

/* ── Header ── */
.cm-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 1rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid var(--color-linen, #d0cfca);
  flex-wrap: wrap;
}
.cm-title {
  margin: 0 0 3px;
  font-size: 1.25rem;
  font-weight: 700;
  color: #1a1a1a;
}
.cm-subtitle {
  margin: 0;
  font-size: 0.8rem;
  color: #5b5c5e;
}

/* ── Alertas heredadas ── */
.uni-alert {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 0.65rem 1rem;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid;
}
.uni-alert--success { background: #edf4f2; border-color: #8c9f96; color: #2b3d36; }
.uni-alert--error   { background: #faf0f0; border-color: #dca6a6; color: #7a2424; }
.cm-alert-sm { margin-bottom: 0.5rem; }

/* ── Tabla ── */
.cm-table-wrap {
  position: relative;
  border: 1px solid #e8e8e5;
  border-radius: 12px;
  overflow: auto;
}
.cm-loading {
  position: absolute;
  inset: 0;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6rem;
  background: rgba(255,255,255,0.8);
  font-size: 12px;
  font-weight: 600;
  color: #5b5c5e;
  border-radius: 12px;
}
.cm-loading i { font-size: 1.2rem; }
.cm-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
  text-align: left;
  transition: opacity 0.2s;
}
.cm-table--dim { opacity: 0.35; pointer-events: none; }
.cm-table th,
.cm-table td {
  padding: 0.75rem 1rem;
  border-bottom: 1px solid #f0f0ee;
  vertical-align: middle;
}
.cm-table th {
  background: #fafaf9;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #5b5c5e;
}
.cm-table tbody tr:hover { background: #fafaf9; }
.cm-table tbody tr:last-child td { border-bottom: none; }
.cm-row--inactive { opacity: 0.5; }
.cm-carrera-name { font-weight: 600; color: #1a1a1a; }
.cm-muted { color: #5b5c5e; font-size: 0.82rem; }
.cm-code {
  background: #f0f0ee;
  padding: 2px 6px;
  border-radius: 5px;
  font-family: monospace;
  font-size: 0.8rem;
}
.cm-empty {
  text-align: center;
  padding: 2.5rem 1rem !important;
  color: #8c9f96;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.85rem;
}
.cm-empty i { font-size: 2rem; opacity: 0.5; }

/* ── Badges ── */
.cm-badge {
  display: inline-block;
  padding: 3px 10px;
  border-radius: 999px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  white-space: nowrap;
}
.cm-badge--active   { background: #ddf0e6; color: #1a5235; border: 1px solid #8ec9a2; }
.cm-badge--inactive { background: #faf0f0; color: #7a2424; border: 1px solid #dca6a6; }

/* ── Acciones de fila ── */
.cm-actions { display: flex; gap: 5px; align-items: center; }
.cm-btn-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 30px; height: 30px;
  border: 1.5px solid #e8e8e5;
  border-radius: 8px;
  background: #fff;
  color: #5b5c5e;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.15s, border-color 0.15s, color 0.15s;
}
.cm-btn-icon:hover { background: #f4f4f2; border-color: #8c9f96; color: #1a1a1a; }
.cm-btn-icon--danger { color: #b85c5c; }
.cm-btn-icon--danger:hover { background: #faf0f0; border-color: #dca6a6; color: #7a2424; }
.cm-btn-icon--success { color: #2a7a4b; }
.cm-btn-icon--success:hover { background: #edf7f1; border-color: #8ec9a2; color: #1a5235; }
.cm-btn-icon:disabled { opacity: 0.4; cursor: not-allowed; }

/* ── Botones compartidos ── */
.cm-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #4e615e;
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 9px 18px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}
.cm-btn-primary:hover:not(:disabled) { background: #3b4a48; }
.cm-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

.cm-btn-ghost {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: transparent;
  color: #5b5c5e;
  border: 1.5px solid #d0cfca;
  border-radius: 20px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.cm-btn-ghost:hover:not(:disabled) { background: #f4f4f2; color: #1a1a1a; }
.cm-btn-ghost:disabled { opacity: 0.6; cursor: not-allowed; }

.cm-btn-danger {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #b85c5c;
  color: #fff;
  border: none;
  border-radius: 20px;
  padding: 9px 18px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}
.cm-btn-danger:hover:not(:disabled) { background: #9c4646; }
.cm-btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Backdrop ── */
.cm-backdrop {
  position: fixed;
  inset: 0;
  z-index: 50;
  display: flex;
  align-items: center;
  justify-content: center;
  background: rgba(0,0,0,0.4);
  padding: 1rem;
}

/* ── Modal ── */
.cm-modal {
  width: min(100%, 32rem);
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 24px 48px rgba(0,0,0,0.12);
  overflow: hidden;
}
.cm-modal--confirm {
  max-width: 26rem;
  padding: 2rem;
  text-align: center;
}

.cm-modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 1.75rem 1rem;
  border-bottom: 1px solid #e8e8e5;
}
.cm-modal-header h4 {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 700;
  color: #1a1a1a;
}
.cm-close {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px; height: 30px;
  background: transparent;
  border: none;
  border-radius: 50%;
  color: #5b5c5e;
  font-size: 1rem;
  cursor: pointer;
  transition: background 0.15s;
}
.cm-close:hover { background: #f0f0ee; color: #1a1a1a; }

/* ── Formulario del modal ── */
.cm-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  padding: 1.25rem 1.75rem 1.75rem;
}
.cm-field {
  display: flex;
  flex-direction: column;
  gap: 5px;
}
.cm-field > span {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #5b5c5e;
}
.cm-field em { color: #b85c5c; font-style: normal; }
.cm-field input,
.cm-field textarea {
  width: 100%;
  background: #fafaf9;
  border: 1.5px solid #d0cfca;
  border-radius: 10px;
  color: #1a1a1a;
  padding: 0.75rem 1rem;
  font: inherit;
  font-size: 13px;
  outline: none;
  resize: vertical;
  transition: border-color 0.15s, box-shadow 0.15s;
}
.cm-field input:focus,
.cm-field textarea:focus {
  border-color: #4e615e;
  box-shadow: 0 0 0 3px rgba(78,97,94,0.1);
}
.cm-field input::placeholder,
.cm-field textarea::placeholder { color: #a0a0a0; }
.cm-field-error {
  font-size: 11px;
  color: #b85c5c;
}

.cm-form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 0.6rem;
  margin-top: 0.25rem;
}
.cm-form-actions--center { justify-content: center; }

/* ── Confirm dialog ── */
.cm-confirm-icon {
  font-size: 2.5rem;
  color: #b07d2e;
  margin-bottom: 0.75rem;
}
.cm-confirm-title {
  margin: 0 0 0.6rem;
  font-size: 1.1rem;
  font-weight: 700;
  color: #1a1a1a;
}
.cm-confirm-body {
  margin: 0 0 1.5rem;
  font-size: 0.875rem;
  line-height: 1.65;
  color: #5b5c5e;
}
.cm-confirm-body strong { color: #1a1a1a; }

/* ── Spin ── */
.cm-spin { animation: cm-rotate 0.8s linear infinite; }
@keyframes cm-rotate { to { transform: rotate(360deg); } }
</style>