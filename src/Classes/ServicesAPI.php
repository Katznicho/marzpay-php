<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class ServicesAPI
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    public function getServices(): array
    {
        return $this->marzpay->makeRequest('GET', '/services');
    }
    
    public function getService(string $serviceId): array
    {
        if (empty($serviceId)) {
            throw new MarzPayException('Service ID is required');
        }
        
        return $this->marzpay->makeRequest('GET', "/services/$serviceId");
    }
    
    public function getServiceProviders(): array
    {
        return $this->marzpay->makeRequest('GET', '/services/providers');
    }
    
    public function getServiceCategories(): array
    {
        return $this->marzpay->makeRequest('GET', '/services/categories');
    }
}
