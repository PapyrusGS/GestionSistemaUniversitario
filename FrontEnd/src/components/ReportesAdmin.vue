<template>
  <div class="rw">

    <!-- Header -->
    <div class="rw-header">
      <div>
        <h1 class="rw-title">Analítica y Reportes</h1>
        <p class="rw-subtitle">Genera información consolidada para la toma de decisiones.</p>
      </div>
    </div>

    <!-- Alertas -->
    <div v-if="successMessage" class="rw-alert rw-alert--success">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="rw-alert rw-alert--error">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ errorMessage }}
    </div>
    <div v-if="infoMessage" class="rw-alert rw-alert--info">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      {{ infoMessage }}
    </div>

    <!-- Panel de Configuración -->
    <div class="rw-panel">

      <!-- Selector de tipo -->
      <p class="rw-section-label">Tipo de reporte</p>
      <div class="rw-type-grid">

        <button
          class="rw-type-btn"
          :class="{ 'rw-type-btn--active': filtros.tipo === 'inscripciones' }"
          @click="selectTipo('inscripciones')"
        >
          <svg class="rw-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
          </svg>
          <span>Inscripciones y Notas</span>
        </button>

        <button
          class="rw-type-btn"
          :class="{ 'rw-type-btn--active': filtros.tipo === 'cursos' }"
          @click="selectTipo('cursos')"
        >
          <svg class="rw-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
          </svg>
          <span>Catálogo de Cursos</span>
        </button>

        <button
          class="rw-type-btn"
          :class="{ 'rw-type-btn--active': filtros.tipo === 'docentes' }"
          @click="selectTipo('docentes')"
        >
          <svg class="rw-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
          </svg>
          <span>Plantilla Docente</span>
        </button>

        <button
          class="rw-type-btn"
          :class="{ 'rw-type-btn--active': filtros.tipo === 'materias' }"
          @click="selectTipo('materias')"
        >
          <svg class="rw-type-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
          </svg>
          <span>Catálogo de Materias</span>
        </button>

      </div>

      <!-- Filtros por Curso y Materia -->
      <div v-if="filtros.tipo === 'inscripciones' || filtros.tipo === 'cursos'" class="rw-filters">
        <div class="rw-filter-group">
          <label class="rw-label">Curso</label>
          <select v-model="filtros.curso" class="rw-select">
            <option value="">Todos los cursos</option>
            <option v-for="c in listadoCursos" :key="c.idCurso" :value="c.idCurso">{{ c.idCurso }}</option>
          </select>
        </div>
        <div class="rw-filter-group">
          <label class="rw-label">Materia</label>
          <select v-model="filtros.materia" class="rw-select">
            <option value="">Todas las materias</option>
            <option v-for="m in listadoMaterias" :key="m.idMateria" :value="m.idMateria">{{ m.sigla || m.idMateria }} — {{ m.nombre }}</option>
          </select>
        </div>
      </div>

      <!-- Botón Procesar -->
      <div class="rw-actions">
        <button class="rw-btn-primary" :disabled="loading" @click="cargarReporte">
          <span v-if="loading" class="rw-spinner"></span>
          <svg v-else width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          {{ loading ? 'Calculando...' : 'Procesar solicitud' }}
        </button>
      </div>

    </div>

    <!-- Botones Exportar (solo cuando hay datos) -->
    <div v-if="reporte.length > 0" class="rw-export-bar">
      <span class="rw-export-label">{{ reporte.length }} registros encontrados</span>
      <div class="rw-export-btns">
        <button class="rw-btn-export rw-btn-export--pdf" :disabled="exporting" @click="exportar('pdf')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
          Exportar PDF
        </button>
        <button class="rw-btn-export rw-btn-export--excel" :disabled="exporting" @click="exportar('excel')">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Exportar Excel
        </button>
      </div>
    </div>

    <!-- Estado vacío -->
    <div v-if="searched && reporte.length === 0 && !loading" class="rw-empty">
      <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" opacity="0.4">
        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="rw-empty-title">Sin resultados</p>
      <p class="rw-empty-sub">No hay información disponible para los filtros aplicados.</p>
    </div>

    <!-- Tabla -->
    <div v-if="reporte.length > 0" class="rw-table-wrap">
      <div class="rw-table-scroll">
        <table class="rw-table">
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
const successMessage  = ref('');
const errorMessage    = ref('');
const infoMessage     = ref('');

