<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerControllerTest extends TestCase
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
    public function test_get_all_customer()
    {
        $customers = Customer::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/customers');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',

                    ],
                ],
            ]);
    }
    public function test_create_customer()
    {

        $data = [
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'customerType' => 'b',
            'address' => 'street 192',
            'city' => 'panchkula',
            'state' => 'Haryana',
            'postalCode' => 13046
        ];

        // Record count before creating a customer
        $initialCount = Customer::count();

        $response =  $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->post('/api/v1/customers', $data);


        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'customerType',
                    'address',
                    'city',
                    'state',
                    'postalCode'
                ],
        ]);

        // Check if the customer was added to the database
        $this->assertEquals($initialCount + 1, Customer::count());

        // Retrieve the created customer from the database
        $createdCustomer = Customer::latest()->first();

        // Check if the attributes match
        $this->assertEquals($data['name'], $createdCustomer->name);
        $this->assertEquals($data['email'], $createdCustomer->email);
        $this->assertEquals($data['customerType'], $createdCustomer->type);
        $this->assertEquals($data['address'], $createdCustomer->address);
        $this->assertEquals($data['city'], $createdCustomer->city);
        $this->assertEquals($data['state'], $createdCustomer->state);
        $this->assertEquals($data['postalCode'], $createdCustomer->postal_code);
    }

    public function test_show_customer()
    {

        $customer = Customer::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/customers/' . $customer->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'customerType', // Make sure it matches the attribute in CustomerResource
                    'address',
                    'city',
                    'state',
                    'postalCode',
                ],
            ]);

        // Check if the returned customer data matches the created customer
        $responseCustomer = $response->json('data');

        $this->assertEquals($customer->id, $responseCustomer['id']);
        $this->assertEquals($customer->name, $responseCustomer['name']);
        $this->assertEquals($customer->email, $responseCustomer['email']);
        $this->assertEquals($customer->type, $responseCustomer['customerType']);
        $this->assertEquals($customer->address, $responseCustomer['address']);
        $this->assertEquals($customer->city, $responseCustomer['city']);
        $this->assertEquals($customer->state, $responseCustomer['state']);
    }

    public function test_update_customer()
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
    public function test_delete_customer()
    {

        $customer = Customer::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->delete('/api/v1/customers/'. $customer->id);

        $response->assertStatus(200);

        // Check if the customer has been deleted from the database
        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
