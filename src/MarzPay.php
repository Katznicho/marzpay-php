<?php

namespace MarzPay;

use MarzPay\Classes\CollectionsAPI;
use MarzPay\Classes\DisbursementsAPI;
use MarzPay\Classes\AccountsAPI;
use MarzPay\Classes\BalanceAPI;
use MarzPay\Classes\TransactionsAPI;
use MarzPay\Classes\ServicesAPI;
use MarzPay\Classes\WebhooksAPI;
use MarzPay\Classes\PhoneVerificationAPI;
use MarzPay\Utils\PhoneNumberUtils;
use MarzPay\Utils\GeneralUtils;
use MarzPay\Utils\CallbackHandler;
use MarzPay\Exceptions\MarzPayException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * MarzPay PHP SDK
 * 
 * Official PHP SDK for MarzPay - Mobile Money Payment Platform for Uganda
 * 
 * @example
 * ```php
 * use MarzPay\MarzPay;
 * 
 * $marzpay = new MarzPay([
 *     'api_key' => 'your_api_key',
 *     'api_secret' => 'your_api_secret'
 * ]);
 * 
 * // Collect money from customer
 * $result = $marzpay->collections()->collectMoney([
 *     'amount' => 5000,
 *     'phone_number' => '0759983853',
 *     'reference' => $marzpay->collections()->generateReference(),
 *     'description' => 'Payment for services'
 * ]);
 * ```
 */
class MarzPay
{
    /**
     * @var array Configuration array
     */
    private $config;

    /**
     * @var Client HTTP client instance
     */
    private $client;

    /**
     * @var CollectionsAPI Collections API instance
     */
    private $collections;

    /**
     * @var DisbursementsAPI Disbursements API instance
     */
    private $disbursements;

    /**
     * @var AccountsAPI Accounts API instance
     */
    private $accounts;

    /**
     * @var BalanceAPI Balance API instance
     */
    private $balance;

    /**
     * @var TransactionsAPI Transactions API instance
     */
    private $transactions;

    /**
     * @var ServicesAPI Services API instance
     */
    private $services;

    /**
     * @var WebhooksAPI Webhooks API instance
     */
    private $webhooks;

    /**
     * @var PhoneVerificationAPI Phone Verification API instance
     */
    private $phoneVerification;

    /**
     * @var PhoneNumberUtils Phone number utilities instance
     */
    private $phoneUtils;

    /**
     * @var GeneralUtils General utilities instance
     */
    private $utils;

    /**
     * @var CallbackHandler Callback handler instance
     */
    private $callbackHandler;

    /**
     * Create a new MarzPay instance
     * 
     * @param array $config Configuration array
     * @param array $config['api_key'] Your MarzPay API key
     * @param array $config['api_secret'] Your MarzPay API secret
     * @param string $config['base_url'] API base URL (default: https://wallet.wearemarz.com/api/v1)
     * @param int $config['timeout'] Request timeout in seconds (default: 30)
     * 
     * @throws MarzPayException When API credentials are missing
     */
    public function __construct(array $config)
    {
        if (empty($config['api_key'])) {
            throw new MarzPayException('API key is required', 'MISSING_API_KEY', 400);
        }
        
        if (empty($config['api_secret'])) {
            throw new MarzPayException('API secret is required', 'MISSING_API_SECRET', 400);
        }

        $this->config = array_merge([
            'base_url' => 'https://wallet.wearemarz.com/api/v1',
            'timeout' => 30,
        ], $config);

        $this->client = new Client([
            'base_uri' => $this->config['base_url'],
            'timeout' => $this->config['timeout'],
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->getAuthHeader(),
            ],
        ]);

        // Initialize API modules
        $this->collections = new CollectionsAPI($this);
        $this->disbursements = new DisbursementsAPI($this);
        $this->accounts = new AccountsAPI($this);
        $this->balance = new BalanceAPI($this);
        $this->transactions = new TransactionsAPI($this);
        $this->services = new ServicesAPI($this);
        $this->webhooks = new WebhooksAPI($this);
        $this->phoneVerification = new PhoneVerificationAPI($this);

