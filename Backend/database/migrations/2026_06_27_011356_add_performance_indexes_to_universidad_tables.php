<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Optimización para el Historial del Estudiante
        Schema::table('estudiante_materia', function (Blueprint $table) {
            $table->index(['idEstudiante', 'estado'], 'idx_historial_estudiante');
        });

        // 2. Optimización para extraer las Notas rápidas
        Schema::table('notas', function (Blueprint $table) {
            $table->index(['idInscripcion', 'estado'], 'idx_nota_rapida');
        });

        // 3. Optimización para la Malla Curricular (Regla de Semestres)
        Schema::table('materias', function (Blueprint $table) {
            $table->index(['idCarrera', 'semestre'], 'idx_malla_carrera');
        });

        // 4. Optimización para Cursos Disponibles
        Schema::table('curso_materia', function (Blueprint $table) {
            $table->index(['idPeriodo', 'idMateria'], 'idx_cursos_activos');
        });

        // 5. Optimización para Validar prerrequisitos
        Schema::table('estudiante_materia', function (Blueprint $table) {
            $table->index(['idEstudiante', 'idCursoMateria'], 'idx_validacion_prerequisito');
        });
    }

    public function down(): void
    {
        // El método down es vital para poder revertir (rollback) si hay algún error
        Schema::table('estudiante_materia', function (Blueprint $table) {
            $table->dropIndex('idx_historial_estudiante');
            $table->dropIndex('idx_validacion_prerequisito');
        });

        Schema::table('notas', function (Blueprint $table) {
            $table->dropIndex('idx_nota_rapida');
        });

        Schema::table('materias', function (Blueprint $table) {
            $table->dropIndex('idx_malla_carrera');
        });

        Schema::table('curso_materia', function (Blueprint $table) {
            $table->dropIndex('idx_cursos_activos');
        });
    }
};
