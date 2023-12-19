<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\V1\CustomerResource;
use App\Http\Resources\V1\InvoiceResource;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Request;


class CustomerResouceTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_resource_return_array()
    {
        // Create a customer
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'type' => 'Business',
            'email' => 'john.doe@example.com',
            'address' => '123 Main St',
            'city' => 'Cityville',
            'state' => 'CA',
            'postal_code' => '12345',
        ]);

        // Create an invoice associated with the customer
        $invoice = Invoice::factory()->create(['customer_id' => $customer->id]);

        // Load the customer resource
        $customerResource = new CustomerResource($customer);

        // Transform the resource into an array
        $customerArray = $customerResource->toArray(new Request());

        // Assert that the array contains the expected values
        $this->assertEquals($customer->id, $customerArray['id']);
        $this->assertEquals($customer->name, $customerArray['name']);
        $this->assertEquals($customer->type, $customerArray['customerType']);
        $this->assertEquals($customer->email, $customerArray['email']);
        $this->assertEquals($customer->address, $customerArray['address']);
        $this->assertEquals($customer->city, $customerArray['city']);
        $this->assertEquals($customer->state, $customerArray['state']);
        $this->assertEquals($customer->postal_code, $customerArray['postalCode']);

    }
}
