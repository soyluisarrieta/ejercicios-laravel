<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_hello_world_route_should_return_status_success(): void
    {
        $response = $this->get('/api/hello-world');
        $response->assertJson(['msg' => 'Hello World!']);
    }
}
