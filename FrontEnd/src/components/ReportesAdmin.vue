<template>
  <div class="ra">

    <!-- Alertas -->
    <div v-if="successMessage" class="ra-alert ra-alert--success">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="ra-alert ra-alert--error">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ errorMessage }}
    </div>
    <div v-if="infoMessage" class="ra-alert ra-alert--info">
      <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      {{ infoMessage }}
    </div>

    <!-- Panel de configuración -->
    <div class="ra-panel">

      <p class="ra-section-label">Tipo de reporte</p>

      <div class="ra-type-grid">
        <button
          class="ra-type-btn"
          :class="{ 'ra-type-btn--active': filtros.tipo === 'inscripciones' }"
          @click="selectTipo('inscripciones')"
        >
          <svg class="ra-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
          <span>Inscripciones y Notas</span>
        </button>

        <button
          class="ra-type-btn"
          :class="{ 'ra-type-btn--active': filtros.tipo === 'cursos' }"
          @click="selectTipo('cursos')"
        >
          <svg class="ra-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
          <span>Catálogo de Cursos</span>
        </button>

        <button
          class="ra-type-btn"
          :class="{ 'ra-type-btn--active': filtros.tipo === 'docentes' }"
          @click="selectTipo('docentes')"
        >
          <svg class="ra-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <span>Plantilla Docente</span>
        </button>

        <button
          class="ra-type-btn"
          :class="{ 'ra-type-btn--active': filtros.tipo === 'materias' }"
          @click="selectTipo('materias')"
        >
          <svg class="ra-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          <span>Catálogo de Materias</span>
        </button>
      </div>

      <!-- Filtros Curso / Materia -->
      <div v-if="filtros.tipo === 'inscripciones' || filtros.tipo === 'cursos'" class="ra-filters">
        <div class="ra-filter-group">
          <label class="ra-label">Curso</label>
          <select v-model="filtros.curso" class="ra-select">
            <option value="">Todos los cursos</option>
            <option v-for="c in listadoCursos" :key="c.idCurso" :value="c.idCurso">{{ c.idCurso }}</option>
          </select>
        </div>
        <div class="ra-filter-group">
          <label class="ra-label">Materia</label>
          <select v-model="filtros.materia" class="ra-select">
            <option value="">Todas las materias</option>
            <option v-for="m in listadoMaterias" :key="m.idMateria" :value="m.idMateria">{{ m.sigla || m.idMateria }} — {{ m.nombre }}</option>
          </select>
        </div>
      </div>

      <!-- Filtros Carrera / Semestre -->
      <div v-if="filtros.tipo === 'materias'" class="ra-filters">
        <div class="ra-filter-group">
          <label class="ra-label">Carrera</label>
          <select v-model="filtros.carrera" class="ra-select">
            <option value="">Todas las carreras</option>
            <option v-for="car in listadoCarreras" :key="car.idCarrera" :value="car.idCarrera">{{ car.nombre }}</option>
          </select>
        </div>
        <div class="ra-filter-group">
          <label class="ra-label">Semestre</label>
          <select v-model="filtros.semestre" class="ra-select">
            <option value="">Todos los semestres</option>
            <option v-for="s in 10" :key="s" :value="s">Semestre {{ s }}</option>
          </select>
        </div>
      </div>

      <!-- Acciones -->
      <div class="ra-actions">
        <button class="uni-btn-action-success" :disabled="loading" @click="cargarReporte">
          <span v-if="loading" class="ra-spinner"></span>
          <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          {{ loading ? 'Calculando...' : 'Procesar solicitud' }}
        </button>
      </div>

    </div>

    <!-- Barra de exportación -->
    <div v-if="reporte.length > 0" class="ra-export-bar">
      <span class="ra-export-count">{{ reporte.length }} registros encontrados</span>
      <div class="ra-export-btns">
        <button class="ra-btn-export ra-btn-export--pdf" :disabled="exporting" @click="exportar('pdf')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Exportar PDF
        </button>
        <button class="ra-btn-export ra-btn-export--excel" :disabled="exporting" @click="exportar('excel')">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Exportar Excel
        </button>
      </div>
    </div>

    <!-- Estado vacío -->
    <div v-if="searched && reporte.length === 0 && !loading" class="ra-empty">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="ra-empty-title">Sin resultados</p>
      <p class="ra-empty-sub">No hay información disponible para los filtros aplicados.</p>
    </div>

    <!-- Tabla -->
    <div v-if="reporte.length > 0" class="ra-table-wrap">
      <div class="ra-table-scroll">
        <table class="ra-table">
          <thead>
            <tr>
              <th v-for="(head, i) in headings" :key="i">{{ head }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(fila, i) in reporte" :key="i">
              <td v-for="(celda, j) in fila" :key="j">{{ celda ?? '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';

const props = defineProps({
  api: { type: Object, required: true }
});

const loading    = ref(false);
const exporting  = ref(false);
const searched   = ref(false);
const reporte    = ref([]);
const headings   = ref([]);
const listadoCursos   = ref([]);
const listadoMaterias = ref([]);
const listadoCarreras = ref([]);
const successMessage  = ref('');
const errorMessage    = ref('');
const infoMessage     = ref('');

const filtros = reactive({ tipo: 'inscripciones', curso: '', materia: '', carrera: '', semestre: '' });

const resetMessages = () => { successMessage.value = ''; errorMessage.value = ''; infoMessage.value = ''; };

const selectTipo = (t) => {
  filtros.tipo = t;
  filtros.curso = '';
  filtros.materia = '';
  filtros.carrera = '';
  filtros.semestre = '';
  reporte.value = [];
  headings.value = [];
  searched.value = false;
};

onMounted(async () => {
  try {
    const { data } = await props.api.get('/reportes/filtros');
    listadoCursos.value   = data.cursos   || [];
    listadoMaterias.value = data.materias || [];
    listadoCarreras.value = data.carreras || [];
  } catch (e) {
    console.error('Error cargando filtros', e);
  }
});

const buildParams = () => {
  const p = new URLSearchParams();
  p.append('tipo', filtros.tipo);
  if (filtros.curso   && (filtros.tipo === 'inscripciones' || filtros.tipo === 'cursos')) p.append('curso',   filtros.curso);
  if (filtros.materia && (filtros.tipo === 'inscripciones' || filtros.tipo === 'cursos')) p.append('materia', filtros.materia);
  if (filtros.carrera  && filtros.tipo === 'materias') p.append('carrera',  filtros.carrera);
  if (filtros.semestre && filtros.tipo === 'materias') p.append('semestre', filtros.semestre);
  return p;
};

const cargarReporte = async () => {
  loading.value = true;
  searched.value = true;
  resetMessages();
  try {
    const { data } = await props.api.get(`/reportes?${buildParams()}`);
    reporte.value  = data.data     || [];
    headings.value = data.headings || [];
    if (reporte.value.length > 0) {
      successMessage.value = 'Reporte generado correctamente.';
      setTimeout(() => successMessage.value = '', 3000);
    }
  } catch (e) {
    console.error(e);
    errorMessage.value = 'Error al comunicarse con el servidor.';
    reporte.value  = [];
    headings.value = [];
  } finally {
    loading.value = false;
  }
};

const exportar = async (formato) => {
  exporting.value = true;
  resetMessages();
  infoMessage.value = `Generando ${formato.toUpperCase()}...`;
  try {
    const p = buildParams();
    p.append('formato', formato);
    const response = await props.api.get(`/reportes/exportar?${p}`, {
      responseType: 'blob',
      headers: { Accept: formato === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }
    });
    const url  = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href  = url;
    link.setAttribute('download', `reporte_${filtros.tipo}_${Date.now()}.${formato === 'pdf' ? 'pdf' : 'xlsx'}`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
    infoMessage.value = '';
    successMessage.value = 'Archivo descargado correctamente.';
    setTimeout(() => successMessage.value = '', 3000);
  } catch (e) {
    console.error(e);
    infoMessage.value = '';
    errorMessage.value = `No se pudo exportar a ${formato.toUpperCase()}.`;
  } finally {
    exporting.value = false;
  }
};
</script>

<style scoped>
/* ── Variables locales que heredan del sistema global ── */
.ra {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  max-width: 1000px;
  color: var(--uni-text);
}

/* ── Alertas — reutilizan tokens del App.vue ── */
.ra-alert {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.65rem 1rem;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid;
}
.ra-alert svg { flex-shrink: 0; }
.ra-alert--success {
  background: var(--uni-success-bg);
  border-color: var(--uni-success-border);
  color: var(--uni-success-text);
}
.ra-alert--error {
  background: var(--uni-error-bg);
  border-color: var(--uni-error-border);
  color: var(--uni-error-text);
}
.ra-alert--info {
  background: #eef3f8;
  border-color: #a8c4dc;
  color: #1e4a6e;
}

/* ── Panel principal ── */
.ra-panel {
  background: #fafafa;
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 12px;
  padding: 1.25rem;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.ra-section-label {
  margin: 0;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--uni-muted);
}

/* ── Tarjetas de tipo ── */
.ra-type-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.6rem;
}
@media (min-width: 600px) {
  .ra-type-grid { grid-template-columns: repeat(4, 1fr); }
}

.ra-type-btn {
  background: var(--color-white);
  border: 1.5px solid var(--color-linen);
  border-radius: 10px;
  padding: 0.85rem 0.6rem;
  cursor: pointer;
  color: var(--uni-muted);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
  font-size: 11px;
  font-weight: 600;
  text-align: center;
  transition: border-color 0.2s, color 0.2s, background 0.2s;
  line-height: 1.3;
}
.ra-type-btn:hover {
  border-color: var(--color-mint-light);
  color: var(--color-mint-dark);
  background: var(--color-white);
}
.ra-type-btn--active {
  border-color: var(--color-mint-dark);
  background: var(--uni-success-bg);
  color: var(--color-mint-dark);
}
.ra-type-icon {
  width: 24px;
  height: 24px;
  flex-shrink: 0;
}

/* ── Filtros ── */
.ra-filters {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding-top: 0.75rem;
  border-top: 1px solid var(--color-linen);
}
@media (min-width: 600px) {
  .ra-filters { flex-direction: row; }
}

.ra-filter-group {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
}

.ra-label {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--uni-muted);
}

.ra-select {
  width: 100%;
  background: var(--color-white);
  border: 1.5px solid var(--color-linen);
  color: var(--uni-text);
  padding: 0.55rem 0.85rem;
  border-radius: 20px;
  font-size: 12px;
  font-family: inherit;
  outline: none;
  appearance: none;
  cursor: pointer;
  transition: border-color 0.2s;
}
.ra-select:focus { border-color: var(--color-mint-dark); }

/* ── Botón procesar — hereda .uni-btn-action-success del App.vue ── */
.ra-actions {
  display: flex;
  justify-content: flex-end;
  padding-top: 0.25rem;
}

.ra-spinner {
  width: 12px;
  height: 12px;
  border: 2px solid rgba(255,255,255,0.4);
  border-top-color: #fff;
  border-radius: 50%;
  animation: ra-spin 0.75s linear infinite;
  flex-shrink: 0;
}
@keyframes ra-spin { to { transform: rotate(360deg); } }

/* ── Barra de exportación ── */
.ra-export-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: #fafafa;
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 12px;
  padding: 0.65rem 1rem;
}

.ra-export-count {
  font-size: 11px;
  font-weight: 600;
  color: var(--uni-muted);
}

.ra-export-btns {
  display: flex;
  gap: 0.5rem;
}

.ra-btn-export {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  border-radius: 20px;
  padding: 0.45rem 0.9rem;
  font-size: 11px;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
  border: 1px solid transparent;
  transition: background 0.2s;
}
.ra-btn-export:disabled { opacity: 0.45; cursor: not-allowed; }

.ra-btn-export--pdf {
  background: var(--uni-error-bg);
  border-color: var(--uni-error-border);
  color: var(--uni-error-text);
}
.ra-btn-export--pdf:hover:not(:disabled) { background: #f5e0e0; }

.ra-btn-export--excel {
  background: var(--uni-success-bg);
  border-color: var(--uni-success-border);
  color: var(--uni-success-text);
}
.ra-btn-export--excel:hover:not(:disabled) { background: #d8ece6; }

/* ── Estado vacío ── */
.ra-empty {
  background: #fafafa;
  border: 1.5px dashed var(--color-linen);
  border-radius: 12px;
  padding: 2.5rem 2rem;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.4rem;
  color: var(--uni-muted);
}
.ra-empty-title {
  margin: 0;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--color-dark-gray);
}
.ra-empty-sub {
  margin: 0;
  font-size: 11px;
  color: var(--uni-muted);
}

/* ── Tabla ── */
.ra-table-wrap {
  background: var(--color-white);
  border: 1px solid rgba(0,0,0,.06);
  border-radius: 12px;
  overflow: hidden;
}
.ra-table-scroll { overflow-x: auto; }

.ra-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
  white-space: nowrap;
}

.ra-table thead tr {
  background: #fafafa;
  border-bottom: 1px solid var(--color-linen);
}

.ra-table th {
  padding: 0.75rem 1rem;
  text-align: left;
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--uni-muted);
}

.ra-table td {
  padding: 0.75rem 1rem;
  color: var(--uni-text);
  border-bottom: 1px solid rgba(0,0,0,.04);
}

.ra-table tbody tr:hover { background: #f7f7f5; }
.ra-table tbody tr:last-child td { border-bottom: none; }
</style>