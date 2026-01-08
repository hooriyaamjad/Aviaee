<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Domain\UseCases\FetchUserForLogin;
use App\Domain\Interfaces\IUserRepository;

uses(\Illuminate\Foundation\Testing\WithoutMiddleware::class);

beforeEach(function () {
    // Mock the IUserRepository for all tests
    $this->mock(IUserRepository::class, function ($mock) {
        $mock->shouldReceive('findByEmail')->andReturnUsing(function ($email) {
            if ($email === 'test@example.com') {
                return [
                    'id' => 1,
                    'password' => Hash::make('secret123')
                ];
            }
            return null;
        });
    });
});

test('login verification endpoint returns 200 for valid credentials', function () {
    $response = $this->postJson('/verify-login-credentials', [
        'email' => 'test@example.com',
        'password' => 'secret123',
    ]);

    $response
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Login successful',
            'user_id' => 1,
        ]);
});

test('login verification endpoint returns 401 for invalid password', function () {
    $response = $this->postJson('/verify-login-credentials', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Invalid email or password.',
        ]);
});

test('login verification endpoint returns 401 for non existing email', function () {
    $response = $this->postJson('/verify-login-credentials', [
        'email' => 'doesnotexist@example.com',
        'password' => 'anything',
    ]);

    $response
        ->assertStatus(401)
        ->assertJson([
            'message' => 'Invalid email or password.',
        ]);
});

test('login verification endpoint returns 422 when email is missing', function () {
    $response = $this->postJson('/verify-login-credentials', [
        'password' => 'secret123',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('login verification endpoint returns 422 when password is missing', function () {
    $response = $this->postJson('/verify-login-credentials', [
        'email' => 'test@example.com',
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
