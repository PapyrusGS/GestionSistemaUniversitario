<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Horario extends Model
{
    use Auditable;

    use HasFactory;

    protected $table = 'horarios';

    protected $primaryKey = 'idHorario';

    protected $fillable = [
        'diaSemana',
        'horaInicio',
        'horaFin',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'diaSemana' => 'integer',
            'estadoA' => 'boolean',
            'fechaA' => 'datetime',
        ];
    }

    public function cursos(): BelongsToMany
    {
        return $this->belongsToMany(Curso::class, 'horariocurso', 'idHorario', 'idCurso')
                    ->withPivot('idHorarioCurso', 'estadoA', 'fechaA', 'UsuarioA')
                    ->withTimestamps();
    }
}
