<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class CursoMateria extends Model
{
    use Auditable;

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

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'idCursoMateria', 'idCursoMateria');
    }

    public function periodo(): BelongsTo
    {
        return $this->belongsTo(Periodo::class, 'idPeriodo', 'idPeriodo');
    }

    /**
     * Usado por withAvg para calcular el promedio de notas sin N+1.
     */
    public function notasDeInscritos(): HasManyThrough
    {
        return $this->hasManyThrough(
            Nota::class,
            Inscripcion::class,
            'idCursoMateria', // FK en estudiantemateria
            'idInscripcion',  // FK en notas
            'idCursoMateria', // PK local
            'idInscripcion'   // PK en estudiantemateria
        );
    }
}
