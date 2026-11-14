<?php

return [
    'table_prefix' => 'itmosoft_',
    'models' => [
        'customer' => ItmoSoft\Customers\Models\Customer::class,
        'address' => ItmoSoft\Customers\Models\CustomerAddress::class,
    ],
    'default_address_type' => 'shipping',
    'address_types' => ['billing', 'shipping', 'other'],
    'customer_number' => [
        'enabled' => false,
        'prefix' => 'CUS-',
    ],
];
