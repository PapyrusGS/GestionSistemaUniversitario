<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import DashboardDocente from './components/DashboardDocente.vue'
import CarreraManagement from './components/CarreraManagement.vue'
import CursoManagement from './components/CursoManagement.vue'
import MateriaManagement from './components/MateriaManagement.vue'
import UserManagement from './components/UserManagement.vue'
import ReportesAdmin from './components/ReportesAdmin.vue'

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
  } else {
    mode.value = 'dashboard-general'
  }
}

function clearSession() {
  token.value = ''
  sessionStorage.removeItem(sessionKey)
  user.value = null
  adminSection.value = 'perfil'
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
    successMessage.value = data.message || 'Sesion iniciada correctamente.'
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
    // Si el token ya expiro o fue revocado, igual limpiamos la sesion local.
  } finally {
    clearSession()
    loading.value = false
    mode.value = 'login'
  }
}

onMounted(loadProfile)
</script>

<template>
  <div v-if="!isAuthenticated" class="auth-shell-wrapper">
    <main class="auth-shell">
      <section class="hero-panel">
        <div class="brand">Universidad</div>
        <h1>Modulo de autenticacion seguro</h1>
        <p>
          Acceso con API protegida por Sanctum, contrasenas cifradas, throttling de intentos y
          control por rol sobre la base de datos real del sistema.
        </p>

        <ul class="feature-list">
          <li>Login con correo o CI</li>
          <li>Token por sesion en el navegador</li>
        </ul>

        <div class="highlight-card">
          <span>Estado</span>
          <strong>{{ isAuthenticated ? 'Autenticado' : 'Pendiente de acceso' }}</strong>
        </div>
      </section>

      <section class="panel">
        <div class="card">
          <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
          <div v-if="errorMessage" class="alert error">{{ errorMessage }}</div>

          <form class="form-grid" @submit.prevent="login">
            <label>
              <span>Correo o CI</span>
              <input v-model.trim="loginForm.login" type="text" autocomplete="username" required />
            </label>

            <label>
              <span>Contrasena</span>
              <input
                v-model="loginForm.password"
                type="password"
                autocomplete="current-password"
                required
              />
            </label>

            <button class="primary" type="submit" :disabled="loading">
              {{ loading ? 'Validando...' : 'Entrar' }}
            </button>
          </form>
        </div>
      </section>
    </main>
  </div>

  <div v-else-if="user?.rol === 'Docente'" class="authenticated-full-workspace">
    <div class="full-screen-app-container">
      <DashboardDocente :user="user" :api="api" :badgeTone="badgeTone" @logout="logout" />
    </div>
  </div>

  <div v-else-if="user?.rol === 'Administrador'" class="authenticated-full-workspace">
    <main class="auth-shell" :class="{ 'full-width-shell': adminSection !== 'perfil' }">
      <section class="hero-panel" v-show="adminSection === 'perfil'">
        <div class="brand">Universidad</div>
        <h1>Modulo de autenticacion seguro</h1>
        <p>
          Acceso con API protegida por Sanctum, contrasenas cifradas, throttling de intentos y
          control por rol sobre la base de datos real del sistema.
        </p>

        <ul class="feature-list">
          <li>Login con correo o CI</li>
          <li>Token por sesion en el navegador</li>
        </ul>

        <div class="highlight-card">
          <span>Estado</span>
          <strong>Autenticado</strong>
        </div>
      </section>

      <section class="panel">
        <div class="card dashboard" :class="{ 'wide-card': adminSection !== 'perfil' }">
          <div class="dashboard-head">
            <div>
              <span class="eyebrow">Sesion activa</span>
              <h2>{{ fullName }}</h2>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; align-items: center;">
              <button
                class="secondary"
                type="button"
                style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                @click="adminSection = 'perfil'"
              >
                Ver perfil
              </button>
              <button
                class="secondary"
                type="button"
                style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                @click="adminSection = 'usuarios'"
              >
                Usuarios
              </button>
              <button
                class="secondary"
                type="button"
                style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                @click="adminSection = 'carreras'"
              >
                Carreras
              </button>
              <button
                class="secondary"
                type="button"
                style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                @click="adminSection = 'materias'"
              >
                Materias
              </button>
              <button
                class="secondary"
                type="button"
                style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                @click="adminSection = 'cursos'"
              >
                Cursos
              </button>
              <button
                class="secondary"
                type="button"
                style="padding: 0.5rem 1rem; font-size: 0.85rem;"
                @click="adminSection = 'reportes'"
              >
                Reportes
              </button>
              <span class="role-badge" :data-tone="badgeTone">{{ roleName }}</span>
            </div>
          </div>

          <div v-if="adminSection === 'usuarios'" style="margin-top: 1rem; width: 100%;">
            <UserManagement :api="api" />
          </div>

          <div v-else-if="adminSection === 'carreras'" style="margin-top: 1rem; width: 100%;">
            <CarreraManagement :api="api" />
          </div>

          <div v-else-if="adminSection === 'materias'" style="margin-top: 1rem; width: 100%;">
            <MateriaManagement :api="api" />
          </div>

          <div v-else-if="adminSection === 'cursos'" style="margin-top: 1rem; width: 100%;">
            <CursoManagement :api="api" />
          </div>

          <div v-else-if="adminSection === 'reportes'" style="margin-top: 1rem; width: 100%;">
            <ReportesAdmin :api="api" />
          </div>

          <div v-else class="info-grid">
            <article>
              <span>Correo</span>
              <strong>{{ user.correo }}</strong>
            </article>
            <article>
              <span>CI</span>
              <strong>{{ user.ci }}</strong>
            </article>
            <article>
              <span>Registro</span>
              <strong>{{ user.fechaRegistro || 'No disponible' }}</strong>
            </article>
            <article>
              <span>Estado</span>
              <strong>{{ user.estado ? 'Activo' : 'Inactivo' }}</strong>
            </article>
          </div>

          <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
          <div v-if="errorMessage" class="alert error">{{ errorMessage }}</div>

          <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; width: 100%;">
            <button class="secondary" type="button" :disabled="loading" @click="logout">
              {{ loading ? 'Cerrando...' : 'Cerrar sesion' }}
            </button>
          </div>
        </div>
      </section>
    </main>
  </div>

  <div v-else class="authenticated-full-workspace">
    <main class="auth-shell">
      <section class="hero-panel">
        <div class="brand">Universidad</div>
        <h1>Panel de estudiante</h1>
        <p>
          Tu sesión está activa. Desde aquí podrás consultar tu información personal y, cuando
          se agreguen los módulos correspondientes, acceder a tu historial académico, inscripciones
          y notas.
        </p>

        <ul class="feature-list">
          <li>Acceso restringido por rol</li>
          <li>Vista de solo lectura</li>
        </ul>

        <div class="highlight-card">
          <span>Estado</span>
          <strong>Autenticado como estudiante</strong>
        </div>
      </section>

      <section class="panel">
        <div class="card dashboard">
          <div class="dashboard-head">
            <div>
              <span class="eyebrow">Sesion activa</span>
              <h2>{{ fullName }}</h2>
            </div>
            <span class="role-badge" :data-tone="badgeTone">{{ roleName }}</span>
          </div>

          <div class="info-grid">
            <article>
              <span>Correo</span>
              <strong>{{ user.correo }}</strong>
            </article>
            <article>
              <span>CI</span>
              <strong>{{ user.ci }}</strong>
            </article>
            <article>
              <span>Registro</span>
              <strong>{{ user.fechaRegistro || 'No disponible' }}</strong>
            </article>
            <article>
              <span>Estado</span>
              <strong>{{ user.estado ? 'Activo' : 'Inactivo' }}</strong>
            </article>
          </div>

          <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
          <div v-if="errorMessage" class="alert error">{{ errorMessage }}</div>

          <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem; width: 100%;">
            <button class="secondary" type="button" :disabled="loading" @click="logout">
              {{ loading ? 'Cerrando...' : 'Cerrar sesion' }}
            </button>
          </div>
        </div>
      </section>
    </main>
  </div>
