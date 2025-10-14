<?php

require_once __DIR__ . '/../vendor/autoload.php';

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

/**
 * Basic usage example for MarzPay PHP SDK
 */

function main() {
    // Initialize the client
    $client = new MarzPay([
        'api_key' => $_ENV['MARZPAY_API_KEY'] ?? 'your_api_key',
        'api_secret' => $_ENV['MARZPAY_API_SECRET'] ?? 'your_api_secret',
    ]);
    
    echo "MarzPay PHP SDK - Basic Usage Example\n";
    echo str_repeat('=', 50) . "\n";
    
    // Test connection
    echo "\n1. Testing API connection...\n";
    $connectionResult = $client->testConnection();
    if ($connectionResult['status'] === 'success') {
        echo "✅ API connection successful!\n";
        echo "   Business: " . $connectionResult['data']['business_name'] . "\n";
    } else {
        echo "❌ API connection failed: " . $connectionResult['message'] . "\n";
        return;
    }
    
    // Generate a reference
    $reference = $client->collections()->generateReference();
    echo "\n2. Generated reference: " . $reference . "\n";
    
    // Collect money example
    echo "\n3. Collecting money from customer...\n";
    try {
        $result = $client->collections()->collectMoney([
            'amount' => 5000,
            'phone_number' => '0759983853',
            'reference' => $reference,
            'description' => 'Payment for services - PHP SDK Demo'
        ]);
        
        echo "✅ Money collection initiated successfully!\n";
        echo "   Collection ID: " . $result['data']['collection_id'] . "\n";
        echo "   Status: " . $result['data']['status'] . "\n";
        echo "   Amount: " . $result['data']['amount'] . " UGX\n";
        
        // Get collection details
        $collectionId = $result['data']['collection_id'];
        echo "\n4. Getting collection details for ID: " . $collectionId . "\n";
        
        $collectionDetails = $client->collections()->getCollection($collectionId);
        echo "✅ Collection details retrieved!\n";
        echo "   Reference: " . $collectionDetails['data']['reference'] . "\n";
        echo "   Phone: " . $collectionDetails['data']['phone_number'] . "\n";
        
    } catch (MarzPayException $e) {
        echo "❌ Error collecting money: " . $e->getMessage() . "\n";
        echo "   Code: " . $e->getErrorCode() . "\n";
        echo "   Status: " . $e->getStatus() . "\n";
    }
    
    // Get available services
    echo "\n5. Getting available collection services...\n";
    try {
        $services = $client->collections()->getServices();
        echo "✅ Available services retrieved!\n";
        echo "   Services: " . count($services['data']['services'] ?? []) . " available\n";
        
    } catch (MarzPayException $e) {
        echo "❌ Error getting services: " . $e->getMessage() . "\n";
    }
    
    // Get SDK info
    echo "\n6. SDK Information:\n";
    $info = $client->getInfo();
    echo "   Name: " . $info['name'] . "\n";
    echo "   Version: " . $info['version'] . "\n";
    echo "   Base URL: " . $info['base_url'] . "\n";
    echo "   Features: " . count($info['features']) . " available\n";
}

// Run the example
main();

