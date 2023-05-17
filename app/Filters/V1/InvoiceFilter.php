<?php

namespace App\Filters\V1 ;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;

class InvoiceFilter extends ApiFilter {

    // $table->id();
    // $table->foreignId('customer_id');
    // $table->integer('amount');
    // $table->string('status'); // Billed, Paid, Void
    // $table->dateTime('billed_date');
    // $table->dateTime('paid_date')->nullable();
    // $table->timestamps();

    //         'id' => $this->id,
    //         'customerId' => $this->customer_id,
    //         'amount' => $this->amount,
    //         'status' => $this->status,
    //         'billedDate' => $this->billed_date,
    //         'paidDate' => $this->paid_date,

    protected $allowParms = [
        'customerId' => ['eq', 'gt', 'lt', 'lte', 'gte'],
        'amount' => ['eq', 'gt', 'lt', 'lte', 'gte'],
        'status' => ['eq', 'ne'],
        'billedDate' => ['eq', 'gt', 'lt', 'lte', 'gte'],
        'paidDate' => ['eq', 'gt', 'lt', 'lte', 'gte'],
        'postalCode' => ['eq', 'gt', 'lt', 'lte', 'gte']
    ];
    protected $columnMap = [
        'customerId' => 'customer_id',
        'billedDate' => 'billed_date',
        'paidDate' => 'paid_date'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
        'ne' => '!=',
    ];

}
