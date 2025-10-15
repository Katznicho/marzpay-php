# MarzPay PHP SDK - Laravel Integration Guide

Complete guide for integrating the MarzPay PHP SDK with Laravel applications.

## 🚀 Quick Start

### 1. Installation

```bash
composer require marzpay/php-sdk
```

### 2. Laravel Auto-Discovery (Recommended)

Laravel will automatically register the service provider and facade. No additional configuration needed!

**For Laravel 5.5+**: The package supports auto-discovery, so the service provider and facade will be automatically registered.

**For older Laravel versions or manual setup**, add to `config/app.php`:

```php
'providers' => [
    // ...
    MarzPay\Laravel\Providers\MarzPayServiceProvider::class,
],

'aliases' => [
    // ...
    'MarzPay' => MarzPay\Laravel\Facades\MarzPay::class,
],
```

### 3. Publish Configuration

```bash
php artisan vendor:publish --tag=marzpay-config
```

### 4. Configure Environment

Add to your `.env` file:

```env
MARZPAY_API_KEY=your_api_key_here
MARZPAY_API_SECRET=your_api_secret_here
MARZPAY_BASE_URL=https://wallet.wearemarz.com/api/v1
MARZPAY_TIMEOUT=30
```

## 📝 Usage Examples

### Using the Facade

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MarzPay\Facades\MarzPay;

