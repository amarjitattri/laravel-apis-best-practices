<?php

namespace App\Filters\V1 ;

use App\Filters\ApiFilter;
use Illuminate\Http\Request;

class CustomerFilter extends ApiFilter {

    protected $allowParms = [
        'name' => ['eq'],
        'customerType' => ['eq'],
        'email' => ['eq'],
        'address' => ['eq'],
        'city' => ['eq'],
        'state' => ['eq'],
        'postalCode' => ['eq', 'gt', 'lt', 'lte', 'gte']
    ];
    protected $columnMap = [
        'postalCode' => 'postal_code',
        'customerType' => 'type'
    ];

    protected $operatorMap = [
        'eq' => '=',
        'lt' => '<',
        'lte' => '<=',
        'gt' => '>',
        'gte' => '>=',
    ];
}
