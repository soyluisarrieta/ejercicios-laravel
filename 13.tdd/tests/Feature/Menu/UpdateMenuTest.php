<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateMenuTest extends TestCase
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
     * Un usuario puede actualizar un menu
     */
    public function test_a_user_can_update_a_menu(): void
    {
        $this->withoutExceptionHandling();

        # Teniendo
        $data = [
            'name' => 'menu updated',
            'description' => 'menu updated description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'PUT', $endpoint, $data);

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

        // Verificar si el menu se actualizo correctamente
        $response->assertJsonPath('data.menu.name', $data['name']);
        $response->assertJsonPath('data.menu.description', $data['description']);

        // Verificar si el menu se retornó correctamente
        $firstPlate = $this->plates->first();
        $response->assertJsonPath('data.menu.plates.0', [
            'name' => $firstPlate->name,
            'description' => $firstPlate->description,
            'price' => (string) $firstPlate->price,
        ]);

        // Verificar si el menu se creó en la base de datos
        $this->assertDatabaseHas('menus', [
            'name' => $data['name'],
            'description' => $data['description'],
            'restaurant_id' => $this->restaurant->id,
        ]);

        // Verificar si el menu creado se relacionó con cada plato
        foreach ($this->plates as $plate) {
            $this->assertDatabaseHas("menus_plates", [
                'menu_id' => 1,
                'plate_id' => $plate->id,
            ]);
        }
    }
}
