<?php

// test('Unauthenticated users cannot access the products page', function () {
//     $this->get('/products')
//     ->assertRedirect('/login');
// });

//Can be written as a shortcut
test('Unauthenticated users cannot access the products page')
    ->get('/products')
    ->assertRedirect('/login');
