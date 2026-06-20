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
            $table->string('idMateria', 100)->primary();    
        
            
            // Llaves foráneas a Carrera y Pensum
            $table->foreignId('idCarrera')->constrained('carreras', 'idCarrera')->onDelete('cascade');
            
            $table->string('idMateriaPrevia', 100)->nullable();
            $table->foreign('idMateriaPrevia')->nullable()->references('idMateria')->on('materias')->onDelete('cascade');  
            
            $table->string('nombre', 255);
            $table->string('semestre', 10);
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
