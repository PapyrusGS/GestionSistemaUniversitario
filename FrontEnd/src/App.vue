<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import DashboardDocente from './components/DashboardDocente.vue'
import CarreraManagement from './components/CarreraManagement.vue'
import CursoManagement from './components/CursoManagement.vue'
import MateriaManagement from './components/MateriaManagement.vue'
import UserManagement from './components/UserManagement.vue'
import ReportesAdmin from './components/ReportesAdmin.vue'
import EstudianteMaterias from './components/EstudianteMaterias.vue'
import EstudianteCarga from './components/EstudianteCarga.vue'
import EstudianteCalificaciones from './components/EstudianteCalificaciones.vue'
import EstudianteHistorial from './components/EstudianteHistorial.vue'
import EstudianteMallaCurricular from './components/EstudianteMallaCurricular.vue'
import EstudianteReportes from './components/EstudianteReportes.vue'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000/api',
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },
})

const sessionKey = 'universidad_auth_token'
const mode = ref('login')
const loading = ref(false)
const token = ref(sessionStorage.getItem(sessionKey) || '')
const user = ref(null)
const successMessage = ref('')
const errorMessage = ref('')
const adminSection = ref('perfil')
const studentSection = ref('perfil')
const studentMessage = ref('')
const studentMessageType = ref('')

const loginForm = reactive({
  login: '',
  password: '',
})