class PaymentController extends Controller
{
    public function collectMoney(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'description' => 'nullable|string'
        ]);

        try {
            $collection = MarzPay::collections()->collectMoney([
                'phone_number' => $request->phone_number,
                'amount' => $request->amount,
                'country' => 'UG',
                'description' => $request->description ?? 'Payment for services'
            ]);

            return response()->json([
                'success' => true,
                'data' => $collection['data']
            ]);

        } catch (\MarzPay\Exceptions\MarzPayException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function sendMoney(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'description' => 'nullable|string'
        ]);

        try {
            $disbursement = MarzPay::disbursements()->sendMoney([
                'phone_number' => $request->phone_number,
                'amount' => $request->amount,
                'country' => 'UG',
                'description' => $request->description ?? 'Payment to customer'
            ]);

            return response()->json([
                'success' => true,
                'data' => $disbursement['data']
            ]);

        } catch (\MarzPay\Exceptions\MarzPayException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function verifyPhone(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string'
        ]);

        try {
            $verification = MarzPay::phoneVerification()->verifyPhoneNumber($request->phone_number);

            return response()->json([
                'success' => $verification['success'],
                'data' => $verification['data'] ?? null,
                'message' => $verification['message']
            ]);

        } catch (\MarzPay\Exceptions\MarzPayException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
```

### Using Dependency Injection

```php
<?php

namespace App\Services;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class PaymentService
{
    protected $marzpay;

    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }

    public function collectPayment($phoneNumber, $amount, $description = null)
    {
        try {
            return $this->marzpay->collections()->collectMoney([
                'phone_number' => $phoneNumber,
                'amount' => $amount,
                'country' => 'UG',
                'description' => $description ?? 'Payment for services'
            ]);
        } catch (MarzPayException $e) {
            throw new \Exception('Payment collection failed: ' . $e->getMessage());
        }
    }

    public function sendPayment($phoneNumber, $amount, $description = null)
    {
        try {
            return $this->marzpay->disbursements()->sendMoney([
                'phone_number' => $phoneNumber,
                'amount' => $amount,
                'country' => 'UG',
                'description' => $description ?? 'Payment to customer'
            ]);
        } catch (MarzPayException $e) {
            throw new \Exception('Payment disbursement failed: ' . $e->getMessage());
        }
    }

    public function verifyPhoneNumber($phoneNumber)
    {
        try {
            return $this->marzpay->phoneVerification()->verifyPhoneNumber($phoneNumber);
        } catch (MarzPayException $e) {
            throw new \Exception('Phone verification failed: ' . $e->getMessage());
        }
    }
}
```

### Using in Controllers with Services

```php
<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function collectMoney(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'description' => 'nullable|string'
        ]);

        try {
            $result = $this->paymentService->collectPayment(
                $request->phone_number,
                $request->amount,
                $request->description
            );

            return response()->json([
                'success' => true,
                'data' => $result['data']
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
```

## 🔧 Configuration

### Configuration File

The published configuration file `config/marzpay.php`:

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MarzPay API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the MarzPay API client.
    | You can set these values in your .env file or directly in this config.
    |
    */

    'api_key' => env('MARZPAY_API_KEY'),
    'api_secret' => env('MARZPAY_API_SECRET'),
    'base_url' => env('MARZPAY_BASE_URL', 'https://wallet.wearemarz.com/api/v1'),
    'timeout' => env('MARZPAY_TIMEOUT', 30),
];
```

### Environment Variables

```env
# MarzPay API Configuration
MARZPAY_API_KEY=your_api_key_here
MARZPAY_API_SECRET=your_api_secret_here
MARZPAY_BASE_URL=https://wallet.wearemarz.com/api/v1
MARZPAY_TIMEOUT=30
```

## 🛣️ Routes

Add routes to your `routes/web.php` or `routes/api.php`:

```php
// API Routes
Route::prefix('api/payments')->group(function () {
    Route::post('/collect', [PaymentController::class, 'collectMoney']);
    Route::post('/send', [PaymentController::class, 'sendMoney']);
    Route::post('/verify-phone', [PaymentController::class, 'verifyPhone']);
    Route::get('/services/collections', function () {
        return MarzPay::collections()->getServices();
    });
    Route::get('/services/disbursements', function () {
        return MarzPay::disbursements()->getServices();
    });
    Route::get('/collections/{uuid}', function ($uuid) {
        return MarzPay::collections()->getCollectionDetails($uuid);
    });
    Route::get('/disbursements/{uuid}', function ($uuid) {
        return MarzPay::disbursements()->getSendMoneyDetails($uuid);
    });
});
```

## 🧪 Testing

### Feature Tests

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use MarzPay\Facades\MarzPay;

class PaymentTest extends TestCase
{
    public function test_can_collect_money()
    {
        $response = $this->postJson('/api/payments/collect', [
            'phone_number' => '0759983853',
            'amount' => 1000,
            'description' => 'Test payment'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'transaction' => [
                            'uuid',
                            'reference',
                            'status'
                        ],
                        'collection' => [
                            'amount',
                            'provider',
                            'phone_number'
                        ]
                    ]
                ]);
    }

    public function test_can_send_money()
    {
        $response = $this->postJson('/api/payments/send', [
            'phone_number' => '0759983853',
            'amount' => 1000,
            'description' => 'Test disbursement'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'transaction' => [
                            'uuid',
                            'reference',
                            'status'
                        ],
                        'withdrawal' => [
                            'amount',
                            'provider',
                            'phone_number'
                        ]
                    ]
                ]);
    }

    public function test_can_verify_phone()
    {
        $response = $this->postJson('/api/payments/verify-phone', [
            'phone_number' => '0759983853'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'data' => [
                        'phone_number',
                        'full_name',
                        'verification_status'
                    ],
                    'message'
                ]);
    }
}
```

### Unit Tests

```php
<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PaymentService;
use MarzPay\Facades\MarzPay;

class PaymentServiceTest extends TestCase
{
    protected $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = app(PaymentService::class);
    }

    public function test_payment_service_can_collect_money()
    {
        $result = $this->paymentService->collectPayment('0759983853', 1000);
        
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('transaction', $result['data']);
        $this->assertEquals('processing', $result['data']['transaction']['status']);
    }

    public function test_payment_service_can_send_money()
    {
        $result = $this->paymentService->sendPayment('0759983853', 1000);
        
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('transaction', $result['data']);
    }

    public function test_payment_service_can_verify_phone()
    {
        $result = $this->paymentService->verifyPhoneNumber('0759983853');
        
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('data', $result);
    }
}
```

## 🔔 Webhooks

### Webhook Controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleMarzPayWebhook(Request $request)
    {
        Log::info('MarzPay Webhook Received', $request->all());

        $payload = $request->all();
        
        // Verify webhook signature if needed
        // $this->verifyWebhookSignature($request);

        // Process the webhook based on the event type
        switch ($payload['event_type'] ?? '') {
            case 'collection.completed':
                $this->handleCollectionCompleted($payload);
                break;
            case 'collection.failed':
                $this->handleCollectionFailed($payload);
                break;
            case 'disbursement.completed':
                $this->handleDisbursementCompleted($payload);
                break;
            case 'disbursement.failed':
                $this->handleDisbursementFailed($payload);
                break;
            default:
                Log::warning('Unknown webhook event type', $payload);
        }

        return response()->json(['status' => 'received']);
    }

    private function handleCollectionCompleted($payload)
    {
        // Update your database
        // Send notifications
        // Update order status
        Log::info('Collection completed', $payload);
    }

    private function handleCollectionFailed($payload)
    {
        // Handle failed collection
        // Send failure notification
        Log::warning('Collection failed', $payload);
    }

    private function handleDisbursementCompleted($payload)
    {
        // Update disbursement status
        Log::info('Disbursement completed', $payload);
    }

    private function handleDisbursementFailed($payload)
    {
        // Handle failed disbursement
        Log::warning('Disbursement failed', $payload);
    }
}
```

### Webhook Routes

```php
// Add to routes/web.php
Route::post('/webhooks/marzpay', [WebhookController::class, 'handleMarzPayWebhook']);
```

## 📊 Database Integration

