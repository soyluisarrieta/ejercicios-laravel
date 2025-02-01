<?php

namespace Tests\Feature;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreatePlateTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create([
            'user_id' => 1,
        ]);
    }

    /**
     * Un usuario puede crear una plato
     */
    public function test_a_user_can_create_a_plate(): void
    {
        # Teniendo
        $data = [
            'name' => 'New plate',
            'description' => 'New plate description',
            'price' => '$123',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates", $data);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'errors',
            'status',
            'data' => [
                'plate' => ['id', 'name', 'price', 'description', 'restaurant_id']
            ],
        ]);
        $response->assertJsonFragment([
            'data' => [
                'plate' => [
                    ...$data,
                    'id' => 1,
                    'restaurant_id' => $this->restaurant->id,
                ]
            ]
        ]);

        $this->assertDatabaseCount("plates", 1);
        $this->assertDatabaseHas("plates", $data);
    }

    /**
     * Un usuario no autenticado no puede crear restaurantes
     */
    public function test_an_unauthenticated_user_cannot_create_restaurants(): void
    {
        # Teniendo
        $data = [
            'name' => 'New restaurant',
            'description' => 'New restaurant description',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/restaurants", $data);

        # Esperando
        $response->assertStatus(401);
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
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

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
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

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
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

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
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }
}
