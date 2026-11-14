# ITMOSOFT Laravel Customers

Customer profiles and address book functionality for Laravel 12 and 13.

## Features

- Customer profile records with contact and company details.
- Optional customer number.
- Active/inactive state and email/phone verification timestamps.
- Multiple billing, shipping and other addresses per customer.
- Separate default billing and shipping addresses.
- Transactional default-address changes.
- Configurable table prefix and model classes.
- Laravel package auto-discovery and migrations.

## Requirements

- PHP 8.3+
- Laravel 12 or 13

## Installation

```bash
composer require itmosoft/laravel-customers
php artisan vendor:publish --tag=customers-config
php artisan migrate
```

## Create a customer

```php
use ItmoSoft\Customers\CustomersManager;

$customer = app(CustomersManager::class)->create([
    'first_name' => 'Jane',
    'last_name' => 'Doe',
    'email' => 'jane@example.com',
    'phone' => '+15551234567',
]);
```

Only the package's documented customer attributes are persisted by the manager. The host application should validate the input before calling it.

## Manage addresses

```php
$address = app(CustomersManager::class)->addAddress($customer, [
    'type' => 'shipping',
    'first_name' => 'Jane',
    'last_name' => 'Doe',
    'address_line_1' => '10 Main Street',
    'city' => 'Austin',
    'country_code' => 'US',
    'is_default' => true,
]);

$customer->defaultShippingAddress();
$customer->defaultBillingAddress();
```

To change the default address:

```php
app(CustomersManager::class)->setDefaultAddress($address);
```

Default addresses are unique per customer and address type at the manager-operation level. If your application permits concurrent address updates, serialize those operations in the application or add a database-specific uniqueness strategy.

## Verification timestamps

```php
app(CustomersManager::class)->markEmailVerified($customer);
app(CustomersManager::class)->markPhoneVerified($customer);
```

These methods only set timestamps. Your application must perform the actual verification challenge and authorization.

## Configuration

Publish `customers-config` to customize:

- `table_prefix`
- `models.customer`
- `models.address`
- `default_address_type`
- `address_types`

The `customer_number` configuration is reserved for application-specific numbering workflows; this initial release does not automatically generate customer numbers.

## Testing

```bash
composer install
composer validate --strict
composer test
```

## Scope and integration boundaries

This package stores customer profiles and addresses. It does not create authentication accounts, passwords, login sessions, customer-group membership, GDPR consent records, marketing subscriptions, order history, tax identities, or tenant authorization. The host application owns those policies and integrations.

The customer tables intentionally do not reference Laravel's `users` table. Applications can associate authentication users with customer records using their own schema.

## License

MIT.
