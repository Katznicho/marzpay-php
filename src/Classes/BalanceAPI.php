<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class BalanceAPI
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    public function getBalance(): array
    {
        return $this->marzpay->makeRequest('GET', '/balance');
    }
    
    public function getBalanceHistory(array $params = []): array
    {
        return $this->marzpay->makeRequest('GET', '/balance/history', $params);
    }
}