</template>

<style scoped>
.auth-shell-wrapper,
.authenticated-full-workspace {
  width: 100vw;
  min-height: 100vh;
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.full-screen-app-container {
  width: 100%;
  min-height: 100vh;
  background: transparent;
}
</style>

<style>
:root {
  color-scheme: dark;
  font-family:
    Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  --bg: #0b1020;
  --bg-soft: rgba(18, 27, 55, 0.72);
  --panel: rgba(14, 20, 41, 0.88);
  --panel-border: rgba(180, 204, 255, 0.18);
  --text: #eef2ff;
  --muted: #aab5d4;
  --primary: #7dd3fc;
  --primary-strong: #38bdf8;
  --danger: #fda4af;
  --success: #86efac;
  --shadow: 0 30px 80px rgba(3, 8, 20, 0.45);
}

* {
  box-sizing: border-box;
}

html,
body,
#app {
  min-height: 100%;
  margin: 0;
}

body {
  min-height: 100vh;
  background:
    radial-gradient(circle at top left, rgba(56, 189, 248, 0.25), transparent 28%),
    radial-gradient(circle at 80% 20%, rgba(99, 102, 241, 0.22), transparent 24%),
    linear-gradient(135deg, #060816, #0b1020 46%, #0f172a);
  color: var(--text);
}

button,
input {
  font: inherit;
}

button {
  cursor: pointer;
}

.auth-shell {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 1.5rem;
  padding: 2rem;
}

.hero-panel,
.panel {
  display: flex;
  align-items: center;
}

.hero-panel {
  flex-direction: column;
  justify-content: center;
  align-items: flex-start;
  padding: 2rem;
}

.brand {
  display: inline-flex;
  align-items: center;
  padding: 0.45rem 0.8rem;
  border: 1px solid rgba(255, 255, 255, 0.18);
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.05);
  letter-spacing: 0.16em;
  text-transform: uppercase;
  font-size: 0.72rem;
  color: var(--primary);
}

