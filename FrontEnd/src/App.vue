<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import DashboardDocente from './components/DashboardDocente.vue'
import CarreraManagement from './components/CarreraManagement.vue'
import CursoManagement from './components/CursoManagement.vue'
import MateriaManagement from './components/MateriaManagement.vue'
import UserManagement from './components/UserManagement.vue'
import ReportesAdmin from './components/ReportesAdmin.vue'
import CursoFisicoManagement from './components/CursoFisicoManagement.vue'
import EstudianteMaterias from './components/EstudianteMaterias.vue'
import EstudianteCarga from './components/EstudianteCarga.vue'
import EstudianteCalificaciones from './components/EstudianteCalificaciones.vue'
import EstudianteHistorial from './components/EstudianteHistorial.vue'
import EstudianteMallaCurricular from './components/EstudianteMallaCurricular.vue'
import EstudianteReportes from './components/EstudianteReportes.vue'
import EstudianteHorario from './components/EstudianteHorario.vue'

// Importamos la nueva vista independiente del perfil estético
import PerfilView from './components/PerfilView.vue'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

const sessionKey = 'universidad_auth_token'
const loading = ref(false)
const token = ref(sessionStorage.getItem(sessionKey) || '')
const user = ref(null)
const successMessage = ref('')
const errorMessage = ref('')

// Estado para definir qué sección se renderiza en cada rol
const adminSection = ref('usuarios')
const studentSection = ref('materias')

// CONTROL DE VISTA GLOBAL DEL ENTORNO: 'dashboard' o 'perfil'
const currentGlobalView = ref('dashboard')

const studentMessage = ref('')
const studentMessageType = ref('')

const loginForm = reactive({ login: '', password: '' })
const showPassword = ref(false)
const showLogoutConfirm = ref(false)

api.interceptors.request.use((config) => {
  if (token.value) config.headers.Authorization = `Bearer ${token.value}`
  return config
})

api.interceptors.response.use(
  (r) => r,
  (error) => {
    if (error.response?.status === 401) clearSession()
    return Promise.reject(error)
  },
)

const isAuthenticated = computed(() => Boolean(user.value && token.value))
const fullName = computed(() => user.value?.nombreCompleto || 'Usuario autenticado')
const roleName = computed(() => user.value?.rol || 'Sin rol')
const badgeTone = computed(() => {
  if (roleName.value === 'Administrador') return 'gold'
  if (roleName.value === 'Docente') return 'blue'
  if (roleName.value === 'Estudiante') return 'green'
  return 'neutral'
})

function persistSession(accessToken, profile) {
  token.value = accessToken
  sessionStorage.setItem(sessionKey, accessToken)
  user.value = profile
  currentGlobalView.value = 'dashboard'
}

function clearSession() {
  token.value = ''
  sessionStorage.removeItem(sessionKey)
  user.value = null
  loginForm.login = ''
  loginForm.password = ''
  adminSection.value = 'usuarios'
  studentSection.value = 'materias'
  currentGlobalView.value = 'dashboard'
}

function onStudentMessage(msg) {
  studentMessage.value = msg.text
  studentMessageType.value = msg.type
}

function goStudentSection(section) {
  studentMessage.value = ''
  studentSection.value = section
}

function resetMessages() {
  successMessage.value = ''
  errorMessage.value = ''
}

function parseError(error, fallback) {
  const response = error.response?.data

  // Prioridad 1: errores de validación específicos por campo (login, password, etc.)
  if (response?.errors) {
    const firstField = Object.values(response.errors)[0]
    if (Array.isArray(firstField) && firstField.length > 0) return firstField[0]
  }

  // Prioridad 2: mensaje general del backend, si no hay errors detallados
  if (response?.message) return response.message
  if (response?.error) return response.error

  return fallback
}

async function loadProfile() {
  if (!token.value) return
  try {
    const { data } = await api.get('/auth/me')
    const payload = data.data ?? data
    user.value = payload.user
  } catch {
    clearSession()
  }
}

const loginErrors = reactive({ login: '', password: '' })