        // Initialize utility modules
        $this->phoneUtils = new PhoneNumberUtils();
        $this->utils = new GeneralUtils();
        $this->callbackHandler = new CallbackHandler($this);
    }

    /**
     * Make HTTP request to MarzPay API
     * 
     * @param string $endpoint API endpoint (without base URL)
     * @param array $options Request options
     * @param string $options['method'] HTTP method (default: GET)
     * @param array $options['body'] Request body
     * @param array $options['headers'] Additional headers
     * 
     * @return array API response
     * 
     * @throws MarzPayException When request fails
     */
    public function request(string $endpoint, array $options = []): array
    {
        $method = $options['method'] ?? 'GET';
        $body = $options['body'] ?? null;
        $headers = $options['headers'] ?? [];

        $requestOptions = [
            'headers' => array_merge([
                'Authorization' => $this->getAuthHeader(),
            ], $headers),
        ];

        if ($body && in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            // Check if we should send as multipart form data
            if (isset($options['content_type']) && $options['content_type'] === 'multipart') {
                $multipart = [];
                foreach ($body as $key => $value) {
                    $multipart[] = [
                        'name' => $key,
                        'contents' => $value
                    ];
                }
                $requestOptions['multipart'] = $multipart;
            } elseif (isset($options['content_type']) && $options['content_type'] === 'form') {
                $requestOptions['form_params'] = $body;
            } else {
                $requestOptions['json'] = $body;
            }
        }

        try {
            // Construct full URL manually to ensure base_uri is used correctly
            $fullUrl = $this->config['base_url'] . $endpoint;
            $response = $this->client->request($method, $fullUrl, $requestOptions);
            $responseBody = $response->getBody()->getContents();
            $responseData = json_decode($responseBody, true);

            // Handle empty or null response
            if ($responseData === null) {
                return [
                    'status' => 'error',
                    'message' => 'Empty or invalid response from API',
                    'data' => null
                ];
            }

            return $responseData;
        } catch (RequestException $e) {
            $response = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 0;
            $responseBody = $response ? $response->getBody()->getContents() : '';
            
            $errorData = json_decode($responseBody, true);
            $message = $errorData['message'] ?? $e->getMessage();
            $code = $errorData['code'] ?? 'REQUEST_FAILED';

            throw new MarzPayException($message, $code, $statusCode);
        } catch (\Exception $e) {
            throw new MarzPayException(
                $e->getMessage() ?: 'Request failed',
                'REQUEST_FAILED',
                0
            );
        }
    }

    /**
     * Update API credentials at runtime
     * 
     * @param string $apiKey New API key
     * @param string $apiSecret New API secret
     * 
     * @throws MarzPayException When credentials are missing
     */
    public function setCredentials(string $apiKey, string $apiSecret): void
    {
        if (empty($apiKey) || empty($apiSecret)) {
            throw new MarzPayException('Both API key and secret are required', 'MISSING_CREDENTIALS', 400);
        }

        $this->config['api_key'] = $apiKey;
        $this->config['api_secret'] = $apiSecret;

        // Update client headers
        $this->client = new Client([
            'base_uri' => $this->config['base_url'],
            'timeout' => $this->config['timeout'],
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => $this->getAuthHeader(),
            ],
        ]);
    }

    /**
     * Get the current authentication header
     * 
     * @return string Base64 encoded authorization header
     */
    public function getAuthHeader(): string
    {
        $credentials = $this->config['api_key'] . ':' . $this->config['api_secret'];
        return 'Basic ' . base64_encode($credentials);
    }

    /**
     * Get SDK version and information
     * 
     * @return array SDK information
     */
    public function getInfo(): array
    {
        return [
            'name' => 'MarzPay PHP SDK',
            'version' => '1.0.0',
            'description' => 'Official PHP SDK for MarzPay - Mobile Money Payment Platform for Uganda',
            'base_url' => $this->config['base_url'],
            'features' => [
                'Collections API',
                'Disbursements API',
                'Accounts API',
                'Balance API',
                'Transactions API',
                'Services API',
                'Webhooks API',
                'Phone Verification API',
                'Phone Number Utilities',
                'General Utilities',
                'Error Handling',
                'Laravel Integration'
            ]
        ];
    }

    /**
     * Make HTTP request to MarzPay API (alias for request method)
     * 
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array $data Request data
     * @return array API response
     */
    public function makeRequest(string $method, string $endpoint, array $data = []): array
    {
        $options = [
            'method' => $method,
            'body' => $data
        ];
        
        return $this->request($endpoint, $options);
    }

    /**
     * Test API connection
     * 
     * @return array Connection test result
     */
    public function testConnection(): array
    {
        try {
            $response = $this->request('/account');
            return [
                'status' => 'success',
                'message' => 'API connection successful',
                'data' => [
                    'account_status' => $response['data']['account']['status']['account_status'] ?? 'unknown',
                    'business_name' => $response['data']['account']['business_name'] ?? 'unknown'
                ]
            ];
        } catch (MarzPayException $e) {
            return [
                'status' => 'error',
                'message' => 'API connection failed',
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }

    // Getters for API modules

    public function collections(): CollectionsAPI
    {
        return $this->collections;
    }

    public function disbursements(): DisbursementsAPI
    {
        return $this->disbursements;
    }

    public function accounts(): AccountsAPI
    {
        return $this->accounts;
    }

    public function balance(): BalanceAPI
    {
        return $this->balance;
    }

    public function transactions(): TransactionsAPI
    {
        return $this->transactions;
    }

    public function services(): ServicesAPI
    {
        return $this->services;
    }

    public function webhooks(): WebhooksAPI
    {
        return $this->webhooks;
    }

    public function phoneVerification(): PhoneVerificationAPI
    {
        return $this->phoneVerification;
    }

    public function phoneUtils(): PhoneNumberUtils
    {
        return $this->phoneUtils;
    }

    public function utils(): GeneralUtils
    {
        return $this->utils;
    }

    public function callbackHandler(): CallbackHandler
    {
        return $this->callbackHandler;
    }

    // Utility methods

    public function formatPhoneNumber(string $phoneNumber): string
    {
        return $this->phoneUtils->formatPhoneNumber($phoneNumber);
    }

    public function isValidPhoneNumber(string $phoneNumber): bool
    {
        return $this->phoneUtils->isValidPhoneNumber($phoneNumber);
    }

    public function generateReference(): string
    {
        return $this->utils->generateReference();
    }

    public function generateReferenceWithPrefix(string $prefix = 'REF'): string
    {
        return $this->utils->generateReferenceWithPrefix($prefix);
    }
}
