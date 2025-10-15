<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

/**
 * Basic usage example for MarzPay PHP SDK
 * 
 * This example demonstrates the core functionality:
 * - Phone verification
 * - Collect money from customers
 * - Send money to recipients
 */

function main() {
    // Load environment variables
    if (file_exists(__DIR__ . '/../.env')) {
        $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }

    // Initialize the client
    $marzpay = new MarzPay([
        'api_key' => $_ENV['MARZPAY_API_KEY'] ?? 'your_api_key',
        'api_secret' => $_ENV['MARZPAY_API_SECRET'] ?? 'your_api_secret',
        'base_url' => 'https://wallet.wearemarz.com/api/v1'
    ]);
    
    echo "MarzPay PHP SDK - Basic Usage Example\n";
    echo str_repeat('=', 60) . "\n\n";
    
    // 1. Phone Verification
    echo "📱 1. PHONE VERIFICATION\n";
    echo str_repeat('-', 30) . "\n";
    try {
        $verification = $marzpay->phoneVerification()->verifyPhoneNumber('0759983853');
        
        if ($verification['success']) {
            echo "✅ Phone verified successfully!\n";
            echo "   Name: " . $verification['data']['full_name'] . "\n";
            echo "   Phone: " . $verification['data']['phone_number'] . "\n";
            echo "   Status: " . $verification['data']['verification_status'] . "\n";
        } else {
            echo "❌ Phone verification failed: " . $verification['message'] . "\n";
        }
    } catch (MarzPayException $e) {
        echo "❌ Error verifying phone: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // 2. Collect Money
    echo "💰 2. COLLECT MONEY\n";
    echo str_repeat('-', 30) . "\n";
    try {
        $collection = $marzpay->collections()->collectMoney([
            'phone_number' => '+256700000000',
            'amount' => 1000,
            'country' => 'UG',
            'description' => 'Payment for services - PHP SDK Demo'
        ]);
        
        echo "✅ Collection initiated successfully!\n";
        echo "   UUID: " . $collection['data']['transaction']['uuid'] . "\n";
        echo "   Amount: " . $collection['data']['collection']['amount']['formatted'] . " " . $collection['data']['collection']['amount']['currency'] . "\n";
        echo "   Provider: " . $collection['data']['collection']['provider'] . "\n";
        echo "   Status: " . $collection['data']['transaction']['status'] . "\n";
        
        $collectionUUID = $collection['data']['transaction']['uuid'];
        
    } catch (MarzPayException $e) {
        echo "❌ Error collecting money: " . $e->getMessage() . "\n";
        $collectionUUID = null;
    }
    
    echo "\n";
    
    // 3. Send Money
    echo "💸 3. SEND MONEY\n";
    echo str_repeat('-', 30) . "\n";
    try {
        $disbursement = $marzpay->disbursements()->sendMoney([
            'phone_number' => '0759983853',
            'amount' => 1000,
            'country' => 'UG',
            'description' => 'Payment to customer - PHP SDK Demo'
        ]);
        
        echo "✅ Send money initiated successfully!\n";
        echo "   UUID: " . $disbursement['data']['transaction']['uuid'] . "\n";
        echo "   Amount: " . $disbursement['data']['withdrawal']['amount']['formatted'] . " " . $disbursement['data']['withdrawal']['amount']['currency'] . "\n";
        echo "   Provider: " . $disbursement['data']['withdrawal']['provider'] . "\n";
        echo "   Status: " . $disbursement['data']['transaction']['status'] . "\n";
        
        $disbursementUUID = $disbursement['data']['transaction']['uuid'];
        
    } catch (MarzPayException $e) {
        echo "❌ Error sending money: " . $e->getMessage() . "\n";
        $disbursementUUID = null;
    }
    
    echo "\n";
    
    // 4. Get Available Services
    echo "🔧 4. AVAILABLE SERVICES\n";
    echo str_repeat('-', 30) . "\n";
    
    try {
        $collectionServices = $marzpay->collections()->getServices();
        $disbursementServices = $marzpay->disbursements()->getServices();
        
        echo "✅ Collection Services: " . $collectionServices['data']['summary']['total_services'] . " providers\n";
        echo "✅ Disbursement Services: " . $disbursementServices['data']['summary']['total_services'] . " providers\n";
        
    } catch (MarzPayException $e) {
        echo "❌ Error getting services: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // 5. Get Transaction Details (if we have UUIDs)
    if ($collectionUUID) {
        echo "📋 5. COLLECTION DETAILS\n";
        echo str_repeat('-', 30) . "\n";
        try {
            $details = $marzpay->collections()->getCollectionDetails($collectionUUID);
            echo "✅ Collection details retrieved!\n";
            echo "   Reference: " . $details['data']['transaction']['reference'] . "\n";
            echo "   Status: " . $details['data']['transaction']['status'] . "\n";
        } catch (MarzPayException $e) {
            echo "❌ Error getting collection details: " . $e->getMessage() . "\n";
        }
    }
    
    if ($disbursementUUID) {
        echo "\n📋 6. DISBURSEMENT DETAILS\n";
        echo str_repeat('-', 30) . "\n";
        try {
            $details = $marzpay->disbursements()->getSendMoneyDetails($disbursementUUID);
            echo "✅ Disbursement details retrieved!\n";
            echo "   Reference: " . $details['data']['transaction']['reference'] . "\n";
            echo "   Status: " . $details['data']['transaction']['status'] . "\n";
        } catch (MarzPayException $e) {
            echo "❌ Error getting disbursement details: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n" . str_repeat('=', 60) . "\n";
    echo "🎉 MarzPay PHP SDK Demo Complete!\n";
    echo "For more examples, visit: https://wallet.wearemarz.com/documentation\n";
}

// Run the example
main();

