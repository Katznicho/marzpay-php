<?php

namespace MarzPay\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class MarzPayTest extends TestCase
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
    
    public function testCanInitializeWithValidConfig(): void
    {
        $config = [
            'api_key' => 'test_key',
            'api_secret' => 'test_secret'
        ];
        
        $marzpay = new MarzPay($config);
        
        $this->assertInstanceOf(MarzPay::class, $marzpay);
    }
    
    public function testThrowsExceptionWithMissingApiKey(): void
    {
        $this->expectException(MarzPayException::class);
        $this->expectExceptionMessage('API key is required');
        
        new MarzPay(['api_secret' => 'test_secret']);
    }
    
    public function testThrowsExceptionWithMissingApiSecret(): void
    {
        $this->expectException(MarzPayException::class);
        $this->expectExceptionMessage('API secret is required');
        
        new MarzPay(['api_key' => 'test_key']);
    }
    
    public function testHasCollectionsApi(): void
    {
        $this->assertNotNull($this->marzpay->collections());
    }
    
    public function testHasDisbursementsApi(): void
    {
        $this->assertNotNull($this->marzpay->disbursements());
    }
    
    public function testHasAccountsApi(): void
    {
        $this->assertNotNull($this->marzpay->accounts());
    }
    
    public function testHasBalanceApi(): void
    {
        $this->assertNotNull($this->marzpay->balance());
    }
    
    public function testHasTransactionsApi(): void
    {
        $this->assertNotNull($this->marzpay->transactions());
    }
    
    public function testHasServicesApi(): void
    {
        $this->assertNotNull($this->marzpay->services());
    }
    
    public function testHasWebhooksApi(): void
    {
        $this->assertNotNull($this->marzpay->webhooks());
    }
    
    public function testHasPhoneVerificationApi(): void
    {
        $this->assertNotNull($this->marzpay->phoneVerification());
    }
    
    public function testCanGetInfo(): void
    {
        $info = $this->marzpay->getInfo();
        
        $this->assertIsArray($info);
        $this->assertArrayHasKey('name', $info);
        $this->assertArrayHasKey('version', $info);
        $this->assertArrayHasKey('base_url', $info);
        $this->assertArrayHasKey('features', $info);
    }
    
    public function testCanGenerateReference(): void
    {
        $reference = $this->marzpay->collections()->generateReference();
        
        $this->assertIsString($reference);
        $this->assertNotEmpty($reference);
        $this->assertMatchesRegularExpression('/^[a-f0-9\-]+$/', $reference);
    }
    
    public function testCanFormatPhoneNumber(): void
    {
        $phone = $this->marzpay->formatPhoneNumber('0759983853');
        
        $this->assertEquals('256759983853', $phone);
    }
    
    public function testCanValidatePhoneNumber(): void
    {
        $this->assertTrue($this->marzpay->isValidPhoneNumber('0759983853'));
        $this->assertTrue($this->marzpay->isValidPhoneNumber('256759983853'));
        $this->assertFalse($this->marzpay->isValidPhoneNumber('123'));
        $this->assertFalse($this->marzpay->isValidPhoneNumber(''));
    }
}
