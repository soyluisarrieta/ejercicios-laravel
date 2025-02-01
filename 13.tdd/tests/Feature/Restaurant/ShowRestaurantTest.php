<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowRestaurantTest extends TestCase
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
     * Un usuario autenticado puede ver los detalles de uno de sus restaurantes
     */
    public function test_an_authenticated_user_must_see_one_of_their_restaurants(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'GET', "{$this->apiBase}/restaurants/{$this->restaurant->id}");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => ['restaurant' => ['id', 'name', 'description', 'slug']],
            'errors',
            'status'
        ]);

        $response->assertJsonFragment([
            'data' => [
                'restaurant' => [
                    'id' => $this->restaurant->id,
                    'name' => $this->restaurant->name,
                    'description' => $this->restaurant->description,
                    'slug' => $this->restaurant->slug
                ]
            ],
        ]);
    }

    /**
     * Un usuario no autenticado no puede ver uno de los restaurantes
     */
    public function test_an_unauthenticated_user_cannot_see_any_restaurants(): void
    {
        # Haciendo
        $response = $this->getJson("{$this->apiBase}/restaurants/{$this->restaurant->id}");

        # Esperando
        $response->assertStatus(401);
    }

    /**
     * Un usuario solo puede ver su propio restaurantes
     */
    public function test_an_user_must_see_only_their_restaurants(): void
    {
        # Teniendo
        $user = User::factory()->create();

        # Haciendo
        $response = $this->apiAs($user, 'GET', "{$this->apiBase}/restaurants/{$this->restaurant->id}");

        # Esperando
        $response->assertStatus(403);
    }
}
