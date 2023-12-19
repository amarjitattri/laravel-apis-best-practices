<?php

namespace Tests\Requests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreInvoiceRequestTest extends TestCase
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

    public function test_valid_store_invoice_request()
    {
        $data = [
            'customerId' => 1,
            'amount' => 100.00,
            'status' => 'B',
            'billedDate' => '2023-01-01',
            'paidDate' => '2023-01-10',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
            ])->post('/api/v1/invoices', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('invoices', [
            'customer_id' => $data['customerId'],
            'amount' => $data['amount'],
            'status' => $data['status'],
            'billed_date' => $data['billedDate'],
            'paid_date' => $data['paidDate'],
        ]);
    }
    public function test_invalid_store_invoice_request()
    {

        $data = [
            'customerId' => 1,
            'amount' => 100.00,

        ];

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->token])
            ->post('/api/v1/invoices', $data);

        $response->assertStatus(302);
    }

}
