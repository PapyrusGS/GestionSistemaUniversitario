<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscripcion extends Model
{
    use Auditable;

    use HasFactory;

    protected $table = 'estudiantemateria';

    protected $primaryKey = 'idInscripcion';

    protected $fillable = [
        'idEstudiante',
        'idCursoMateria',
        'fecha',
        'estado',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'datetime',
            'estado' => 'boolean',
            'estadoA' => 'boolean',
            'fechaA' => 'datetime',
        ];
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'idEstudiante', 'idUsuario');
    }

    public function cursoMateria(): BelongsTo
    {
        return $this->belongsTo(CursoMateria::class, 'idCursoMateria', 'idCursoMateria');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'idInscripcion', 'idInscripcion');
    }

    public function notaMasReciente()
    {
        return $this->hasOne(Nota::class, 'idInscripcion', 'idInscripcion')->latestOfMany('idNota');
    }
}
