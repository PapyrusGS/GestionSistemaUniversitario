<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    use Auditable;

    use HasFactory;

    protected $table = 'carreras';

    protected $primaryKey = 'idCarrera';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
        'fechaRegistro',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    protected function casts(): array
    {
        return [
            'estado'      => 'boolean',
            'estadoA'     => 'boolean',
            'fechaRegistro' => 'datetime',
            'fechaA'      => 'datetime',
        ];
    }
}
