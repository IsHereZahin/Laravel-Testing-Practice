<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertSee('Documentation');

        $response->assertStatus(200);
    }

    public function test_the_dont_see_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertDontSee('This is a test');

        $response->assertStatus(200);
    }
}
