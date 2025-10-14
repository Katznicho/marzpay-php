<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class PhoneVerificationAPI
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    public function validatePhoneNumber(string $phoneNumber): array
    {
        if (!$this->marzpay->isValidPhoneNumber($phoneNumber)) {
            throw new MarzPayException('Invalid phone number format');
        }
        
        $formattedPhone = $this->marzpay->formatPhoneNumber($phoneNumber);
        
        return $this->marzpay->makeRequest('POST', '/phone-verification/validate', [
            'phone_number' => $formattedPhone
        ]);
    }
    
    public function sendVerificationCode(string $phoneNumber): array
    {
        if (!$this->marzpay->isValidPhoneNumber($phoneNumber)) {
            throw new MarzPayException('Invalid phone number format');
        }
        
        $formattedPhone = $this->marzpay->formatPhoneNumber($phoneNumber);
        
        return $this->marzpay->makeRequest('POST', '/phone-verification/send-code', [
            'phone_number' => $formattedPhone
        ]);
    }
    
    public function verifyCode(string $phoneNumber, string $code): array
    {
        if (!$this->marzpay->isValidPhoneNumber($phoneNumber)) {
            throw new MarzPayException('Invalid phone number format');
        }
        
        if (empty($code)) {
            throw new MarzPayException('Verification code is required');
        }
        
        $formattedPhone = $this->marzpay->formatPhoneNumber($phoneNumber);
        
        return $this->marzpay->makeRequest('POST', '/phone-verification/verify-code', [
            'phone_number' => $formattedPhone,
            'code' => $code
        ]);
    }
    
    public function getVerificationStatus(string $phoneNumber): array
    {
        if (!$this->marzpay->isValidPhoneNumber($phoneNumber)) {
            throw new MarzPayException('Invalid phone number format');
        }
        
        $formattedPhone = $this->marzpay->formatPhoneNumber($phoneNumber);
        
        return $this->marzpay->makeRequest('GET', "/phone-verification/status/$formattedPhone");
    }
}
