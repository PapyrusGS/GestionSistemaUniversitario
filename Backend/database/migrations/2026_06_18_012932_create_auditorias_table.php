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
        Schema::create('auditorias', function (Blueprint $table) {
            $table->id('idAuditoria');
            $table->string('tabla_nombre', 100);
            $table->integer('registro_id');
            $table->char('accion', 1); // 'I', 'U', 'D'
            $table->string('campo', 100)->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            
            // ID del usuario que generó el cambio (relacionado a la tabla usuarios)
            $table->foreignId('usuario_a')->nullable()->constrained('usuarios', 'idUsuario')->onDelete('set null');
            
            $table->dateTime('fecha_a')->useCurrent();
            $table->string('direccion_ip', 45)->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditorias');
    }
};