api.interceptors.request.use((config) => {
  if (token.value) {
    config.headers.Authorization = `Bearer ${token.value}`
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      clearSession()
    }
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

  if (profile.rol === 'Docente') {
    mode.value = 'dashboard-docente'
  } else if (profile.rol === 'Estudiante') {
    mode.value = 'dashboard-estudiante'
  } else {
    mode.value = 'dashboard-general'
  }
}

function clearSession() {
  token.value = ''
  sessionStorage.removeItem(sessionKey)
  user.value = null
  adminSection.value = 'perfil'
  studentSection.value = 'perfil'
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
  if (response?.message) {
    return response.message
  }

  if (response?.errors) {
    const firstField = Object.values(response.errors)[0]
    if (Array.isArray(firstField) && firstField.length > 0) {
      return firstField[0]
    }
  }

  return fallback
}

async function loadProfile() {
  if (!token.value) return

  try {
    const { data } = await api.get('/auth/me')
    const payload = data.data ?? data
    user.value = payload.user

    if (payload.user.rol === 'Docente') {
      mode.value = 'dashboard-docente'
    } else if (payload.user.rol === 'Estudiante') {
      mode.value = 'dashboard-estudiante'
    } else {
      mode.value = 'dashboard-general'
    }
  } catch {
    clearSession()
  }
}

async function login() {
  loading.value = true
  resetMessages()

  try {
    const { data } = await api.post('/auth/login', loginForm)
    const payload = data.data ?? data

    persistSession(payload.token, payload.user)

    // No mostrar mensaje después del login
    successMessage.value = ''

  } catch (error) {
    errorMessage.value = parseError(error, 'No pudimos iniciar sesion.')
  } finally {
    loading.value = false
  }
}

async function logout() {
  loading.value = true
  resetMessages()

  try {
    await api.post('/auth/logout')
  } catch {
    // Sesión remota expirada
  } finally {
    clearSession()
    loading.value = false
    mode.value = 'login'
  }
}

onMounted(loadProfile)
</script>

<template>
  <!-- ─── LOGIN ─────────────────────────────────────────────────────────── -->
  <div v-if="!isAuthenticated" class="uni-outer-wrapper">
    <div class="uni-capsule-card">

      <!-- Bloque del Formulario (Izquierda) -->
      <div class="uni-capsule-left">
        <div class="uni-brand">
          <i class="ti ti-building-community"></i>
          Universidad
        </div>

        <div class="uni-form-box">
          <div class="uni-avatar-placeholder">
            <i class="ti ti-user-circle"></i>
          </div>

          <div v-if="successMessage" class="uni-alert uni-alert--success">{{ successMessage }}</div>
          <div v-if="errorMessage" class="uni-alert uni-alert--error">{{ errorMessage }}</div>

          <form @submit.prevent="login" class="uni-form-grid">
            <div class="uni-field">
              <div class="uni-input-wrap">
                <i class="ti ti-user"></i>
                <input
                  v-model.trim="loginForm.login"
                  type="text"
                  placeholder="Usuario o Correo"
                  autocomplete="username"
                  required
                />
              </div>
            </div>

            <div class="uni-field">
              <div class="uni-input-wrap">
                <i class="ti ti-lock"></i>
                <input
                  v-model="loginForm.password"
                  type="password"
                  placeholder="Contraseña"
                  autocomplete="current-password"
                  required
                />
              </div>
            </div>

            <button class="uni-btn-primary" type="submit" :disabled="loading">
              {{ loading ? 'VERIFICANDO...' : 'INGRESAR' }}
            </button>
          </form>
        </div>

        <div class="uni-dots-indicator">
          <span class="active"></span>
          <span></span>
          <span></span>
        </div>
      </div>

      <!-- Bloque del Arte Líquido (Derecha) -->
      <div class="uni-capsule-right">
        <nav class="uni-top-nav">
          <span class="uni-nav-link">Inicio</span>
          <span class="uni-nav-link">Soporte</span>
          <span class="uni-nav-link">Contacto</span>
        </nav>

        <div class="uni-hero-content">
          <h1>Bienvenido</h1>
          <p>Portal institucional de gestión académica. Accede a tus cursos, materias y control docente desde un entorno unificado.</p>
        </div>
      </div>

    </div>
  </div>

  <!-- ─── DOCENTE ────────────────────────────────────────────────────────── -->
  <div v-else-if="user?.rol === 'Docente'" class="uni-full-workspace">
    <DashboardDocente :user="user" :api="api" :badgeTone="badgeTone" @logout="logout" />
  </div>

  <!-- ─── ADMINISTRADOR ─────────────────────────────────────────────────── -->
  <div v-else-if="user?.rol === 'Administrador'" class="uni-full-workspace">
    <div class="uni-admin-shell">

      <aside class="uni-admin-sidebar">
      <div>
        <div class="uni-brand">
          <i class="ti ti-building-community"></i>
          Universidad
        </div>

        <div class="uni-hero uni-hero--sm">
          <h1>Panel de<br><em>administración</em></h1>
          <p>Sistema académico integrado.</p>
        </div>
      </div>

      <div>
        <div class="uni-foot">
          <span class="uni-dot"></span>
          Conexión segura
        </div>

        <div class="uni-sidebar-actions">
          <button
            class="uni-sidebar-logout"
            :disabled="loading"
            @click="logout"
          >
            <i class="ti ti-logout"></i>
            {{ loading ? 'Cerrando...' : 'Cerrar sesión' }}
          </button>
        </div>
      </div>
      </aside>

      <main class="uni-admin-main">
        <div class="uni-dashboard-card">

          <div class="uni-dashboard-head">
            <div>
              <span class="uni-eyebrow">Sesión activa</span>
              <h2 class="uni-dashboard-name">{{ fullName }}</h2>
            </div>
            <div class="uni-dashboard-actions">
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'perfil' }" @click="adminSection = 'perfil'">
                <i class="ti ti-user" aria-hidden="true"></i>Perfil
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'usuarios' }" @click="adminSection = 'usuarios'">
                <i class="ti ti-users" aria-hidden="true"></i>Usuarios
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'carreras' }" @click="adminSection = 'carreras'">
                <i class="ti ti-school" aria-hidden="true"></i>Carreras
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'materias' }" @click="adminSection = 'materias'">
                <i class="ti ti-book" aria-hidden="true"></i>Materias
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'cursos' }" @click="adminSection = 'cursos'">
                <i class="ti ti-calendar" aria-hidden="true"></i>Cursos
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': adminSection === 'reportes' }" @click="adminSection = 'reportes'">
                <i class="ti ti-file-report" aria-hidden="true"></i>Reportes
              </button>
              <span class="uni-role-badge" :data-tone="badgeTone">{{ roleName }}</span>
            </div>
          </div>

          <div v-if="adminSection === 'usuarios'" class="uni-section-body">
            <UserManagement :api="api" />
          </div>
          <div v-else-if="adminSection === 'carreras'" class="uni-section-body">
            <CarreraManagement :api="api" />
          </div>
          <div v-else-if="adminSection === 'materias'" class="uni-section-body">
            <MateriaManagement :api="api" />
          </div>
          <div v-else-if="adminSection === 'cursos'" class="uni-section-body">
            <CursoManagement :api="api" />
          </div>
          <div v-else-if="adminSection === 'reportes'" class="uni-section-body">
            <ReportesAdmin :api="api" />
          </div>
          <div v-else class="uni-info-grid">
            <article class="uni-info-card">
              <span>Correo</span>
              <strong>{{ user.correo }}</strong>
            </article>
            <article class="uni-info-card">
              <span>CI</span>
              <strong>{{ user.ci }}</strong>
            </article>
            <article class="uni-info-card">
              <span>Registro</span>
              <strong>{{ user.fechaRegistro || 'No disponible' }}</strong>
            </article>
            <article class="uni-info-card">
              <span>Estado</span>
              <strong>{{ user.estado ? 'Activo' : 'Inactivo' }}</strong>
            </article>
          </div>

          <div v-if="successMessage" class="uni-alert uni-alert--success">{{ successMessage }}</div>
          <div v-if="errorMessage" class="uni-alert uni-alert--error">{{ errorMessage }}</div>
        </div>
      </main>
    </div>
  </div>

  <!-- ─── ESTUDIANTE ────────────────────────────────────────────────────── -->
  <div v-else-if="user?.rol === 'Estudiante'" class="uni-full-workspace">
    <div class="uni-admin-shell">
          <aside class="uni-admin-sidebar">
      <div>
        <div class="uni-brand">
          <i class="ti ti-building-community"></i>
          Universidad
        </div>

        <div class="uni-hero uni-hero--sm">
          <h1>Panel del<br><em>estudiante</em></h1>
          <p>Sistema académico integrado.</p>
        </div>
      </div>

      <div>
        <div class="uni-foot">
          <span class="uni-dot"></span>
          Conexión segura
        </div>

        <div class="uni-sidebar-actions">
          <button
            class="uni-sidebar-logout"
            :disabled="loading"
            @click="logout"
          >
            <i class="ti ti-logout"></i>
            {{ loading ? 'Cerrando...' : 'Cerrar sesión' }}
          </button>
        </div>
      </div>
    </aside>
      <main class="uni-admin-main">
        <div class="uni-dashboard-card">

          <div class="uni-dashboard-head">
            <div>
              <span class="uni-eyebrow">Sesión activa</span>
              <h2 class="uni-dashboard-name">{{ fullName }}</h2>
            </div>
            <div class="uni-dashboard-actions">
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'perfil' }" @click="goStudentSection('perfil')">
                <i class="ti ti-user" aria-hidden="true"></i>Mi Perfil
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'materias' }" @click="goStudentSection('materias')">
                <i class="ti ti-book" aria-hidden="true"></i>Materias Disponibles
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'inscritas' }" @click="goStudentSection('inscritas')">
                <i class="ti ti-clipboard-list" aria-hidden="true"></i>Mis Materias
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'notas' }" @click="goStudentSection('notas')">
                <i class="ti ti-star" aria-hidden="true"></i>Mis Notas
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'historial' }" @click="goStudentSection('historial')">
                <i class="ti ti-history" aria-hidden="true"></i>Historial Academico
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'malla' }" @click="goStudentSection('malla')">
                <i class="ti ti-layout-columns" aria-hidden="true"></i>Malla
              </button>
              <button class="uni-nav-btn" :class="{ 'uni-nav-btn--active': studentSection === 'reportes' }" @click="goStudentSection('reportes')">
                <i class="ti ti-file-report" aria-hidden="true"></i>Mis Reportes
              </button>
              <span class="uni-role-badge" :data-tone="badgeTone">{{ roleName }}</span>
            </div>
          </div>

          <div v-if="studentMessage" :class="'uni-alert ' + (studentMessageType === 'error' ? 'uni-alert--error' : 'uni-alert--success')">{{ studentMessage }}</div>

          <div v-if="studentSection === 'perfil'" class="uni-info-grid">
            <article class="uni-info-card">
              <span>Correo</span>
              <strong>{{ user.correo }}</strong>
            </article>
            <article class="uni-info-card">
              <span>CI</span>
              <strong>{{ user.ci }}</strong>
            </article>
            <article class="uni-info-card">
              <span>Rol</span>
              <strong>{{ user.rol }}</strong>
            </article>
            <article class="uni-info-card">
              <span>Estado</span>
              <strong>{{ user.estado ? 'Activo' : 'Inactivo' }}</strong>
            </article>
          </div>

          <div v-else-if="studentSection === 'materias'">
            <EstudianteMaterias :user="user" :api="api" @message="onStudentMessage" />
          </div>
          <div v-else-if="studentSection === 'inscritas'">
            <EstudianteCarga :user="user" :api="api" @message="onStudentMessage" />
          </div>
          <div v-else-if="studentSection === 'notas'">
            <EstudianteCalificaciones :user="user" :api="api" @message="onStudentMessage" />
          </div>
          <div v-else-if="studentSection === 'historial'">
            <EstudianteHistorial :user="user" :api="api" @message="onStudentMessage" />
          </div>
          <div v-else-if="studentSection === 'malla'">
            <EstudianteMallaCurricular :user="user" :api="api" @message="onStudentMessage" />
          </div>
          <div v-else-if="studentSection === 'reportes'">
            <EstudianteReportes :user="user" :api="api" @message="onStudentMessage" />
          </div>

        </div>
      </main>
    </div>
  </div>

  <!-- ─── FALLBACK ──────────────────────────────────────────────────────── -->
  <div v-else class="uni-full-workspace">
    <div class="uni-outer-wrapper">
      <div class="uni-capsule-card" style="grid-template-columns: 1fr; padding: 4rem; text-align: center;">
        <h1 style="font-family: 'Playfair Display', serif;">Sesión Activa</h1>
        <p>Redireccionando al entorno universitario principal...</p>
      </div>
    </div>
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

  --uni-bg-outer:     #d0cfca;
  --uni-text:         #1a1a1a;
  --uni-muted:        #5b5c5e;

  --uni-success-bg:     #edf4f2;
  --uni-success-border: #8c9f96;
  --uni-success-text:   #2b3d36;
  --uni-error-bg:       #faf0f0;
  --uni-error-border:   #dca6a6;
  --uni-error-text:     #7a2424;
}

