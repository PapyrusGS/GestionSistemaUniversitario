<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstudianteCarrera extends Model
{
    protected $table = 'estudiante_carrera';

    protected $primaryKey = 'idEstudianteCarrera';

    protected $fillable = [
        'idEstudiante',
        'idCarrera',
        'fechaRegistro',
        'estado',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'estado'        => 'boolean',
            'estadoA'       => 'boolean',
            'fechaRegistro' => 'datetime',
            'fechaA'        => 'datetime',
        ];
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'idCarrera', 'idCarrera');
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'idEstudiante', 'idEstudiante');
    }
}
