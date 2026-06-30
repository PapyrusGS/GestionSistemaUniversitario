<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    use Auditable;

    use HasFactory;

    protected $table = 'notas';

    protected $primaryKey = 'idNota';

    protected $fillable = [
        'idInscripcion',
        'nota',
        'fechaRegistro',
        'fechaActualizacion',
        'estado',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'nota' => 'decimal:2',
            'fechaRegistro' => 'datetime',
            'fechaActualizacion' => 'datetime',
            'estado' => 'boolean',
            'fechaA' => 'datetime',
            'estadoA' => 'boolean',
        ];
    }

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class, 'idInscripcion', 'idInscripcion');
    }
}
