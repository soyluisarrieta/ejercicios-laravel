<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Crear una restaurante
     */
    public function test_create_a_restaurant(): void
    {
        # Teniendo
        $data = [
            'name' => 'New restaurant',
            'description' => 'New restaurant description',
        ];
        $slug = strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name']));

        # Haciendo
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $response->assertJsonFragment(['message' => 'OK', 'status' => 200]);
        $response->assertJsonFragment([
            'data' => [
                'restaurant' => [
                    'id' => 1,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'slug' => $slug,
                ]
            ]
        ]);

        $this->assertDatabaseCount("restaurant", 1);
        $this->assertDatabaseHas('restaurant', [
            'id' => 1,
            'user_id' => 1,
            'slug' => $slug,
            ...$data
        ]);
    }
}
