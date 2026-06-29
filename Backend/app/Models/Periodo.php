<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Periodo extends Model
{
    use Auditable;

    protected $table = 'periodos';

    protected $primaryKey = 'idPeriodo';

    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'fechaInicioSemestre',
        'fechaFinSemestre',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado'              => 'boolean',
            'fechaInicioSemestre' => 'date',
            'fechaFinSemestre'    => 'date',
        ];
    }

    public function cursosMaterias(): HasMany
    {
        return $this->hasMany(CursoMateria::class, 'idPeriodo', 'idPeriodo');
    }
}
