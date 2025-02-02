<?php

namespace Tests\Feature;

use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePlateTest extends TestCase
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
     * Un usuario puede eliminar sus platos
     */
    public function test_a_user_can_delete_their_plates(): void
    {
        $this->withoutExceptionHandling();

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs($this->user, 'DELETE', $endpoint);

        # Esperando
        $response->assertStatus(200);
        $this->assertDatabaseMissing('plates', ['id' => $this->plate->id]);
    }
}
