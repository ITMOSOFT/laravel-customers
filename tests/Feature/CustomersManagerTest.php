<?php

namespace ItmoSoft\Customers\Tests\Feature;

use ItmoSoft\Customers\CustomersManager;
use ItmoSoft\Customers\Models\Customer;
use ItmoSoft\Customers\Tests\TestCase;

class CustomersManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate')->run();
    }

    public function test_customer_can_be_created_and_email_is_normalized(): void
    {
        $customer = app(CustomersManager::class)->create([
            'first_name' => 'Adeel',
            'email' => ' ADEEL@EXAMPLE.COM ',
            'untrusted_field' => 'ignored',
        ]);

        $this->assertSame('adeel@example.com', $customer->email);
        $this->assertNull($customer->getAttribute('untrusted_field'));
        $this->assertTrue($customer->is_active);
    }

    public function test_customer_can_have_separate_default_billing_and_shipping_addresses(): void
    {
        $manager = app(CustomersManager::class);
        $customer = Customer::create(['first_name' => 'Test']);

        $shipping = $manager->addAddress($customer, [
            'type' => 'shipping',
            'address_line_1' => '1 Main Street',
            'country_code' => 'pk',
            'is_default' => true,
        ]);

        $billing = $manager->addAddress($customer, [
            'type' => 'billing',
            'address_line_1' => '2 Office Road',
            'is_default' => true,
        ]);

        $this->assertTrue($shipping->fresh()->is_default);
        $this->assertTrue($billing->fresh()->is_default);
        $this->assertSame('PK', $shipping->country_code);
    }

    public function test_setting_a_new_default_unsets_the_previous_default_of_same_type(): void
    {
        $manager = app(CustomersManager::class);
        $customer = Customer::create(['first_name' => 'Test']);

        $first = $manager->addAddress($customer, [
            'type' => 'shipping', 'address_line_1' => 'First', 'is_default' => true,
        ]);
        $second = $manager->addAddress($customer, [
            'type' => 'shipping', 'address_line_1' => 'Second',
        ]);

        $manager->setDefaultAddress($second);

        $this->assertFalse($first->fresh()->is_default);
        $this->assertTrue($second->fresh()->is_default);
    }
}
