<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeletePlateRelationsTest extends TestCase
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
        $this->plate = Plate::factory()->hasAttached(Menu::factory())->create(['restaurant_id' => $this->restaurant->id]);
    }

    /**
     * Un usuario puede eliminar sus platos relacionados
     */
    public function test_a_user_can_delete_their_related_plates(): void
    {
        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs($this->user, 'DELETE', $endpoint);

        # Esperando
        $response->assertStatus(200);
        $this->assertDatabaseMissing('plates', ['id' => $this->plate->id]);
        $this->assertDatabaseMissing('menus_plates', ['plate_id' => $this->plate->id]);
    }

    /**
     * Un usuario no autenticado no puede eliminar platos relacionados
     */
    public function test_a_unauthenticated_user_cannot_delete_any_related_plates(): void
    {
        # Haciendo
        $response = $this->deleteJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}");

        # Esperando
        $response->assertStatus(401);
        $this->assertDatabaseHas('plates', ['id' => $this->plate->id]);
    }

    /**
     * Un usuario no puede eliminar los platos relacionados de otros usuarios
     */
    public function test_a_user_cannot_delete_another_user_related_plates(): void
    {
        # Teniendo
        $user = User::factory()->create();

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/plates/{$this->plate->id}";
        $response = $this->apiAs($user, 'DELETE', $endpoint);

        # Esperando
        $response->assertStatus(403);
        $this->assertDatabaseHas('plates', ['id' => $this->plate->id]);
    }
}
