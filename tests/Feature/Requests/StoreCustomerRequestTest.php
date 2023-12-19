<?php

namespace Tests\Requests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreCustomerRequestTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }
    public function test_valid_store_customer_request()
    {
        $data = [
            'name' => 'Test Customer',
            'customerType' => 'B',
            'email' => 'test@example.com',
            'address' => 'Street 123',
            'city' => 'Test City',
            'state' => 'Test State',
            'postalCode' => '12345',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
            ])->post('/api/v1/customers', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('customers', [
            'name' => $data['name'],
            'type' => strtoupper($data['customerType']),
            'email' => $data['email'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'postal_code' => $data['postalCode'],
        ]);
    }
    public function test_invalid_store_customer_request()
    {
        $data = [
            'name' => 'Test Customer',
            'customerType' => 'B',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
            ])->post('/api/v1/customers', $data);

        $response->assertStatus(302);
    }
}
