<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditorias';

    protected $primaryKey = 'idAuditoria';

    protected $fillable = [
        'tabla_nombre',
        'registro_id',
        'accion',
        'campo',
        'valor_anterior',
        'valor_nuevo',
        'usuario_a',
        'fecha_a',
        'direccion_ip',
    ];

    protected function casts(): array
    {
        return [
            'fecha_a' => 'datetime',
        ];
    }

    public function usuarioResponsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_a', 'idUsuario');
    }
}
