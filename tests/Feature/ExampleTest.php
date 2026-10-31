<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the welcome page displays the cashier workspace', function () {
    $response = $this->get('/');

    $response->assertOk()->assertSee('Current Order')->assertSee('KHQR');
});
