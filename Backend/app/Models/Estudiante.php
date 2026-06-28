<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estudiante extends Model
{
    protected $table = 'estudiante';

    protected $primaryKey = 'idEstudiante';

    protected $fillable = [
        'idUsuario',
        'nombrePadre',
        'nombreMadre',
        'apellidoPadre',
        'apellidoMadre',
        'numeroPadre',
        'numeroMadre',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'estadoA' => 'boolean',
            'fechaA'  => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idUsuario', 'idUsuario');
    }

    public function carreras(): HasMany
    {
        return $this->hasMany(EstudianteCarrera::class, 'idEstudiante', 'idEstudiante');
    }

    public function carreraActiva(): HasOne
    {
        return $this->hasOne(EstudianteCarrera::class, 'idEstudiante', 'idEstudiante')
            ->where('estado', 1)
            ->with('carrera');
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'idEstudiante', 'idEstudiante');
    }
}
