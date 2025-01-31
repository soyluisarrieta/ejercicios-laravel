<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Actualizar contraseña de usuario autenticado
     */
    public function test_an_authenticated_user_can_update_their_password(): void
    {
        # Teniendo
        $data = [
            'old_password' => 'password',
            'password' => 'NewPassword',
            'password_confirmation' => 'NewPassword',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/password", $data);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);

        $this->assertTrue(Hash::check($data['password'], User::find(1)->password));
    }

    /**
     * La contraseña es requerida
     */
    public function test_old_password_must_be_required(): void
    {
        # Teniendo
        $data = [
            'old_password' => '',
            'password' => 'NewPassword',
            'password_confirmation' => 'NewPassword',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/password", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['old_password']]);
    }

    /**
     * La contraseña anterior es requerida
     */
    public function test_password_must_be_required(): void
    {
        # Teniendo
        $data = [
            'old_password' => 'password',
            'password' => '',
            'password_confirmation' => 'NewPassword',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/password", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    /**
     * La confirmación de la contraseña es requerida
     */
    public function test_password_must_be_confirmed(): void
    {
        # Teniendo
        $data = [
            'old_password' => 'password',
            'password' => 'NewPassword',
            'password_confirmation' => '',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/password", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['password']]);
    }

    /**
     * La contraseña anterior debe coincidir
     */
    public function test_old_password_must_be_match(): void
    {
        # Teniendo
        $data = [
            'old_password' => 'notpassword',
            'password' => 'NewPassword',
            'password_confirmation' => 'NewPassword',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/password", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['old_password']]);
    }
}
