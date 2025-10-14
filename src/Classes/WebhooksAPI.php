<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class WebhooksAPI
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    public function handleWebhook(string $payload): WebhookPayload
    {
        return new WebhookPayload($payload, $this->marzpay);
    }
    
    public function createWebhook(array $params): array
    {
        $required = ['url', 'events'];
        foreach ($required as $field) {
            if (!isset($params[$field])) {
                throw new MarzPayException("Missing required parameter: $field");
            }
        }
        
        return $this->marzpay->makeRequest('POST', '/webhooks', $params);
    }
    
    public function getWebhooks(): array
    {
        return $this->marzpay->makeRequest('GET', '/webhooks');
    }
    
    public function getWebhook(string $webhookId): array
    {
        if (empty($webhookId)) {
            throw new MarzPayException('Webhook ID is required');
        }
        
        return $this->marzpay->makeRequest('GET', "/webhooks/$webhookId");
    }
    
    public function updateWebhook(string $webhookId, array $params): array
    {
        if (empty($webhookId)) {
            throw new MarzPayException('Webhook ID is required');
        }
        
        return $this->marzpay->makeRequest('PUT', "/webhooks/$webhookId", $params);
    }
    
    public function deleteWebhook(string $webhookId): array
    {
        if (empty($webhookId)) {
            throw new MarzPayException('Webhook ID is required');
        }
        
        return $this->marzpay->makeRequest('DELETE', "/webhooks/$webhookId");
    }
    
    public function testWebhook(string $webhookId): array
    {
        if (empty($webhookId)) {
            throw new MarzPayException('Webhook ID is required');
        }
        
        return $this->marzpay->makeRequest('POST', "/webhooks/$webhookId/test");
    }
}

class WebhookPayload
{
    private array $data;
    private bool $isValid = false;
    private MarzPay $marzpay;
    
    public function __construct(string $payload, MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
        $this->data = json_decode($payload, true) ?? [];
        $this->validateSignature();
    }
    
    public function isValid(): bool
    {
        return $this->isValid;
    }
    
    public function getTransactionId(): ?string
    {
        return $this->data['transaction_id'] ?? null;
    }
    
    public function getStatus(): ?string
    {
        return $this->data['status'] ?? null;
    }
    
    public function getEvent(): ?string
    {
        return $this->data['event'] ?? null;
    }
    
    public function getData(): array
    {
        return $this->data;
    }
    
    private function validateSignature(): void
    {
        // In a real implementation, you would validate the webhook signature
        // For now, we'll assume it's valid if it contains required fields
        $this->isValid = isset($this->data['transaction_id']) && isset($this->data['status']);
    }
}