html, body, #app {
  margin: 0;
  height: 100%;          /* altura exacta, sin overflow */
  overflow: hidden;      /* la página en sí nunca scrollea */
  font-family: 'Montserrat', ui-sans-serif, system-ui, sans-serif;
  background: var(--color-white);
  color: var(--uni-text);
  -webkit-font-smoothing: antialiased;
}

button, input { font: inherit; }
button { cursor: pointer; }

/* ─── LOGIN ───────────────────────────────────────────────────────────── */
.uni-outer-wrapper {
  min-height: 100vh;
  width: 100vw;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: var(--color-linen);
  background-image: linear-gradient(135deg, #d0cfca 0%, #bfb09b 100%);
  padding: 2rem;
}

.uni-capsule-card {
  width: 100%;
  max-width: 1020px;
  height: 560px;
  background: var(--color-white);
  border-radius: 24px;
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.12);
  display: grid;
  grid-template-columns: 380px 1fr;
  overflow: hidden;
}

.uni-capsule-left {
  padding: 3rem 2.5rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: var(--color-white);
}

.uni-form-box {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  margin-top: -1rem;
}

.uni-avatar-placeholder {
  display: flex;
  justify-content: center;
  color: var(--color-mint-dark);
  font-size: 64px;
  margin-bottom: 0.5rem;
}

