<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import axios from 'axios'

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

const loginForm = reactive({
  login: '',
  password: '',
})

const registerForm = reactive({
  nombre1: '',
  nombre2: '',
  apellido1: '',
  apellido2: '',
  ci: '',
  correo: '',
  password: '',
  password_confirmation: '',
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
}

function clearSession() {
  token.value = ''
  sessionStorage.removeItem(sessionKey)
  user.value = null
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
  if (!token.value) {
    return
  }

  try {
    const { data } = await api.get('/auth/me')
    user.value = data.user
  } catch {
    clearSession()
  }
}

async function login() {
  loading.value = true
  resetMessages()

  try {
    const { data } = await api.post('/auth/login', loginForm)
    persistSession(data.token, data.user)
    successMessage.value = 'Sesión iniciada correctamente.'
    mode.value = 'login'
  } catch (error) {
    errorMessage.value = parseError(error, 'No pudimos iniciar sesión.')
  } finally {
    loading.value = false
  }
}

async function register() {
  loading.value = true
  resetMessages()

  try {
    const { data } = await api.post('/auth/register', registerForm)
    persistSession(data.token, data.user)
    successMessage.value = 'Cuenta creada y sesión iniciada.'
    mode.value = 'login'
  } catch (error) {
    errorMessage.value = parseError(error, 'No pudimos registrar la cuenta.')
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
    // Si el token ya expiró o fue revocado, igual limpiamos la sesión local.
  } finally {
    clearSession()
    loading.value = false
    mode.value = 'login'
  }
}

function switchMode(nextMode) {
  mode.value = nextMode
  resetMessages()
}

onMounted(loadProfile)
</script>

<template>
  <main class="auth-shell">
    <section class="hero-panel">
      <div class="brand">Universidad</div>
      <h1>Módulo de autenticación seguro</h1>
      <p>
        Acceso con API protegida por Sanctum, contraseñas cifradas, throttling de intentos y
        control por rol sobre la base de datos real del sistema.
      </p>

      <ul class="feature-list">
        <li>Login con correo o CI</li>
        <li>Registro automático como estudiante</li>
        <li>Token por sesión en el navegador</li>
      </ul>

      <div class="highlight-card">
        <span>Estado</span>
        <strong>{{ isAuthenticated ? 'Autenticado' : 'Pendiente de acceso' }}</strong>
      </div>
    </section>

    <section class="panel">
      <div v-if="!isAuthenticated" class="card">
        <div class="tabs">
          <button type="button" :class="{ active: mode === 'login' }" @click="switchMode('login')">
            Iniciar sesión
          </button>
          <button
            type="button"
            :class="{ active: mode === 'register' }"
            @click="switchMode('register')"
          >
            Crear cuenta
          </button>
        </div>

        <div v-if="successMessage" class="alert success">{{ successMessage }}</div>
        <div v-if="errorMessage" class="alert error">{{ errorMessage }}</div>

        <form v-if="mode === 'login'" class="form-grid" @submit.prevent="login">
          <label>
            <span>Correo o CI</span>
            <input v-model.trim="loginForm.login" type="text" autocomplete="username" required />
          </label>

          <label>
            <span>Contraseña</span>
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

        <form v-else class="form-grid" @submit.prevent="register">
          <div class="two-cols">
            <label>
              <span>Primer nombre</span>
              <input v-model.trim="registerForm.nombre1" type="text" required />
            </label>
            <label>
              <span>Segundo nombre</span>
              <input v-model.trim="registerForm.nombre2" type="text" />
            </label>
          </div>

          <div class="two-cols">
            <label>
              <span>Primer apellido</span>
              <input v-model.trim="registerForm.apellido1" type="text" required />
            </label>
            <label>
              <span>Segundo apellido</span>
              <input v-model.trim="registerForm.apellido2" type="text" />
            </label>
          </div>

          <div class="two-cols">
            <label>
              <span>CI</span>
              <input v-model.trim="registerForm.ci" type="text" required />
            </label>
            <label>
              <span>Correo</span>
              <input v-model.trim="registerForm.correo" type="email" required />
            </label>
          </div>

          <div class="two-cols">
            <label>
              <span>Contraseña</span>
              <input
                v-model="registerForm.password"
                type="password"
                autocomplete="new-password"
                minlength="8"
                required
              />
            </label>
            <label>
              <span>Confirmar contraseña</span>
              <input
                v-model="registerForm.password_confirmation"
                type="password"
                autocomplete="new-password"
                minlength="8"
                required
              />
            </label>
          </div>

          <button class="primary" type="submit" :disabled="loading">
            {{ loading ? 'Creando...' : 'Registrar estudiante' }}
          </button>
        </form>
      </div>

      <div v-else class="card dashboard">
        <div class="dashboard-head">
          <div>
            <span class="eyebrow">Sesión activa</span>
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

        <button class="secondary" type="button" :disabled="loading" @click="logout">
          {{ loading ? 'Cerrando...' : 'Cerrar sesión' }}
        </button>
      </div>
    </section>
  </main>
</template>

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
}

.tabs {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 0.5rem;
  margin-bottom: 1rem;
  padding: 0.35rem;
  background: rgba(255, 255, 255, 0.04);
  border-radius: 1rem;
}

.tabs button {
  border: 0;
  border-radius: 0.8rem;
  padding: 0.85rem 1rem;
  color: var(--muted);
  background: transparent;
  transition:
    transform 0.2s ease,
    background 0.2s ease,
    color 0.2s ease;
}

.tabs button.active {
  color: var(--text);
  background: linear-gradient(135deg, rgba(56, 189, 248, 0.22), rgba(125, 211, 252, 0.16));
}

.tabs button:hover {
  transform: translateY(-1px);
}

.form-grid {
  display: grid;
  gap: 1rem;
}

.two-cols {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
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

  .two-cols,
  .info-grid {
    grid-template-columns: 1fr;
  }

  .dashboard-head {
    flex-direction: column;
  }
}
</style>
