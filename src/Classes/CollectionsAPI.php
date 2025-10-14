<?php

namespace MarzPay\Classes;

use MarzPay\MarzPay;
use MarzPay\Exceptions\MarzPayException;
use Ramsey\Uuid\Uuid;

/**
 * Collections API - Money collection from customers via mobile money
 * 
 * This class handles all collection-related operations including:
 * - Initiating money collections
 * - Retrieving collection details
 * - Getting available collection services
 * 
 * @example
 * ```php
 * $marzpay = new MarzPay($config);
 * 
 * // Collect money from customer
 * $result = $marzpay->collections()->collectMoney([
 *     'amount' => 5000,
 *     'phone_number' => '0759983853',
 *     'reference' => '550e8400-e29b-41d4-a716-446655440000',
 *     'description' => 'Payment for services'
 * ]);
 * ```
 */
class CollectionsAPI
{
    /**
     * @var MarzPay MarzPay instance
     */
    private $marzpay;

    /**
     * Create a new CollectionsAPI instance
     * 
     * @param MarzPay $marzpay MarzPay instance
     */
    public function __construct(MarzPay $marzpay)
    {
        $this->marzpay = $marzpay;
    }

    /**
     * Collect money from a customer via mobile money
     * 
     * @param array $params Collection parameters
     * @param int $params['amount'] Amount in UGX (500-10,000,000)
     * @param string $params['phone_number'] Customer's phone number
     * @param string $params['reference'] Unique UUID4 reference for the transaction
     * @param string|null $params['description'] Payment description
     * @param string|null $params['callback_url'] Custom webhook URL
     * @param string $params['country'] Country code (default: UG)
     * 
     * @return array Collection result with transaction details
     * 
     * @throws MarzPayException When validation fails or API request fails
     * 
     * @example
     * ```php
     * try {
     *     $result = $marzpay->collections()->collectMoney([
     *         'amount' => 10000,
     *         'phone_number' => '0759983853',
     *         'reference' => $marzpay->collections()->generateReference(),
     *         'description' => 'Payment for services'
     *     ]);
     *     
     *     echo "Collection ID: " . $result['data']['collection_id'];
     * } catch (MarzPayException $e) {
     *     echo "Error: " . $e->getMessage();
     * }
     * ```
     */
    public function collectMoney(array $params): array
    {
        $this->validateCollectMoneyParams($params);

        $payload = [
            'amount' => $params['amount'],
            'phone_number' => $this->formatPhoneNumber($params['phone_number']),
            'reference' => $params['reference'],
            'description' => $params['description'] ?? null,
            'callback_url' => $params['callback_url'] ?? null,
            'country' => $params['country'] ?? 'UG',
        ];

        return $this->marzpay->request('/collections', [
            'method' => 'POST',
            'body' => $payload
        ]);
    }

    /**
     * Get collection details by ID
     * 
     * @param string $collectionId Collection ID
     * @return array Collection details
     * 
     * @throws MarzPayException When request fails
     */
    public function getCollection(string $collectionId): array
    {
        if (empty($collectionId)) {
            throw MarzPayException::validationError('Collection ID is required');
        }

        return $this->marzpay->request("/collections/{$collectionId}");
    }

    /**
     * Get all collections with optional filters
     * 
     * @param array $filters Optional filters
     * @param int $filters['page'] Page number (default: 1)
     * @param int $filters['limit'] Items per page (default: 20)
     * @param string $filters['status'] Filter by status
     * @param string $filters['from_date'] Filter from date (YYYY-MM-DD)
     * @param string $filters['to_date'] Filter to date (YYYY-MM-DD)
     * 
     * @return array Collections list
     */
    public function getCollections(array $filters = []): array
    {
        $queryParams = [];
        
        if (isset($filters['page'])) {
            $queryParams['page'] = $filters['page'];
        }
        
        if (isset($filters['limit'])) {
            $queryParams['limit'] = $filters['limit'];
        }
        
        if (isset($filters['status'])) {
            $queryParams['status'] = $filters['status'];
        }
        
        if (isset($filters['from_date'])) {
            $queryParams['from_date'] = $filters['from_date'];
        }
        
        if (isset($filters['to_date'])) {
            $queryParams['to_date'] = $filters['to_date'];
        }

        $endpoint = '/collections';
        if (!empty($queryParams)) {
            $endpoint .= '?' . http_build_query($queryParams);
        }

        return $this->marzpay->request($endpoint);
    }

    /**
     * Get available collection services
     * 
     * @return array Available services
     */
    public function getServices(): array
    {
        return $this->marzpay->request('/collections/services');
    }

    /**
     * Generate a unique reference for collections
     * 
     * @return string UUID4 reference
     */
    public function generateReference(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * Validate collect money parameters
     * 
     * @param array $params Parameters to validate
     * @throws MarzPayException When validation fails
     */
    private function validateCollectMoneyParams(array $params): void
    {
        $errors = [];

        // Validate amount
        if (!isset($params['amount'])) {
            $errors[] = 'Amount is required';
        } elseif (!is_numeric($params['amount'])) {
            $errors[] = 'Amount must be numeric';
        } elseif ($params['amount'] < 500) {
            $errors[] = 'Amount must be at least 500 UGX';
        } elseif ($params['amount'] > 10000000) {
            $errors[] = 'Amount must not exceed 10,000,000 UGX';
        }

        // Validate phone number
        if (!isset($params['phone_number'])) {
            $errors[] = 'Phone number is required';
        } elseif (empty($params['phone_number'])) {
            $errors[] = 'Phone number cannot be empty';
        }

        // Validate reference
        if (!isset($params['reference'])) {
            $errors[] = 'Reference is required';
        } elseif (empty($params['reference'])) {
            $errors[] = 'Reference cannot be empty';
        }

        if (!empty($errors)) {
            throw MarzPayException::validationError('Validation failed', $errors);
        }
    }

    /**
     * Format phone number for API
     * 
     * @param string $phoneNumber Phone number to format
     * @return string Formatted phone number
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
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