.uni-form-grid {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.uni-field { width: 100%; }

.uni-input-wrap { position: relative; }

.uni-input-wrap i {
  position: absolute;
  left: 16px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 16px;
  color: var(--color-dark-gray);
}

.uni-input-wrap input {
  width: 100%;
  background: transparent;
  border: 1.5px solid var(--color-dark-gray);
  border-radius: 30px;
  color: var(--color-black);
  padding: 12px 16px 12px 46px;
  font-size: 13px;
  font-weight: 500;
  outline: none;
  transition: all 0.2s ease;
}

.uni-input-wrap input:focus {
  border-color: var(--color-mint-dark);
  box-shadow: 0 0 0 3px rgba(103, 125, 123, 0.15);
}

.uni-input-wrap input::placeholder {
  color: #909090;
  font-weight: 400;
}

.uni-btn-primary {
  width: 100%;
  background: var(--color-mint-dark);
  border: none;
  border-radius: 30px;
  color: var(--color-white);
  font-size: 12px;
  font-weight: 700;
  padding: 13px;
  letter-spacing: 0.15em;
  text-align: center;
  transition: background-color 0.2s ease;
  margin-top: 0.5rem;
}

.uni-btn-primary:hover:not(:disabled) { background: var(--color-dark-gray); }

.uni-dots-indicator {
  display: flex;
  gap: 6px;
  justify-content: center;
}

.uni-dots-indicator span {
  width: 6px;
  height: 6px;
  background: #dbdbdb;
  border-radius: 50%;
}

.uni-dots-indicator span.active {
  background: var(--color-dark-gray);
  width: 18px;
  border-radius: 10px;
}

.uni-capsule-right {
  background-color: var(--color-mint-dark);
  background-image:
    radial-gradient(at 80% 20%, var(--color-sand) 0px, transparent 50%),
    radial-gradient(at 20% 80%, var(--color-mint-light) 0px, transparent 50%),
    radial-gradient(at 0% 0%, var(--color-mint-dark) 0px, transparent 70%);
  padding: 3rem 4rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  color: var(--color-white);
}

.uni-top-nav {
  display: flex;
  gap: 2rem;
  justify-content: flex-end;
}

.uni-nav-link {
  font-size: 12px;
  font-weight: 500;
  letter-spacing: 0.05em;
  opacity: 0.8;
  cursor: pointer;
  transition: opacity 0.2s;
}

.uni-nav-link:hover { opacity: 1; }

.uni-hero-content { max-width: 460px; margin-bottom: 2rem; }

.uni-hero-content h1 {
  font-family: 'Montserrat', sans-serif;
  font-size: 3.5rem;
  font-weight: 700;
  margin: 0 0 1rem 0;
  letter-spacing: -0.03em;
}

.uni-hero-content p {
  font-size: 13px;
  line-height: 1.7;
  opacity: 0.85;
  margin: 0;
  font-weight: 400;
}

/* ─── WORKSPACE ───────────────────────────────────────────────────────── */
.uni-full-workspace {
  width: 100%;
  height: 100%;          /* ocupa exactamente el viewport, sin crecer */
  overflow: hidden;
  background: #f6f6f4;
}

.uni-admin-shell {
  height: 100%;          /* mismo: no min-height */
  display: grid;
  grid-template-columns: 340px 1fr;
}

.uni-admin-sidebar {
  padding: 3rem 2rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: var(--color-linen);
  border-right: 1px solid rgba(0, 0, 0, 0.06);
  overflow: hidden;      /* sidebar nunca scrollea */
}

.uni-admin-main {
  padding: 3rem;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  height: 100%;          /* ocupa todo el alto disponible */
  overflow-y: auto;      /* aquí sí puede haber scroll si el contenido crece */
}

.uni-dashboard-card {
  width: 100%;
  max-width: 900px;
  background: var(--color-white);
  border-radius: 16px;
  padding: 2.5rem;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
  display: flex;
  flex-direction: column;
  gap: 2rem;
  border: 1px solid rgba(0, 0, 0, 0.05);
  /* altura limitada al main para que los componentes hijos scrolleen */
  max-height: calc(100vh - 6rem);
  min-height: 0;
}

.uni-dashboard-head {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  border-bottom: 1px solid var(--color-linen);
  padding-bottom: 1.5rem;
  gap: 1.5rem;
  flex-wrap: wrap;
}

.uni-eyebrow {
  font-size: 11px;
  font-weight: 600;
  color: var(--color-mint-light);
  text-transform: uppercase;
  letter-spacing: 0.08em;
  display: block;
}

.uni-dashboard-name {
  font-family: 'Playfair Display', serif;
  font-size: 1.6rem;
  margin: 4px 0 0 0;
}

.uni-dashboard-actions {
  display: flex;
  gap: 6px;
  align-items: center;
  flex-wrap: wrap;
}

.uni-nav-btn {
  background: transparent;
  border: 1px solid transparent;
  border-radius: 20px;
  color: var(--uni-muted);
  font-size: 12px;
  font-weight: 600;
  padding: 8px 14px;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background 0.15s ease, color 0.15s ease;
}

.uni-nav-btn:hover { background: var(--color-linen); color: var(--color-black); }

.uni-nav-btn--active {
  background: var(--color-linen);
  color: var(--color-black);
}

.uni-role-badge {
  border-radius: 20px;
  padding: 6px 14px;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  background: var(--color-sand);
  color: #54442d;
}

.uni-section-body {
  width: 100%;
  flex: 1;
  min-height: 0;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
}

.uni-info-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}

