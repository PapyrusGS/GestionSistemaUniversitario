<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    // The primary key is 'idCurso' and it's a string
    protected $primaryKey = 'idCurso';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'idCurso',
        'capacidad',
        'estado',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
            'estadoA' => 'boolean',
            'fechaA' => 'datetime',
        ];
    }

    public function cursosMaterias(): HasMany
    {
        return $this->hasMany(CursoMateria::class, 'idCurso', 'idCurso');
    }

    public function horarios(): BelongsToMany
    {
        return $this->belongsToMany(Horario::class, 'horariocurso', 'idCurso', 'idHorario')
                    ->withPivot('idHorarioCurso', 'estadoA', 'fechaA', 'UsuarioA')
                    ->withTimestamps();
    }
}
