<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }
}


/////////////////////////////////////////////////////// Use this for case of #01 //////////////////////////////////////////////////
// <?php

// namespace Tests;

// use App\Models\User;
// use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

// abstract class TestCase extends BaseTestCase
// {
//     use CreatesApplication;

//     public User $user;
//     public User $admin;

//     protected function setUp(): void
//     {
//         parent::setUp();
//         $this->withoutVite();


//         $this->user = $this->createUser();
//         $this->admin = $this->createUser(isAdmin: true);
//     }

//     private function createUser(bool $isAdmin = false): User
//     {
//         return User::factory()->create([
//             'is_admin' => $isAdmin,
//         ]);
//     }
// }

//
// ////////////////////////////////////////  Case #1 -  //////////////////////////////////////////
// remove     private User $user;
    // private User $admin;

    // protected function setUp(): void
    // {
    //     parent::setUp();

    //     info('Set up executed');  // Check all test history in storage/logs/laravel.log

    //     $this->user = $this->createUser();
    //     $this->admin = $this->createUser(isAdmin: true);
    // }
////////////////////////  and  ////////////////////////////
    // private function createUser(bool $isAdmin = false): User
    // {
    //     return User::factory()->create([
    //         'is_admin' => $isAdmin,
    //     ]);
    // }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//
