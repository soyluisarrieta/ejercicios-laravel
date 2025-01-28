<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Registrar un nuevo usuario
     */
    public function test_a_user_can_register(): void
    {
        # Teniendo
        $data = [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment(['message' => 'OK', 'status' => 200]);
        $response->assertJsonFragment([
            'data' => [
                'user' => [
                    'id' => $response->json('data.user.id'),
                    'email' => $data['email'],
                    'name' => $data['name'],
                    'last_name' => $data['last_name'],
                ]
            ]
        ]);

        $this->assertDatabaseCount("users", 1);
        $this->assertDatabaseHas('users', ['email' => $data['email']]);
    }

    /**
     * El nombre es requerido
     */
    public function test_name_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => '',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

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
            'name' => 'J',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

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
            'name' => 'John',
            'email' => 'johndoe@example.com',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

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
            'name' => 'John',
            'last_name' => 'D',
            'email' => 'johndoe@example.com',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

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
            'name' => 'John',
            'email' => '',
            'last_name' => 'Doe',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

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
            'name' => 'John',
            'email' => 'johndoeexample.com',
            'last_name' => 'Doe',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

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
            'name' => 'John',
            'email' => 'emailexisting@example.com',
            'last_name' => 'Doe',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
    }

    /**
     * La contraseña es requerida
     */
    public function test_password_must_be_required(): void
    {
        // $this->withoutExceptionHandling();

        # Teniendo
        $data = [
            'name' => 'John',
            'email' => 'johndoe@example.com',
            'last_name' => 'Doe',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    /**
     * La contraseña debe contener almenos 8 caracteres
     */
    public function test_password_must_have_at_lease_8_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@example.com',
            'password' => 'pass',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/register", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }
}
