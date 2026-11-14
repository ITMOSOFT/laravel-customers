<?php

namespace ItmoSoft\Customers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        $model = config('customers.models.customer', Customer::class);

        return $this->belongsTo($model, 'customer_id');
    }

    public function getTable(): string
    {
        return $this->table ?: config('customers.table_prefix', 'itmosoft_').'customer_addresses';
    }
}
