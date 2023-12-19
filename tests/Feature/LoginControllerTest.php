<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->json('POST', '/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);


        $response->assertStatus(200)
            ->assertJsonStructure([
                   "data" => [
                     'userId',
                        'name',
                        'email',
                        'accessToken',
                        'updateToken',
                        'basic',
                   ]

        ]);
    }
    public function test_login_with_invalid_credentials()
    {
        $response = $this->json('POST', '/api/login', [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'Token' => [],
                'message' => 'User Not Authorize',
        ]);
    }
}
