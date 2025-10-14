<?php

namespace MarzPay\Tests\Unit;

use PHPUnit\Framework\TestCase;
use MarzPay\Exceptions\MarzPayException;

class MarzPayExceptionTest extends TestCase
{
    public function testCanCreateExceptionWithMessage(): void
    {
        $message = 'Test error message';
        $exception = new MarzPayException($message);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }
    
    public function testCanCreateExceptionWithCode(): void
    {
        $message = 'Test error message';
        $code = 400;
        $exception = new MarzPayException($message, 'TEST_ERROR', $code);
        
        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals($code, $exception->getCode());
    }
    
    public function testCanSetErrorCode(): void
    {
        $exception = new MarzPayException('Test error');
        $exception->setErrorCode('VALIDATION_ERROR');
        
        $this->assertEquals('VALIDATION_ERROR', $exception->getErrorCode());
    }
    
    public function testCanSetStatus(): void
    {
        $exception = new MarzPayException('Test error');
        $exception->setStatus(400);
        
        $this->assertEquals(400, $exception->getStatus());
    }
    
    public function testCanSetResponseData(): void
    {
        $responseData = ['error' => 'Invalid request'];
        $exception = new MarzPayException('Test error');
        $exception->setResponseData($responseData);
        
        $this->assertEquals($responseData, $exception->getResponseData());
    }
    
    public function testCanCreateFromApiResponse(): void
    {
        $apiResponse = [
            'status' => 'error',
            'message' => 'API Error',
            'error_code' => 'API_ERROR',
            'data' => ['details' => 'Something went wrong']
        ];
        
        $exception = MarzPayException::fromApiResponse($apiResponse);
        
        $this->assertEquals('API Error', $exception->getMessage());
        $this->assertEquals('API_ERROR', $exception->getErrorCode());
        $this->assertEquals(400, $exception->getStatus()); // Should be 400 for error status
        $this->assertEquals($apiResponse['data'], $exception->getResponseData());
    }
}
