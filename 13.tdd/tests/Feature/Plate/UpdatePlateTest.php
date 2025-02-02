<?php

namespace Tests\Feature;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdatePlateTest extends TestCase
{
    use RefreshDatabase;

    protected $restaurant;
    protected $plate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->restaurant = Restaurant::factory()->create(['user_id' => 1]);
        $this->plate = Plate::factory()->create([
            'name' => 'plate name',
            'description' => 'plate description',
            'price' => '$123',
            'restaurant_id' => $this->restaurant->id,
        ]);
    }

    /**
     * Un usuario puede actualizar un plato
     */
    public function test_a_user_can_update_a_plate(): void
    {
        # Teniendo
        $data = [
            'name' => 'NEW plate',
            'description' => 'NEW plate description',
            'price' => '$456'
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs(User::find(1), 'PUT', $endpoint, $data);

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
                    'id' => $this->plate->id,
                    'restaurant_id' => $this->restaurant->id
                ]
            ]
        ]);

        $this->assertDatabaseMissing('plates', [
            'name' => 'plate name',
            'description' => 'plate description',
            'price' => '$123',
        ]);
    }

    /**
     * Un usuario no puede actualizar un platos de otro usuario
     */
    public function test_a_user_cannot_update_a_plate_of_another_users(): void
    {
        # Teniendo
        $data = [
            'name' => 'NEW plate',
            'description' => 'NEW plate description',
            'price' => '$456'
        ];
        $user = User::factory()->create();

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs($user, 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(403);
    }

    /**
     * El nombre del plato es requerido
     */
    public function test_plate_name_field_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => '',
            'description' => 'NEW plate description',
            'price' => '$456'
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs(User::find(1), 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * El nombre del plato debe tener al menos 3 caracteres
     */
    public function test_plate_name_field_must_have_at_lease_3_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'NE',
            'description' => 'NEW plate description',
            'price' => '$456'
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs(User::find(1), 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * La descripción del plato es requerido
     */
    public function test_plate_description_field_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => 'NEW plate',
            'description' => '',
            'price' => '$456'
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs(User::find(1), 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }

    /**
     * La descripción del plato debe tener al menos 3 caracteres
     */
    public function test_plate_description_field_must_have_at_lease_3_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'NEW plate',
            'description' => 'NE',
            'price' => '$456'
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs(User::find(1), 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }
}
