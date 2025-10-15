<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class DisbursementsAPI
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    public function sendMoney(array $params): array
    {
        $this->validateSendMoneyParams($params);

        // Generate a valid UUID if no reference provided
        $reference = $params['reference'] ?? $this->generateUUID();
        
        $payload = [
            'amount' => (string) $params['amount'], // API expects string
            'phone_number' => $this->formatPhoneNumber($params['phone_number']),
            'reference' => $reference,
            'description' => $params['description'] ?? 'Payment to customer',
            'callback_url' => $params['callback_url'] ?? null,
            'country' => $params['country'] ?? 'UG',
        ];

        return $this->marzpay->request('/send-money', [
            'method' => 'POST',
            'body' => $payload,
            'content_type' => 'multipart'
        ]);
    }
    
    /**
     * Get available send money services
     * 
     * @return array Available services
     */
    public function getServices(): array
    {
        return $this->marzpay->request('/send-money/services');
    }

    /**
     * Get send money details by UUID
     * 
     * @param string $uuid Send money UUID
     * @return array Send money details
     * 
     * @throws MarzPayException When request fails
     */
    public function getSendMoneyDetails(string $uuid): array
    {
        return $this->marzpay->request('/send-money/' . $uuid);
    }

    /**
     * Get disbursement details (alias for getSendMoneyDetails)
     * 
     * @param string $disbursementId Disbursement UUID
     * @return array Disbursement details
     */
    public function getDisbursement(string $disbursementId): array
    {
        return $this->getSendMoneyDetails($disbursementId);
    }
    
    /**
     * Validate send money parameters
     * 
     * @param array $params Parameters to validate
     * @throws MarzPayException When validation fails
     */
    private function validateSendMoneyParams(array $params): void
    {
        if (empty($params['amount'])) {
            throw new MarzPayException('Amount is required', 'MISSING_AMOUNT', 400);
        }
        
        if (!is_numeric($params['amount']) || $params['amount'] <= 0) {
            throw new MarzPayException('Amount must be a positive number', 'INVALID_AMOUNT', 400);
        }
        
        if (empty($params['phone_number'])) {
            throw new MarzPayException('Phone number is required', 'MISSING_PHONE_NUMBER', 400);
        }
    }

    /**
     * Format phone number for API
     * 
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters except +
        $phoneNumber = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // Remove + if present for processing
        $phoneNumber = str_replace('+', '', $phoneNumber);
        
        // Add country code if not present
        if (!str_starts_with($phoneNumber, '256')) {
            if (str_starts_with($phoneNumber, '0')) {
                $phoneNumber = '256' . substr($phoneNumber, 1);
            } else {
                $phoneNumber = '256' . $phoneNumber;
            }
        }

        // Add + prefix as required by API
        return '+' . $phoneNumber;
    }

    /**
     * Generate a valid UUID v4
     * 
     * @return string
     */
    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    private function formatParams(array $params): array
    {
        $formatted = $params;
        
        // Format phone number
        if (isset($formatted['phone_number'])) {
            $formatted['phone_number'] = $this->marzpay->formatPhoneNumber($formatted['phone_number']);
        }
        
        return $formatted;
    }
}
