<?php

namespace Tests\Unit\Model;

use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_invoice_belongs_to_customer()
    {
        $customer = Customer::factory()->create();

        // Creating an invoice
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'amount' => 100.00,
            'status' => 'unpaid',
            'billed_date' => now(),
            'paid_date' => null,
        ]);

        // Assert that the invoice was created successfully
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'customer_id' => $customer->id,
            'amount' => 100.00,
            'status' => 'unpaid',
            'billed_date' => now(),
            'paid_date' => null,
        ]);

        // Assert that the invoice is associated with the correct customer
        $this->assertEquals($customer->id, $invoice->customer_id);
    }
}
