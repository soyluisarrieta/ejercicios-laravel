<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class ListMenuTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Restaurant $restaurant;
    protected Collection $menus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $plates = Plate::factory()->count(100)->create(['restaurant_id' => $this->restaurant->id,]);
        $this->menu = Menu::factory()
            ->count(150)
            ->hasAttached($plates->random(15))
            ->create(['restaurant_id' => $this->restaurant->id]);
    }

    /**
     * Un usuario puede ver sus menus
     */
    public function test_a_user_can_see_their_menus(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'GET', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'status',
            'errors',
            'data' => [
                'menus' => [
                    '*' => ['id', 'name', 'description', 'restaurant_id']
                ]
            ]
        ]);
    }

    /**
     * Un usuario puede ver sus menus paginados
     */
    public function test_a_user_can_see_their_paginated_menus(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'GET', "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonCount(15, 'data.menus');
        $response->assertJsonStructure([
            'data' => ['total', 'current_page', 'total_pages', 'per_page', 'count']
        ]);
        $response->assertJsonPath('data.total', 150);
        $response->assertJsonPath('data.current_page', 1);
        $response->assertJsonPath('data.total_pages', 10);
        $response->assertJsonPath('data.per_page', 15);
        $response->assertJsonPath('data.count', 15);
    }

    /**
     * Un usuario no autenticado no puede ver los menus
     */
    public function test_an_unauthenticated_user_cannot_see_menus(): void
    {
        # Haciendo
        $response = $this->getJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/menus");

        # Esperando
        $response->assertStatus(401);
    }
}
