<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\Plate;
use App\Models\Restaurant;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateMenuTest extends TestCase
{
    use RefreshDatabase;

    protected Restaurant $restaurant;
    protected User $user;
    protected $plates;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->user = User::factory()->create();
        $this->restaurant = Restaurant::factory()->create(['user_id' => $this->user->id]);
        $this->plates = Plate::factory()->count(15)->create(['restaurant_id' => $this->restaurant->id]);
    }

    /**
     * Un usuario puede crear un menu
     */
    public function test_a_user_can_create_a_menu(): void
    {
        # Teniendo
        $data = [
            'name' => 'New menu',
            'description' => 'New menu description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($this->user, 'POST', $endpoint, $data);

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

    /**
     * El nombre del menu es requerido
     */
    public function test_menu_name_field_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => '',
            'description' => 'New menu description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($this->user, 'POST', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * El nombre del menu debe tener al menos 3 caracteres
     */
    public function test_menu_name_field_must_have_at_lease_3_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'Ne',
            'description' => 'New menu description',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($this->user, 'POST', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * La descripción del menu es requerido
     */
    public function test_menu_description_field_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => 'New menu',
            'description' => '',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($this->user, 'POST', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }

    /**
     * La descripción del menu debe tener al menos 3 caracteres
     */
    public function test_menu_description_field_must_have_at_lease_3_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'New menu',
            'description' => 'Ne',
            'plate_ids' => $this->plates->pluck('id')
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($this->user, 'POST', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['description']]);
    }

    /**
     * Los platos del menu son requeridos
     */
    public function test_menu_plates_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => 'New menu',
            'description' => 'New menu description',
            'plate_ids' => []
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($this->user, 'POST', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['plate_ids']]);
    }

    /**
     * Los platos del menu deben existir
     */
    public function test_menu_plates_must_exists(): void
    {
        # Teniendo
        $data = [
            'name' => 'New menu',
            'description' => 'New menu description',
            'plate_ids' => [100]
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($this->user, 'POST', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['plate_ids.0']]);
    }

    /**
     * El restaurante del menu debe pertener al usuario
     */
    public function test_menu_restaurant_must_belongs_to_user(): void
    {
        # Teniendo
        $data = [
            'name' => 'New menu',
            'description' => 'New menu description',
            'plate_ids' => [1]
        ];
        $user = User::factory()->create();

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($user, 'POST', $endpoint, $data);

        # Esperando
        $response->assertStatus(403);
    }

    /**
     * Los platos del menu deben pertener al usuario
     */
    public function test_menu_plates_must_belongs_to_user(): void
    {
        # Teniendo
        $plate = Plate::factory()->create();
        $data = [
            'name' => 'New menu',
            'description' => 'New menu description',
            'plate_ids' => [$plate->id]
        ];

        # Haciendo
        $endpoint = "{$this->apiBase}/restaurants/{$this->restaurant->id}/menus";
        $response = $this->apiAs($this->user, 'POST', $endpoint, $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['plate_ids.0']]);
    }
}
