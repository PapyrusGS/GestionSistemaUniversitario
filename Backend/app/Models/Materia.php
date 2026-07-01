<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use Auditable;

    use HasFactory;

    protected $table = 'materias';

    protected $primaryKey = 'idMateria';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'idMateria',
        'idCarrera',
        'idMateriaPrevia',
        'nombre',
        'semestre',
        'fechaRegistro',
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
            'fechaRegistro' => 'datetime',
            'fechaA' => 'datetime',
            'semestre' => 'string',
        ];
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class, 'idCarrera', 'idCarrera');
    }

    public function prerrequisito(): BelongsTo
    {
        return $this->belongsTo(self::class, 'idMateriaPrevia', 'idMateria');
    }

    public function dependientes(): HasMany
    {
        return $this->hasMany(self::class, 'idMateriaPrevia', 'idMateria');
    }
}
