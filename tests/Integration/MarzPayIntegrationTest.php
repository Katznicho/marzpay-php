<?php

namespace MarzPay\Tests\Integration;

use PHPUnit\Framework\TestCase;
use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class MarzPayIntegrationTest extends TestCase
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
        
        // Check if we have real credentials (not test values)
        $this->hasRealCredentials = !in_array($apiKey, ['test_api_key', 'your_api_key_here']) && 
                                   !in_array($apiSecret, ['test_api_secret', 'your_api_secret_here']);
    }
    
    public function testCanTestConnection(): void
    {
        if (!$this->hasRealCredentials) {
            $this->markTestSkipped('No real API credentials provided');
        }
        
        $result = $this->marzpay->testConnection();
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('status', $result);
        
        if ($result['status'] === 'success') {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('business_name', $result['data']);
        }
    }
    
    public function testCanGetServices(): void
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
            }
        } catch (MarzPayException $e) {
            // If it's an authentication error, that's expected without real credentials
            if (strpos($e->getMessage(), 'authentication') !== false || 
                strpos($e->getMessage(), 'unauthorized') !== false) {
                $this->markTestSkipped('Authentication failed - check API credentials');
            }
            throw $e;
        }
    }
    
    public function testCanGetBalance(): void
    {
        if (!$this->hasRealCredentials) {
            $this->markTestSkipped('No real API credentials provided');
        }
        
        try {
            $result = $this->marzpay->balance()->getBalance();
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('status', $result);
            
            if ($result['status'] === 'success') {
                $this->assertArrayHasKey('data', $result);
                $this->assertArrayHasKey('balance', $result['data']);
            }
        } catch (MarzPayException $e) {
            if (strpos($e->getMessage(), 'authentication') !== false || 
                strpos($e->getMessage(), 'unauthorized') !== false) {
                $this->markTestSkipped('Authentication failed - check API credentials');
            }
            throw $e;
        }
    }
    
    public function testCanGetAccountInfo(): void
    {
        if (!$this->hasRealCredentials) {
            $this->markTestSkipped('No real API credentials provided');
        }
        
        try {
            $result = $this->marzpay->accounts()->getAccount();
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('status', $result);
            
            if ($result['status'] === 'success') {
                $this->assertArrayHasKey('data', $result);
            }
        } catch (MarzPayException $e) {
            if (strpos($e->getMessage(), 'authentication') !== false || 
                strpos($e->getMessage(), 'unauthorized') !== false) {
                $this->markTestSkipped('Authentication failed - check API credentials');
            }
            throw $e;
        }
    }
    
    public function testCanValidatePhoneNumber(): void
    {
        $phoneNumber = getenv('MARZPAY_TEST_PHONE') ?: '0759983853';
        
        try {
            $result = $this->marzpay->phoneVerification()->validatePhoneNumber($phoneNumber);
            
            $this->assertIsArray($result);
            $this->assertArrayHasKey('status', $result);
            
            if ($result['status'] === 'success') {
                $this->assertArrayHasKey('data', $result);
                $this->assertArrayHasKey('is_valid', $result['data']);
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
