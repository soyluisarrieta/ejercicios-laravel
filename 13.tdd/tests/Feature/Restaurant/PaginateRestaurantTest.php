<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected $restaurants;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurants = Restaurant::factory()->count(150)->create([
            'user_id' => 1
        ]);
    }

    /**
     * Un usuario autenticado puede ver sus restaurantes por página
     */
    public function test_an_user_must_see_their_restaurants_by_page(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'GET', "{$this->apiBase}/restaurants");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data.restaurants');
        $response->assertJsonStructure([
            'message',
            'errors',
            'status',
            'data' => [
                'restaurants',
                'total',
                'current_page',
                'per_page',
                'total_pages',
                'count'
            ],
        ]);
    }
}
