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

        $response->assertJsonCount(15, 'data.menu.plates');

        // Verificar si el menu creado se relacionó con cada plato
        foreach ($this->plates as $plate) {
            $this->assertDatabaseHas("menus_plates", [
                'menu_id' => 1,
                'plate_id' => $plate->id,
            ]);
        }
    }

    /**
     * Un usuario puede agregar un nuevo plato al menu
     */
    public function test_a_user_can_add_a_new_plate_to_a_menu(): void
    {
        # Teniendo
        $newPlate = Plate::factory()->create([
            'restaurant_id' => $this->restaurant->id,
        ]);
        $data = [
            'name' => 'menu updated',
            'description' => 'menu updated description',
            'plate_ids' => [...$this->plates->pluck('id'), $newPlate->id]
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(200);

        // Verificar el plato nuevo
        $response->assertJsonCount(16, 'data.menu.plates');
        $this->assertDatabaseHas('menus_plates', [
            'menu_id' => 1,
            'plate_id' => $newPlate->id,
        ]);

        // Verificar si el menu creado se relacionó con cada plato
        foreach ($this->plates as $plate) {
            $this->assertDatabaseHas("menus_plates", [
                'menu_id' => 1,
                'plate_id' => $plate->id,
            ]);
        }
    }

    /**
     * Un usuario puede eliminar platos al menu
     */
    public function test_a_user_can_remove_plates_to_a_menu(): void
    {
        $this->withoutExceptionHandling();

        # Teniendo
        $plateIds = $this->plates->splice(0, 4)->pluck('id');
        $data = [
            'name' => 'menu updated',
            'description' => 'menu updated description',
            'plate_ids' => $plateIds
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(200);

        // Verificar el plato nuevo
        $response->assertJsonCount(4, 'data.menu.plates');

        // Verificar si el menu creado se relacionó con cada plato
        foreach ($plateIds as $plateId) {
            $this->assertDatabaseHas("menus_plates", [
                'menu_id' => 1,
                'plate_id' => $plateId,
            ]);
        }

        $this->assertDatabaseMissing("menus_plates", [
            'menu_id' => 1,
            'plate_id' => $this->plates->last()->id,
        ]);
    }

    /**
     * Un usuario no autenticado no puede actualizar un menu
     */
    public function test_a_unauthenticated_user_cannot_update_a_menu(): void
    {
        # Teniendo
        $data = [
            'name' => 'menu updated',
            'description' => 'menu updated description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->putJson($endpoint, $data);

        # Esperando
        $response->assertStatus(401);
    }

    /**
     * El nombre del menu es requerido
     */
    public function test_menu_name_field_must_be_required_to_update(): void
    {
        # Teniendo
        $data = [
            'name' => '',
            'description' => 'menu updated description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * El nombre del menu debe tener al menos 3 caracteres
     */
    public function test_menu_name_field_must_have_at_lease_3_characters_to_update(): void
    {
        # Teniendo
        $data = [
            'name' => 'me',
            'description' => 'menu updated description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * La descripción del menu es requerido
     */
    public function test_menu_description_field_must_be_required_to_update(): void
    {
        # Teniendo
        $data = [
            'name' => 'menu updated',
            'description' => '',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }

    /**
     * La descripción del menu debe tener al menos 3 caracteres
     */
    public function test_menu_description_field_must_have_at_lease_3_characters_to_update(): void
    {
        # Teniendo
        $data = [
            'name' => 'menu updated',
            'description' => 'me',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }

    /**
     * Los platos duplicados del menu se deben ignorar
     */
    public function test_menu_duplicated_plates_must_be_ignored_to_update(): void
    {
        # Teniendo
        $data = [
            'name' => 'menu updated',
            'description' => 'menu updated description',
            'plate_ids' => [$this->plates->first()->id, $this->plates->first()->id]
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus/{$this->menu->id}";
        $response = $this->apiAs($this->user, 'PUT', $endpoint, $data);

        # Esperando
        $response->assertStatus(200);
        $this->assertDatabaseCount('menus_plates', 1);
        $this->assertTrue(Menu::first()->plates->contains($this->plates->first()));
    }
}