.uni-info-card {
  padding: 1.25rem;
  background: #fafafa;
  border-radius: 8px;
  border-left: 3px solid var(--color-mint-light);
}

.uni-info-card span {
  font-size: 11px;
  color: var(--color-dark-gray);
  display: block;
  text-transform: uppercase;
  margin-bottom: 4px;
}

.uni-info-card strong {
  display: block;
  font-size: 1rem;
  word-break: break-word;
}

.uni-dashboard-footer {
  display: flex;
  justify-content: flex-end;
}

.uni-btn-secondary {
  background: transparent;
  border: 1px solid var(--color-dark-gray);
  border-radius: 20px;
  padding: 8px 16px;
  font-size: 12px;
  font-weight: 600;
  color: var(--color-dark-gray);
  display: flex;
  align-items: center;
  gap: 6px;
  transition: background 0.15s ease;
}

.uni-btn-secondary:hover:not(:disabled) {
  background: var(--color-linen);
}

.uni-brand {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--color-dark-gray);
}

.uni-hero--sm h1 {
  font-family: 'Playfair Display', serif;
  font-size: 1.8rem;
  line-height: 1.2;
  margin: 1.5rem 0 0.75rem;
}

.uni-hero--sm h1 em {
  font-style: italic;
  color: var(--color-mint-dark);
}

