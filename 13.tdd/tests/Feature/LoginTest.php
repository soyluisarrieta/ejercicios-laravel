<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Un usuario existente puede iniciar sesión.
     */
    public function test_an_existing_user_can_login(): void
    {
        // $this->withoutExceptionHandling();

        # Teniendo
        $credentials = ['email' => 'luis@arrieta.com', 'password' => 'password'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);
        // $response->dump();

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    /**
     * Un usuario no existente no puede iniciar sesión.
     */
    public function test_a_non_existing_user_cannot_login(): void
    {
        # Teniendo
        $credentials = ['email' => 'luis_no@arrieta.com', 'password' => 'password'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        # Esperando
        $response->assertStatus(401);
        $response->assertJsonFragment(['status' => 401, 'message' => 'Unauthorized']);
    }

    /**
     * El correo electrónico es requerido
     */
    public function test_email_must_be_required(): void
    {
        # Teniendo
        $credentials = ['password' => 'password'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field is required.']]]);
    }

    /**
     * El correo electrónico es inválido
     */
    public function test_email_must_be_valid_email(): void
    {
        # Teniendo
        $credentials = ['email' => 'luisarrieta.com', 'password' => 'password'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email field must be a valid email address.']]]);
    }

    /**
     * La contraseña es requerida
     */
    public function test_password_must_be_required(): void
    {
        # Teniendo
        $credentials = ['email' => 'luis@arrieta.com'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
        $response->assertJsonFragment(['errors' => ['password' => ['The password field is required.']]]);
    }

    /**
     * La contraseña es requerida
     */
    public function test_password_must_have_at_lease_8_characters(): void
    {
        # Teniendo
        $credentials = ['email' => 'luis@arrieta.com', 'password' => 'pass'];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
        $response->assertJsonFragment(['errors' => ['password' => ['The password field must be at least 8 characters.']]]);
    }
}
