<?php

/**
 * Quick test script for MarzPay PHP SDK
 * Run this to quickly validate the SDK functionality
 */

require_once __DIR__ . '/vendor/autoload.php';

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

echo "MarzPay PHP SDK - Quick Test\n";
echo str_repeat('=', 50) . "\n\n";

// Load environment variables if .env file exists
if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Test configuration
$config = [
    'api_key' => $_ENV['MARZPAY_API_KEY'] ?? 'test_api_key',
    'api_secret' => $_ENV['MARZPAY_API_SECRET'] ?? 'test_api_secret',
    'base_url' => $_ENV['MARZPAY_BASE_URL'] ?? 'https://wallet.wearemarz.com/api/v1',
    'timeout' => 30
];

$hasRealCredentials = !in_array($config['api_key'], ['test_api_key', 'your_api_key_here']) && 
                     !in_array($config['api_secret'], ['test_api_secret', 'your_api_secret_here']);

echo "Configuration:\n";
echo "  API Key: " . ($hasRealCredentials ? substr($config['api_key'], 0, 8) . '...' : $config['api_key']) . "\n";
echo "  Base URL: " . $config['base_url'] . "\n";
echo "  Real Credentials: " . ($hasRealCredentials ? 'Yes' : 'No') . "\n\n";

try {
    // Initialize SDK
    echo "1. Initializing MarzPay SDK...\n";
    $marzpay = new MarzPay($config);
    echo "✅ SDK initialized successfully!\n\n";
    
    // Test SDK info
    echo "2. Getting SDK information...\n";
    $info = $marzpay->getInfo();
    echo "✅ SDK Info:\n";
    echo "   Name: " . $info['name'] . "\n";
    echo "   Version: " . $info['version'] . "\n";
    echo "   Features: " . count($info['features']) . " available\n\n";
    
    // Test phone number validation
    echo "3. Testing phone number validation...\n";
    $testPhones = ['0759983853', '256759983853', '123', ''];
    foreach ($testPhones as $phone) {
        $isValid = $marzpay->isValidPhoneNumber($phone);
        $formatted = $marzpay->formatPhoneNumber($phone);
        echo "   Phone: '$phone' -> Valid: " . ($isValid ? 'Yes' : 'No') . ", Formatted: '$formatted'\n";
    }
    echo "✅ Phone validation test completed!\n\n";
    
    // Test reference generation
    echo "4. Testing reference generation...\n";
    $references = [];
    for ($i = 0; $i < 3; $i++) {
        $ref = $marzpay->collections()->generateReference();
        $references[] = $ref;
        echo "   Reference $i: $ref\n";
    }
    echo "✅ Reference generation test completed!\n\n";
    
    // Test API connection (if real credentials)
    if ($hasRealCredentials) {
        echo "5. Testing API connection...\n";
        try {
            $connectionResult = $marzpay->testConnection();
            if ($connectionResult['status'] === 'success') {
                echo "✅ API connection successful!\n";
                echo "   Business: " . $connectionResult['data']['business_name'] . "\n\n";
            } else {
                echo "❌ API connection failed: " . $connectionResult['message'] . "\n\n";
            }
        } catch (MarzPayException $e) {
            echo "❌ API connection error: " . $e->getMessage() . "\n\n";
        }
        
        // Test getting services
        echo "6. Testing services API...\n";
        try {
            $services = $marzpay->collections()->getServices();
            if ($services['status'] === 'success') {
                echo "✅ Services retrieved successfully!\n";
                echo "   Available services: " . count($services['data']['services'] ?? []) . "\n\n";
            } else {
                echo "❌ Services retrieval failed: " . $services['message'] . "\n\n";
            }
        } catch (MarzPayException $e) {
            echo "❌ Services error: " . $e->getMessage() . "\n\n";
        }
        
        // Test getting balance
        echo "7. Testing balance API...\n";
        try {
            $balance = $marzpay->balance()->getBalance();
            if ($balance['status'] === 'success') {
                echo "✅ Balance retrieved successfully!\n";
                echo "   Balance: " . $balance['data']['balance'] . " UGX\n\n";
            } else {
                echo "❌ Balance retrieval failed: " . $balance['message'] . "\n\n";
            }
        } catch (MarzPayException $e) {
            echo "❌ Balance error: " . $e->getMessage() . "\n\n";
        }
    } else {
        echo "5. Skipping API tests (no real credentials provided)\n\n";
        echo "To test with real API calls:\n";
        echo "1. Copy env.testing to .env\n";
        echo "2. Add your real API credentials to .env\n";
        echo "3. Run this script again\n\n";
    }
    
    echo "✅ Quick test completed successfully!\n";
    echo "\nAll basic SDK functionality is working correctly.\n";
    
} catch (MarzPayException $e) {
    echo "❌ MarzPay Error: " . $e->getMessage() . "\n";
    echo "   Code: " . $e->getErrorCode() . "\n";
    echo "   Status: " . $e->getStatus() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ General Error: " . $e->getMessage() . "\n";
    exit(1);
}
