<?php

namespace ItmoSoft\Customers\Tests\Unit;

use ItmoSoft\Customers\Models\Customer;
use ItmoSoft\Customers\Tests\TestCase;

class CustomerModelTest extends TestCase
{
    public function test_customer_model_uses_configured_table_prefix(): void
    {
        $this->assertSame('itmosoft_customers', (new Customer())->getTable());
    }
}
