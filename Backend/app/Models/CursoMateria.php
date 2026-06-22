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
        ];
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'idCurso', 'idCurso');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'idMateria', 'idMateria');
    }

    public function docente()
    {
        return $this->belongsTo(User::class, 'idDocente', 'idUsuario');
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class, 'idCursoMateria', 'idCursoMateria');
    }
}
