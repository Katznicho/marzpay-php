# MarzPay PHP SDK

Official PHP SDK for MarzPay - Mobile Money Payment Platform for Uganda.

## Features

- 🚀 **Complete API Coverage** - Collections, Disbursements, Accounts, Balance, Transactions, Services, Webhooks, and Phone Verification
- 🛡️ **Laravel Integration** - Native Laravel service provider and facade support
- 🔧 **Error Handling** - Comprehensive error handling with custom exception classes
- 📱 **Phone Number Utilities** - Built-in phone number validation and formatting
- 🔗 **Webhook Support** - Easy webhook handling and validation
- 🧪 **Testing** - Full test coverage with PHPUnit
- 📚 **Documentation** - Comprehensive documentation and examples

## Installation

### Composer

```bash
composer require marzpay/php-sdk
```

### Laravel

The package will automatically register the service provider and facade.

## Quick Start

### Basic Usage

```php
<?php

use MarzPay\MarzPay;

$marzpay = new MarzPay([
    'api_key' => 'your_api_key',
    'api_secret' => 'your_api_secret'
]);

// Collect money from customer
$result = $marzpay->collections()->collectMoney([
    'amount' => 5000,
    'phone_number' => '0759983853',
    'reference' => $marzpay->collections()->generateReference(),
    'description' => 'Payment for services'
]);
```

### Laravel Usage

```php
<?php

// Using the facade
use MarzPay;

$result = MarzPay::collections()->collectMoney([
    'amount' => 5000,
    'phone_number' => '0759983853',
    'reference' => MarzPay::collections()->generateReference(),
    'description' => 'Payment for services'
]);

// Using dependency injection
public function __construct(MarzPay $marzpay)
{
    $this->marzpay = $marzpay;
}
```

## API Reference

### Collections API

```php
// Collect money
$result = $marzpay->collections()->collectMoney([
    'amount' => 10000,
    'phone_number' => '0759983853',
    'reference' => 'unique-reference-id',
    'description' => 'Payment for services'
]);

// Get collection details
$collection = $marzpay->collections()->getCollection('collection-id');

// Get available services
$services = $marzpay->collections()->getServices();
```

### Disbursements API

```php
// Send money
$result = $marzpay->disbursements()->sendMoney([
    'amount' => 5000,
    'phone_number' => '0759983853',
    'reference' => 'unique-reference-id',
    'description' => 'Refund payment'
]);

// Get disbursement details
$disbursement = $marzpay->disbursements()->getDisbursement('disbursement-id');
```

### Webhooks

```php
// Handle webhook
$webhook = $marzpay->webhooks()->handleWebhook($request->getContent());

if ($webhook->isValid()) {
    // Process the webhook
    $transactionId = $webhook->getTransactionId();
    $status = $webhook->getStatus();
}
```

## Configuration

### Environment Variables

```env
MARZPAY_API_KEY=your_api_key
MARZPAY_API_SECRET=your_api_secret
MARZPAY_BASE_URL=https://wallet.wearemarz.com/api/v1
MARZPAY_TIMEOUT=30
```

### Laravel Configuration

```php
// config/marzpay.php
return [
    'api_key' => env('MARZPAY_API_KEY'),
    'api_secret' => env('MARZPAY_API_SECRET'),
    'base_url' => env('MARZPAY_BASE_URL', 'https://wallet.wearemarz.com/api/v1'),
    'timeout' => env('MARZPAY_TIMEOUT', 30),
];
```

## Error Handling

```php
use MarzPay\Exceptions\MarzPayException;

try {
    $result = $marzpay->collections()->collectMoney($params);
} catch (MarzPayException $e) {
    echo "Error: " . $e->getMessage();
    echo "Code: " . $e->getCode();
    echo "Status: " . $e->getStatus();
}
```

## Testing

```bash
composer test
composer test-coverage
```

## License

MIT License. See [LICENSE](LICENSE) for details.

## Support

- Documentation: [https://docs.marzpay.com](https://wallet.wearemarz.com/documentation)
- Issues: [https://github.com/Katznicho/marzpay-php/issues](https://github.com/Katznicho/marzpay-php/issues)
- Email: dev@wearemarz.com
