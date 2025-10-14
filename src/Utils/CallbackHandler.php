<?php

namespace MarzPay\Utils;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;

class CallbackHandler
{
    private MarzPay $marzpay;
    
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }
    
    /**
     * Handle callback request
     */
    public function handleCallback(array $callbackData): array
    {
        // Validate callback data
        $this->validateCallbackData($callbackData);
        
        // Process callback based on type
        $callbackType = $callbackData['type'] ?? 'unknown';
        
        switch ($callbackType) {
            case 'collection':
                return $this->handleCollectionCallback($callbackData);
            case 'disbursement':
                return $this->handleDisbursementCallback($callbackData);
            case 'transaction':
                return $this->handleTransactionCallback($callbackData);
            default:
                return $this->handleGenericCallback($callbackData);
        }
    }
    
    /**
     * Handle collection callback
     */
    private function handleCollectionCallback(array $callbackData): array
    {
        $collectionId = $callbackData['collection_id'] ?? null;
        $status = $callbackData['status'] ?? null;
        $reference = $callbackData['reference'] ?? null;
        
        // Log callback
        $this->logCallback('collection', $callbackData);
        
        // Update local state if needed
        // This would typically update a database or trigger other actions
        
        return [
            'status' => 'success',
            'message' => 'Collection callback processed',
            'collection_id' => $collectionId,
            'reference' => $reference,
            'callback_status' => $status
        ];
    }
    
    /**
     * Handle disbursement callback
     */
    private function handleDisbursementCallback(array $callbackData): array
    {
        $disbursementId = $callbackData['disbursement_id'] ?? null;
        $status = $callbackData['status'] ?? null;
        $reference = $callbackData['reference'] ?? null;
        
        // Log callback
        $this->logCallback('disbursement', $callbackData);
        
        // Update local state if needed
        
        return [
            'status' => 'success',
            'message' => 'Disbursement callback processed',
            'disbursement_id' => $disbursementId,
            'reference' => $reference,
            'callback_status' => $status
        ];
    }
    
    /**
     * Handle transaction callback
     */
    private function handleTransactionCallback(array $callbackData): array
    {
        $transactionId = $callbackData['transaction_id'] ?? null;
        $status = $callbackData['status'] ?? null;
        
        // Log callback
        $this->logCallback('transaction', $callbackData);
        
        // Update local state if needed
        
        return [
            'status' => 'success',
            'message' => 'Transaction callback processed',
            'transaction_id' => $transactionId,
            'callback_status' => $status
        ];
    }
    
    /**
     * Handle generic callback
     */
    private function handleGenericCallback(array $callbackData): array
    {
        // Log callback
        $this->logCallback('generic', $callbackData);
        
        return [
            'status' => 'success',
            'message' => 'Generic callback processed',
            'data' => $callbackData
        ];
    }
    
    /**
     * Validate callback data
     */
    private function validateCallbackData(array $callbackData): void
    {
        if (empty($callbackData)) {
            throw new MarzPayException('Callback data cannot be empty');
        }
        
        // Add more validation as needed
        // For example, check for required fields, validate signatures, etc.
    }
    
    /**
     * Log callback for debugging/auditing
     */
    private function logCallback(string $type, array $data): void
    {
        // In a real implementation, you would log to a file, database, or monitoring service
        // For now, we'll just format the data for potential logging
        
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'type' => $type,
            'data' => $data
        ];
        
        // You could implement actual logging here
        // error_log('MarzPay Callback: ' . json_encode($logData));
    }
    
    /**
     * Verify callback signature (if using webhook signatures)
     */
    public function verifyCallbackSignature(string $payload, string $signature, string $secret): bool
    {
        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
    
    /**
     * Get callback URL for a transaction
     */
    public function getCallbackUrl(string $transactionId, ?string $baseUrl = null): string
    {
        $baseUrl = $baseUrl ?: $_SERVER['HTTP_HOST'] ?? 'localhost';
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        
        return $protocol . '://' . $baseUrl . '/marzpay/callback/' . $transactionId;
    }
}
