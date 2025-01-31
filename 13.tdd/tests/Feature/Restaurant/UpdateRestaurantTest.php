<?php

namespace Tests\Feature;

use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateRestaurantTest extends TestCase
{
    use RefreshDatabase;

    protected $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1,
            'name' => 'Restaurant',
            'description' => 'Restaurant description',
        ]);
    }

    /**
     * Actualizar una restaurante
     */
    public function test_an_user_can_update_a_restaurant(): void
    {
        # Teniendo
        $data = [
            'name' => 'New restaurant',
            'description' => 'New restaurant description',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

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
        $this->assertDatabaseMissing('restaurants', [
            'name' => 'Restaurant',
            'description' => 'Restaurant description',
        ]);
    }

    /**
     * El nombre del restaurante es requerido
     */
    public function test_restaurant_name_field_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => '',
            'description' => 'New restaurant description',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * El nombre del restaurante es requerido
     */
    public function test_restaurant_name_field_must_have_at_lease_3_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'ne',
            'description' => 'New restaurant description',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * La descripción del restaurante es requerido
     */
    public function test_restaurant_description_field_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => 'New restaurant',
            'description' => '',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }

    /**
     * La descripción del restaurante es requerido
     */
    public function test_restaurant_description_field_must_have_at_lease_3_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'New restaurant',
            'description' => 'Ne',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'PUT', "{$this->apiBase}/restaurants/{$this->restaurant->id}", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }
}
