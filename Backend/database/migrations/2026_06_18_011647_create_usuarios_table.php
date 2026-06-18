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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('idUsuario');
            
            // Llave foránea conectada a la tabla 'roles'
            $table->foreignId('idRol')->constrained('roles', 'idRol')->onDelete('cascade');
            
            $table->string('nombre1', 255);
            $table->string('nombre2', 255)->nullable(); // nullable por si no tienen segundo nombre
            $table->string('apellido1', 255);
            $table->string('apellido2', 255)->nullable();
            $table->string('ci', 255)->unique(); // El CI suele ser único
            $table->string('correo', 255)->unique();
            $table->string('password', 255);
            $table->dateTime('fechaRegistro')->useCurrent(); // Toma la fecha actual por defecto
            $table->boolean('estado')->default(true);
            
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
        Schema::dropIfExists('usuarios');
    }
};
