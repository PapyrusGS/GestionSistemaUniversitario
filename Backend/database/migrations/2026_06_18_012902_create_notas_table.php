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
        Schema::create('notas', function (Blueprint $table) {
            $table->id('idNota');
            
            // Llave foránea conectada a inscripciones (corrigiendo el typo 'idIncripcion' a 'idInscripcion')
            $table->foreignId('idInscripcion')->constrained('estudiantematerias', 'idInscripcion')->onDelete('cascade');
            
            $table->decimal('nota', 8, 2); 
            $table->dateTime('fechaRegistro')->useCurrent();
            $table->dateTime('fechaActualizacion')->nullable()->useCurrentOnUpdate();
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
        Schema::dropIfExists('notas');
    }
};
