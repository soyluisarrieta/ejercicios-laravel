<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteMenuTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $restaurant;
    protected $plates;
    protected $menu;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plates = Plate::factory()->count(15)->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $this->menu = Menu::factory()
            ->hasAttached($this->plates)
            ->create(['restaurant_id' => $this->restaurant->id]);
    }

    /**
     * Un usuario puede eliminar sus menus
     */
    public function test_a_user_can_delete_their_menus(): void
    {
        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'DELETE', $endpoint);

        # Esperando
        $response->assertStatus(200);
        $this->assertDatabaseMissing('menus', ['id' => $this->menu->id]);
        $this->assertDatabaseMissing('menus_plates', ['menu_id' => $this->menu->id]);
    }

    /**
     * Un usuario no autenticado no puede eliminar menus
     */
    public function test_a_unauthenticated_user_cannot_delete_any_menus(): void
    {
        # Haciendo
        $response = $this->deleteJson("{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}");

        # Esperando
        $response->assertStatus(401);
        $this->assertDatabaseHas('plates', ['id' => $this->menu->id]);
    }

    /**
     * Un usuario no puede eliminar los menus de otros usuarios
     */
    public function test_a_user_cannot_delete_another_user_menus(): void
    {
        # Teniendo
        $user = User::factory()->create();

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($user, 'DELETE', $endpoint);

        # Esperando
        $response->assertStatus(403);
        $this->assertDatabaseHas('menus', ['id' => $this->menu->id]);
    }
}
