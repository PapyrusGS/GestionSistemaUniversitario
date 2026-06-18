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
        Schema::create('inscripciones', function (Blueprint $table) {
            $table->id('idInscripcion');
            
            // Llaves foráneas (idCursoMateria asumirá una tabla externa 'cursos_materias' u otra similar cuando la crees)
            $table->foreignId('idEstudiante')->constrained('usuarios', 'idUsuario')->onDelete('cascade');
            $table->unsignedBigInteger('idCursoMateria'); // Si ya tienes la tabla curso_materia, puedes agregar el ->constrained()
            
            $table->dateTime('fecha')->useCurrent();
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
        Schema::dropIfExists('inscripciones');
    }
};
