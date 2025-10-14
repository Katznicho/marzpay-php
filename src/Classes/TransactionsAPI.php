<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class TransactionsAPI
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    public function getTransactions(array $params = []): array
    {
        return $this->marzpay->makeRequest('GET', '/transactions', $params);
    }
    
    public function getTransaction(string $transactionId): array
    {
        if (empty($transactionId)) {
            throw new MarzPayException('Transaction ID is required');
        }
        
        return $this->marzpay->makeRequest('GET', "/transactions/$transactionId");
    }
    
    public function searchTransactions(array $params): array
    {
        return $this->marzpay->makeRequest('GET', '/transactions/search', $params);
    }
    
    public function exportTransactions(array $params = []): array
    {
        return $this->marzpay->makeRequest('GET', '/transactions/export', $params);
    }
}
