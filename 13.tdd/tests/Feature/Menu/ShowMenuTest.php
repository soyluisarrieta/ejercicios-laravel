<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowMenuTest extends TestCase
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
     * Un usuario puede ver los detalles de un menu
     */
    public function test_a_user_can_show_a_menu(): void
    {
        $this->withoutExceptionHandling();

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'GET', $endpoint);
        $response->dump();

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'errors',
            'status',
            'data' => [
                'menu' => [
                    'id',
                    'name',
                    'description',
                    'plates' => [
                        '*' => ['name', 'description', 'price']
                    ]
                ]
            ],
        ]);

        // Verificar si el menu se obtuvo el menu y sus platos correspondientes
        $response->assertJsonPath('data.menu.name', $this->menu->name);
        $response->assertJsonPath('data.menu.description', $this->menu->description);

        $firstPlate = $this->plates->first();
        $response->assertJsonPath('data.menu.plates.0', [
            'name' => $firstPlate->name,
            'description' => $firstPlate->description,
            'price' => (string) $firstPlate->price,
        ]);

        // Verificar si el menu se creó en la base de datos
        $this->assertDatabaseHas('menus', [
            'name' => $this->menu->name,
            'description' => $this->menu->description,
            'restaurant_id' => $this->restaurant->id,
        ]);

        $response->assertJsonCount(15, 'data.menu.plates');

        // Verificar si el menu creado se relacionó con cada plato
        foreach ($this->plates as $plate) {
            $this->assertDatabaseHas("menus_plates", [
                'menu_id' => 1,
                'plate_id' => $plate->id,
            ]);
        }
    }
}
