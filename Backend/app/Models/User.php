<?php

namespace App\Models;

use App\Traits\Auditable;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Auditable;

    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';

    protected $primaryKey = 'idUsuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'idRol',
        'nombre1',
        'nombre2',
        'apellido1',
        'apellido2',
        'ci',
        'correo',
        'telefono',
        'password',
        'fechaRegistro',
        'estado',
        'fechaA',
        'UsuarioA',
        'estadoA',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fechaRegistro' => 'datetime',
            'fechaA' => 'datetime',
            'estado' => 'boolean',
            'estadoA' => 'boolean',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'idRol', 'idRol');
    }

    public function cursosMaterias(): HasMany
    {
        return $this->hasMany(CursoMateria::class, 'idDocente', 'idUsuario');
    }

    public function getNombreCompletoAttribute(): string
    {
        return collect([
            $this->nombre1,
            $this->nombre2,
            $this->apellido1,
            $this->apellido2,
        ])
            ->filter()
            ->implode(' ');
    }
}