function validateLogin() {
  let valid = true
  loginErrors.login = ''
  loginErrors.password = ''
  if (!loginForm.login.trim()) {
    loginErrors.login = 'El correo es obligatorio.'
    valid = false
  }
  if (!loginForm.password) {
    loginErrors.password = 'La contraseña es obligatoria.'
    valid = false
  }
  return valid
}

async function login() {
  resetMessages()
  if (!validateLogin()) return
  loading.value = true
  try {
    const { data } = await api.post('/auth/login', loginForm)
    const payload = data.data ?? data
    persistSession(payload.token, payload.user)
    successMessage.value = ''
  } catch (error) {
    errorMessage.value = parseError(error, 'No pudimos iniciar sesión.')
  } finally {
    loading.value = false
  }
}

function confirmLogout() {
  showLogoutConfirm.value = true
}

async function executeLogout() {
  loading.value = true
  showLogoutConfirm.value = false
  resetMessages()
  try {
    await api.post('/auth/logout')
  } catch { /* sesión remota expirada */ } finally {
    clearSession()
    loading.value = false
  }
}

function cancelLogout() {
  showLogoutConfirm.value = false
}

onMounted(loadProfile)
</script>

<template>
  <div v-if="!isAuthenticated" class="uni-outer-wrapper">
    <div class="uni-capsule-card">
      <div class="uni-capsule-left">
        <div class="uni-brand">
          <i class="ti ti-building-community"></i>
          Universidad
        </div>
        <div class="uni-form-box">
          <div class="uni-avatar-placeholder">
            <i class="ti ti-user-circle"></i>
          </div>
          <div v-if="successMessage" class="uni-alert uni-alert--success"><i class="ti ti-circle-check"></i> {{ successMessage }}</div>
          <div v-if="errorMessage"   class="uni-alert uni-alert--error"><i class="ti ti-alert-circle"></i> {{ errorMessage }}</div>
          <form @submit.prevent="login" class="uni-form-grid">
            <div class="uni-field">
              <div class="uni-input-wrap">
                <i class="ti ti-user"></i>
                <input v-model.trim="loginForm.login" type="text" placeholder="Usuario o Correo" autocomplete="username" />
              </div>
              <span v-if="loginErrors.login" class="uni-field-error">{{ loginErrors.login }}</span>
            </div>
            <div class="uni-field">
              <div class="uni-input-wrap">
                <i class="ti ti-lock"></i>
                <input v-model="loginForm.password" :type="showPassword ? 'text' : 'password'" placeholder="Contraseña" autocomplete="current-password" />
                <button type="button" class="uni-pw-toggle" @click="showPassword = !showPassword" tabindex="-1">
                  <i :class="showPassword ? 'ti ti-eye-off' : 'ti ti-eye'"></i>
                </button>
              </div>
              <span v-if="loginErrors.password" class="uni-field-error">{{ loginErrors.password }}</span>
            </div>
            <button class="uni-btn-primary" type="submit" :disabled="loading">
              {{ loading ? 'VERIFICANDO...' : 'INGRESAR' }}
            </button>
          </form>
        </div>
        <div class="uni-dots-indicator">
        </div>
      </div>
      <div class="uni-capsule-right">
        <nav class="uni-top-nav">
        </nav>
        <div class="uni-hero-content">
          <h1>Bienvenido</h1>
          <p>Portal institucional de gestión académica. Accede a tus cursos, materias y control docente desde un entorno unificado.</p>
        </div>
      </div>
    </div>
  </div>

  <div v-else class="uni-workspace">
    <main class="uni-main">
      <div class="uni-main-header">
        <div class="uni-main-header-left">
          <span class="uni-eyebrow">
            {{ currentGlobalView === 'perfil' ? 'Ajustes del perfil' : 'Panel de ' + roleName }}
          </span>
          <h1 class="uni-main-title">{{ fullName }}</h1>
        </div>
        <div class="uni-main-header-right">
          <div class="uni-header-actions">
            <button v-if="currentGlobalView === 'dashboard'" class="uni-header-btn" @click="currentGlobalView = 'perfil'" title="Ver Perfil">
              <i class="ti ti-user"></i><span>Perfil</span>
            </button>
            <button v-else class="uni-header-btn uni-header-btn--back" @click="currentGlobalView = 'dashboard'" title="Volver">
              <i class="ti ti-arrow-back-up"></i><span>Volver</span>
            </button>
            <button class="uni-header-btn uni-header-btn--logout" @click="confirmLogout" :disabled="loading" title="Cerrar sesión">
              <i class="ti ti-logout"></i><span>{{ loading ? 'Saliendo...' : 'Salir' }}</span>
            </button>
          </div>
        </div>
      </div>

      <PerfilView 
        v-if="currentGlobalView === 'perfil'"
        :user="user"
        :fullName="fullName"
        :roleName="roleName"
        :api="api"
      />
      

      <template v-else>
        <div v-if="user?.rol === 'Administrador'" class="uni-dashboard-card">
          <div class="uni-tab-bar">
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'usuarios' }"  @click="adminSection = 'usuarios'"><i class="ti ti-users"></i>Usuarios</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'carreras' }"  @click="adminSection = 'carreras'"><i class="ti ti-school"></i>Carreras</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'materias' }"  @click="adminSection = 'materias'"><i class="ti ti-book"></i>Materias</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'cursos-fisicos' }"  @click="adminSection = 'cursos-fisicos'"><i class="ti ti-building"></i>Aulas</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'cursos' }"    @click="adminSection = 'cursos'"><i class="ti ti-calendar"></i>Cursos</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'reportes' }"  @click="adminSection = 'reportes'"><i class="ti ti-file-report"></i>Reportes</button>
          </div>

          <div class="uni-section-body">
            <UserManagement    v-if="adminSection === 'usuarios'" :api="api" />
            <CarreraManagement v-else-if="adminSection === 'carreras'" :api="api" />
            <MateriaManagement v-else-if="adminSection === 'materias'" :api="api" />
            <CursoFisicoManagement v-else-if="adminSection === 'cursos-fisicos'" :api="api" />
            <CursoManagement   v-else-if="adminSection === 'cursos'"   :api="api" />
            <ReportesAdmin     v-else-if="adminSection === 'reportes'" :api="api" />
          </div>

          <div v-if="successMessage" class="uni-alert uni-alert--success">{{ successMessage }}</div>
          <div v-if="errorMessage"   class="uni-alert uni-alert--error">{{ errorMessage }}</div>
        </div>

        <div v-else-if="user?.rol === 'Docente'" class="uni-dashboard-card">
          <div class="uni-section-body">
            <DashboardDocente :user="user" :api="api" :badgeTone="badgeTone" @logout="logout" />
          </div>
        </div>

        <div v-else-if="user?.rol === 'Estudiante'" class="uni-dashboard-card">
          <div class="uni-tab-bar">
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'materias' }"  @click="goStudentSection('materias')"><i class="ti ti-book"></i>Inscripciones</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'inscritas' }" @click="goStudentSection('inscritas')"><i class="ti ti-clipboard-list"></i>Mis Materias</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'horario' }"   @click="goStudentSection('horario')"><i class="ti ti-calendar"></i>Horario</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'notas' }"     @click="goStudentSection('notas')"><i class="ti ti-star"></i>Mis Notas</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'historial' }" @click="goStudentSection('historial')"><i class="ti ti-history"></i>Historial</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'malla' }"     @click="goStudentSection('malla')"><i class="ti ti-layout-columns"></i>Malla</button>
            <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'reportes' }"  @click="goStudentSection('reportes')"><i class="ti ti-file-report"></i>Reportes</button>
          </div>

          <div v-if="studentMessage" :class="'uni-alert ' + (studentMessageType === 'error' ? 'uni-alert--error' : 'uni-alert--success')">{{ studentMessage }}</div>

          <div class="uni-section-body">
            <EstudianteMaterias        v-if="studentSection === 'materias'"      :user="user" :api="api" @message="onStudentMessage" />
            <EstudianteCarga           v-else-if="studentSection === 'inscritas'" :user="user" :api="api" @message="onStudentMessage" />
            <EstudianteHorario         v-else-if="studentSection === 'horario'"   :user="user" :api="api" />
            <EstudianteCalificaciones  v-else-if="studentSection === 'notas'"     :user="user" :api="api" @message="onStudentMessage" />
            <EstudianteHistorial       v-else-if="studentSection === 'historial'" :user="user" :api="api" @message="onStudentMessage" />
            <EstudianteMallaCurricular v-else-if="studentSection === 'malla'"     :user="user" :api="api" @message="onStudentMessage" />
            <EstudianteReportes        v-else-if="studentSection === 'reportes'"  :user="user" :api="api" @message="onStudentMessage" />
          </div>
        </div>

        <div v-else class="uni-outer-wrapper">
          <div class="uni-capsule-card" style="grid-template-columns:1fr;padding:4rem;text-align:center;">
            <h1 style="font-family:'Playfair Display',serif;">Sesión Activa</h1>
            <p>Redireccionando al entorno universitario principal...</p>
          </div>
        </div>
      </template>
    </main>

    <!-- Confirmación de cierre de sesión -->
    <Teleport to="body">
      <div v-if="showLogoutConfirm" class="uni-backdrop" @mousedown.self="cancelLogout">
        <div class="uni-confirm-dialog">
          <div class="uni-confirm-icon"><i class="ti ti-alert-triangle"></i></div>
          <h3>Cerrar sesión</h3>
          <p>¿Estás seguro de que deseas salir del sistema?</p>
          <div class="uni-confirm-actions">
            <button class="uni-btn-ghost" type="button" @click="cancelLogout">Cancelar</button>
            <button class="uni-btn-danger" type="button" @click="executeLogout" :disabled="loading">
              {{ loading ? 'Saliendo...' : 'Cerrar sesión' }}
            </button>
          </div>
        </div>
      </div>
    </Teleport>

  </div>
