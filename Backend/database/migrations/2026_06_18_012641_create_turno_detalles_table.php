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
        Schema::create('turno_detalles', function (Blueprint $table) {
            $table->id('idTurnoDetalle');
            
            // Llave foránea conectada a la tabla turnos
            $table->foreignId('idTurno')->constrained('turnos', 'idTurno')->onDelete('cascade');
            
            $table->integer('diaSemana'); // Ej: 1 para Lunes, 2 para Martes...
            $table->time('horaInicio');
            $table->time('horaFin');
            
            // Campos de auditoría
            $table->dateTime('fechaA')->nullable();
            $table->string('UsuarioA', 255)->nullable();
            $table->boolean('estadoA')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turno_detalles');
    }
};
