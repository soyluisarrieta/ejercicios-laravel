<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    /**
     * Un usuario existente puede iniciar sesión.
     */
    public function test_an_existing_user_can_login(): void
    {
        // $this->withoutExceptionHandling();

        # Teniendo
        $credentials = [
            'email' => 'luis@arrieta.com',
            'password' => 'password',
        ];

        # Haciendo
        $response = $this->post("{$this->apiBase}/login", $credentials);
        // $response->dump();

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }
}