</template>

<style>
@import url('https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css');
@import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500;1,700&family=Montserrat:wght@400;500;600;700&display=swap');

*, *::before, *::after { box-sizing: border-box; }

:root {
  --color-dark-gray:  #5b5c5e;
  --color-mint-dark:  #697d7b;
  --color-mint-light: #8c9f96;
  --color-sand:       #bfb09b;
  --color-linen:      #d0cfca;
  --color-white:      #ffffff;
  --color-black:      #000000;
  
  /* Paleta unificada para acciones de confirmación y cancelación */
  --color-success-btn: #4e615e; /* Verde que combina con el Mint oscuro */
  --color-success-btn-hover: #3b4a48;
  --color-danger-btn:  #b85c5c; /* Rojo que combina sutilmente con los pasteles */
  --color-danger-btn-hover: #9c4646;

  --uni-muted:        #5b5c5e;
  --uni-text:         #1a1a1a;
  --uni-success-bg:     #edf4f2;
  --uni-success-border: #8c9f96;
  --uni-success-text:   #2b3d36;
  --uni-error-bg:       #faf0f0;
  --uni-error-border:   #dca6a6;
  --uni-error-text:     #7a2424;
  --sidebar-w: 76px;
}

html, body, #app {
  margin: 0; height: 100%; overflow: hidden;
  font-family: 'Montserrat', ui-sans-serif, system-ui, sans-serif;
  background: var(--color-white);
  color: var(--uni-text);
  -webkit-font-smoothing: antialiased;
}
button, input { font: inherit; }
button { cursor: pointer; }

