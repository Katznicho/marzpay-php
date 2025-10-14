<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class AccountsAPI
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    public function getAccount(): array
    {
        return $this->marzpay->makeRequest('GET', '/accounts/me');
    }
    
    public function updateAccount(array $params): array
    {
        return $this->marzpay->makeRequest('PUT', '/accounts/me', $params);
    }
    
    public function getAccountSettings(): array
    {
        return $this->marzpay->makeRequest('GET', '/accounts/settings');
    }
    
    public function updateAccountSettings(array $params): array
    {
        return $this->marzpay->makeRequest('PUT', '/accounts/settings', $params);
    }
}
