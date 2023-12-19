<?php

namespace Tests\Requests\Feature;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateInvoiceRequestTest extends TestCase
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
    public function test_valid_invoice_update_request()
    {
        $invoice = Invoice::factory()->create();

        $updatedData = [
            'amount' => 150.00,
            'customer_id' => 2,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
            ])->patch('/api/v1/invoices/'. $invoice->id, $updatedData);

        $response->assertStatus(200);

        // Refresh the invoice model instance to get the updated data from the database
        $invoice->refresh();

        // Check if the invoice data has been updated in the database
        $this->assertEquals($updatedData['amount'], $invoice->amount);
        $this->assertEquals($updatedData['customer_id'], $invoice->customer_id);
    }

    public function test_invalid_invoice_update_request()
    {
        $data = [
            'amount' => 150.00,
            'customer_id' => 2,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
            ])->post('/api/v1/invoices', $data);

        $response->assertStatus(302);
    }
}
