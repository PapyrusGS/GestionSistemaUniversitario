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
        Schema::create('horariocurso', function (Blueprint $table) {
            $table->id('idHorarioCurso');
            
            
            $table->foreignId('idHorario')->constrained('horarios', 'idHorario')->onDelete('cascade');
            $table->foreignId('idCurso')->constrained('cursos', 'idCurso')->onDelete('cascade');
            
            // Campos de auditoría
            $table->dateTime('fechaA')->nullable();
            $table->string('UsuarioA', 255)->nullable();
            $table->boolean('estadoA')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horario');
    }
};
