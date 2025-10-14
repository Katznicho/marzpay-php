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
        // Validate required parameters
        $required = ['amount', 'phone_number', 'reference', 'description'];
        foreach ($required as $field) {
            if (!isset($params[$field])) {
                throw new MarzPayException("Missing required parameter: $field");
            }
        }
        
        // Validate amount
        if (!is_numeric($params['amount']) || $params['amount'] <= 0) {
            throw new MarzPayException('Amount must be a positive number');
        }
        
        // Validate phone number
        if (!$this->marzpay->isValidPhoneNumber($params['phone_number'])) {
            throw new MarzPayException('Invalid phone number format');
        }
        
        // Format parameters
        $formattedParams = $this->formatParams($params);
        
        return $this->marzpay->makeRequest('POST', '/disbursements', $formattedParams);
    }
    
    public function getDisbursement(string $disbursementId): array
    {
        if (empty($disbursementId)) {
            throw new MarzPayException('Disbursement ID is required');
        }
        
        return $this->marzpay->makeRequest('GET', "/disbursements/$disbursementId");
    }
    
    public function getDisbursements(array $params = []): array
    {
        return $this->marzpay->makeRequest('GET', '/disbursements', $params);
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