/* ══ LOGIN ══════════════════════════════════════════════════════ */
.uni-outer-wrapper {
  min-height: 100vh; width: 100vw;
  display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #d0cfca 0%, #bfb09b 100%);
  padding: 2rem;
}
.uni-capsule-card {
  width: 100%; max-width: 1020px; height: 560px;
  background: var(--color-white); border-radius: 24px;
  box-shadow: 0 20px 50px rgba(0,0,0,.12);
  display: grid; grid-template-columns: 380px 1fr; overflow: hidden;
}
.uni-capsule-left {
  padding: 3rem 2.5rem; display: flex; flex-direction: column;
  justify-content: space-between; background: var(--color-white);
}
.uni-form-box { display: flex; flex-direction: column; gap: 1.25rem; margin-top: -1rem; }
.uni-avatar-placeholder { display: flex; justify-content: center; color: var(--color-mint-dark); font-size: 70px; margin-bottom: .5rem; }
.uni-form-grid { display: flex; flex-direction: column; gap: 1rem; }
.uni-field { width: 100%; }
.uni-input-wrap { position: relative; }
.uni-input-wrap i { position: absolute; left: 16px; top: 50%; transform: translateY(-50%); font-size: 16px; color: var(--color-dark-gray); }
.uni-input-wrap input {
  width: 100%; background: transparent;
  border: 1.5px solid var(--color-dark-gray); border-radius: 30px;
  color: var(--color-black); padding: 12px 40px 12px 46px;
  font-size: 13px; font-weight: 500; outline: none; transition: all .2s;
}
.uni-input-wrap input:focus { border-color: var(--color-mint-dark); box-shadow: 0 0 0 3px rgba(103,125,123,.15); }
.uni-input-wrap input::placeholder { color: #909090; font-weight: 400; }
.uni-field-error { display: block; font-size: 11px; color: #b85c5c; margin-top: 5px; padding-left: 4px; font-weight: 500; }
.uni-btn-primary {
  width: 100%; background: var(--color-mint-dark); border: none; border-radius: 30px;
  color: var(--color-white); font-size: 12px; font-weight: 700; padding: 13px;
  letter-spacing: .15em; text-align: center; transition: background .2s; margin-top: .5rem;
}
.uni-btn-primary:hover:not(:disabled) { background: var(--color-dark-gray); }
.uni-dots-indicator { display: flex; gap: 6px; justify-content: center; }
.uni-dots-indicator span { width: 6px; height: 6px; background: #dbdbdb; border-radius: 50%; }
.uni-dots-indicator span.active { background: var(--color-dark-gray); width: 18px; border-radius: 10px; }
.uni-capsule-right {
  background-color: var(--color-mint-dark);
  background-image: radial-gradient(at 80% 20%, var(--color-sand) 0px, transparent 50%),
                    radial-gradient(at 20% 80%, var(--color-mint-light) 0px, transparent 50%),
                    radial-gradient(at 0% 0%, var(--color-mint-dark) 0px, transparent 70%);
  padding: 3rem 4rem; display: flex; flex-direction: column;
  justify-content: space-between; color: var(--color-white);
}
.uni-top-nav { display: flex; gap: 2rem; justify-content: flex-end; }
.uni-nav-link { font-size: 12px; font-weight: 500; letter-spacing: .05em; opacity: .8; cursor: pointer; transition: opacity .2s; }
.uni-nav-link:hover { opacity: 1; }
.uni-hero-content { max-width: 460px; margin-bottom: 2rem; }
.uni-hero-content h1 { font-family: 'Montserrat', sans-serif; font-size: 3.5rem; font-weight: 700; margin: 0 0 1rem; letter-spacing: -.03em; }
.uni-hero-content p  { font-size: 13px; line-height: 1.7; opacity: .85; margin: 0; }

/* ══ WORKSPACE ══════════════════════════════════════════════════ */
.uni-workspace {
  width: 100%; height: 100%;
  display: grid;
  grid-template-rows: 1fr;
  grid-template-columns: 1fr;
  overflow: hidden; background: #f4f4f2;
}

/* ── Botón toggle de contraseña ── */
.uni-pw-toggle {
  position: absolute; right: 14px; top: 50%;
  transform: translateY(-50%);
  width: 24px; height: 24px;
  background: none; border: none; color: var(--color-dark-gray);
  cursor: pointer; padding: 0; margin: 0;
  display: flex; align-items: center; justify-content: center;
  font-size: 16px;
}
.uni-pw-toggle i {
  display: block;
  line-height: 1;
}
.uni-pw-toggle:hover { color: var(--color-mint-dark); }

/* ── Acciones del header ── */
.uni-main-header-right {
  display: flex; align-items: center; gap: 0.75rem;
  flex-shrink: 0;
}
.uni-header-actions {
  display: flex; align-items: center; gap: 4px;
}
.uni-header-btn {
  display: inline-flex; align-items: center; gap: 5px;
  background: transparent; border: none; border-radius: 8px;
  padding: 6px 10px; font-size: 11px; font-weight: 600;
  color: var(--uni-muted); cursor: pointer;
  transition: background .15s, color .15s; line-height: 1;
}
.uni-header-btn i { font-size: 16px; }
.uni-header-btn:hover { background: var(--color-linen); color: var(--color-black); }
.uni-header-btn--back { color: var(--color-mint-dark); }
.uni-header-btn--logout { color: #a05050; }
.uni-header-btn--logout:hover { background: #faf0f0; color: #7a2424; }
.uni-header-btn:disabled { opacity: 0.5; cursor: not-allowed; }

/* ── Confirmación de cierre de sesión ── */
.uni-backdrop {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.45);
  display: grid; place-items: center;
  padding: 1rem; z-index: 100;
}
.uni-confirm-dialog {
  background: #fff; border-radius: 18px;
  padding: 2rem; max-width: 380px; width: 100%;
  text-align: center; box-shadow: 0 24px 48px rgba(0,0,0,0.14);
}
.uni-confirm-icon { font-size: 3rem; color: #dca6a6; margin-bottom: 1rem; line-height: 1; }
.uni-confirm-dialog h3 { margin: 0 0 0.5rem; font-size: 1.15rem; font-weight: 700; color: #1a1a1a; }
.uni-confirm-dialog p { margin: 0 0 1.5rem; font-size: 0.85rem; color: #5b5c5e; }
.uni-confirm-actions { display: flex; gap: 0.6rem; justify-content: center; }
.uni-btn-ghost {
  display: inline-flex; align-items: center; gap: 6px;
  background: transparent; color: #5b5c5e;
  border: 1.5px solid #d0cfca; border-radius: 20px;
  padding: 8px 18px; font-size: 12px; font-weight: 600;
  cursor: pointer; transition: background .15s;
}
.uni-btn-ghost:hover { background: #f4f4f2; color: #1a1a1a; }
.uni-btn-danger {
  display: inline-flex; align-items: center; gap: 6px;
  background: #b85c5c; color: #fff; border: none;
  border-radius: 20px; padding: 8px 18px; font-size: 12px;
  font-weight: 600; cursor: pointer; transition: background .15s;
}
.uni-btn-danger:hover { background: #9c4646; }
.uni-btn-danger:disabled { opacity: 0.6; cursor: not-allowed; }

/* ── Área principal ── */
.uni-main {
  display: flex; flex-direction: column;
  height: 100%; overflow: hidden;
}

.uni-main-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 1.25rem 2rem; border-bottom: 1px solid rgba(0,0,0,.06);
  background: var(--color-white); flex-shrink: 0; gap: 1rem; flex-wrap: wrap;
}
.uni-main-header-left { display: flex; flex-direction: column; gap: 2px; }
.uni-eyebrow {
  font-size: 10px; font-weight: 600; color: var(--color-mint-light);
  text-transform: uppercase; letter-spacing: .08em;
}
.uni-main-title {
  font-family: 'Playfair Display', serif; font-size: 1.5rem;
  margin: 0; font-weight: 700; line-height: 1.2;
}

/* Card que contiene tab-bar + contenido */
.uni-dashboard-card {
  margin: 1.5rem;
  background: var(--color-white);
  border-radius: 16px;
  border: 1px solid rgba(0,0,0,.05);
  box-shadow: 0 4px 20px rgba(0,0,0,.04);
  display: flex; flex-direction: column;
  overflow: hidden;
  flex: 1;
  min-height: 0;
}

/* ── Tab bar (menú horizontal) ── */
.uni-tab-bar {
  display: flex; align-items: center; gap: 4px; flex-wrap: wrap;
  padding: 1rem 1.5rem .75rem;
  border-bottom: 1px solid var(--color-linen);
  flex-shrink: 0;
}

.uni-nav-btn {
  background: transparent; border: 1px solid transparent; border-radius: 20px;
  color: var(--uni-muted); font-size: 12px; font-weight: 600;
  padding: 7px 13px; display: flex; align-items: center; gap: 6px;
  transition: background .15s, color .15s;
}
.uni-nav-btn:hover { background: var(--color-linen); color: var(--color-black); }
.uni-nav-btn--active { background: var(--color-linen); color: var(--color-black); }

/* ── Área de contenido ── */
.uni-section-body {
  flex: 1; min-height: 0; overflow-y: auto;
  padding: 1.5rem;
  display: flex; flex-direction: column; gap: 1rem;
}

/* Encabezado interno para vista de perfil unificado */
.uni-profile-header-area {
  padding: 1.5rem 1.5rem 0rem;
}
.uni-profile-header-area h2 { font-size: 1.2rem; margin: 0 0 4px 0; color: var(--color-black); font-weight: 600; }
.uni-profile-header-area p { font-size: 12px; margin: 0; color: var(--color-dark-gray); }
.uni-profile-actions { margin-top: 1.5rem; display: flex; gap: 1rem; }

/* ── Perfil grid ── */
.uni-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  gap: 1.25rem;
}
.uni-info-card {
  padding: 1.25rem; background: #fafafa;
  border-radius: 10px; border-left: 3px solid var(--color-mint-light);
}
.uni-info-card span { font-size: 10px; color: var(--color-dark-gray); display: block; text-transform: uppercase; letter-spacing: .05em; margin-bottom: 4px; }
.uni-info-card strong { display: block; font-size: 1rem; word-break: break-word; }

/* ── BOTONES GLOBALES DE CONFIRMACIÓN / CANCELACIÓN SEMÁNTICA ── */
.uni-btn-action-success {
  background: var(--color-success-btn); color: var(--color-white);
  border: none; border-radius: 20px; padding: 10px 20px;
  font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;
  transition: background .15s;
}
.uni-btn-action-success:hover { background: var(--color-success-btn-hover); }

.uni-btn-action-danger {
  background: var(--color-danger-btn); color: var(--color-white);
  border: none; border-radius: 20px; padding: 10px 20px;
  font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;
  transition: background .15s;
}
.uni-btn-action-danger:hover { background: var(--color-danger-btn-hover); }

/* ── Badge ── */
.uni-role-badge {
  border-radius: 20px; padding: 5px 14px;
  font-size: 10px; font-weight: 700; text-transform: uppercase;
  background: var(--color-sand); color: #54442d;
}
.uni-role-badge[data-tone="blue"]  { background: #dce8f4; color: #1e4a6e; }
.uni-role-badge[data-tone="green"] { background: #ddf0e6; color: #1a5235; }

/* ── Alerts ── */
.uni-alert { padding: .75rem 1rem; border-radius: 20px; font-size: 12px; border: 1px solid; }
.uni-alert--success { background: var(--uni-success-bg); border-color: var(--uni-success-border); color: var(--uni-success-text); }
.uni-alert--error   { background: var(--uni-error-bg);   border-color: var(--uni-error-border);   color: var(--uni-error-text); }

/* ══ RESPONSIVE ═════════════════════════════════════════════════ */
@media (max-width: 960px) {
  .uni-capsule-card { grid-template-columns: 1fr; height: auto; max-width: 450px; }
  .uni-capsule-right { display: none; }
}
@media (max-width: 640px) {
  .uni-dashboard-card { margin: 1rem; }
  .uni-main-header { padding: 1rem 1.25rem; }
  .uni-main-header-right { flex-wrap: wrap; }
  .uni-info-grid { grid-template-columns: 1fr; }
}
</style>