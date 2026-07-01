<template>
  <div class="uni-profile-container">
    
    <!-- Encabezado Limpio -->
    <div class="uni-page-header">
      <div>
        <h2>Gestión de Perfil</h2>
        <p>Actualiza la seguridad de tus accesos y revisa los datos maestros de tu cuenta.</p>
      </div>
    </div>

    <!-- Layout Amplio y Distribuido -->
    <div class="profile-main-layout">
      
      <!-- COLUMNA IZQUIERDA: Resumen de Cuenta -->
      <aside class="profile-info-sidebar">
        <div class="uni-clean-card avatar-banner-box">
          <div class="profile-avatar-circle">
            {{ profileInitials }}
          </div>
          <h3>{{ fullName }}</h3>
          <span class="profile-badge-role">{{ roleName }}</span>
        </div>

        <div class="uni-clean-card details-box">
          <div class="profile-detail-item">
            <div class="detail-icon"><i class="ti ti-id"></i></div>
            <div class="detail-content">
              <label>Carnet de Identidad</label>
              <span>{{ user.ci }}</span>
            </div>
          </div>

          <div class="profile-detail-item">
            <div class="detail-icon"><i class="ti ti-mail"></i></div>
            <div class="detail-content">
              <label>Correo electrónico</label>
              <span>{{ user.correo }}</span>
            </div>
          </div>
        </div>

        <div class="profile-note-banner">
          <i class="ti ti-info-circle"></i>
          <p>Para modificar tus datos personales validados, por favor ponte en contacto con Administración Académica.</p>
        </div>
      </aside>

      <!-- COLUMNA DERECHA: Bloques de Trabajo Expandidos -->
      <main class="profile-content-area">
        
        <!-- Bloque Seguridad -->
        <section class="uni-clean-card work-section">
          <div class="section-title-bar">
            <i class="ti ti-lock-password"></i>
            <div>
              <h3>Seguridad y Contraseña</h3>
              <p>Te recomendamos cambiar tu clave periódicamente para mantener tu cuenta segura.</p>
            </div>
          </div>

          <div v-if="profileErrorMessage" class="uni-alert uni-alert--error">
            <i class="ti ti-alert-circle"></i>
            {{ profileErrorMessage }}
          </div>

          <div v-if="profileSuccessMessage" class="uni-alert uni-alert--success">
            <i class="ti ti-circle-check"></i>
            {{ profileSuccessMessage }}
          </div>

          <form class="profile-form" @submit.prevent="handleUpdatePassword">
            <div class="uni-form-group">
              <label>Contraseña actual</label>
              <div class="uni-input-wrap">
                <i class="ti ti-lock"></i>
                <input
                  v-model="profileForm.current_password"
                  type="password"
                  placeholder="Ingresa tu contraseña actual"
                  autocomplete="current-password"
                  @input="profileTouched.current_password && validateProfile('current_password')"
                  @blur="validateProfile('current_password')"
                  />
                  <span v-if="profileErrors.current_password" class="uni-field-error">{{ profileErrors.current_password }}</span>
              </div>
            </div>

            <div class="profile-password-grid">
              <div class="uni-form-group">
                <label>Nueva contraseña</label>
                <div class="uni-input-wrap">
                  <i class="ti ti-key"></i>
                  <input
                  v-model="profileForm.new_password"
                  type="password"
                  placeholder="Mínimo 8 caracteres, 1 letra y 1 número"
                  autocomplete="new-password"
                  @input="
                  profileTouched.new_password &&
                  validateProfile('new_password');
                  profileTouched.new_password_confirmation &&
                  validateProfile('new_password_confirmation')
                  "
                  @blur="validateProfile('new_password')"
                  />
                  <span v-if="profileErrors.new_password" class="uni-field-error">{{ profileErrors.new_password }}</span>
                  <span class="uni-field-hint">Mínimo 8 caracteres, al menos una letra y un número.</span>
                </div>
              </div>

              <div class="uni-form-group">
                <label>Confirmar nueva contraseña</label>
                <div class="uni-input-wrap">
                  <i class="ti ti-shield-check"></i>
                  <input
                  v-model="profileForm.new_password_confirmation"
                  type="password"
                  placeholder="Repite la contraseña exactamente"
                  autocomplete="new-password"
                  @input="
                  profileTouched.new_password_confirmation &&
                  validateProfile('new_password_confirmation')
                  "
                  @blur="validateProfile('new_password_confirmation')"
                  />
                  <span v-if="profileErrors.new_password_confirmation" class="uni-field-error">{{ profileErrors.new_password_confirmation }}</span>
                </div>
              </div>
            </div>

            <div class="profile-actions">
              <button class="uni-btn-action-success" :disabled="profileLoading">
                <i class="ti ti-device-floppy"></i>
                {{ profileLoading ? 'Actualizando...' : 'Actualizar contraseña' }}
              </button>
            </div>
          </form>
        </section>

        <!-- Bloque Información de Contacto -->
        <section class="uni-clean-card work-section">
          <div class="section-title-bar">
            <i class="ti ti-phone"></i>
            <div>
              <h3>Información de Contacto</h3>
              <p>Mantén tus canales de comunicación actualizados para notificaciones institucionales.</p>
            </div>
          </div>

          <div v-if="contactSuccessMessage" class="uni-alert uni-alert--success">
            <i class="ti ti-circle-check"></i>
            {{ contactSuccessMessage }}
          </div>
          
          <div v-if="contactErrorMessage" class="uni-alert uni-alert--error">
            <i class="ti ti-alert-circle"></i>
            {{ contactErrorMessage }}
          </div>

          <form class="profile-form" @submit.prevent="handleUpdateContact">
            <div class="uni-form-group">
              <label>Número de Teléfono / Celular</label>
              <div class="uni-input-wrap">
                <i class="ti ti-phone"></i>
                <input
                v-model="contactForm.telefono"
                type="tel"
                inputmode="numeric"
                placeholder="Ej: 71234567"
                maxlength="8"
                @input="contactForm.telefono = filterDigits(contactForm.telefono); contactTouched.telefono && validateContact('telefono')"
                @blur="contactForm.telefono = filterDigits(contactForm.telefono); validateContact('telefono')"
                />
                <span v-if="contactErrors.telefono" class="uni-field-error">{{ contactErrors.telefono }}</span>
              </div>
            </div>

            <div class="profile-actions">
              <button class="uni-btn-action-success" :disabled="contactLoading">
                <i class="ti ti-device-floppy"></i>
                {{ contactLoading ? 'Guardando...' : 'Guardar teléfono' }}
              </button>
            </div>
          </form>
        </section>

      </main>

    </div>
  </div>
