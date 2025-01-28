<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdateUserDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Actualizar usuario autenticado
     */
    public function test_an_authenticated_user_can_modify_their_data(): void
    {
        # Teniendo
        $data = [
            'name' => 'Luis Updated',
            'last_name' => 'Arrieta Updated',
            'email' => 'luis@arrieta.com',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment(['message' => 'OK', 'status' => 200]);
        $response->assertJsonFragment([
            'email' => $data['email'],
            'name' => $data['name'],
            'last_name' => $data['last_name'],
        ]);

        $this->assertDatabaseMissing('users', [
            'email' => $data['email'],
            'name' => 'Luis',
            'last_name' => 'Arrieta',
        ]);
    }

    /**
     * El nombre es requerido
     */
    public function test_name_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => '',
            'last_name' => 'Arrieta',
            'email' => 'luis@arrieta.com',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * El nombre debe contener almenos 2 caracteres
     */
    public function test_name_must_have_at_lease_2_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'L',
            'last_name' => 'Arrieta',
            'email' => 'luis@arrieta.com',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * El apellido es requerido
     */
    public function test_lastname_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => 'Luis',
            'email' => 'luis@arrieta.com',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

    /**
     * El apellido debe contener almenos 2 caracteres
     */
    public function test_lastname_must_have_at_lease_2_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'Luis',
            'last_name' => 'A',
            'email' => 'luis@arrieta.com',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['last_name']]);
    }

    /**
     * El correo electrónico es requerido
     */
    public function test_email_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => 'Luis',
            'email' => '',
            'last_name' => 'Arrieta',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    /**
     * El correo electrónico es inválido
     */
    public function test_email_must_be_valid_email(): void
    {
        # Teniendo
        $data = [
            'name' => 'Luis',
            'email' => 'luisarrieta.com',
            'last_name' => 'Arrieta',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    /**
     * El correo electrónico ya existe
     */
    public function test_email_must_be_unique(): void
    {
        # Teniendo
        User::factory()->create(['email' => 'emailexisting@example.com']);

        $data = [
            'name' => 'Luis',
            'email' => 'emailexisting@example.com',
            'last_name' => 'Arrieta',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    /**
     * No puede actualizar la contraseña 
     */
    public function test_an_authenticated_user_cannot_modify_their_password(): void
    {
        # Teniendo
        $data = [
            'name' => 'Luis Updated',
            'last_name' => 'Arrieta Updated',
            'email' => 'luis@arrieta.com',
            'password' => 'newpassword',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/profile", $data);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment(['message' => 'OK', 'status' => 200]);

        $user = User::find(1);
        $this->assertFalse(Hash::check($data['password'], $user->password));
    }
}
