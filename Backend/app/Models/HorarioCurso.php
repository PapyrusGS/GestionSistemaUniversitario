<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class HorarioCurso extends Pivot
{
    protected $table = 'horariocurso';

    protected $primaryKey = 'idHorarioCurso';
    public $incrementing = true;

    protected $fillable = [
        'idHorario',
        'idCurso',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'estadoA' => 'boolean',
            'fechaA' => 'datetime',
        ];
    }
}
