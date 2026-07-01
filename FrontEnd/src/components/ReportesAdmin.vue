<template>
  <div class="ra">

    <!-- ── Alertas ──────────────────────────────────────────────────────── -->
    <div v-if="successMessage" class="ra-alert ra-alert--success">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 13l4 4L19 7"/></svg>
      {{ successMessage }}
    </div>
    <div v-if="errorMessage" class="ra-alert ra-alert--error">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      {{ errorMessage }}
    </div>
    <div v-if="infoMessage" class="ra-alert ra-alert--info">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
      {{ infoMessage }}
    </div>

    <!-- ── Tabs ─────────────────────────────────────────────────────────── -->
    <div class="ra-tabs">
      <button
        v-for="tab in tabs" :key="tab.id"
        class="ra-tab-btn"
        :class="{ 'ra-tab-btn--active': activeTab === tab.id }"
        @click="switchTab(tab.id)"
      >
        <span class="ra-tab-icon">{{ tab.icon }}</span>
        {{ tab.label }}
      </button>
    </div>

    <!-- ── Panel de filtros ──────────────────────────────────────────────── -->
    <div class="ra-panel">

      <!-- ── Tab: Rendimiento ── -->
      <template v-if="activeTab === 'rendimiento'">
        <p class="ra-section-label">Filtros — Rendimiento Académico</p>
        <div class="ra-filters">
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-periodo-rend">Período Académico</label>
            <select id="sel-periodo-rend" v-model="filtrosRendimiento.idPeriodo" class="ra-select">
              <option value="">Todos los períodos</option>
              <option v-for="p in listadoPeriodos" :key="p.idPeriodo" :value="p.idPeriodo">{{ p.nombre }}</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-carrera-rend">Carrera</label>
            <select id="sel-carrera-rend" v-model="filtrosRendimiento.idCarrera" class="ra-select">
              <option value="">Todas las carreras</option>
              <option v-for="c in listadoCarreras" :key="c.idCarrera" :value="c.idCarrera">{{ c.nombre }}</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-docente-rend">Docente</label>
            <select id="sel-docente-rend" v-model="filtrosRendimiento.idDocente" class="ra-select">
              <option value="">Todos los docentes</option>
              <option v-for="d in listadoDocentes" :key="d.idDocente" :value="d.idDocente">{{ d.nombre }}</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-semestre-rend">Semestre</label>
            <select id="sel-semestre-rend" v-model="filtrosRendimiento.semestre" class="ra-select">
              <option value="">Todos los semestres</option>
              <option v-for="s in [1,2,3,4,5,6,7,8,9,10]" :key="s" :value="s">Semestre {{ s }}</option>
            </select>
          </div>
        </div>
      </template>

      <!-- ── Tab: Kárdex ── -->
      <template v-else-if="activeTab === 'kardex'">
        <p class="ra-section-label">Filtros — Kárdex de Estudiante</p>
        <div class="ra-filters">
          <div class="ra-filter-group">
            <label class="ra-label" for="input-ci">Cédula de Identidad (C.I.)</label>
            <input id="input-ci" v-model="filtrosKardex.ci" type="text" class="ra-input" placeholder="Ej: 12345678" @keyup.enter="cargarReporte"/>
          </div>
        </div>
      </template>

      <!-- ── Tab: Auditoría ── -->
      <template v-else-if="activeTab === 'auditoria'">
        <p class="ra-section-label">Filtros — Auditoría General del Sistema</p>
        <div class="ra-filters">
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-tabla-aud">Tabla</label>
            <select id="sel-tabla-aud" v-model="filtrosAuditoria.tabla" class="ra-select">
              <option v-for="t in listadoTablas" :key="t.key" :value="t.key">{{ t.label }}</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-accion-aud">Acción</label>
            <select id="sel-accion-aud" v-model="filtrosAuditoria.accion" class="ra-select">
              <option value="">Todas las acciones</option>
              <option value="C">Creación</option>
              <option value="U">Actualización</option>
              <option value="D">Eliminación</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-usuario-aud">Usuario</label>
            <select id="sel-usuario-aud" v-model="filtrosAuditoria.idUsuario" class="ra-select">
              <option value="">Todos los usuarios</option>
              <option v-for="u in listadoUsuarios" :key="u.idUsuario" :value="u.idUsuario">{{ u.nombre }}</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="date-desde">Fecha Desde</label>
            <input id="date-desde" v-model="filtrosAuditoria.fecha_desde" type="date" class="ra-input" />
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="date-hasta">Fecha Hasta</label>
            <input id="date-hasta" v-model="filtrosAuditoria.fecha_hasta" type="date" class="ra-input" />
          </div>
        </div>
      </template>

      <!-- ── Tab: Ocupación ── -->
      <template v-else-if="activeTab === 'ocupacion'">
        <p class="ra-section-label">Filtros — Ocupación de Cursos</p>
        <div class="ra-filters">
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-periodo-ocup">Período Académico</label>
            <select id="sel-periodo-ocup" v-model="filtrosOcupacion.idPeriodo" class="ra-select">
              <option value="">Todos los períodos</option>
              <option v-for="p in listadoPeriodos" :key="p.idPeriodo" :value="p.idPeriodo">{{ p.nombre }}</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-carrera-ocup">Carrera</label>
            <select id="sel-carrera-ocup" v-model="filtrosOcupacion.idCarrera" class="ra-select">
              <option value="">Todas las carreras</option>
              <option v-for="c in listadoCarreras" :key="c.idCarrera" :value="c.idCarrera">{{ c.nombre }}</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-docente-ocup">Docente</label>
            <select id="sel-docente-ocup" v-model="filtrosOcupacion.idDocente" class="ra-select">
              <option value="">Todos los docentes</option>
              <option v-for="d in listadoDocentes" :key="d.idDocente" :value="d.idDocente">{{ d.nombre }}</option>
            </select>
          </div>
        </div>
      </template>

      <!-- ── Tab: Horario ── -->
      <template v-else-if="activeTab === 'horario'">
        <p class="ra-section-label">Filtros — Horario de Docente</p>
        <div class="ra-filters">
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-docente-hor">Docente *</label>
            <select id="sel-docente-hor" v-model="filtrosHorario.idDocente" class="ra-select">
              <option value="" disabled>Selecciona un docente</option>
              <option v-for="d in listadoDocentes" :key="d.idDocente" :value="d.idDocente">{{ d.nombre }}</option>
            </select>
          </div>
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-periodo-hor">Período Académico</label>
            <select id="sel-periodo-hor" v-model="filtrosHorario.idPeriodo" class="ra-select">
              <option value="">Todos los períodos</option>
              <option v-for="p in listadoPeriodos" :key="p.idPeriodo" :value="p.idPeriodo">{{ p.nombre }}</option>
            </select>
          </div>
        </div>
      </template>

      <!-- ── Tab: Estudiantes por Carrera ── -->
      <template v-else-if="activeTab === 'estudiantesCarrera'">
        <p class="ra-section-label">Filtros — Estudiantes por Carrera</p>
        <div class="ra-filters">
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-carrera-est">Carrera</label>
            <select id="sel-carrera-est" v-model="filtrosEstudiantesCarrera.idCarrera" class="ra-select">
              <option value="">Todas las carreras</option>
              <option v-for="c in listadoCarreras" :key="c.idCarrera" :value="c.idCarrera">{{ c.nombre }}</option>
            </select>
          </div>
        </div>
      </template>

      <!-- ── Tab: Carga Docente ── -->
      <template v-else-if="activeTab === 'cargaDocente'">
        <p class="ra-section-label">Filtros — Carga Docente</p>
        <div class="ra-filters">
          <div class="ra-filter-group">
            <label class="ra-label" for="sel-periodo-carga">Período Académico</label>
            <select id="sel-periodo-carga" v-model="filtrosCargaDocente.idPeriodo" class="ra-select">
              <option value="">Todos los períodos</option>
              <option v-for="p in listadoPeriodos" :key="p.idPeriodo" :value="p.idPeriodo">{{ p.nombre }}</option>
            </select>
          </div>
        </div>
      </template>

      <!-- ── Botón procesar ── -->
      <div class="ra-actions">
        <button class="uni-btn-action-success" :disabled="loading" @click="cargarReporte">
          <span v-if="loading" class="ra-spinner"></span>
          <svg v-else width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          {{ loading ? 'Calculando...' : 'Procesar solicitud' }}
        </button>
      </div>
    </div>

    <!-- ── Barra de exportación ─────────────────────────────────────────── -->
    <div v-if="currentRowCount > 0" class="ra-export-bar">
      <span class="ra-export-count">{{ currentRowCount }} registros encontrados</span>
      <div class="ra-export-btns">
        <button class="ra-btn-export ra-btn-export--pdf" :disabled="exporting" @click="exportar('pdf')">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          PDF
        </button>
        <button class="ra-btn-export ra-btn-export--excel" :disabled="exporting" @click="exportar('excel')">
          <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
          Excel
        </button>
      </div>
    </div>

    <!-- ── Estado vacío ─────────────────────────────────────────────────── -->
    <div v-if="searched[activeTab] && currentRowCount === 0 && !loading && !(activeTab === 'kardex' && resultKardex.cabecera)" class="ra-empty">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
      <p class="ra-empty-title">Sin resultados</p>
      <p class="ra-empty-sub">No hay información disponible para los filtros aplicados.</p>
    </div>

    <!-- ── Tabla Rendimiento ─────────────────────────────────────────────── -->
    <div v-if="activeTab === 'rendimiento' && resultRendimiento.data.length > 0" class="ra-table-wrap">
      <div class="ra-table-scroll">
        <table class="ra-table">
          <thead><tr><th v-for="(h, i) in resultRendimiento.headings" :key="i">{{ h }}</th></tr></thead>
          <tbody>
            <tr v-for="(row, i) in resultRendimiento.data" :key="i">
              <td>{{ row[0] }}</td>
              <td class="ra-num">{{ row[1] }}</td>
              <td>{{ row[2] }}</td>
              <td>{{ row[3] }}</td>
              <td class="ra-num">{{ row[4] }}</td>
              <td>
                <span class="ra-badge" :class="row[5] === 'Sin notas' ? 'ra-badge--gray' : parseFloat(row[5]) >= 51 ? 'ra-badge--green' : 'ra-badge--yellow'">
                  {{ row[5] }}
                </span>
              </td>
              <td>
                <span class="ra-badge" :class="row[6] === 'N/A' ? 'ra-badge--gray' : parseFloat(row[6]) >= 60 ? 'ra-badge--green' : parseFloat(row[6]) >= 40 ? 'ra-badge--yellow' : 'ra-badge--red'">
                  {{ row[6] }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Tabla Kárdex ──────────────────────────────────────────────────── -->
    <template v-if="activeTab === 'kardex' && resultKardex.cabecera">
      <div class="ra-kardex-card">
        <div class="ra-kardex-field"><span class="ra-kardex-label">Estudiante</span><span class="ra-kardex-value">{{ resultKardex.cabecera.nombre }}</span></div>
        <div class="ra-kardex-field"><span class="ra-kardex-label">C.I.</span><span class="ra-kardex-value">{{ resultKardex.cabecera.ci }}</span></div>
        <div class="ra-kardex-field"><span class="ra-kardex-label">Correo</span><span class="ra-kardex-value">{{ resultKardex.cabecera.correo }}</span></div>
        <div class="ra-kardex-field"><span class="ra-kardex-label">Carrera</span><span class="ra-kardex-value">{{ resultKardex.cabecera.carrera }}</span></div>
        <div v-if="resultKardex.historial.length > 0" class="ra-kardex-field"><span class="ra-kardex-label">Aprobadas</span><span class="ra-kardex-value">{{ kardexStats.aprobadas }} / {{ kardexStats.total }}</span></div>
        <div v-if="resultKardex.historial.length > 0" class="ra-kardex-field"><span class="ra-kardex-label">Promedio</span><span class="ra-kardex-value">{{ kardexStats.promedio }}</span></div>
      </div>
      <template v-if="resultKardex.historial.length > 0">
        <div class="ra-table-wrap">
          <div class="ra-table-scroll">
            <table class="ra-table">
              <thead><tr><th>Período</th><th>Materia</th><th>Sem.</th><th>Nota</th><th>Estado</th></tr></thead>
              <tbody>
                <tr v-for="(fila, i) in resultKardex.historial" :key="i">
                  <td>{{ fila.periodo }}</td>
                  <td>{{ fila.materia }}</td>
                  <td class="ra-num">{{ fila.semestre }}</td>
                  <td class="ra-num">{{ fila.nota ?? '—' }}</td>
                  <td>
                    <span class="ra-badge" :class="{ 'ra-badge--green': fila.estadoAcademico === 'Aprobada', 'ra-badge--red': fila.estadoAcademico === 'Reprobada', 'ra-badge--gray': fila.estadoAcademico === 'Sin nota' }">
                      {{ fila.estadoAcademico }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </template>
      <p v-else class="ra-empty">El estudiante no tiene materias cursadas en su historial.</p>
    </template>

    <!-- ── Tabla Auditoría ───────────────────────────────────────────────── -->
    <div v-if="activeTab === 'auditoria' && resultAuditoria.data.length > 0" class="ra-table-wrap">
      <div class="ra-table-scroll">
        <table class="ra-table">
          <thead><tr><th v-for="(h, i) in resultAuditoria.headings" :key="i">{{ h }}</th></tr></thead>
          <tbody>
            <tr v-for="(row, i) in resultAuditoria.data" :key="i">
              <td class="ra-mono">{{ row[0] }}</td>
              <td>{{ row[1] }}</td>
              <td><span class="ra-badge ra-badge--gray">{{ row[2] }}</span></td>
              <td>
                <span class="ra-badge" :class="{ 'ra-badge--green': row[3] === 'Creación', 'ra-badge--blue': row[3] === 'Actualización', 'ra-badge--red': row[3] === 'Eliminación' }">
                  {{ row[3] }}
                </span>
              </td>
              <td class="ra-mono">{{ row[4] }}</td>
              <td class="ra-mono ra-val">{{ row[5] }}</td>
              <td class="ra-mono ra-val">{{ row[6] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Tabla Ocupación ───────────────────────────────────────────────── -->
    <div v-if="activeTab === 'ocupacion' && resultOcupacion.data.length > 0" class="ra-table-wrap">
      <div class="ra-table-scroll">
        <table class="ra-table">
          <thead><tr><th v-for="(h, i) in resultOcupacion.headings" :key="i">{{ h }}</th></tr></thead>
          <tbody>
            <tr v-for="(row, i) in resultOcupacion.data" :key="i">
              <td>{{ row[0] }}</td><td>{{ row[1] }}</td><td class="ra-num">{{ row[2] }}</td>
              <td>{{ row[3] }}</td><td>{{ row[4] }}</td><td class="ra-num">{{ row[5] }}</td>
              <td class="ra-num">{{ row[6] }}</td><td class="ra-num">{{ row[7] }}</td>
              <td>
                <div class="ra-ocup-cell">
                  <div class="ra-bar-wrap"><div class="ra-bar-fill" :style="{ width: row[8], background: ocupBarColor(row[8]) }"></div></div>
                  <span class="ra-ocup-pct" :class="ocupBadgeClass(row[8])">{{ row[8] }}</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Tabla Horario Docente ─────────────────────────────────────────── -->
    <template v-if="activeTab === 'horario' && resultHorario.data.length > 0">
      <div class="ra-kardex-card">
        <div class="ra-kardex-field"><span class="ra-kardex-label">Docente</span><span class="ra-kardex-value">{{ resultHorario.docente }}</span></div>
      </div>
      <div class="ra-table-wrap">
        <div class="ra-table-scroll">
          <table class="ra-table">
            <thead><tr><th v-for="(h, i) in resultHorario.headings" :key="i">{{ h }}</th></tr></thead>
            <tbody>
              <tr v-for="(row, i) in resultHorario.data" :key="i">
                <td>{{ row[0] }}</td><td>{{ row[1] }}</td><td>{{ row[2] }}</td>
                <td style="white-space: pre-wrap;">{{ row[3] }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- ── Tabla Estudiantes por Carrera ──────────────────────────────────── -->
    <div v-if="activeTab === 'estudiantesCarrera' && resultEstudiantesCarrera.data.length > 0" class="ra-table-wrap">
      <div class="ra-table-scroll">
        <table class="ra-table">
          <thead><tr><th v-for="(h, i) in resultEstudiantesCarrera.headings" :key="i">{{ h }}</th></tr></thead>
          <tbody>
            <tr v-for="(row, i) in resultEstudiantesCarrera.data" :key="i">
              <td><span class="ra-badge ra-badge--blue">{{ row[0] }}</span></td>
              <td>{{ row[1] }}</td>
              <td class="ra-mono">{{ row[2] }}</td>
              <td>{{ row[3] }}</td>
              <td>{{ row[4] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- ── Tabla Carga Docente ─────────────────────────────────────────── -->
    <div v-if="activeTab === 'cargaDocente' && resultCargaDocente.data.length > 0" class="ra-table-wrap">
      <div class="ra-table-scroll">
        <table class="ra-table ra-table-carga">
          <thead><tr><th v-for="(h, i) in resultCargaDocente.headings" :key="i">{{ h }}</th></tr></thead>
          <tbody>
            <tr v-for="(row, i) in resultCargaDocente.data" :key="i">
              <td class="font-medium">{{ row[0] }}</td>
              <td class="ra-mono">{{ row[1] }}</td>
              <td class="ra-num">
                <span class="ra-badge" :class="row[2] === 0 ? 'ra-badge--gray' : row[2] >= 4 ? 'ra-badge--red' : 'ra-badge--green'">{{ row[2] }}</span>
              </td>
              <td class="ra-num">{{ row[3] }}</td>
              <td>{{ row[4] }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';

const props = defineProps({
  api: { type: Object, required: true }
});

// ── Tabs ────────────────────────────────────────────────────────────────────
const tabs = [
  { id: 'rendimiento',        label: 'Rendimiento',       icon: '📊' },
  { id: 'kardex',             label: 'Kárdex',            icon: '🎓' },
  { id: 'auditoria',          label: 'Auditoría',         icon: '🔍' },
  { id: 'ocupacion',          label: 'Ocupación',         icon: '📋' },
  { id: 'horario',            label: 'Horario Docente',   icon: '⏰' },
  { id: 'estudiantesCarrera', label: 'Est. por Carrera',  icon: '👥' },
  { id: 'cargaDocente',       label: 'Carga Docente',     icon: '👨‍🏫' },
];
const activeTab = ref('rendimiento');

// ── Estado global ────────────────────────────────────────────────────────────
const loading        = ref(false);
const exporting      = ref(false);
const successMessage = ref('');
const errorMessage   = ref('');
const infoMessage    = ref('');

const searched = reactive({
  rendimiento: false, kardex: false, auditoria: false, ocupacion: false,
  horario: false, estudiantesCarrera: false, cargaDocente: false,
});

// ── Listas de opciones ───────────────────────────────────────────────────────
const listadoPeriodos = ref([]);
const listadoCarreras = ref([]);
const listadoDocentes = ref([]);
const listadoUsuarios = ref([]);
const listadoTablas   = ref([{ key: '', label: 'Todas las tablas' }]);

// ── Filtros por tab ──────────────────────────────────────────────────────────
const filtrosRendimiento       = reactive({ idPeriodo: '', idCarrera: '', idDocente: '', semestre: '' });
const filtrosKardex            = reactive({ ci: '' });
const filtrosAuditoria         = reactive({ fecha_desde: '', fecha_hasta: '', tabla: '', accion: '', idUsuario: '' });
const filtrosOcupacion         = reactive({ idPeriodo: '', idCarrera: '', idDocente: '' });
const filtrosHorario           = reactive({ idDocente: '', idPeriodo: '' });
const filtrosEstudiantesCarrera = reactive({ idCarrera: '' });
const filtrosCargaDocente      = reactive({ idPeriodo: '' });

// ── Resultados por tab ───────────────────────────────────────────────────────
const resultRendimiento        = reactive({ headings: [], data: [] });
const resultKardex             = reactive({ cabecera: null, historial: [] });
const resultAuditoria          = reactive({ headings: [], data: [] });
const resultOcupacion          = reactive({ headings: [], data: [] });
const resultHorario            = reactive({ docente: '', headings: [], data: [] });
const resultEstudiantesCarrera = reactive({ headings: [], data: [] });
const resultCargaDocente       = reactive({ headings: [], data: [] });

// ── Stats Kárdex ─────────────────────────────────────────────────────────────
const kardexStats = computed(() => {
  const hist = resultKardex.historial;
  const conNota = hist.filter(r => r.nota !== null);
  const aprobadas = conNota.filter(r => parseFloat(r.nota) >= 51).length;
  const promedio = conNota.length
    ? (conNota.reduce((s, r) => s + parseFloat(r.nota), 0) / conNota.length).toFixed(1)
    : '—';
  return { total: hist.length, aprobadas, promedio };
});

// ── Cantidad de filas del tab activo ────────────────────────────────────────
const currentRowCount = computed(() => {
  if (activeTab.value === 'rendimiento')        return resultRendimiento.data.length;
  if (activeTab.value === 'kardex')             return resultKardex.historial.length;
  if (activeTab.value === 'auditoria')          return resultAuditoria.data.length;
  if (activeTab.value === 'ocupacion')          return resultOcupacion.data.length;
  if (activeTab.value === 'horario')            return resultHorario.data.length;
  if (activeTab.value === 'estudiantesCarrera') return resultEstudiantesCarrera.data.length;
  if (activeTab.value === 'cargaDocente')       return resultCargaDocente.data.length;
  return 0;
});

// ── Helpers de mensajes ──────────────────────────────────────────────────────
const resetMessages = () => { successMessage.value = ''; errorMessage.value = ''; infoMessage.value = ''; };
const flash = (type, msg, ms = 3500) => {
  if (type === 'success') { successMessage.value = msg; setTimeout(() => successMessage.value = '', ms); }
  if (type === 'error')   { errorMessage.value   = msg; }
  if (type === 'info')    { infoMessage.value     = msg; }
};

// ── Cambiar tab ──────────────────────────────────────────────────────────────
const switchTab = (id) => { activeTab.value = id; resetMessages(); };

// ── Carga inicial de opciones ────────────────────────────────────────────────
onMounted(async () => {
  try {
    const { data } = await props.api.get('/reportes/filtros');
    listadoPeriodos.value = data.periodos          || [];
    listadoCarreras.value = data.carreras          || [];
    listadoDocentes.value = data.docentes          || [];
    listadoUsuarios.value = data.usuarios          || [];
    listadoTablas.value   = data.tablasAuditables  || [{ key: '', label: 'Todas las tablas' }];
  } catch (e) {
    console.error('Error cargando filtros', e);
  }
});

// ── Procesar reporte ─────────────────────────────────────────────────────────
const cargarReporte = async () => {
  resetMessages();
  loading.value = true;
  searched[activeTab.value] = true;

  try {
    if (activeTab.value === 'rendimiento') {
      const p = new URLSearchParams();
      if (filtrosRendimiento.idPeriodo) p.append('idPeriodo', filtrosRendimiento.idPeriodo);
      if (filtrosRendimiento.idCarrera)  p.append('idCarrera',  filtrosRendimiento.idCarrera);
      if (filtrosRendimiento.idDocente)  p.append('idDocente',  filtrosRendimiento.idDocente);
      if (filtrosRendimiento.semestre)   p.append('semestre',   filtrosRendimiento.semestre);
      const { data } = await props.api.get(`/reportes/rendimiento?${p}`);
      resultRendimiento.headings = data.headings || [];
      resultRendimiento.data     = data.data     || [];

    } else if (activeTab.value === 'kardex') {
      const ci = filtrosKardex.ci.trim();
      if (!ci) { flash('error', 'Ingresa el CI del estudiante.'); return; }
      const { data } = await props.api.get(`/reportes/kardex?ci=${encodeURIComponent(ci)}`);
      resultKardex.cabecera  = data.cabecera  || null;
      resultKardex.historial = data.historial || [];

    } else if (activeTab.value === 'auditoria') {
      const p = new URLSearchParams();
      if (filtrosAuditoria.fecha_desde) p.append('fecha_desde', filtrosAuditoria.fecha_desde);
      if (filtrosAuditoria.fecha_hasta) p.append('fecha_hasta', filtrosAuditoria.fecha_hasta);
      if (filtrosAuditoria.tabla)       p.append('tabla',       filtrosAuditoria.tabla);
      if (filtrosAuditoria.accion)      p.append('accion',      filtrosAuditoria.accion);
      if (filtrosAuditoria.idUsuario)   p.append('idUsuario',   filtrosAuditoria.idUsuario);
      const { data } = await props.api.get(`/reportes/auditoria-notas?${p}`);
      resultAuditoria.headings = data.headings || [];
      resultAuditoria.data     = data.data     || [];

    } else if (activeTab.value === 'ocupacion') {
      const p = new URLSearchParams();
      if (filtrosOcupacion.idPeriodo) p.append('idPeriodo', filtrosOcupacion.idPeriodo);
      if (filtrosOcupacion.idCarrera) p.append('idCarrera', filtrosOcupacion.idCarrera);
      if (filtrosOcupacion.idDocente) p.append('idDocente', filtrosOcupacion.idDocente);
      const { data } = await props.api.get(`/reportes/ocupacion?${p}`);
      resultOcupacion.headings = data.headings || [];
      resultOcupacion.data     = data.data     || [];

    } else if (activeTab.value === 'horario') {
      if (!filtrosHorario.idDocente) { flash('error', 'Debes seleccionar un docente.'); loading.value = false; return; }
      const p = new URLSearchParams();
      p.append('idDocente', filtrosHorario.idDocente);
      if (filtrosHorario.idPeriodo) p.append('idPeriodo', filtrosHorario.idPeriodo);
      const { data } = await props.api.get(`/reportes/horario-docente?${p}`);
      resultHorario.docente  = data.docente  || '';
      resultHorario.headings = data.headings || [];
      resultHorario.data     = data.data     || [];

    } else if (activeTab.value === 'estudiantesCarrera') {
      const p = new URLSearchParams();
      if (filtrosEstudiantesCarrera.idCarrera) p.append('idCarrera', filtrosEstudiantesCarrera.idCarrera);
      const { data } = await props.api.get(`/reportes/estudiantes-carrera?${p}`);
      resultEstudiantesCarrera.headings = data.headings || [];
      resultEstudiantesCarrera.data     = data.data     || [];

    } else if (activeTab.value === 'cargaDocente') {
      const p = new URLSearchParams();
      if (filtrosCargaDocente.idPeriodo) p.append('idPeriodo', filtrosCargaDocente.idPeriodo);
      const { data } = await props.api.get(`/reportes/carga-docente?${p}`);
      resultCargaDocente.headings = data.headings || [];
      resultCargaDocente.data     = data.data     || [];
    }

    if (currentRowCount.value > 0) flash('success', 'Reporte generado correctamente.');

  } catch (e) {
    console.error(e);
    const msg = e?.response?.data?.message || 'Error al comunicarse con el servidor.';
    flash('error', msg);
  } finally {
    loading.value = false;
  }
};

// ── Exportar ─────────────────────────────────────────────────────────────────
const exportar = async (formato) => {
  exporting.value = true;
  resetMessages();
  flash('info', `Generando ${formato.toUpperCase()}...`);

  try {
    let url   = '';
    const ext = formato === 'pdf' ? 'pdf' : 'xlsx';

    if (activeTab.value === 'rendimiento') {
      const p = new URLSearchParams({ formato });
      if (filtrosRendimiento.idPeriodo) p.append('idPeriodo', filtrosRendimiento.idPeriodo);
      if (filtrosRendimiento.idCarrera)  p.append('idCarrera',  filtrosRendimiento.idCarrera);
      if (filtrosRendimiento.idDocente)  p.append('idDocente',  filtrosRendimiento.idDocente);
      if (filtrosRendimiento.semestre)   p.append('semestre',   filtrosRendimiento.semestre);
      url = `/reportes/rendimiento/exportar?${p}`;

    } else if (activeTab.value === 'kardex') {
      const p = new URLSearchParams({ ci: filtrosKardex.ci.trim(), formato });
      url = `/reportes/kardex/exportar?${p}`;

    } else if (activeTab.value === 'auditoria') {
      const p = new URLSearchParams({ formato });
      if (filtrosAuditoria.fecha_desde) p.append('fecha_desde', filtrosAuditoria.fecha_desde);
      if (filtrosAuditoria.fecha_hasta) p.append('fecha_hasta', filtrosAuditoria.fecha_hasta);
      if (filtrosAuditoria.tabla)       p.append('tabla',       filtrosAuditoria.tabla);
      if (filtrosAuditoria.accion)      p.append('accion',      filtrosAuditoria.accion);
      if (filtrosAuditoria.idUsuario)   p.append('idUsuario',   filtrosAuditoria.idUsuario);
      url = `/reportes/auditoria-notas/exportar?${p}`;

    } else if (activeTab.value === 'ocupacion') {
      const p = new URLSearchParams({ formato });
      if (filtrosOcupacion.idPeriodo) p.append('idPeriodo', filtrosOcupacion.idPeriodo);
      if (filtrosOcupacion.idCarrera) p.append('idCarrera', filtrosOcupacion.idCarrera);
      if (filtrosOcupacion.idDocente) p.append('idDocente', filtrosOcupacion.idDocente);
      url = `/reportes/ocupacion/exportar?${p}`;

    } else if (activeTab.value === 'horario') {
      const p = new URLSearchParams({ formato });
      p.append('idDocente', filtrosHorario.idDocente);
      if (filtrosHorario.idPeriodo) p.append('idPeriodo', filtrosHorario.idPeriodo);
      url = `/reportes/horario-docente/exportar?${p}`;

    } else if (activeTab.value === 'estudiantesCarrera') {
      const p = new URLSearchParams({ formato });
      if (filtrosEstudiantesCarrera.idCarrera) p.append('idCarrera', filtrosEstudiantesCarrera.idCarrera);
      url = `/reportes/estudiantes-carrera/exportar?${p}`;

    } else if (activeTab.value === 'cargaDocente') {
      const p = new URLSearchParams({ formato });
      if (filtrosCargaDocente.idPeriodo) p.append('idPeriodo', filtrosCargaDocente.idPeriodo);
      url = `/reportes/carga-docente/exportar?${p}`;
    }

    const accept = formato === 'pdf'
      ? 'application/pdf'
      : 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    const response = await props.api.get(url, {
      responseType: 'blob',
      headers: { Accept: accept },
    });

    const blobUrl  = window.URL.createObjectURL(new Blob([response.data]));
    const link     = document.createElement('a');
    link.href      = blobUrl;
    link.setAttribute('download', `reporte_${activeTab.value}_${Date.now()}.${ext}`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(blobUrl);

    infoMessage.value = '';
    flash('success', 'Archivo descargado correctamente.');
  } catch (e) {
    console.error(e);
    infoMessage.value = '';
    flash('error', `No se pudo exportar a ${formato.toUpperCase()}.`);
  } finally {
    exporting.value = false;
  }
};

// ── Helpers visuales para Ocupación ─────────────────────────────────────────
const ocupBarColor = (pctStr) => {
  const v = parseFloat(pctStr);
  if (v >= 90) return '#fc8181';
  if (v >= 60) return '#f6ad55';
  if (v >= 30) return '#63b3ed';
  return '#68d391';
};
const ocupBadgeClass = (pctStr) => {
  const v = parseFloat(pctStr);
  if (v >= 90) return 'ra-ocup-pct--full';
  if (v >= 60) return 'ra-ocup-pct--high';
  if (v >= 30) return 'ra-ocup-pct--medium';
  return 'ra-ocup-pct--low';
};
</script>

<style scoped>
/* ── Contenedor principal ── */
.ra { display: flex; flex-direction: column; gap: 1rem; max-width: 1200px; color: var(--uni-text); }

/* ── Alertas ── */
.ra-alert { display: flex; align-items: center; gap: 0.5rem; padding: 0.65rem 1rem; border-radius: 20px; font-size: 12px; font-weight: 500; border: 1px solid; }
.ra-alert svg { flex-shrink: 0; }
.ra-alert--success { background: var(--uni-success-bg); border-color: var(--uni-success-border); color: var(--uni-success-text); }
.ra-alert--error   { background: var(--uni-error-bg);   border-color: var(--uni-error-border);   color: var(--uni-error-text);   }
.ra-alert--info    { background: #eef3f8; border-color: #a8c4dc; color: #1e4a6e; }

/* ── Tabs ── */
.ra-tabs { display: flex; gap: 0.3rem; background: #fafafa; border: 1px solid rgba(0,0,0,.07); border-radius: 12px; padding: 0.4rem; flex-wrap: wrap; }
.ra-tab-btn { flex: 1; min-width: fit-content; display: flex; align-items: center; justify-content: center; gap: 0.35rem; padding: 0.5rem 0.55rem; font-size: 11px; font-weight: 600; font-family: inherit; border: none; border-radius: 8px; background: transparent; color: var(--uni-muted); cursor: pointer; transition: background 0.18s, color 0.18s; white-space: nowrap; }
.ra-tab-btn:hover { background: var(--color-linen); color: var(--uni-text); }
.ra-tab-btn--active { background: var(--color-mint-dark, #2d6a4f); color: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.15); }
.ra-tab-icon { font-size: 12px; }

/* ── Panel principal ── */
.ra-panel { background: #fafafa; border: 1px solid rgba(0,0,0,.06); border-radius: 12px; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; }
.ra-section-label { margin: 0; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--uni-muted); }

/* ── Filtros ── */
.ra-filters { display: flex; flex-direction: column; gap: 0.75rem; padding-top: 0.75rem; border-top: 1px solid var(--color-linen); flex-wrap: wrap; }
@media (min-width: 600px) { .ra-filters { flex-direction: row; } }
.ra-filter-group { flex: 1; min-width: 160px; display: flex; flex-direction: column; gap: 0.3rem; }
.ra-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--uni-muted); }
.ra-select, .ra-input { width: 100%; background: var(--color-white); border: 1.5px solid var(--color-linen); color: var(--uni-text); padding: 0.55rem 0.85rem; border-radius: 20px; font-size: 12px; font-family: inherit; outline: none; transition: border-color 0.2s; }
.ra-select { appearance: none; cursor: pointer; }
.ra-select:focus, .ra-input:focus { border-color: var(--color-mint-dark); }

/* ── Acciones ── */
.ra-actions { display: flex; justify-content: flex-end; padding-top: 0.25rem; }
.ra-spinner { width: 12px; height: 12px; border: 2px solid rgba(255,255,255,0.4); border-top-color: #fff; border-radius: 50%; animation: ra-spin 0.75s linear infinite; flex-shrink: 0; }
@keyframes ra-spin { to { transform: rotate(360deg); } }

/* ── Barra de exportación ── */
.ra-export-bar { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; background: #fafafa; border: 1px solid rgba(0,0,0,.06); border-radius: 12px; padding: 0.65rem 1rem; }
.ra-export-count { font-size: 11px; font-weight: 600; color: var(--uni-muted); }
.ra-export-btns { display: flex; gap: 0.5rem; }
.ra-btn-export { display: inline-flex; align-items: center; gap: 0.35rem; border-radius: 20px; padding: 0.45rem 0.9rem; font-size: 11px; font-weight: 700; cursor: pointer; font-family: inherit; border: 1px solid transparent; transition: background 0.2s; }
.ra-btn-export:disabled { opacity: 0.45; cursor: not-allowed; }
.ra-btn-export--pdf { background: var(--uni-error-bg); border-color: var(--uni-error-border); color: var(--uni-error-text); }
.ra-btn-export--pdf:hover:not(:disabled) { background: #f5e0e0; }
.ra-btn-export--excel { background: var(--uni-success-bg); border-color: var(--uni-success-border); color: var(--uni-success-text); }
.ra-btn-export--excel:hover:not(:disabled) { background: #d8ece6; }

/* ── Estado vacío ── */
.ra-empty { background: #fafafa; border: 1.5px dashed var(--color-linen); border-radius: 12px; padding: 2.5rem 2rem; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 0.4rem; color: var(--uni-muted); }
.ra-empty-title { margin: 0; font-size: 0.9rem; font-weight: 700; color: var(--color-dark-gray); }
.ra-empty-sub   { margin: 0; font-size: 11px; color: var(--uni-muted); }

/* ── Tabla genérica ── */
.ra-table-wrap { background: var(--color-white); border: 1px solid rgba(0,0,0,.06); border-radius: 12px; overflow: hidden; }
.ra-table-scroll { overflow-x: auto; }
.ra-table { width: 100%; border-collapse: collapse; font-size: 12px; white-space: nowrap; }
.ra-table thead tr { background: #fafafa; border-bottom: 1px solid var(--color-linen); }
.ra-table th { padding: 0.75rem 1rem; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: var(--uni-muted); }
.ra-table td { padding: 0.7rem 1rem; color: var(--uni-text); border-bottom: 1px solid rgba(0,0,0,.04); }
.ra-table tbody tr:hover { background: #f7f7f5; }
.ra-table tbody tr:last-child td { border-bottom: none; }
.ra-num  { text-align: right; font-variant-numeric: tabular-nums; }
.ra-mono { font-family: monospace; font-size: 11px; }
.ra-val  { max-width: 140px; overflow: hidden; text-overflow: ellipsis; }
.font-medium { font-weight: 600; }

/* ── Badges ── */
.ra-badge { display: inline-block; padding: 2px 9px; border-radius: 20px; font-size: 10px; font-weight: 700; }
.ra-badge--green  { background: #c6f6d5; color: #22543d; }
.ra-badge--yellow { background: #fefcbf; color: #744210; }
.ra-badge--red    { background: #fed7d7; color: #822727; }
.ra-badge--blue   { background: #bee3f8; color: #1a365d; }
.ra-badge--gray   { background: #e2e8f0; color: #4a5568; }

/* ── Kárdex header card ── */
.ra-kardex-card { display: flex; flex-wrap: wrap; gap: 1rem; background: #faf5ff; border: 1px solid #d6bcfa; border-radius: 12px; padding: 1rem 1.25rem; }
.ra-kardex-field { display: flex; flex-direction: column; gap: 0.15rem; min-width: 140px; }
.ra-kardex-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #805ad5; }
.ra-kardex-value { font-size: 12px; font-weight: 600; color: var(--uni-text); }

/* ── Ocupación — barra de progreso ── */
.ra-ocup-cell { display: flex; align-items: center; gap: 6px; min-width: 110px; }
.ra-bar-wrap  { width: 55px; height: 7px; background: #e2e8f0; border-radius: 4px; overflow: hidden; flex-shrink: 0; }
.ra-bar-fill  { height: 100%; border-radius: 4px; transition: width 0.3s; }
.ra-ocup-pct  { font-size: 11px; font-weight: 700; }
.ra-ocup-pct--full   { color: #c53030; }
.ra-ocup-pct--high   { color: #c05621; }
.ra-ocup-pct--medium { color: #2b6cb0; }
.ra-ocup-pct--low    { color: #276749; }
/* ── Tabla Carga Docente centrada ── */
.ra-table-carga th,
.ra-table-carga td {
  text-align: center;
  vertical-align: middle;
}

.ra-table-carga .ra-num,
.ra-table-carga .ra-mono,
.ra-table-carga .font-medium {
  text-align: center;
}
</style>