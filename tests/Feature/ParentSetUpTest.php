<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParentSetUpTest extends TestCase
{

    use RefreshDatabase;

    private User $user;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        info('Set up executed');  // Check all test history in storage/logs/laravel.log

        $this->user = $this->createUser();
        $this->admin = $this->createUser(isAdmin: true);
    }

    public function test_admin_can_see_add_new_product_button (): void
    {
        $response = $this->actingAs($this->admin)->get('/products');

        // $response->assertStatus(200);
        $response->assertOk();
        $response->assertSee('Add new product');
    }

    public function test_user_can_not_see_add_new_product_button (): void
    {
        $response = $this->actingAs($this->user)->get('/products');

        // $response->assertStatus(200);
        $response->assertOk();
        $response->assertDontSee('Add new product');
    }

    public function test_non_verifyed_user_can_not_access_products_page (): void
    {
        $response = $this->get('/products');
        $response->assertStatus(302);
    }

    private function createUser(bool $isAdmin = false): User
    {
        return User::factory()->create([
            'is_admin' => $isAdmin,
        ]);
    }
}
