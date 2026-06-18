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
        Schema::create('pensums', function (Blueprint $table) {
            $table->id('idPensum');
            
            // Llave foránea que conecta con la tabla carreras
            $table->foreignId('idCarrera')->constrained('carreras', 'idCarrera')->onDelete('cascade');
            
            $table->string('nombre', 255);
            $table->integer('numMaterias');
            $table->integer('numSemestres');
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
        Schema::dropIfExists('pensums');
    }
};