.uni-hero--sm p {
  font-size: 12px;
  color: var(--uni-muted);
  line-height: 1.6;
}

.uni-foot {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: var(--uni-muted);
}

.uni-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--color-mint-light);
  display: inline-block;
}

.uni-alert {
  padding: 0.75rem 1rem;
  border-radius: 20px;
  font-size: 12px;
  border: 1px solid;
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

.uni-sidebar-actions {
  margin-top: auto;
  padding-top: 1.5rem;
}

.uni-sidebar-logout {
  width: 100%;
  border: none;
  border-radius: 12px;
  background: #5b5c5e;
  color: white;
  padding: 12px 16px;
  font-weight: 600;
  font-size: 13px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  transition: all .2s ease;
}

.uni-sidebar-logout:hover:not(:disabled) {
  background: #454648;
  transform: translateY(-1px);
}

.uni-sidebar-logout:disabled {
  opacity: .6;
  cursor: not-allowed;
}
/* ─── RESPONSIVE ──────────────────────────────────────────────────────── */
@media (max-width: 960px) {
  .uni-capsule-card {
    grid-template-columns: 1fr;
    height: auto;
    max-width: 450px;
  }
  .uni-capsule-right { display: none; }
  .uni-admin-shell { grid-template-columns: 1fr; }
  .uni-admin-sidebar { display: none !important; }
}

@media (max-width: 640px) {
  .uni-admin-main { padding: 1.5rem; }
  .uni-dashboard-card { padding: 1.5rem; }
  .uni-info-grid { grid-template-columns: 1fr; }
  .uni-dashboard-head { flex-direction: column; align-items: flex-start; }
}
</style>