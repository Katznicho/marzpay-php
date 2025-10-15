# MarzPay PHP SDK - API Reference

Complete API reference for the MarzPay PHP SDK.

## Table of Contents

- [Core Classes](#core-classes)
- [Collections API](#collections-api)
- [Disbursements API](#disbursements-api)
- [Phone Verification API](#phone-verification-api)
- [Exceptions](#exceptions)
- [Response Formats](#response-formats)

## Core Classes

### MarzPay

The main SDK class that provides access to all API modules.

#### Constructor

```php
new MarzPay(array $config)
```

**Parameters:**
- `$config` (array) - Configuration array with the following keys:
  - `api_key` (string, required) - Your MarzPay API key
  - `api_secret` (string, required) - Your MarzPay API secret
  - `base_url` (string, optional) - API base URL (default: `https://wallet.wearemarz.com/api/v1`)
  - `timeout` (int, optional) - Request timeout in seconds (default: `30`)

**Example:**
```php
$marzpay = new MarzPay([
    'api_key' => 'your_api_key',
    'api_secret' => 'your_api_secret',
    'base_url' => 'https://wallet.wearemarz.com/api/v1',
    'timeout' => 30
]);
```

#### Methods

##### `collections()`
Returns the Collections API instance.

```php
$collections = $marzpay->collections();
```

##### `disbursements()`
Returns the Disbursements API instance.

```php
$disbursements = $marzpay->disbursements();
```

##### `phoneVerification()`
Returns the Phone Verification API instance.

```php
$phoneVerification = $marzpay->phoneVerification();
```

##### `getInfo()`
Returns SDK information.

```php
$info = $marzpay->getInfo();
```

##### `testConnection()`
Tests the API connection.

```php
$result = $marzpay->testConnection();
```

##### `getAuthHeader()`
Returns the authentication header string.

```php
$authHeader = $marzpay->getAuthHeader(); // "Basic base64_encoded_credentials"
```

## Collections API

### Methods

#### `collectMoney(array $params)`

Initiates a payment collection from a customer.

**Parameters:**
- `$params` (array) - Collection parameters:
  - `phone_number` (string, required) - Customer's phone number
  - `amount` (int|string, required) - Amount to collect (in smallest currency unit)
  - `country` (string, optional) - Country code (default: `UG`)
  - `reference` (string, optional) - Custom reference (auto-generated if not provided)
  - `description` (string, optional) - Payment description (default: `Payment for services`)
  - `callback_url` (string, optional) - Webhook callback URL

**Returns:** `array` - Collection response

**Example:**
```php
$collection = $marzpay->collections()->collectMoney([
    'phone_number' => '0759983853',
    'amount' => 1000,
    'country' => 'UG',
    'reference' => 'ORDER-123',
    'description' => 'Payment for services',
    'callback_url' => 'https://your-app.com/webhook'
]);
```

#### `getServices()`

Gets available collection services and providers.

**Returns:** `array` - Available services

**Example:**
```php
$services = $marzpay->collections()->getServices();
```

#### `getCollectionDetails(string $uuid)`

Gets collection details by UUID.

**Parameters:**
- `$uuid` (string, required) - Collection UUID

**Returns:** `array` - Collection details

**Example:**
```php
$details = $marzpay->collections()->getCollectionDetails('123e4567-e89b-12d3-a456-426614174000');
```

## Disbursements API

### Methods

#### `sendMoney(array $params)`

Sends money to a recipient.

**Parameters:**
- `$params` (array) - Disbursement parameters:
  - `phone_number` (string, required) - Recipient's phone number
  - `amount` (int|string, required) - Amount to send (in smallest currency unit)
  - `country` (string, optional) - Country code (default: `UG`)
  - `reference` (string, optional) - Custom reference (auto-generated if not provided)
  - `description` (string, optional) - Payment description (default: `Payment to customer`)
  - `callback_url` (string, optional) - Webhook callback URL

**Returns:** `array` - Disbursement response

**Example:**
```php
$disbursement = $marzpay->disbursements()->sendMoney([
    'phone_number' => '0759983853',
    'amount' => 1000,
    'country' => 'UG',
    'reference' => 'PAYOUT-123',
    'description' => 'Payment to customer',
    'callback_url' => 'https://your-app.com/webhook'
]);
```

#### `getServices()`

Gets available disbursement services and providers.

**Returns:** `array` - Available services

**Example:**
```php
$services = $marzpay->disbursements()->getServices();
```

#### `getSendMoneyDetails(string $uuid)`

Gets disbursement details by UUID.

**Parameters:**
- `$uuid` (string, required) - Disbursement UUID

**Returns:** `array` - Disbursement details

**Example:**
```php
$details = $marzpay->disbursements()->getSendMoneyDetails('123e4567-e89b-12d3-a456-426614174000');
```

#### `getDisbursement(string $disbursementId)`

Alias for `getSendMoneyDetails()`.

**Parameters:**
- `$disbursementId` (string, required) - Disbursement UUID

**Returns:** `array` - Disbursement details

## Phone Verification API

### Methods

#### `verifyPhoneNumber(string $phoneNumber)`

Verifies a phone number and retrieves associated user information.

**Parameters:**
- `$phoneNumber` (string, required) - Phone number to verify

**Returns:** `array` - Verification response

**Example:**
```php
$verification = $marzpay->phoneVerification()->verifyPhoneNumber('0759983853');
```

#### `getServiceInfo()`

Gets phone verification service information.

**Returns:** `array` - Service information

**Example:**
```php
$serviceInfo = $marzpay->phoneVerification()->getServiceInfo();
```

#### `getSubscriptionStatus()`

Gets subscription status for phone verification service.

**Returns:** `array` - Subscription status

**Example:**
```php
$status = $marzpay->phoneVerification()->getSubscriptionStatus();
```

## ❌ Exceptions

### MarzPayException

The main exception class for MarzPay SDK errors.

#### Constructor

```php
new MarzPayException(string $message, string $errorCode = null, int $statusCode = null, array $responseData = null)
```

#### Methods

##### `getErrorCode()`
Returns the error code.

##### `getStatus()`
Returns the HTTP status code.

##### `getResponseData()`
Returns the raw response data.

##### `setErrorCode(string $errorCode)`
Sets the error code.

##### `setStatus(int $status)`
Sets the HTTP status code.

##### `setResponseData(array $responseData)`
Sets the response data.

##### `fromApiResponse(array $response)`
Creates a MarzPayException from an API response.

**Example:**
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

## Response Formats

### Collection Response

```json
{
    "status": "success",
    "message": "Collection initiated successfully.",
    "data": {
        "transaction": {
            "uuid": "123e4567-e89b-12d3-a456-426614174000",
            "reference": "ORDER-123",
            "status": "processing",
            "provider_reference": null
        },
        "collection": {
            "amount": {
                "formatted": "1,000.00",
                "raw": "1000",
                "currency": "UGX"
            },
            "provider": "airtel",
            "phone_number": "+256759983853",
            "mode": "airteluganda"
        },
        "timeline": {
            "initiated_at": "2025-10-15 03:01:14",
            "estimated_settlement": "2025-10-15 03:06:14"
        },
        "metadata": {
            "response_timestamp": "2025-10-15 03:01:14",
            "sandbox_mode": false
        }
    }
}
```

### Disbursement Response

```json
{
    "status": "success",
    "message": "Send money initiated successfully.",
    "data": {
        "transaction": {
            "uuid": "4e7fb3fa-c13a-4b05-8acd-cf60ff68cb94",
            "reference": "PAYOUT-123",
            "status": "pending",
            "provider_reference": "MTN_REF_123456789"
        },
        "withdrawal": {
            "amount": {
                "formatted": "1,000.00",
                "raw": "1000",
                "currency": "UGX"
            },
            "charge": {
                "formatted": "100.00",
                "raw": "100",
                "currency": "UGX"
            },
            "provider": "Airtel",
            "phone_number": "+256759983853",
            "description": "Payment to customer"
        },
        "timeline": {
            "created_at": "2025-10-15 03:06:20",
            "updated_at": "2025-10-15 03:06:20"
        },
        "metadata": {
            "response_timestamp": "2025-10-15 03:06:20"
        }
    }
}
```

### Phone Verification Response

```json
{
    "success": true,
    "message": "Phone number verified successfully",
    "data": {
        "phone_number": "256759983853",
        "first_name": "JOHN",
        "last_name": "MUSINGUZI",
        "full_name": "JOHN MUSINGUZI",
        "verification_status": "verified"
    },
    "phone_number": "256759983853",
    "verified_at": "2025-10-15T03:01:14.000000Z"
}
```

### Services Response

```json
{
    "status": "success",
    "data": {
        "account": {
            "uuid": "73a2f46c-5a87-4e18-8f0f-6e191abeb108"
        },
        "countries": {
            "UG": {
                "country_code": "UG",
                "country_name": "Uganda",
                "providers": [
                    {
                        "provider": "MTN",
                        "name": "MTN Collection",
                        "currency": "UGX",
                        "mode": "mtnuganda",
                        "subscription_uuid": "c3f403c0-4bb5-426a-8f27-5e9cf7eb63b9"
                    },
                    {
                        "provider": "Airtel",
                        "name": "Airtel Collection",
                        "currency": "UGX",
                        "mode": "airteluganda",
                        "subscription_uuid": "4fede4c6-de97-43ee-91db-355bc050b573"
                    }
                ],
                "total_providers": 2
            }
        },
        "summary": {
            "total_countries": 1,
            "total_services": 2
        },
        "metadata": {
            "response_timestamp": "2025-10-15 03:04:43"
        }
    }
}
```

### Error Response

```json
{
    "success": false,
    "message": "Validation failed",
    "error": "Phone number is required",
    "errors": {
        "phone_number": ["The phone number field is required."],
        "amount": ["The amount must be at least 100."]
    }
}
```

## Utility Methods

### Phone Number Formatting

The SDK automatically handles phone number formatting:

- Input: `0759983853` → Output: `+256759983853`
- Input: `+256759983853` → Output: `+256759983853`
- Input: `256759983853` → Output: `+256759983853`

### UUID Generation

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

## 🌐 HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | Success |
| 400 | Bad Request - Invalid parameters |
| 401 | Unauthorized - Invalid API credentials |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation failed |
| 429 | Too Many Requests - Rate limit exceeded |
| 500 | Internal Server Error |

## Webhooks

### Webhook Events

The SDK supports webhook callbacks for the following events:

- `collection.completed` - Collection successfully completed
- `collection.failed` - Collection failed
- `disbursement.completed` - Disbursement successfully completed
- `disbursement.failed` - Disbursement failed

### Webhook Payload

```json
{
    "event_type": "collection.completed",
    "data": {
        "transaction": {
            "uuid": "123e4567-e89b-12d3-a456-426614174000",
            "reference": "ORDER-123",
            "status": "completed"
        },
        "collection": {
            "amount": {
                "formatted": "1,000.00",
                "raw": "1000",
                "currency": "UGX"
            },
            "provider": "airtel",
            "phone_number": "+256759983853"
        }
    },
    "timestamp": "2025-10-15T03:06:14.000000Z"
}
```

## 📝 Best Practices

### 1. Error Handling

Always wrap API calls in try-catch blocks:

```php
try {
    $result = $marzpay->collections()->collectMoney($params);
} catch (MarzPayException $e) {
    // Handle error appropriately
    Log::error('MarzPay API Error', [
        'message' => $e->getMessage(),
        'code' => $e->getErrorCode(),
        'status' => $e->getStatus()
    ]);
}
```

### 2. Validation

Validate input parameters before making API calls:

```php
if (empty($params['phone_number'])) {
    throw new InvalidArgumentException('Phone number is required');
}

if (!is_numeric($params['amount']) || $params['amount'] <= 0) {
    throw new InvalidArgumentException('Amount must be a positive number');
}
```

### 3. Logging

Log important operations for debugging and monitoring:

```php
Log::info('Payment initiated', [
    'uuid' => $result['data']['transaction']['uuid'],
    'amount' => $result['data']['collection']['amount']['raw'],
    'phone' => $result['data']['collection']['phone_number']
]);
```

### 4. Timeout Configuration

Set appropriate timeouts for your use case:

```php
$marzpay = new MarzPay([
    'api_key' => 'your_api_key',
    'api_secret' => 'your_api_secret',
    'timeout' => 60 // 60 seconds for long-running operations
]);
```

## Debugging

### Enable Debug Mode

Set a longer timeout and check response details:

```php
$marzpay = new MarzPay([
    'api_key' => 'your_api_key',
    'api_secret' => 'your_api_secret',
    'timeout' => 120 // Longer timeout for debugging
]);

try {
    $result = $marzpay->collections()->collectMoney($params);
    var_dump($result); // Debug output
} catch (MarzPayException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getErrorCode() . "\n";
    echo "Status: " . $e->getStatus() . "\n";
    if ($e->getResponseData()) {
        echo "Response: " . json_encode($e->getResponseData(), JSON_PRETTY_PRINT) . "\n";
    }
}
```

---

**For more information, visit the [MarzPay Documentation](https://docs.wearemarz.com)**
