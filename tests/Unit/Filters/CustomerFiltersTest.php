<?php

namespace Tests\Unit\Filters;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class CustomerFiltersTest extends TestCase
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
    public function test_customer_filter_by_customer_name()
    {

        $customer = Customer::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/customers?name[eq]='.$customer->name);


        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'customerType', // Make sure it matches the attribute in CustomerResource
                    'address',
                    'city',
                    'state',
                    'postalCode',
                ]
            ],
        ]);

        // Check if the returned customer data matches the created customer
        $responseCustomer = $response->json('data')[0];

        $this->assertEquals($customer->id, $responseCustomer['id']);
        $this->assertEquals($customer->name, $responseCustomer['name']);
        $this->assertEquals($customer->email, $responseCustomer['email']);
        $this->assertEquals($customer->type, $responseCustomer['customerType']);
        $this->assertEquals($customer->address, $responseCustomer['address']);
        $this->assertEquals($customer->city, $responseCustomer['city']);
        $this->assertEquals($customer->state, $responseCustomer['state']);
    }
    public function test_customer_filter_get_customer_by_customer_type()
    {
        $customerTypeI = Customer::factory()->create(['type' => 'I']);
        $customerTypeII = Customer::factory()->create(['type' => 'b']);
        $customerTypeIII = Customer::factory()->create(['type' => 'B']);

        // Make a request to filter customers by type 'I'
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/customers?customerType[eq]=I');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [ // '*' indicates that each item in the array should have the same structure
                        'id',
                        'name',
                        'email',
                        'customerType',
                        'address',
                        'city',
                        'state',
                        'postalCode',
                    ],
                ],
        ]);

        // Check if the response contains the expected customers
        $responseCustomers = $response->json('data')[0];

        // Check the details of the returned customer
        $this->assertEquals($customerTypeI->id, $responseCustomers['id']);
        $this->assertEquals($customerTypeI->name, $responseCustomers['name']);
        $this->assertEquals($customerTypeI->email, $responseCustomers['email']);
        $this->assertEquals($customerTypeI->type, $responseCustomers['customerType']);
        $this->assertEquals($customerTypeI->address, $responseCustomers['address']);
        $this->assertEquals($customerTypeI->city, $responseCustomers['city']);
        $this->assertEquals($customerTypeI->state, $responseCustomers['state']);
        $this->assertEquals($customerTypeI->postal_code, $responseCustomers['postalCode']);

    }
    public function test_customer_filter_get_customers_filter_by_postal_code()
    {
        $customerTypeI = Customer::factory()->create(['type' => 'I', 'postal_code' => 3000]);
        $customerTypeII = Customer::factory()->create(['type' => 'II', 'postal_code' => 2500]);
        $customerTypeIII = Customer::factory()->create(['type' => 'III', 'postal_code' => 2200]);

        // Make a request to filter customers by postal code greater than 2000 and customer type 'I'
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token,
        ])->get('/api/v1/customers?postalcode[gt]=2000&customerType[eq]=I');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [ // '*' indicates that each item in the array should have the same structure
                        'id',
                        'name',
                        'email',
                        'customerType',
                        'address',
                        'city',
                        'state',
                        'postalCode',
                    ],
                ],
            ]);

        // Check if the response contains the expected customers
        $responseCustomers = $response->json('data');

        // Check the details of the returned customer
        $this->assertEquals($customerTypeI->id, $responseCustomers[0]['id']);
        $this->assertEquals($customerTypeI->name, $responseCustomers[0]['name']);
        $this->assertEquals($customerTypeI->email, $responseCustomers[0]['email']);
        $this->assertEquals($customerTypeI->type, $responseCustomers[0]['customerType']);
        $this->assertEquals($customerTypeI->address, $responseCustomers[0]['address']);
        $this->assertEquals($customerTypeI->city, $responseCustomers[0]['city']);
        $this->assertEquals($customerTypeI->state, $responseCustomers[0]['state']);
        $this->assertEquals($customerTypeI->postal_code, $responseCustomers[0]['postalCode']);
    }
}
