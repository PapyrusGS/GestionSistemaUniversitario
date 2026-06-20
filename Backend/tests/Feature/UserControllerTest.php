<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    private Rol $adminRol;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard roles for testing
        $this->adminRol = Rol::query()->create([
            'nombre' => 'Administrador',
            'descripcion' => 'Administrador de sistema',
            'estado' => true,
        ]);

        $this->adminUser = User::query()->create([
            'idRol' => $this->adminRol->idRol,
            'nombre1' => 'Admin',
            'nombre2' => 'System',
            'apellido1' => 'User',
            'apellido2' => 'Root',
            'ci' => '0000001',
            'correo' => 'admin@example.com',
            'password' => Hash::make('AdminSec#123'),
            'fechaRegistro' => now(),
            'estado' => true,
        ]);
    }

    public function test_unauthenticated_user_cannot_access_index(): void
    {
        $response = $this->getJson('/api/users');
        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_access_store(): void
    {
        $response = $this->postJson('/api/users', []);
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_users(): void
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    '*' => [
                        'idUsuario',
                        'idRol',
                        'rol',
                        'nombreCompleto',
                        'nombre1',
                        'nombre2',
                        'apellido1',
                        'apellido2',
                        'ci',
                        'correo',
                        'estado',
                        'fechaRegistro',
                    ]
                ]
            ]);

        $this->assertCount(1, $response->json('users'));
    }

    public function test_authenticated_user_can_create_user_with_valid_data(): void
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'idRol' => $this->adminRol->idRol,
            'nombre1' => 'Pedro',
            'nombre2' => 'Luis',
            'apellido1' => 'Perez',
            'apellido2' => 'Sosa',
            'ci' => '9876543',
            'correo' => 'pedro.perez@example.com',
            'password' => 'SecurePass123!',
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'idUsuario',
                    'idRol',
                    'rol',
                    'nombreCompleto',
                    'nombre1',
                    'nombre2',
                    'apellido1',
                    'apellido2',
                    'ci',
                    'correo',
                    'estado',
                    'fechaRegistro',
                ]
            ])
            ->assertJsonPath('user.correo', 'pedro.perez@example.com')
            ->assertJsonPath('user.rol', 'Administrador');

        $this->assertDatabaseHas('usuarios', [
            'correo' => 'pedro.perez@example.com',
            'ci' => '9876543',
            'idRol' => $this->adminRol->idRol,
        ]);

        // Assert password has been hashed/encrypted correctly
        $newUser = User::query()->where('correo', 'pedro.perez@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertTrue(Hash::check('SecurePass123!', $newUser->password));
    }

    public function test_create_user_validation_fails_with_weak_password(): void
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'idRol' => $this->adminRol->idRol,
            'nombre1' => 'Pedro',
            'apellido1' => 'Perez',
            'ci' => '9876543',
            'correo' => 'pedro.perez@example.com',
            'password' => '123', // clearly too weak
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_create_user_validation_fails_with_duplicate_correo(): void
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'idRol' => $this->adminRol->idRol,
            'nombre1' => 'Pedro',
            'apellido1' => 'Perez',
            'ci' => '9876543',
            'correo' => 'admin@example.com', // Duplicate of adminUser's email
            'password' => 'SecurePass123!',
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['correo']);
    }

    public function test_create_user_validation_fails_with_duplicate_ci(): void
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'idRol' => $this->adminRol->idRol,
            'nombre1' => 'Pedro',
            'apellido1' => 'Perez',
            'ci' => '0000001', // Duplicate of adminUser's CI
            'correo' => 'pedro.perez@example.com',
            'password' => 'SecurePass123!',
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['ci']);
    }

    public function test_create_user_validation_fails_with_non_existent_idRol(): void
    {
        Sanctum::actingAs($this->adminUser);

        $data = [
            'idRol' => 9999, // Non-existent
            'nombre1' => 'Pedro',
            'apellido1' => 'Perez',
            'ci' => '9876543',
            'correo' => 'pedro.perez@example.com',
            'password' => 'SecurePass123!',
        ];

        $response = $this->postJson('/api/users', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['idRol']);
    }

    public function test_unauthenticated_user_cannot_access_roles(): void
    {
        $response = $this->getJson('/api/roles');
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_list_roles(): void
    {
        Sanctum::actingAs($this->adminUser);

        $response = $this->getJson('/api/roles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'roles' => [
                    '*' => [
                        'idRol',
                        'nombre',
                        'descripcion',
                        'estado',
                    ]
                ]
            ]);

        $this->assertCount(1, $response->json('roles'));
    }
}
