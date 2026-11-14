<?php

namespace ItmoSoft\Customers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use ItmoSoft\Customers\Models\Customer;
use ItmoSoft\Customers\Models\CustomerAddress;

class CustomersManager
{
    public function create(array $attributes): Customer
    {
        $model = config('customers.models.customer', Customer::class);

        return $model::create($this->customerAttributes($attributes));
    }

    public function update(Customer $customer, array $attributes): Customer
    {
        $customer->fill($this->customerAttributes($attributes));
        $customer->save();

        return $customer->refresh();
    }

    public function addAddress(Customer $customer, array $attributes): CustomerAddress
    {
        $types = config('customers.address_types', ['billing', 'shipping', 'other']);
        $type = $attributes['type'] ?? config('customers.default_address_type', 'shipping');

        if (! in_array($type, $types, true)) {
            throw new InvalidArgumentException('Unsupported customer address type.');
        }

        return DB::transaction(function () use ($customer, $attributes, $type): CustomerAddress {
            $model = config('customers.models.address', CustomerAddress::class);
            $isDefault = (bool) ($attributes['is_default'] ?? false);

            if ($isDefault) {
                $customer->addresses()->where('type', $type)->update(['is_default' => false]);
            }

            $address = new $model();
            $address->fill([
                'type' => $type,
                'label' => $attributes['label'] ?? null,
                'first_name' => $attributes['first_name'] ?? null,
                'last_name' => $attributes['last_name'] ?? null,
                'company' => $attributes['company'] ?? null,
                'phone' => $attributes['phone'] ?? null,
                'address_line_1' => $attributes['address_line_1'] ?? null,
                'address_line_2' => $attributes['address_line_2'] ?? null,
                'city' => $attributes['city'] ?? null,
                'state' => $attributes['state'] ?? null,
                'postal_code' => $attributes['postal_code'] ?? null,
                'country_code' => isset($attributes['country_code']) ? strtoupper($attributes['country_code']) : null,
                'is_default' => $isDefault,
            ]);

            $customer->addresses()->save($address);

            return $address;
        });
    }

    public function setDefaultAddress(CustomerAddress $address): CustomerAddress
    {
        return DB::transaction(function () use ($address): CustomerAddress {
            $customer = $address->customer()->lockForUpdate()->firstOrFail();
            $customer->addresses()
                ->where('type', $address->type)
                ->update(['is_default' => false]);

            $address->forceFill(['is_default' => true])->save();

            return $address->refresh();
        });
    }

    public function deleteAddress(CustomerAddress $address): void
    {
        $address->delete();
    }

    public function markEmailVerified(Customer $customer): Customer
    {
        $customer->forceFill(['email_verified_at' => now()])->save();

        return $customer->refresh();
    }

    public function markPhoneVerified(Customer $customer): Customer
    {
        $customer->forceFill(['phone_verified_at' => now()])->save();

        return $customer->refresh();
    }

    private function customerAttributes(array $attributes): array
    {
        $allowed = [
            'customer_number', 'first_name', 'last_name', 'email', 'phone',
            'company', 'date_of_birth', 'notes', 'is_active',
        ];

        $filtered = array_intersect_key($attributes, array_flip($allowed));

        if (isset($filtered['email'])) {
            $filtered['email'] = strtolower(trim($filtered['email']));
        }

        return $filtered;
    }
}