</template>

<script setup>
import { reactive, ref, computed } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  fullName: { type: String, required: true },
  roleName: { type: String, required: true },
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

const contactForm = reactive({
  telefono: props.user?.telefono || ''
})
const contactSuccessMessage = ref('')
const contactErrorMessage = ref('')
const contactLoading = ref(false)

const profileErrors = reactive({
    current_password:'',
    new_password:'',
    new_password_confirmation:''
})

const contactErrors = reactive({
    telefono:''
})

const profileTouched = reactive({
    current_password:false,
    new_password:false,
    new_password_confirmation:false
})

const contactTouched = reactive({
    telefono:false
})

const profileValidators = {

current_password:v=>{
    if(!v) return 'La contraseña actual es obligatoria.'
},

new_password:v=>{

    if(!v)
        return 'La nueva contraseña es obligatoria.'

    if(v.length<8)
        return 'Debe tener al menos 8 caracteres.'

    if(!/[A-Za-z]/.test(v))
        return 'Debe contener al menos una letra.'

    if(!/[0-9]/.test(v))
        return 'Debe contener al menos un número.'
},

new_password_confirmation:v=>{

    if(!v)
        return 'Debe confirmar la contraseña.'

    if(v!==profileForm.new_password)
        return 'Las contraseñas no coinciden.'
}

}