### Migration for Payment Records

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('marzpay_uuid')->unique();
            $table->string('reference')->unique();
            $table->string('type'); // 'collection' or 'disbursement'
            $table->string('phone_number');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('UGX');
            $table->string('provider');
            $table->string('status');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('initiated_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['type', 'status']);
            $table->index('phone_number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
```

### Payment Model

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'marzpay_uuid',
        'reference',
        'type',
        'phone_number',
        'amount',
        'currency',
        'provider',
        'status',
        'description',
        'metadata',
        'initiated_at',
        'completed_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'amount' => 'decimal:2',
        'initiated_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function isCollection()
    {
        return $this->type === 'collection';
    }

    public function isDisbursement()
    {
        return $this->type === 'disbursement';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}
```

## 🎯 Best Practices

### 1. Error Handling

Always wrap MarzPay API calls in try-catch blocks:

```php
try {
    $result = MarzPay::collections()->collectMoney($params);
} catch (\MarzPay\Exceptions\MarzPayException $e) {
    // Log the error
    Log::error('MarzPay API Error', [
        'message' => $e->getMessage(),
        'code' => $e->getErrorCode(),
        'status' => $e->getStatus(),
        'params' => $params
    ]);
    
    // Return appropriate response
    return response()->json(['error' => 'Payment processing failed'], 400);
}
```

### 2. Validation

Always validate input parameters:

```php
$request->validate([
    'phone_number' => 'required|string|regex:/^[0-9+]{10,15}$/',
    'amount' => 'required|numeric|min:100|max:1000000',
    'description' => 'nullable|string|max:255'
]);
```

### 3. Logging

Log important operations:

```php
Log::info('Payment initiated', [
    'uuid' => $result['data']['transaction']['uuid'],
    'amount' => $result['data']['collection']['amount']['raw'],
    'phone' => $result['data']['collection']['phone_number']
]);
```

### 4. Queue Jobs

For better performance, use queue jobs for API calls:

```php
<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MarzPay\Facades\MarzPay;

class ProcessPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $paymentData;

    public function __construct($paymentData)
    {
        $this->paymentData = $paymentData;
    }

    public function handle()
    {
        try {
            $result = MarzPay::collections()->collectMoney($this->paymentData);
            
            // Update database
            // Send notifications
            
        } catch (\MarzPay\Exceptions\MarzPayException $e) {
            // Handle error
            Log::error('Payment job failed', ['error' => $e->getMessage()]);
        }
    }
}
```

## 🔒 Security

### 1. Webhook Verification

```php
private function verifyWebhookSignature(Request $request)
{
    $signature = $request->header('X-MarzPay-Signature');
    $payload = $request->getContent();
    $secret = config('marzpay.webhook_secret');
    
    $expectedSignature = hash_hmac('sha256', $payload, $secret);
    
    if (!hash_equals($expectedSignature, $signature)) {
        throw new \Exception('Invalid webhook signature');
    }
}
```

### 2. Rate Limiting

```php
// In routes/api.php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/payments/collect', [PaymentController::class, 'collectMoney']);
    Route::post('/payments/send', [PaymentController::class, 'sendMoney']);
});
```

## 📱 Frontend Integration

### Vue.js Component Example

```vue
<template>
  <div>
    <form @submit.prevent="collectMoney">
      <div>
        <label>Phone Number:</label>
        <input v-model="form.phone_number" type="tel" required>
      </div>
      <div>
        <label>Amount (UGX):</label>
        <input v-model="form.amount" type="number" min="100" required>
      </div>
      <div>
        <label>Description:</label>
        <input v-model="form.description" type="text">
      </div>
      <button type="submit" :disabled="loading">
        {{ loading ? 'Processing...' : 'Collect Money' }}
      </button>
    </form>
    
    <div v-if="result" class="result">
      <h3>Payment Result:</h3>
      <p>Status: {{ result.data.transaction.status }}</p>
      <p>Reference: {{ result.data.transaction.reference }}</p>
      <p>Amount: {{ result.data.collection.amount.formatted }} {{ result.data.collection.amount.currency }}</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        phone_number: '',
        amount: '',
        description: ''
      },
      loading: false,
      result: null
    }
  },
  methods: {
    async collectMoney() {
      this.loading = true;
      try {
        const response = await axios.post('/api/payments/collect', this.form);
        this.result = response.data;
      } catch (error) {
        alert('Error: ' + error.response.data.error);
      } finally {
        this.loading = false;
      }
    }
  }
}
</script>
```

## 🚀 Deployment

### Environment Configuration

For production, ensure your environment variables are properly set:

```env
# Production Configuration
MARZPAY_API_KEY=your_production_api_key
MARZPAY_API_SECRET=your_production_api_secret
MARZPAY_BASE_URL=https://wallet.wearemarz.com/api/v1
MARZPAY_TIMEOUT=30

# Optional: Webhook secret for signature verification
MARZPAY_WEBHOOK_SECRET=your_webhook_secret
```

### Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
```

## 📞 Support

For Laravel-specific support:

- 📧 Email: support@wearemarz.com
- 📚 Official Documentation: [MarzPay API Documentation](https://wallet.wearemarz.com/documentation)
- 🐛 Issues: [GitHub Issues](https://github.com/marzpay/php-sdk/issues)

---

**Happy coding with MarzPay and Laravel! 🚀**
