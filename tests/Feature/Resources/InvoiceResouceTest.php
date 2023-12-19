<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\V1\InvoiceResource;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Tests\TestCase;

class InvoiceResouceTest extends TestCase
{
    public function test_invoice_resource_to_array()
    {
        // Create an invoice
        $invoice = Invoice::factory()->create([
            'customer_id' => 1,
            'amount' => 100.00,
            'status' => 'unpaid',
            'billed_date' => '2023-01-01',
            'paid_date' => null,
        ]);

        // Load the invoice resource
        $invoiceResource = new InvoiceResource($invoice);

        // Transform the resource into an array
        $invoiceArray = $invoiceResource->toArray(new Request());

        // Assert that the array contains the expected values
        $this->assertEquals($invoice->id, $invoiceArray['id']);
        $this->assertEquals($invoice->customer_id, $invoiceArray['customerId']);
        $this->assertEquals($invoice->amount, $invoiceArray['amount']);
        $this->assertEquals($invoice->status, $invoiceArray['status']);
        $this->assertEquals($invoice->billed_date, $invoiceArray['billedDate']);
        $this->assertEquals($invoice->paid_date, $invoiceArray['paidDate']);
    }
}
