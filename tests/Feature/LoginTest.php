<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;

// Use RefreshDatabase for migrations + optional WithoutMiddleware
uses(RefreshDatabase::class, WithoutMiddleware::class)->beforeEach(function () {
    // Dynamically configure in-memory SQLite
    config([
        'database.default' => 'sqlite',
        'database.connections.sqlite.database' => ':memory:',
    ]);

    // Run migrations manually in case RefreshDatabase doesn't pick them up
    $this->artisan('migrate');
});

test('login verification endpoint returns 200 for valid credentials', function () {
    $password = 'secret123';

    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make($password),
    ]);

    $response = $this->postJson('/verify-login-credentials', [
        'email' => 'test@example.com',
        'password' => $password,
    ]);

    $response
        ->assertStatus(200)
        ->assertJson([
            'message' => 'Login successful',
            'user_id' => $user->id,
        ]);
});

test('login verification endpoint returns 401 for invalid password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('correct-password'),
    ]);

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
