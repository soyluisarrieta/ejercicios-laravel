<?php

namespace Tests\Feature;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPlateTest extends TestCase
{
    use RefreshDatabase;

    protected $plates;
    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create();
        $this->plates = Plate::factory()->count(15)->create([
            'restaurant_id' => $this->restaurant->id
        ]);
    }

    /**
     * Un usuario puede ver sus platos por restaurante
     */
    public function test_a_user_can_see_their_plates_by_restaurant(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'GET', "{$this->apiBase}/{$this->restaurant->id}/plates");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => [
                'plates' => [
                    '*' => ['id', 'restaurant_id', 'name', 'description', 'price']
                ]
            ],
            'errors',
            'status'
        ]);
        $response->assertJsonCount(15, 'data.plates');

        foreach (range(0, 14) as $platePosition) {
            $response->assertJsonPath("data.plates.{$platePosition}.restaurant_id", $this->restaurant->id);
        }
    }

    /**
     * Un usuario puede ver sus platos por restaurante paginados
     */
    public function test_a_user_can_see_their_paginated_plates_by_restaurant(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'GET', "{$this->apiBase}/{$this->restaurant->id}/plates");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data.plates');
        $response->assertJsonStructure([
            'message',
            'errors',
            'status',
            'data' => [
                'plates',
                'total',
                'current_page',
                'per_page',
                'total_pages',
                'count'
            ],
        ]);

        $response->assertJsonPath('data.total', 15);
        $response->assertJsonPath('data.current_page', 1);
        $response->assertJsonPath('data.per_page', 15);
        $response->assertJsonPath('data.total_pages', 1);
        $response->assertJsonPath('data.count', 15);
    }
}