.hero-panel h1 {
  max-width: 12ch;
  margin: 1.25rem 0 1rem;
  font-size: clamp(2.8rem, 6vw, 5.6rem);
  line-height: 0.95;
}

.hero-panel p {
  max-width: 34rem;
  margin: 0;
  color: var(--muted);
  font-size: 1.05rem;
  line-height: 1.7;
}

.feature-list {
  list-style: none;
  padding: 0;
  margin: 1.5rem 0 0;
  display: grid;
  gap: 0.85rem;
}

.feature-list li {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  color: var(--text);
}

.feature-list li::before {
  content: '';
  width: 0.75rem;
  height: 0.75rem;
  border-radius: 999px;
  background: linear-gradient(135deg, var(--primary), var(--primary-strong));
  box-shadow: 0 0 0 0.35rem rgba(125, 211, 252, 0.12);
}

.highlight-card,
.card {
  background: linear-gradient(180deg, rgba(14, 20, 41, 0.94), rgba(11, 16, 32, 0.92));
  border: 1px solid var(--panel-border);
  box-shadow: var(--shadow);
  backdrop-filter: blur(18px);
}

.highlight-card {
  margin-top: 2rem;
  min-width: 18rem;
  border-radius: 1.4rem;
  padding: 1.1rem 1.2rem;
}

.highlight-card span,
.eyebrow,
.info-grid span,
.form-grid span,
.highlight-card strong {
  display: block;
}

.highlight-card span,
.eyebrow,
.info-grid span,
.form-grid span {
  color: var(--muted);
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.12em;
}

