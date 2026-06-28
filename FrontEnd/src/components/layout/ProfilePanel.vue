<script setup>
import { reactive, ref, computed } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  fullName: { type: String, default: '' },
  roleName: { type: String, default: '' },
  api: { type: Object, required: true }
})

const profileForm = reactive({
  current_password: '',
  new_password: '',
  new_password_confirmation: ''
})
const profileSuccessMessage = ref('')
const profileErrorMessage = ref('')
const profileLoading = ref(false)

function resetProfileForm() {
  profileForm.current_password = ''
  profileForm.new_password = ''
  profileForm.new_password_confirmation = ''
}

async function handleUpdatePassword() {
  profileErrorMessage.value = ''
  profileSuccessMessage.value = ''

  if (profileForm.new_password !== profileForm.new_password_confirmation) {
    profileErrorMessage.value = 'La nueva contraseña y su confirmación no coinciden.'
    return
  }
  if (profileForm.new_password.length < 6) {
    profileErrorMessage.value = 'La nueva contraseña debe tener al menos 6 caracteres.'
    return
  }

  profileLoading.value = true
  try {
    await props.api.put('/perfil', {
      current_password: profileForm.current_password,
      password: profileForm.new_password,
      password_confirmation: profileForm.new_password_confirmation
    })
    profileSuccessMessage.value = 'Contraseña actualizada correctamente.'
    resetProfileForm()
  } catch (error) {
    if (error.response?.data?.errors) {
      profileErrorMessage.value = Object.values(error.response.data.errors)[0][0]
    } else {
      profileErrorMessage.value = error.response?.data?.message || 'Error al actualizar las credenciales.'
    }
  } finally {
    profileLoading.value = false
  }
}

const profileInitials = computed(() => {
  const pNombre = props.user?.nombre1 || props.user?.nombre || ''
  const pApellido = props.user?.apellido1 || ''
  return `${pNombre.charAt(0)}${pApellido.charAt(0)}`.toUpperCase() || '?'
})
</script>

