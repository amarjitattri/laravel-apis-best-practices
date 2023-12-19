<?php

namespace Tests\Unit\Model;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{

    use RefreshDatabase;

    public function test_customer_has_invoices_relationship()
    {
        // Create a customer
        $customer = Customer::factory()->create();

        // Create some invoices for the customer
        $invoices = [
            ['amount' => 100.00, 'status' => 'B', 'billed_date' => now(), 'paid_date' => now()],
            ['amount' => 150.00, 'status' => 'P', 'billed_date' => now(), 'paid_date' => now()],
            // Add more invoices as needed
        ];

        $customer->invoices()->createMany($invoices);

        // Retrieve the customer with invoices from the database
        $customerWithInvoices = Customer::with('invoices')->find($customer->id);

        // Assert that the invoices relationship is loaded
        $this->assertTrue($customerWithInvoices->relationLoaded('invoices'));

        // Assert that the number of invoices matches the number created
        $this->assertCount(count($invoices), $customerWithInvoices->invoices);

        // Assert that each invoice has the correct attributes
        foreach ($customerWithInvoices->invoices as $index => $invoice) {
            $this->assertEquals($invoices[$index]['amount'], $invoice->amount);
            $this->assertEquals($invoices[$index]['status'], $invoice->status);
        }
    }
}
