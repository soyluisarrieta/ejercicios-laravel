<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected $restaurants;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurants = Restaurant::factory()->count(10)->create([
            'user_id' => 1
        ]);
    }

    /**
     * Un usuario autenticado puede ver sus restaurantes
     */
    public function test_an_authenticated_user_must_see_their_restaurants(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'GET', "{$this->apiBase}/restaurants");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => ['restaurants'],
            'errors',
            'status'
        ]);
        $response->assertJsonCount(10, 'data.restaurants');
    }

    /**
     * Un usuario no autenticado no puede ver los restaurantes
     */
    public function test_an_unauthenticated_user_cannot_see_restaurants(): void
    {
        # Haciendo
        $response = $this->getJson("{$this->apiBase}/restaurants");

        # Esperando
        $response->assertStatus(401);
    }

    /**
     * Un usuario solo puede ver sus propios restaurantes
     */
    public function test_an_user_must_see_only_their_restaurants(): void
    {
        # Teniendo
        $user = User::factory()->create();

        # Haciendo
        $response = $this->apiAs($user, 'GET', "{$this->apiBase}/restaurants");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => ['restaurants'],
            'errors',
            'status'
        ]);
        $response->assertJsonCount(0, 'data.restaurants');
    }
}
