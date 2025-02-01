<?php

namespace Tests\Feature;

use App\Models\Plate;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListPlateTest extends TestCase
{
    use RefreshDatabase;

    protected $plates;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
        $this->plates = Plate::factory()->count(15)->create();
    }

    /**
     * Un usuario puede ver sus platos
     */
    public function test_a_user_can_see_their_plates(): void
    {
        # Haciendo
        $response = $this->apiAs(User::find(1), 'GET', "{$this->apiBase}/plates");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'data' => ['plates'],
            'errors',
            'status'
        ]);
        $response->assertJsonCount(15, 'data.plates');
    }
}
