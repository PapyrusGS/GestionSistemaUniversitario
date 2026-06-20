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
        Schema::create('estudiante', function (Blueprint $table) {
            $table->id('idEstudiante');
            
            // Llave foránea conectada a la tabla 'usuarios'
            $table->foreignId('idUsuario')->constrained('usuarios', 'idUsuario')->onDelete('cascade');
            
            $table->string('nombrePadre', 255);
            $table->string('nombreMadre', 255)->nullable(); // nullable por si no tienen segundo nombre
            $table->string('apellidoPadre', 255);
            $table->string('apellidoMadre', 255)->nullable();
            $table->string('numeroPadre', 50)->nullable();
            $table->string('numeroMadre', 50)->nullable();
            
            // Campos de auditoría
            $table->dateTime('fechaA')->nullable();
            $table->string('UsuarioA', 255)->nullable();
            $table->boolean('estadoA')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estudiante');
    }
};
