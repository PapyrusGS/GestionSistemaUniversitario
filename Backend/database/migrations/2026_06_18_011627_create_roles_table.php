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
        Schema::create('roles', function (Blueprint $table) {
            // En Laravel, 'id' es el estándar para la llave primaria auto-incremental
            $table->id('idRol'); 
            $table->string('nombre', 255);
            $table->string('descripcion', 255)->nullable();
            $table->boolean('estado')->default(true); // 'bit' se maneja como boolean en Laravel
            
            // Campos de auditoría (pueden ser nulables si no se llenan de inmediato)
            $table->dateTime('fechaA')->nullable();
            $table->string('UsuarioA', 255)->nullable();
            $table->boolean('estadoA')->nullable();
            
            // Esto añadirá automáticamente 'created_at' y 'updated_at' 
            // que reemplazan o complementan tu 'fechaRegistro'
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
