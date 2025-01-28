<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}