<template>
  <div class="profile-panel">
    <div class="profile-info-card">
      <div class="profile-avatar-section">
        <div class="profile-avatar-circle">{{ profileInitials }}</div>
        <h3>{{ fullName || user.nombreCompleto }}</h3>
        <span class="profile-badge-role">{{ roleName || user.rol }}</span>
      </div>

      <div class="profile-details">
        <div class="profile-detail-item">
          <i class="ti ti-id"></i>
          <div>
            <span class="profile-detail-label">CI</span>
            <strong>{{ user.ci }}</strong>
          </div>
        </div>
        <div class="profile-detail-item">
          <i class="ti ti-mail"></i>
          <div>
            <span class="profile-detail-label">Correo</span>
            <strong>{{ user.correo }}</strong>
          </div>
        </div>
        <div class="profile-detail-item">
          <i class="ti ti-circle-check"></i>
          <div>
            <span class="profile-detail-label">Estado</span>
            <strong :class="user.estado ? 'text-active' : 'text-inactive'">{{ user.estado ? 'Activo' : 'Inactivo' }}</strong>
          </div>
        </div>
      </div>

      <div class="profile-note">
        <i class="ti ti-info-circle"></i>
        <p>Para modificar tus datos personales comunícate con Administración Académica.</p>
      </div>
    </div>

    <div class="profile-security-card">
      <div class="profile-security-header">
        <i class="ti ti-lock-password"></i>
        <span>Seguridad y Contraseña</span>
      </div>

      <div class="profile-form-body">
        <div v-if="profileSuccessMessage" class="uni-alert uni-alert--success">
          <i class="ti ti-circle-check"></i>
          {{ profileSuccessMessage }}
        </div>
        <div v-if="profileErrorMessage" class="uni-alert uni-alert--error">
          <i class="ti ti-alert-circle"></i>
          {{ profileErrorMessage }}
        </div>

        <form class="profile-form" @submit.prevent="handleUpdatePassword">
          <div class="uni-form-group">
            <label>Contraseña actual</label>
            <div class="uni-input-wrap">
              <i class="ti ti-lock"></i>
              <input v-model="profileForm.current_password" type="password" placeholder="Introduce tu contraseña actual" required />
            </div>
          </div>

          <div class="profile-password-grid">
            <div class="uni-form-group">
              <label>Nueva contraseña</label>
              <div class="uni-input-wrap">
                <i class="ti ti-key"></i>
                <input v-model="profileForm.new_password" type="password" placeholder="Mínimo 6 caracteres" required />
              </div>
            </div>
            <div class="uni-form-group">
              <label>Confirmar contraseña</label>
              <div class="uni-input-wrap">
                <i class="ti ti-shield-check"></i>
                <input v-model="profileForm.new_password_confirmation" type="password" placeholder="Repite la contraseña" required />
              </div>
            </div>
          </div>

          <div class="profile-actions">
            <button class="uni-btn-action uni-btn-action--accept" :disabled="profileLoading">
              <i class="ti ti-device-floppy"></i>
              {{ profileLoading ? 'Actualizando...' : 'Actualizar contraseña' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped>
.profile-panel {
  display: grid;
  grid-template-columns: 340px 1fr;
  gap: 1.5rem;
  align-items: start;
  padding: 0;
  width: 100%;
}

/* ── Left card — Info ── */
.profile-info-card {
  background: #fff;
  border: 1px solid var(--color-linen);
  border-radius: 14px;
  overflow: hidden;
}

.profile-avatar-section {
  background: #f8f9f8;
  padding: 2rem 1.5rem;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  border-bottom: 1px solid var(--color-linen);
}

.profile-avatar-circle {
  width: 75px;
  height: 75px;
  background: var(--color-mint-dark);
  color: #fff;
  font-size: 1.8rem;
  font-weight: 700;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1rem;
}

.profile-avatar-section h3 {
  margin: 0 0 .35rem;
  font-family: 'Playfair Display', serif;
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--color-black);
}

.profile-badge-role {
  font-size: .7rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: .05em;
  padding: .25rem .75rem;
  border-radius: 99px;
  background: var(--color-linen);
  color: var(--color-mint-dark);
}

.profile-details {
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.profile-detail-item {
  display: flex;
  gap: 12px;
  align-items: center;
}

.profile-detail-item i {
  width: 36px;
  height: 36px;
  background: #f8f9f8;
  border: 1px solid var(--color-linen);
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  color: var(--color-mint-light);
  flex-shrink: 0;
}

.profile-detail-item > div {
  display: flex;
  flex-direction: column;
  gap: 1px;
}

.profile-detail-label {
  font-size: .7rem;
  font-weight: 600;
  color: var(--color-dark-gray);
  text-transform: uppercase;
  letter-spacing: .05em;
}

.profile-detail-item strong {
  font-size: .95rem;
  font-weight: 600;
  color: var(--color-black);
}

.text-active { color: #1a5235; }
.text-inactive { color: #7a2424; }

.profile-note {
  margin: 0 1.25rem 1.25rem;
  padding: .75rem 1rem;
  border-radius: 10px;
  background: #fcf9f0;
  border: 1px solid #e8dcc8;
  color: #7a6b3a;
  display: flex;
  gap: 8px;
  align-items: flex-start;
}

.profile-note i {
  font-size: 1rem;
  margin-top: 2px;
  flex-shrink: 0;
}

.profile-note p {
  margin: 0;
  font-size: .78rem;
  line-height: 1.4;
}

/* ── Right card — Security ── */
.profile-security-card {
  background: #fff;
  border: 1px solid var(--color-linen);
  border-radius: 14px;
  overflow: hidden;
}

.profile-security-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 1.25rem 1.5rem;
  border-bottom: 1px solid var(--color-linen);
  font-weight: 700;
  font-size: 1rem;
  color: var(--color-mint-dark);
}

.profile-security-header i {
  font-size: 1.2rem;
}

.profile-form-body {
  padding: 1.5rem;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.uni-form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.uni-form-group label {
  font-size: .75rem;
  font-weight: 600;
  color: var(--color-dark-gray);
  text-transform: uppercase;
  letter-spacing: .05em;
}

.uni-input-wrap {
  position: relative;
}

.uni-input-wrap i {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 15px;
  color: var(--color-dark-gray);
}

.uni-input-wrap input {
  width: 100%;
  border: 1.5px solid var(--color-dark-gray);
  border-radius: 30px;
  background: transparent;
  color: var(--color-black);
  padding: 11px 16px 11px 42px;
  font-size: 13px;
  font-weight: 500;
  outline: none;
  transition: border-color .2s, box-shadow .2s;
  font-family: inherit;
}

.uni-input-wrap input:focus {
  border-color: var(--color-mint-dark);
  box-shadow: 0 0 0 3px rgba(103, 125, 123, .15);
}

.uni-input-wrap input::placeholder {
  color: #909090;
  font-weight: 400;
}

.profile-password-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.25rem;
}

.profile-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: .5rem;
}

/* ── Alerts ── */
.uni-alert {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: .75rem 1rem;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid;
  margin-bottom: 1rem;
}

.uni-alert--success {
  background: var(--uni-success-bg);
  border-color: var(--uni-success-border);
  color: var(--uni-success-text);
}

.uni-alert--error {
  background: var(--uni-error-bg);
  border-color: var(--uni-error-border);
  color: var(--uni-error-text);
}

@media (max-width: 900px) {
  .profile-panel {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 600px) {
  .profile-password-grid {
    grid-template-columns: 1fr;
  }
  .profile-actions button {
    width: 100%;
    justify-content: center;
  }
}
</style>
