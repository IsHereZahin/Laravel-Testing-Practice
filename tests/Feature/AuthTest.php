<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{

    use RefreshDatabase;


    public function test_login_redirect_to_products(): void
    {
        User::create([
            'name'  => 'user',
            'email'=> 'user@gmail.com',
            'password'=> bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'user@gmail.com',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/products');
    }


    public function test_unauthenticated_user_cannot_access_product(): void
    {
        $response = $this->get('/products');

        $response->assertStatus(302);
        $response->assertRedirect('login');
    }
}
