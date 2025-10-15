# MarzPay PHP SDK

A comprehensive PHP SDK for the MarzPay API, providing seamless integration for payment collections, disbursements, and phone verification services in Uganda.

## 🚀 Features

- **💰 Collect Money**: Accept payments from customers via MTN, Airtel, and other providers
- **💸 Send Money**: Send money to recipients using various payment providers
- **📱 Phone Verification**: Verify phone numbers and retrieve user information
- **🔧 Laravel Integration**: Full Laravel support with ServiceProvider and Facade
- **🛡️ Type Safety**: Comprehensive error handling and validation
- **📚 Well Documented**: Extensive documentation and examples

## 📖 Documentation

- **[Complete Guide](README.md)** - This file with basic usage examples
- **[Laravel Integration Guide](README-LARAVEL.md)** - Complete Laravel integration guide
- **[API Reference](API-REFERENCE.md)** - Detailed API documentation
- **[Testing Guide](README-TESTING.md)** - Testing setup and examples

## 📦 Installation

### Via Composer

```bash
composer require marzpay/php-sdk
```

### Manual Installation

1. Download the SDK files
2. Include the autoloader:

```php
require_once 'vendor/autoload.php';
```

## ⚙️ Configuration

### Environment Variables

Create a `.env` file or set environment variables:

```env
MARZPAY_API_KEY=your_api_key_here
MARZPAY_API_SECRET=your_api_secret_here
MARZPAY_BASE_URL=https://wallet.wearemarz.com/api/v1
MARZPAY_TIMEOUT=30
```

### Basic Usage

```php
<?php

require_once 'vendor/autoload.php';

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

// Initialize the SDK
$config = [
    'api_key' => 'your_api_key',
    'api_secret' => 'your_api_secret',
    'base_url' => 'https://wallet.wearemarz.com/api/v1',
    'timeout' => 30
];

$marzpay = new MarzPay($config);
```

## 💰 Collect Money

Accept payments from customers:

