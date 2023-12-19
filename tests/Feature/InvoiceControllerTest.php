<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
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
    public function test_get_all_invoices()
    {
        // Assuming you have InvoiceFactory to create test data
        Invoice::factory()->count(5)->create();

        $response = $this->withHeaders(
            ['Authorization' => 'Bearer ' . $this->token]
            )->get('/api/v1/invoices');

        // dd($response->json());
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonStructure([
                'data' =>[
                        [
                            'id',
                            'customerId',
                            'amount',
                            'status',
                            'billedDate',
                            'paidDate'
                        ]
                    ]
            ]
        );
    }
    public function test_get_invoic_filtering_by_a_specific_column()
    {
        $status = fake()->randomElement(['B', 'P', 'V']);

        Invoice::factory()->create([
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
                ]
            );
    }
    public function test_invoice_create()
    {
        // $this->withoutExceptionHandling();
        $data = [
            "customerId" => 1,
            "amount" =>  5504,
            "status" =>  "B",
            "billedDate" =>  "2023-08-15",
            "paidDate"=> "2023-10-15"

        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
            ])->post('/api/v1/invoices', $data);

        // dd($response->json());
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                        'id',
                        'customerId',
                        'amount',
                        'status',
                        'billedDate',
                        'paidDate'
                    ]
                ]);

        // Check if the invoice was added to the database
        $createInvoices = Invoice::latest()->first();

        $this->assertEquals($data['customerId'], $createInvoices->customer_id);
        $this->assertEquals($data['amount'], $createInvoices->amount);
        $this->assertEquals($data['status'], $createInvoices->status);
    }
    public function test_invoice_get_by_id()
    {
        // Assuming you have an Invoice model and factory
        $invoice = Invoice::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
            ])->get('/api/v1/invoices/'.$invoice->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'customerId',
                    'amount',
                    'status',
                    'billedDate',
                    'paidDate'
                ]
            ]);

        // Check if the returned invoice data matches the created invoice
        $responseInvoice = $response->json('data');

        $this->assertEquals($invoice->id, $responseInvoice['id']);
        $this->assertEquals($invoice->amount, $responseInvoice['amount']);
        $this->assertEquals($invoice->customer_id, $responseInvoice['customerId']);
        $this->assertEquals($invoice->amount, $responseInvoice['amount']);
        $this->assertEquals($invoice->status, $responseInvoice['status']);
    }
    public function test_invoice_Update()
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
    public function test_delete_invoice()
    {
        $invoice = Invoice::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token
            ])->delete('/api/v1/invoices/'. $invoice->id);

        $response->assertStatus(200);

        // Check if the invoice has been deleted from the database
        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id
        ]);
    }
}
