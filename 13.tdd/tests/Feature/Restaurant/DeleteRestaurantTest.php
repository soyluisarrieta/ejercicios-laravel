<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1
        ]);
    }

    /**
     * Un usuario autenticado puede eliminar sus restaurantes
     */
    public function test_an_authenticated_user_must_delete_their_restaurants(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'DELETE', "{$this->apiBase}/restaurants/{$this->restaurant->id}");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['message', 'data', 'errors', 'status']);
        $this->assertDatabaseCount('restaurants', 0);
    }

    /**
     * Un usuario no autenticado no puede eliminar restaurantes
     */
    public function test_an_unauthenticated_user_cannot_see_restaurants(): void
    {
        # Haciendo
        $response = $this->deleteJson("{$this->apiBase}/restaurants/{$this->restaurant->id}");

        # Esperando
        $response->assertStatus(401);
    }

    /**
     * Un usuario solo puede eliminar sus propios restaurantes
     */
    public function test_an_user_must_see_only_their_restaurants(): void
    {
        # Teniendo
        $user = User::factory()->create();

        # Haciendo
        $response = $this->apiAs($user, 'DELETE', "{$this->apiBase}/restaurants/{$this->restaurant->id}");

        # Esperando
        $response->assertStatus(403);
        $this->assertDatabaseCount('restaurants', 1);
    }
}