.highlight-card strong {
  margin-top: 0.4rem;
  font-size: 1.2rem;
}

.panel {
  justify-content: center;
  padding: 2rem 0;
}

.card {
  width: min(100%, 44rem);
  border-radius: 1.8rem;
  padding: 1.4rem;
  transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.card.wide-card {
  width: min(100%, 75rem);
}

.auth-shell.full-width-shell {
  grid-template-columns: 1fr;
}

.form-grid {
  display: grid;
  gap: 1rem;
}

label {
  display: grid;
  gap: 0.55rem;
}

input {
  width: 100%;
  border: 1px solid rgba(180, 204, 255, 0.14);
  border-radius: 0.9rem;
  background: rgba(6, 10, 23, 0.72);
  color: var(--text);
  padding: 0.95rem 1rem;
  outline: none;
  transition:
    border-color 0.2s ease,
    box-shadow 0.2s ease,
    transform 0.2s ease;
}

input:focus {
  border-color: rgba(125, 211, 252, 0.8);
  box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.14);
  transform: translateY(-1px);
}

.primary,
.secondary {
  border: 0;
  border-radius: 0.95rem;
  padding: 0.95rem 1.1rem;
  font-weight: 700;
  transition:
    transform 0.2s ease,
    opacity 0.2s ease,
    box-shadow 0.2s ease;
}

.primary {
  color: #02131e;
  background: linear-gradient(135deg, #67e8f9, #7dd3fc 50%, #38bdf8);
  box-shadow: 0 12px 40px rgba(56, 189, 248, 0.28);
}

.secondary {
  color: var(--text);
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(180, 204, 255, 0.18);
}

.primary:hover,
.secondary:hover {
  transform: translateY(-1px);
}

.primary:disabled,
.secondary:disabled {
  opacity: 0.68;
  cursor: wait;
}

.alert {
  margin-bottom: 1rem;
  padding: 0.9rem 1rem;
  border-radius: 0.9rem;
  border: 1px solid transparent;
}

.alert.success {
  background: rgba(34, 197, 94, 0.12);
  border-color: rgba(134, 239, 172, 0.2);
  color: var(--success);
}

.alert.error {
  background: rgba(244, 63, 94, 0.12);
  border-color: rgba(253, 164, 175, 0.2);
  color: var(--danger);
}

.dashboard {
  display: grid;
  gap: 1.25rem;
}

.dashboard-head {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
  align-items: flex-start;
}

.dashboard h2 {
  margin: 0.25rem 0 0;
  font-size: clamp(1.5rem, 3vw, 2.2rem);
}

.role-badge {
  border-radius: 999px;
  padding: 0.6rem 0.9rem;
  font-size: 0.82rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
}

.role-badge[data-tone='gold'] {
  color: #fef3c7;
  background: rgba(245, 158, 11, 0.16);
}

.role-badge[data-tone='blue'] {
  color: #bfdbfe;
  background: rgba(59, 130, 246, 0.16);
}

.role-badge[data-tone='green'] {
  color: #bbf7d0;
  background: rgba(34, 197, 94, 0.16);
}

.role-badge[data-tone='neutral'] {
  color: var(--text);
  background: rgba(255, 255, 255, 0.08);
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.85rem;
}

.info-grid article {
  padding: 1rem;
  border-radius: 1rem;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(180, 204, 255, 0.12);
}

.info-grid strong {
  display: block;
  margin-top: 0.45rem;
  font-size: 1rem;
  word-break: break-word;
}

@media (max-width: 980px) {
  .auth-shell {
    grid-template-columns: 1fr;
  }

  .hero-panel {
    padding-bottom: 0;
  }
}

@media (max-width: 720px) {
  .auth-shell {
    padding: 1rem;
  }

  .card {
    padding: 1rem;
    border-radius: 1.4rem;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }

  .dashboard-head {
    flex-direction: column;
  }
}
</style>
