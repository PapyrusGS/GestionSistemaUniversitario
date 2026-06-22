<?php

namespace Tests\Feature;

use App\Models\Carrera;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CarreraControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $rol = Rol::query()->create([
            'nombre'      => 'Administrador',
            'descripcion' => 'Administrador de sistema',
            'estado'      => true,
        ]);

        $this->adminUser = User::query()->create([
            'idRol'         => $rol->idRol,
            'nombre1'       => 'Admin',
            'apellido1'     => 'Test',
            'ci'            => '0000099',
            'correo'        => 'admin.carrera@example.com',
            'password'      => Hash::make('AdminSec#123'),
            'fechaRegistro' => now(),
            'estado'        => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_carreras(): void
    {
        $this->getJson('/api/carreras')->assertStatus(401);
    }

    public function test_authenticated_user_can_list_carreras(): void
    {
        Sanctum::actingAs($this->adminUser);

        Carrera::query()->create(['nombre' => 'Ingeniería en Sistemas', 'estado' => true, 'fechaRegistro' => now()]);
        Carrera::query()->create(['nombre' => 'Contaduría Pública', 'estado' => false, 'fechaRegistro' => now()]);

        $response = $this->getJson('/api/carreras');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'carreras' => [
                    '*' => ['idCarrera', 'nombre', 'descripcion', 'estado', 'fechaRegistro'],
                ]
            ]);

        $this->assertCount(1, $response->json('carreras'));
    }

    public function test_authenticated_user_can_create_carrera(): void
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->postJson('/api/carreras', [
            'nombre'      => 'Ingeniería Civil',
            'descripcion' => 'Carrera de ingeniería civil',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('carrera.nombre', 'Ingeniería Civil')
            ->assertJsonPath('carrera.estado', true);

        $this->assertDatabaseHas('carreras', ['nombre' => 'Ingeniería Civil', 'estado' => 1]);
    }

    public function test_create_carrera_fails_with_duplicate_nombre(): void
    {
        Sanctum::actingAs($this->adminUser);

        Carrera::query()->create(['nombre' => 'Medicina', 'estado' => true, 'fechaRegistro' => now()]);

        $response = $this->postJson('/api/carreras', ['nombre' => 'Medicina']);

        $response->assertStatus(422)->assertJsonValidationErrors(['nombre']);
    }

    public function test_authenticated_user_can_update_carrera(): void
    {
        Sanctum::actingAs($this->adminUser);

        $carrera = Carrera::query()->create([
            'nombre'      => 'Derecho',
            'descripcion' => 'Carrera de derecho',
            'estado'      => true,
            'fechaRegistro' => now(),
        ]);

        $response = $this->putJson("/api/carreras/{$carrera->idCarrera}", [
            'nombre'      => 'Derecho Actualizado',
            'descripcion' => 'Nueva descripción',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('carrera.nombre', 'Derecho Actualizado');

        $this->assertDatabaseHas('carreras', ['nombre' => 'Derecho Actualizado']);
    }

    public function test_update_carrera_same_nombre_does_not_fail(): void
    {
        Sanctum::actingAs($this->adminUser);

        $carrera = Carrera::query()->create([
            'nombre'      => 'Economía',
            'estado'      => true,
            'fechaRegistro' => now(),
        ]);

        $response = $this->putJson("/api/carreras/{$carrera->idCarrera}", [
            'nombre' => 'Economía', // same name, should pass unique check
        ]);

        $response->assertStatus(200);
    }

    public function test_destroy_performs_logical_delete(): void
    {
        Sanctum::actingAs($this->adminUser);

        $carrera = Carrera::query()->create([
            'nombre'      => 'Psicología',
            'estado'      => true,
            'fechaRegistro' => now(),
        ]);

        $response = $this->deleteJson("/api/carreras/{$carrera->idCarrera}");

        $response->assertStatus(200)
            ->assertJsonPath('carrera.estado', false);

        // Must NOT be physically deleted — still exists in the database
        $this->assertDatabaseHas('carreras', ['idCarrera' => $carrera->idCarrera, 'estado' => 0]);
        $this->assertDatabaseCount('carreras', 1);
    }

    public function test_destroy_already_disabled_carrera_returns_422(): void
    {
        Sanctum::actingAs($this->adminUser);

        $carrera = Carrera::query()->create([
            'nombre'      => 'Filosofía',
            'estado'      => false,
            'fechaRegistro' => now(),
        ]);

        $response = $this->deleteJson("/api/carreras/{$carrera->idCarrera}");

        $response->assertStatus(422);
    }
}
