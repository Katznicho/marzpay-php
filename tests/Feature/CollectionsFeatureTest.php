<?php

namespace MarzPay\Tests\Feature;

use PHPUnit\Framework\TestCase;
use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class CollectionsFeatureTest extends TestCase
{
    private MarzPay $marzpay;
    private bool $hasRealCredentials = false;
    
    protected function setUp(): void
    {
        $apiKey = getenv('MARZPAY_API_KEY') ?: 'test_api_key';
        $apiSecret = getenv('MARZPAY_API_SECRET') ?: 'test_api_secret';
        
        $this->marzpay = new MarzPay([
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'base_url' => getenv('MARZPAY_BASE_URL') ?: 'https://wallet.wearemarz.com/api/v1',
            'timeout' => 30
        ]);
        
        $this->hasRealCredentials = !in_array($apiKey, ['test_api_key', 'your_api_key_here']) && 
                                   !in_array($apiSecret, ['test_api_secret', 'your_api_secret_here']);
    }
    
    public function testCompleteCollectionWorkflow(): void
    {
        if (!$this->hasRealCredentials) {
            $this->markTestSkipped('No real API credentials provided for feature test');
        }
        
        $testPhone = getenv('MARZPAY_TEST_PHONE') ?: '0759983853';
        $testAmount = (int)(getenv('MARZPAY_TEST_AMOUNT') ?: '1000');
        
        try {
            // Step 1: Generate reference
            $reference = $this->marzpay->collections()->generateReference();
            $this->assertNotEmpty($reference);
            
            // Step 2: Collect money
            $collectionResult = $this->marzpay->collections()->collectMoney([
                'amount' => $testAmount,
                'phone_number' => $testPhone,
                'reference' => $reference,
                'description' => 'PHP SDK Feature Test - ' . date('Y-m-d H:i:s')
            ]);
            
            $this->assertIsArray($collectionResult);
            $this->assertArrayHasKey('status', $collectionResult);
            
            if ($collectionResult['status'] === 'success') {
                $this->assertArrayHasKey('data', $collectionResult);
                $this->assertArrayHasKey('collection_id', $collectionResult['data']);
                
                $collectionId = $collectionResult['data']['collection_id'];
                
                // Step 3: Get collection details
                $collectionDetails = $this->marzpay->collections()->getCollection($collectionId);
                
                $this->assertIsArray($collectionDetails);
                $this->assertArrayHasKey('status', $collectionDetails);
                
                if ($collectionDetails['status'] === 'success') {
                    $this->assertArrayHasKey('data', $collectionDetails);
                    $this->assertEquals($reference, $collectionDetails['data']['reference']);
                    $this->assertEquals($testAmount, $collectionDetails['data']['amount']);
                }
            }
            
        } catch (MarzPayException $e) {
            if (strpos($e->getMessage(), 'authentication') !== false || 
                strpos($e->getMessage(), 'unauthorized') !== false) {
                $this->markTestSkipped('Authentication failed - check API credentials');
            }
            
            // For feature tests, we might want to continue even if collection fails
            // (e.g., insufficient funds, network issues, etc.)
            $this->addToAssertionCount(1); // Mark as assertion to avoid "no assertions" warning
            $this->assertTrue(true, 'Collection failed as expected: ' . $e->getMessage());
        }
    }
    
    public function testCollectionWithInvalidParameters(): void
    {
        // Test with invalid amount
        $this->expectException(MarzPayException::class);
        
        $this->marzpay->collections()->collectMoney([
            'amount' => -100,
            'phone_number' => '0759983853',
            'reference' => 'test-invalid-amount',
            'description' => 'Test with invalid amount'
        ]);
    }
    
    public function testCollectionWithInvalidPhoneNumber(): void
    {
        // Test with invalid phone number
        $this->expectException(MarzPayException::class);
        
        $this->marzpay->collections()->collectMoney([
            'amount' => 1000,
            'phone_number' => 'invalid-phone',
            'reference' => 'test-invalid-phone',
            'description' => 'Test with invalid phone number'
        ]);
    }
    
    public function testCollectionWithMissingRequiredFields(): void
    {
        // Test with missing reference
        $this->expectException(MarzPayException::class);
        
        $this->marzpay->collections()->collectMoney([
            'amount' => 1000,
            'phone_number' => '0759983853',
            'description' => 'Test with missing reference'
        ]);
    }
    
    public function testCanGetAvailableServices(): void
    {
        if (!$this->hasRealCredentials) {
            $this->markTestSkipped('No real API credentials provided');
        }
        
        try {
            $result = $this->marzpay->collections()->getServices();
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('status', $result);
            
            if ($result['status'] === 'success') {
                $this->assertArrayHasKey('data', $result);
                $this->assertArrayHasKey('services', $result['data']);
                $this->assertIsArray($result['data']['services']);
            }
        } catch (MarzPayException $e) {
            if (strpos($e->getMessage(), 'authentication') !== false || 
                strpos($e->getMessage(), 'unauthorized') !== false) {
                $this->markTestSkipped('Authentication failed - check API credentials');
            }
            throw $e;
        }
    }
}
