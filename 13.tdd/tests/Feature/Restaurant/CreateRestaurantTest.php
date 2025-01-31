<?php

namespace Tests\Feature;

use App\Models\Restaurant;
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
    public function test_an_user_can_create_a_restaurant(): void
    {
        # Teniendo
        $data = [
            'name' => 'New restaurant',
            'description' => 'New restaurant description',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => ['restaurant' => ['id', 'name', 'slug', 'description']],
            'errors',
            'status'
        ]);

        $this->assertDatabaseCount("restaurants", 1);
        $restaurant = Restaurant::first();
        $this->assertStringContainsString('new-restaurant', $restaurant->slug);
        $this->assertDatabaseHas('restaurants', [
            'id' => 1,
            'user_id' => 1,
            ...$data
        ]);
    }
}