```php
try {
    $collection = $marzpay->collections()->collectMoney([
        'phone_number' => '0759983853',
        'amount' => 1000,
        'country' => 'UG',
        'description' => 'Payment for services'
    ]);
    
    echo "Collection initiated: " . $collection['data']['transaction']['uuid'] . "\n";
    echo "Amount: " . $collection['data']['collection']['amount']['formatted'] . "\n";
    echo "Status: " . $collection['data']['transaction']['status'] . "\n";
    
} catch (MarzPayException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Get Available Services

```php
$services = $marzpay->collections()->getServices();
echo "Available providers: " . json_encode($services['data']['countries']['UG']['providers']);
```

### Get Collection Details

```php
$details = $marzpay->collections()->getCollectionDetails($uuid);
echo "Status: " . $details['data']['transaction']['status'] . "\n";
```

## 💸 Send Money

Send money to recipients:

```php
try {
    $disbursement = $marzpay->disbursements()->sendMoney([
        'phone_number' => '0759983853',
        'amount' => 1000,
        'country' => 'UG',
        'description' => 'Payment to customer'
    ]);
    
    echo "Send money initiated: " . $disbursement['data']['transaction']['uuid'] . "\n";
    echo "Amount: " . $disbursement['data']['withdrawal']['amount']['formatted'] . "\n";
    echo "Provider: " . $disbursement['data']['withdrawal']['provider'] . "\n";
    
} catch (MarzPayException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Get Send Money Details

```php
$details = $marzpay->disbursements()->getSendMoneyDetails($uuid);
echo "Status: " . $details['data']['transaction']['status'] . "\n";
```

## 📱 Phone Verification

Verify phone numbers and get user information:

```php
try {
    $verification = $marzpay->phoneVerification()->verifyPhoneNumber('0759983853');
    
    if ($verification['success']) {
        echo "User: " . $verification['data']['full_name'] . "\n";
        echo "Phone: " . $verification['data']['phone_number'] . "\n";
        echo "Status: " . $verification['data']['verification_status'] . "\n";
    }
    
} catch (MarzPayException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
```

### Get Service Information

```php
$serviceInfo = $marzpay->phoneVerification()->getServiceInfo();
echo "Service: " . $serviceInfo['data']['service_name'] . "\n";
echo "Active: " . ($serviceInfo['data']['is_active'] ? 'Yes' : 'No') . "\n";
```

## 🔧 Laravel Integration

### Installation

1. Install the package via Composer
2. Register the service provider in `config/app.php`:

```php
'providers' => [
    // ...
    MarzPay\Laravel\Providers\MarzPayServiceProvider::class,
],
```

3. Add the facade alias in `config/app.php`:

```php
'aliases' => [
    // ...
    'MarzPay' => MarzPay\Laravel\Facades\MarzPay::class,
],
```

4. Publish the configuration file:

```bash
php artisan vendor:publish --tag=marzpay-config
```

### Laravel Usage

#### Using the Facade

```php
<?php

use MarzPay\Facades\MarzPay;

class PaymentController extends Controller
{
    public function collectMoney(Request $request)
    {
        try {
            $collection = MarzPay::collections()->collectMoney([
                'phone_number' => $request->phone_number,
                'amount' => $request->amount,
                'country' => 'UG',
                'description' => 'Payment for services'
            ]);
            
            return response()->json($collection);
            
        } catch (\MarzPay\Exceptions\MarzPayException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    public function sendMoney(Request $request)
    {
        try {
            $disbursement = MarzPay::disbursements()->sendMoney([
                'phone_number' => $request->phone_number,
                'amount' => $request->amount,
                'country' => 'UG',
                'description' => 'Payment to customer'
            ]);
            
            return response()->json($disbursement);
            
        } catch (\MarzPay\Exceptions\MarzPayException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    
    public function verifyPhone(Request $request)
    {
        try {
            $verification = MarzPay::phoneVerification()->verifyPhoneNumber($request->phone_number);
            
            return response()->json($verification);
            
        } catch (\MarzPay\Exceptions\MarzPayException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

#### Using Dependency Injection

```php
<?php

use MarzPay\MarzPay;

class PaymentService
{
    protected $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    public function processPayment($phoneNumber, $amount)
    {
        return $this->marzpay->collections()->collectMoney([
            'phone_number' => $phoneNumber,
            'amount' => $amount,
            'country' => 'UG'
        ]);
    }
}
```

### Laravel Configuration

The configuration file `config/marzpay.php`:

```php
<?php

return [
    'api_key' => env('MARZPAY_API_KEY'),
    'api_secret' => env('MARZPAY_API_SECRET'),
    'base_url' => env('MARZPAY_BASE_URL', 'https://wallet.wearemarz.com/api/v1'),
    'timeout' => env('MARZPAY_TIMEOUT', 30),
];
```

## 🧪 Testing

### Running Tests

```bash
# Run unit tests
php run-tests.php unit

# Run integration tests (requires real API credentials)
php run-tests.php integration

# Run all tests
php run-tests.php all

# Run tests with coverage
php run-tests.php coverage
```

### Test Configuration

1. Copy `env.testing` to `.env`
2. Add your real API credentials to `.env`
3. Run tests

## 📋 API Reference

### Collections API

- `collectMoney($params)` - Initiate a payment collection
- `getServices()` - Get available collection services
- `getCollectionDetails($uuid)` - Get collection details by UUID

### Disbursements API

- `sendMoney($params)` - Send money to recipient
- `getServices()` - Get available disbursement services
- `getSendMoneyDetails($uuid)` - Get send money details by UUID

### Phone Verification API

- `verifyPhoneNumber($phoneNumber)` - Verify phone number
- `getServiceInfo()` - Get service information
- `getSubscriptionStatus()` - Check subscription status

## 🔒 Error Handling

The SDK provides comprehensive error handling:

```php
try {
    $result = $marzpay->collections()->collectMoney($params);
} catch (MarzPayException $e) {
    echo "Error Code: " . $e->getErrorCode() . "\n";
    echo "Status: " . $e->getStatus() . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    
    if ($e->getResponseData()) {
        echo "Response: " . json_encode($e->getResponseData()) . "\n";
    }
}
```

## 📞 Phone Number Formatting

The SDK automatically handles phone number formatting:

- Input: `0759983853` → Output: `+256759983853`
- Input: `+256759983853` → Output: `+256759983853`
- Input: `256759983853` → Output: `+256759983853`

## 🆔 UUID Generation

The SDK automatically generates valid UUID v4 references when not provided:

```php
// Automatic UUID generation
$collection = $marzpay->collections()->collectMoney([
    'phone_number' => '0759983853',
    'amount' => 1000
    // reference will be auto-generated
]);

// Custom UUID
$collection = $marzpay->collections()->collectMoney([
    'phone_number' => '0759983853',
    'amount' => 1000,
    'reference' => 'your-custom-uuid-here'
]);
```

## 🔗 Webhooks

The SDK supports webhook callbacks:

```php
$collection = $marzpay->collections()->collectMoney([
    'phone_number' => '0759983853',
    'amount' => 1000,
    'callback_url' => 'https://your-app.com/webhook'
]);
```

## 📊 Response Format

All API responses follow a consistent format:

```json
{
    "status": "success",
    "message": "Collection initiated successfully.",
    "data": {
        "transaction": {
            "uuid": "123e4567-e89b-12d3-a456-426614174000",
            "reference": "your-reference",
            "status": "processing"
        },
        "collection": {
            "amount": {
                "formatted": "1,000.00",
                "raw": "1000",
                "currency": "UGX"
            },
            "provider": "airtel",
            "phone_number": "+256759983853"
        },
        "timeline": {
            "initiated_at": "2025-10-15 03:01:14",
            "estimated_settlement": "2025-10-15 03:06:14"
        }
    }
}
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:

- 📧 Email: support@wearemarz.com
- 📚 Documentation: [API Documentation](https://docs.wearemarz.com)
- 🐛 Issues: [GitHub Issues](https://github.com/marzpay/php-sdk/issues)

## 🎉 Changelog

### Version 1.0.0
- Initial release
- Collections API support
- Disbursements API support
- Phone Verification API support
- Laravel integration
- Comprehensive error handling
- Automatic UUID generation
- Phone number formatting
- Full test coverage

---

**Made with ❤️ by the MarzPay team**