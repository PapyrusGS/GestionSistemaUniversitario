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
        Schema::create('estudiante_carrera', function (Blueprint $table) {
            $table->id('idEstudianteCarrera');
            
            // Llaves foráneas (Asumiendo que los estudiantes están en la tabla 'usuarios')
            $table->foreignId('idEstudiante')->constrained('estudiante', 'idEstudiante')->onDelete('cascade');
            $table->foreignId('idCarrera')->constrained('carreras', 'idCarrera')->onDelete('cascade');
            
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
        Schema::dropIfExists('estudiante_carrera');
    }
};
