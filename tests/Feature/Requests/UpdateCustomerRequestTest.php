<?php

namespace Tests\Requests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateCustomerRequestTest extends TestCase
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
    public function test_valid_customer_update_request()
    {
        $customer = Customer::factory()->create();

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'customerType' => 'b',
            'address' => 'Updated Street',
            'city' => 'Updated City',
            'state' => 'Updated State',
            'postalCode' => '12345',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->patch('/api/v1/customers/'. $customer->id, $updatedData);


        $response->assertStatus(200);

        // Refresh the customer model instance to get the updated data from the database
        $customer->refresh();

        // Check if the customer data has been updated in the database
        $this->assertEquals($updatedData['name'], $customer->name);
        $this->assertEquals($updatedData['email'], $customer->email);
        $this->assertEquals($updatedData['customerType'], $customer->type);
        $this->assertEquals($updatedData['address'], $customer->address);
        $this->assertEquals($updatedData['city'], $customer->city);
        $this->assertEquals($updatedData['state'], $customer->state);
        $this->assertEquals($updatedData['postalCode'], $customer->postal_code);
    }

    public function test_invalid_customer_update_request()
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
