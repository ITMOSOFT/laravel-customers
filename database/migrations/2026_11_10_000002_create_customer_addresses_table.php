<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $prefix = config('customers.table_prefix', 'itmosoft_');

        Schema::create($prefix.'customer_addresses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('customer_id')->constrained($prefix.'customers')->cascadeOnDelete();
            $table->string('type', 20)->default('shipping')->index();
            $table->string('label')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('company')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code', 30)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['customer_id', 'type', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(config('customers.table_prefix', 'itmosoft_').'customer_addresses');
    }
};