const filtros = reactive({ tipo: 'inscripciones', curso: '', materia: '' });

const resetMessages = () => { successMessage.value = ''; errorMessage.value = ''; infoMessage.value = ''; };

const selectTipo = (t) => {
  filtros.tipo = t;
  filtros.curso = '';
  filtros.materia = '';
  reporte.value = [];
  headings.value = [];
  searched.value = false;
};

onMounted(async () => {
  try {
    const { data } = await props.api.get('/reportes/filtros');
    listadoCursos.value   = data.cursos   || [];
    listadoMaterias.value = data.materias || [];
  } catch (e) {
    console.error('Error cargando filtros', e);
  }
});

const buildParams = () => {
  const p = new URLSearchParams();
  p.append('tipo', filtros.tipo);
  if (filtros.curso   && (filtros.tipo === 'inscripciones' || filtros.tipo === 'cursos')) p.append('curso',   filtros.curso);
  if (filtros.materia && (filtros.tipo === 'inscripciones' || filtros.tipo === 'cursos')) p.append('materia', filtros.materia);
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
    successMessage.value = `Archivo descargado correctamente.`;
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
/* =====================
   Wrapper & Header
===================== */
.rw {
  color: #e2e8f0;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
  max-width: 1100px;
}

.rw-header { display: flex; align-items: center; justify-content: space-between; }

.rw-title {
  font-size: 1.75rem;
  font-weight: 800;
  margin: 0 0 0.25rem;
  background: linear-gradient(135deg, #a5b4fc, #f0abfc);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}
.rw-subtitle { margin: 0; color: #94a3b8; font-size: 0.9rem; }

/* =====================
   Alertas
===================== */
.rw-alert {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.75rem 1rem;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  font-weight: 500;
}
.rw-alert svg { flex-shrink: 0; }
.rw-alert--success { background: rgba(16,185,129,0.12); border: 1px solid rgba(16,185,129,0.3); color: #6ee7b7; }
.rw-alert--error   { background: rgba(239,68,68,0.12);  border: 1px solid rgba(239,68,68,0.3);  color: #fca5a5; }
.rw-alert--info    { background: rgba(59,130,246,0.12);  border: 1px solid rgba(59,130,246,0.3);  color: #93c5fd; }

/* =====================
   Panel
===================== */
.rw-panel {
  background: rgba(15,23,42,0.5);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 1.25rem;
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.rw-section-label {
  margin: 0;
  font-size: 0.75rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #64748b;
}

/* =====================
   Tipo de reporte (tarjetas)
===================== */
.rw-type-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
}
@media (min-width: 640px) {
  .rw-type-grid { grid-template-columns: repeat(4, 1fr); }
}

.rw-type-btn {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 0.875rem;
  padding: 1rem 0.75rem;
  cursor: pointer;
  color: #94a3b8;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.6rem;
  font-size: 0.8rem;
  font-weight: 600;
  text-align: center;
  transition: all 0.25s ease;
  line-height: 1.3;
}
.rw-type-btn:hover {
  background: rgba(255,255,255,0.07);
  color: #e2e8f0;
  transform: translateY(-2px);
}
.rw-type-btn--active {
  background: linear-gradient(145deg, rgba(99,102,241,0.25), rgba(168,85,247,0.15));
  border-color: rgba(167,139,250,0.45);
  color: #e0e7ff;
  box-shadow: 0 6px 20px -6px rgba(99,102,241,0.4);
}
.rw-type-icon {
  width: 28px;
  height: 28px;
  flex-shrink: 0;
}

/* =====================
   Filtros
===================== */
.rw-filters {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  padding-top: 1rem;
  border-top: 1px solid rgba(255,255,255,0.06);
}
@media (min-width: 640px) {
  .rw-filters { flex-direction: row; }
}
.rw-filter-group { flex: 1; display: flex; flex-direction: column; gap: 0.4rem; }
.rw-label { font-size: 0.75rem; font-weight: 600; color: #7c8db0; text-transform: uppercase; letter-spacing: 0.06em; }
.rw-select {
  width: 100%;
  background: rgba(8,16,38,0.7);
  border: 1px solid rgba(255,255,255,0.1);
  color: #e2e8f0;
  padding: 0.6rem 0.9rem;
  border-radius: 0.6rem;
  font-size: 0.875rem;
  outline: none;
  appearance: none;
  cursor: pointer;
  transition: border-color 0.2s;
}
.rw-select:focus { border-color: #818cf8; }
.rw-select option { background: #1e293b; }

/* =====================
   Botón procesar
===================== */
.rw-actions { display: flex; justify-content: flex-end; }
.rw-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: #fff;
  border: none;
  border-radius: 0.7rem;
  padding: 0.65rem 1.4rem;
  font-size: 0.875rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 8px 20px -8px rgba(99,102,241,0.7);
  transition: all 0.25s ease;
}
.rw-btn-primary:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(99,102,241,0.8); }
.rw-btn-primary:disabled { opacity: 0.6; cursor: wait; }
.rw-spinner {
  width: 14px; height: 14px;
  border: 2px solid rgba(255,255,255,0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* =====================
   Barra de exportación
===================== */
.rw-export-bar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.75rem;
  background: rgba(15,23,42,0.4);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 0.875rem;
  padding: 0.75rem 1.25rem;
}
.rw-export-label { font-size: 0.82rem; color: #64748b; font-weight: 600; }
.rw-export-btns { display: flex; gap: 0.6rem; }
.rw-btn-export {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  border-radius: 0.6rem;
  padding: 0.5rem 1rem;
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  border: 1px solid transparent;
  transition: all 0.2s ease;
}
.rw-btn-export:disabled { opacity: 0.45; cursor: not-allowed; }
.rw-btn-export--pdf   { background: rgba(239,68,68,0.12); border-color: rgba(239,68,68,0.3); color: #fca5a5; }
.rw-btn-export--pdf:hover:not(:disabled)   { background: rgba(239,68,68,0.22); transform: translateY(-1px); }
.rw-btn-export--excel { background: rgba(16,185,129,0.12); border-color: rgba(16,185,129,0.3); color: #6ee7b7; }
.rw-btn-export--excel:hover:not(:disabled) { background: rgba(16,185,129,0.22); transform: translateY(-1px); }

/* =====================
   Estado vacío
===================== */
.rw-empty {
  background: rgba(15,23,42,0.2);
  border: 2px dashed rgba(255,255,255,0.08);
  border-radius: 1.25rem;
  padding: 3rem 2rem;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.5rem;
}
.rw-empty-title { margin: 0; font-size: 1rem; font-weight: 700; color: #94a3b8; }
.rw-empty-sub   { margin: 0; font-size: 0.85rem; color: #475569; }

/* =====================
   Tabla
===================== */
.rw-table-wrap {
  background: rgba(10,18,38,0.5);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 1rem;
  overflow: hidden;
}
.rw-table-scroll { overflow-x: auto; }
.rw-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
  white-space: nowrap;
}
.rw-table thead tr { background: rgba(0,0,0,0.25); }
.rw-table th {
  padding: 0.85rem 1.1rem;
  text-align: left;
  font-size: 0.7rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #64748b;
  border-bottom: 1px solid rgba(255,255,255,0.05);
}
.rw-table td {
  padding: 0.85rem 1.1rem;
  color: #cbd5e1;
  border-bottom: 1px solid rgba(255,255,255,0.03);
}
.rw-table tbody tr:hover { background: rgba(255,255,255,0.02); }
.rw-table tbody tr:last-child td { border-bottom: none; }
</style>
