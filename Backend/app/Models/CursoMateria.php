<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoMateria extends Model
{
    use HasFactory;

    protected $table = 'cursos_materias';

    protected $primaryKey = 'idCursoMateria';

    protected $fillable = [
        'idCurso',
        'idMateria',
        'idDocente',
        'idPeriodo',
        'fechaInicio',
        'fechaFin',
        'maxInscritos',
        'fechaRegistro',
        'estado',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'fechaInicio' => 'datetime',
            'fechaFin' => 'datetime',
            'fechaRegistro' => 'datetime',
            'fechaA' => 'datetime',
            'estado' => 'boolean',
            'estadoA' => 'boolean',
            'maxInscritos' => 'integer',
        ];
    }
}
