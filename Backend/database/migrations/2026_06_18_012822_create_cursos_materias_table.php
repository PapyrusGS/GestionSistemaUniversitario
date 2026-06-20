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
        Schema::create('cursos_materias', function (Blueprint $table) {
            $table->id('idCursoMateria');
            
            // Llaves foráneas conectadas a sus respectivas tablas
            $table->string('idCurso', 100);
            $table->foreign('idCurso')->references('idCurso')->on('cursos')->onDelete('cascade');
           
            $table->string('idMateria', 100);
            $table->foreign('idMateria')->references('idMateria')->on('materias')->onDelete('cascade');

            
            // Asumiendo que el docente está registrado en la tabla 'usuarios'
            $table->foreignId('idDocente')->constrained('usuarios', 'idUsuario')->onDelete('cascade');

            $table->foreignId('idPeriodo')->constrained('periodos', 'idPeriodo')->onDelete('cascade');


            $table->dateTime('fechaInicio');
            $table->dateTime('fechaFin');
            $table->integer('maxInscritos');
            $table->dateTime('fechaRegistro')->useCurrent();
            $table->boolean('estado')->default(true);
            
            // Campos de auditoría
            $table->dateTime('fechaA')->nullable();
            $table->string('UsuarioA', 255)->nullable();
            $table->boolean('estadoA')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos_materias');
    }
};
