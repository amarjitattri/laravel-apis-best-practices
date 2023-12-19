<?php

namespace Tests\Unit\Filters;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicesFilterTest extends TestCase
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
    public function test_invoice_filter_get_invoice_by_invoice_status()
    {
        $status = fake()->randomElement(['B', 'P', 'V']);

        $invoice = Invoice::factory()->create([
            'customer_id' => Customer::factory(),
            'amount' => 1010,
            'status' => 'V',
            'billed_date' => fake()->dateTimeThisDecade(),
            'paid_date' => $status == 'P' ? fake()->dateTimeThisDecade() : NULL,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/api/v1/invoices?status[eq]=V');


        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'customerId',
                        'amount',
                        'status',
                        'billedDate',
                        'paidDate'
                    ]
                ]
        ]);

        $responseInvoice = $response->json('data')[0];

        $this->assertEquals($invoice->id, $responseInvoice['id']);
        $this->assertEquals($invoice->customer_id, $responseInvoice['customerId']);
        $this->assertEquals($invoice->amount, $responseInvoice['amount']);
        $this->assertEquals($invoice->status, $responseInvoice['status']);
    }
    public function test_invoice_filter_get_invoice_filter_by_customer_id()
    {
        $invoice = Invoice::factory()->create([
            'customer_id' => '1',
            'amount' => 1010,
            'status' => 'V',
            'billed_date' => fake()->dateTimeThisDecade(),
            'paid_date' => fake()->dateTimeThisDecade()
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
        ])->get('/api/v1/invoices?customerId[eq]=1');


        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [
                    [
                        'id',
                        'customerId',
                        'amount',
                        'status',
                        'billedDate',
                        'paidDate'
                    ]
                ]
        ]);

        $responseInvoice = $response->json('data')[0];

        $this->assertEquals($invoice->id, $responseInvoice['id']);
        $this->assertEquals($invoice->customer_id, $responseInvoice['customerId']);
        $this->assertEquals($invoice->amount, $responseInvoice['amount']);
        $this->assertEquals($invoice->status, $responseInvoice['status']);
    }
}
