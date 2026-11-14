<?php

namespace ItmoSoft\Customers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    public function addresses(): HasMany
    {
        $model = config('customers.models.address', CustomerAddress::class);

        return $this->hasMany($model, 'customer_id');
    }

    public function defaultBillingAddress(): ?CustomerAddress
    {
        return $this->addresses()->where('type', 'billing')->where('is_default', true)->first();
    }

    public function defaultShippingAddress(): ?CustomerAddress
    {
        return $this->addresses()->where('type', 'shipping')->where('is_default', true)->first();
    }

    public function getTable(): string
    {
        return $this->table ?: config('customers.table_prefix', 'itmosoft_').'customers';
    }
}