const contactValidators={

telefono:v=>{
    if(v && !/^[67]\d{7}$/.test(v.trim()))
        return 'El teléfono debe empezar con 6 o 7 y tener exactamente 8 dígitos.'
}
}

function filterDigits(value) {
  return value.replace(/\D/g, '')
}

function resetProfileForm() {
  profileForm.current_password = ''
  profileForm.new_password = ''
  profileForm.new_password_confirmation = ''
}

function validateProfile(field){

    profileTouched[field]=true

    profileErrors[field]=
        profileValidators[field](profileForm[field]) || ''
}

function validateContact(field){

    contactTouched[field]=true

    contactErrors[field]=
        contactValidators[field](contactForm[field]) || ''
}

function validateAllProfile(){

    let valid=true

    Object.keys(profileTouched)
        .forEach(k=>profileTouched[k]=true)

    Object.keys(profileValidators).forEach(field=>{

        const error=
            profileValidators[field](profileForm[field])

        profileErrors[field]=error||''

        if(error)
            valid=false

    })

    return valid
}

function validateAllContact(){

    let valid=true

    contactTouched.telefono=true

    const error=
        contactValidators.telefono(contactForm.telefono)

    contactErrors.telefono=error||''

    if(error)
        valid=false

    return valid
}

async function handleUpdatePassword() {
  if(!validateAllProfile())
    return
  profileErrorMessage.value = ''
  profileSuccessMessage.value = ''

  profileLoading.value = true
  try {
    await props.api.put('/auth/perfil', {
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

async function handleUpdateContact() {
  contactErrorMessage.value = ''
  contactSuccessMessage.value = ''

  if (!validateAllContact()) return

  contactLoading.value = true
  try {
    const { data } = await props.api.put('/auth/profile', {
      telefono: contactForm.telefono
    })

    contactSuccessMessage.value = data.message || 'Teléfono actualizado correctamente.'
  } catch (error) {
    if (error.response?.data?.errors) {
      contactErrorMessage.value = Object.values(error.response.data.errors)[0][0]
    } else {
      contactErrorMessage.value = error.response?.data?.message || 'Error al actualizar el teléfono.'
    }
  } finally {
    contactLoading.value = false
  }
}

const profileInitials = computed(() => {
  const pNombre = props.user?.nombre1 || props.user?.nombre || ''
  const pApellido = props.user?.apellido1 || ''
  return `${pNombre.charAt(0)}${pApellido.charAt(0)}`.toUpperCase() || '?'
})
</script>

<style scoped>
/* Contenedor Base */
.uni-profile-container {
  max-width: 1200px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 2rem;
  min-height: 100%;
  overflow-y: auto;
  padding: 1.5rem 1rem 4rem 2rem; 
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

/* Header de la Página */
.uni-page-header h2 {
  margin: 0;
  font-family: 'Montserrat', sans-serif;
  font-size: 1.8rem;
  font-weight: 700;
  color: var(--color-black, #111);
}

.uni-page-header p {
  margin: 0.4rem 0 0;
  color: var(--color-dark-gray, #666);
  font-size: 14px;
}

/* Grid Principal de Trabajo */
.profile-main-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2.5rem;
  align-items: start;
}

/* Nuevas Tarjetas Limpias (Efecto Desencerrado) */
.uni-clean-card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 4px 18px rgba(0, 0, 0, 0.03);
  border: 1px solid rgba(0, 0, 0, 0.04);
  padding: 2rem;
  margin-bottom: 1.75rem;
  transition: transform 0.2s, box-shadow 0.2s;
}

/* Ajustes específicos de bloques izquierdos */
.profile-info-sidebar {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.avatar-banner-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 2.5rem 1.5rem;
}

.profile-avatar-circle {
  width: 84px;
  height: 84px;
  background: var(--color-mint-dark, #4e615e);
  color: #ffffff;
  font-size: 2rem;
  font-weight: 700;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.25rem;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
}

.avatar-banner-box h3 {
  margin: 0 0 0.5rem;
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--color-black, #111);
}

.profile-badge-role {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .06em;
  padding: 5px 14px;
  border-radius: 20px;
  background: var(--color-linen, #edf2f7);
  color: var(--color-mint-dark, #4e615e);
}

.details-box {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.profile-detail-item {
  display: flex;
  gap: 16px;
  align-items: center;
}

.profile-detail-item .detail-icon {
  width: 40px;
  height: 40px;
  background: #f8fafc;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  color: var(--color-mint-light, #8c9f96);
  flex-shrink: 0;
}

.detail-content {
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.detail-content label {
  font-size: 11px;
  font-weight: 600;
  color: var(--color-dark-gray, #888);
  text-transform: uppercase;
  letter-spacing: .04em;
}

.detail-content span {
  font-size: 0.95rem;
  font-weight: 600;
  color: var(--color-black, #222);
}

/* Banner de Nota Informativa */
.profile-note-banner {
  padding: 1rem 1.25rem;
  border-radius: 12px;
  background: #fbf9f4;
  border-left: 4px solid var(--color-sand, #bfb09b);
  color: #615545;
  display: flex;
  gap: 12px;
}

.profile-note-banner i {
  font-size: 1.2rem;
  color: var(--color-sand, #bfb09b);
  margin-top: 1px;
}

.profile-note-banner p {
  margin: 0;
  font-size: 12px;
  line-height: 1.5;
}

/* Columna Derecha (Secciones amplias) */
.profile-content-area {
  display: flex;
  flex-direction: column;
}

/* Encabezados internos de los formularios */
.section-title-bar {
  display: flex;
  gap: 1.25rem;
  align-items: flex-start;
  margin-bottom: 2rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.05);
  padding-bottom: 1.25rem;
}

.section-title-bar i {
  font-size: 1.6rem;
  color: var(--color-mint-dark, #4e615e);
  background: rgba(84, 110, 122, 0.06);
  padding: 10px;
  border-radius: 12px;
}

.section-title-bar h3 {
  margin: 0;
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--color-black, #111);
}

.section-title-bar p {
  margin: 4px 0 0;
  font-size: 13px;
  color: var(--color-dark-gray, #777);
}

/* Formularios distribuidos */
.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1.75rem;
}

.uni-form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.uni-form-group label {
  font-size: 12px;
  font-weight: 600;
  color: var(--color-dark-gray, #555);
  text-transform: uppercase;
  letter-spacing: .03em;
}

.profile-password-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.75rem;
}

.profile-actions {
  display: flex;
  justify-content: flex-end;
  margin-top: 0.5rem;
}

.uni-field-error {
  display: block;
  font-size: 11px;
  color: #b85c5c;
  margin-top: 5px;
  font-weight: 500;
}

.uni-field-hint {
  display: block;
  font-size: 10px;
  color: #8c9f96;
  margin-top: 4px;
  font-weight: 500;
}

/* Alertas estilizadas */
.uni-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 1rem 1.25rem;
  border-radius: 12px;
  font-size: 13px;
  font-weight: 500;
  margin-bottom: 1.5rem;
}

.uni-alert--error {
  background: var(--uni-error-bg, #fff5f5);
  border: 1px solid var(--uni-error-border, #feb2b2);
  color: var(--uni-error-text, #c53030);
}

.uni-alert--success {
  background: var(--uni-success-bg, #f0fff4);
  border: 1px solid var(--uni-success-border, #9ae6b4);
  color: var(--uni-success-text, #22543d);
}

/* Adaptación responsiva fluida */
@media (max-width: 992px) {
  .profile-main-layout {
    grid-template-columns: 1fr;
    gap: 1.5rem;
  }
  .uni-profile-container {
    padding: 1rem;
  }
}

@media (max-width: 640px) {
  .profile-password-grid {
    grid-template-columns: 1fr;
    gap: 1.25rem;
  }
  .uni-clean-card {
    padding: 1.5rem;
  }
  .profile-actions button {
    width: 100%;
    justify-content: center;
  }
}
</style>