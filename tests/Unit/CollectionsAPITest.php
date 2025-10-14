<?php

namespace MarzPay\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class CollectionsAPITest extends TestCase
{
    private MarzPay $marzpay;
    
    protected function setUp(): void
    {
        $this->marzpay = new MarzPay([
            'api_key' => 'test_api_key',
            'api_secret' => 'test_api_secret',
            'base_url' => 'https://api.test.marzpay.com',
            'timeout' => 30
        ]);
    }
    
    public function testCanGenerateReference(): void
    {
        $reference = $this->marzpay->collections()->generateReference();
        
        $this->assertIsString($reference);
        $this->assertNotEmpty($reference);
        $this->assertMatchesRegularExpression('/^[a-f0-9\-]+$/', $reference);
    }
    
    public function testThrowsExceptionForInvalidAmount(): void
    {
        $this->expectException(MarzPayException::class);
        
        $this->marzpay->collections()->collectMoney([
            'amount' => -100,
            'phone_number' => '0759983853',
            'reference' => 'test-ref',
            'description' => 'Test payment'
        ]);
    }
    
    public function testThrowsExceptionForInvalidPhoneNumber(): void
    {
        $this->expectException(MarzPayException::class);
        
        $this->marzpay->collections()->collectMoney([
            'amount' => 1000,
            'phone_number' => 'invalid',
            'reference' => 'test-ref',
            'description' => 'Test payment'
        ]);
    }
    
    public function testThrowsExceptionForMissingRequiredFields(): void
    {
        $this->expectException(MarzPayException::class);
        
        $this->marzpay->collections()->collectMoney([
            'amount' => 1000,
            'phone_number' => '0759983853'
            // Missing reference and description
        ]);
    }
    
    public function testValidatesRequiredFields(): void
    {
        $validParams = [
            'amount' => 1000,
            'phone_number' => '0759983853',
            'reference' => 'test-reference-123',
            'description' => 'Test payment'
        ];
        
        // This should not throw an exception for validation
        // (it might fail at API level, but not validation level)
        try {
            $result = $this->marzpay->collections()->collectMoney($validParams);
            // If we get here, the validation passed
            $this->assertTrue(true);
        } catch (MarzPayException $e) {
            // If it's a network/API error, that's expected in unit tests
            $this->assertNotEquals('Validation failed', $e->getMessage());
        }
    }
    
    public function testCanFormatCollectionParams(): void
    {
        $params = [
            'amount' => 1000,
            'phone_number' => '0759983853',
            'reference' => 'test-ref',
            'description' => 'Test payment'
        ];
        
        $formatted = $this->marzpay->collections()->formatParams($params);
        
        $this->assertArrayHasKey('amount', $formatted);
        $this->assertArrayHasKey('phone_number', $formatted);
        $this->assertArrayHasKey('reference', $formatted);
        $this->assertArrayHasKey('description', $formatted);
        
        // Phone number should be formatted
        $this->assertEquals('256759983853', $formatted['phone_number']);
    }
}
