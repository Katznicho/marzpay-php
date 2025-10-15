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
    
    /**
     * Verify a phone number and retrieve associated user information
     * 
     * @param string $phoneNumber Uganda phone number
     * @return array Verification result with user information
     * 
     * @throws MarzPayException When request fails
     */
    public function verifyPhoneNumber(string $phoneNumber): array
    {
        $formattedPhone = $this->formatPhoneNumber($phoneNumber);
        
        $payload = [
            'phone_number' => $formattedPhone
        ];
        
        return $this->marzpay->request('/phone-verification/verify', [
            'method' => 'POST',
            'body' => $payload,
            'content_type' => 'json'
        ]);
    }
    
    /**
     * Get information about the phone verification service
     * 
     * @return array Service information
     */
    public function getServiceInfo(): array
    {
        return $this->marzpay->request('/phone-verification/service-info');
    }
    
    /**
     * Check subscription status for phone verification service
     * 
     * @return array Subscription status
     */
    public function getSubscriptionStatus(): array
    {
        return $this->marzpay->request('/phone-verification/subscription-status');
    }
    
    /**
     * Format phone number for API (remove + prefix, ensure 256XXXXXXXXX format)
     * 
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters except +
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Remove + if present
        $phoneNumber = str_replace('+', '', $phoneNumber);
        
        // Add country code if not present
        if (!str_starts_with($phoneNumber, '256')) {
            if (str_starts_with($phoneNumber, '0')) {
                $phoneNumber = '256' . substr($phoneNumber, 1);
            } else {
                $phoneNumber = '256' . $phoneNumber;
            }
        }
        
        return $phoneNumber;
    }
}
