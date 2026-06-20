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
        Schema::create('materias', function (Blueprint $table) {
            $table->id('idMateria');
            
            // Llaves foráneas a Carrera y Pensum
            $table->foreignId('idCarrera')->constrained('carreras', 'idCarrera')->onDelete('cascade');
            
            // Relación recursiva (Prerrequisito). Nullable por si no tiene materia previa.
            $table->foreignId('idMateriaPrevia')->nullable()->constrained('materias', 'idMateria')->onDelete('set null');
            
            $table->string('nombre', 255);
            $table->string('semestre')->unique();
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
        Schema::dropIfExists('materias');
    }
};
