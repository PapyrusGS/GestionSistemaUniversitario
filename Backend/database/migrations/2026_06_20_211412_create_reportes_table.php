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
        Schema::create('reportes', function (Blueprint $table) {
            // idReporte int PRIMARY KEY AUTO_INCREMENT
            $table->integer('idReporte')->autoIncrement()->primary();
            
            $table->string('tipo', 255)->nullable();
            $table->string('filtros', 255)->nullable();
            
            // idUsuario int (Llave foránea opcional si deseas amarrarla a la tabla usuarios)
            // Si quieres activar la restricción de llave foránea real, descomenta la siguiente línea:
            $table->foreignid('idUsuario')->references('idUsuario')->on('usuarios')->onDelete('cascade');

            $table->dateTime('fechaGeneracion')->nullable();

            // Campos de auditoría
            $table->dateTime('fechaA')->nullable();
            $table->string('UsuarioA', 255)->nullable();
            $table->boolean('estadoA')->nullable(); // En Laravel, boolean mapea a un tipo equivalente a BIT/TINYINT(1)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
