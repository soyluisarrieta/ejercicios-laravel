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
        $this->withoutExceptionHandling();

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
}
