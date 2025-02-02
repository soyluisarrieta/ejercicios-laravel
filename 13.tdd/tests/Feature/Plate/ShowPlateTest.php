<?php

namespace Tests\Feature;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowPlateTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected Plate $plate;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plate = Plate::factory()->create(['restaurant_id' => $this->restaurant->id]);
    }

    /**
     * Un usuario puede ver los detalles de uno plato
     */
    public function test_a_user_can_show_a_plate(): void
    {
        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs($this->user, 'GET', $endpoint);

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'errors',
            'status',
            'data' => ['plate' => ['id', 'name', 'description', 'price', 'restaurant_id']],
        ]);

        $response->assertJsonFragment([
            'data' => [
                'plate' => [
                    'id' => $this->plate->id,
                    'name' => $this->plate->name,
                    'description' => $this->plate->description,
                    'price' => (string) $this->plate->price,
                    'restaurant_id' => $this->plate->restaurant_id
                ]
            ],
        ]);
    }

    /**
     * Un usuario no autenticado no puede ver los detalles de ningun plato
     */
    public function test_a_unauthenticated_user_cannot_see_any_plate(): void
    {
        # Haciendo
        $response = $this->getJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        # Esperando
        $response->assertStatus(401);
    }

    /**
     * Un usuario no puede ver los detalles de un plato de otro usuario
     */
    public function test_a_user_cannot_see_a_plate_of_another_user(): void
    {
        # Teniendo
        $user = User::factory()->create();

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs($user, 'GET', $endpoint);

        # Esperando
        $response->assertStatus(403);
    }
}
