<?php

namespace MarzPay\Exceptions;

use Exception;

/**
 * MarzPay Custom Exception Class
 * 
 * Extends the standard Exception class to provide additional context
 * for MarzPay-specific errors including error codes and HTTP status.
 * 
 * @extends Exception
 * 
 * @example
 * ```php
 * try {
 *     $marzpay->collections()->collectMoney($invalidParams);
 * } catch (MarzPayException $e) {
 *     echo "Error Code: " . $e->getCode();
 *     echo "HTTP Status: " . $e->getStatus();
 *     echo "Message: " . $e->getMessage();
 * }
 * ```
 */
class MarzPayException extends Exception
{
    /**
     * @var string Error code for programmatic handling
     */
    protected $errorCode;

    /**
     * @var int HTTP status code
     */
    protected $statusCode;

    /**
     * @var array Additional error details
     */
    protected $details;

    /**
     * Create a new MarzPayException
     * 
     * @param string $message Error message
     * @param string $errorCode Error code for programmatic handling
     * @param int $statusCode HTTP status code
     * @param array $details Additional error details
     */
    public function __construct(string $message = '', string $errorCode = 'UNKNOWN_ERROR', int $statusCode = 0, array $details = [])
    {
        parent::__construct($message, $statusCode);
        $this->errorCode = $errorCode;
        $this->statusCode = $statusCode;
        $this->details = $details;
    }

    /**
     * Get the error code
     * 
     * @return string Error code
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * Get the HTTP status code
     * 
     * @return int HTTP status code
     */
    public function getStatus(): int
    {
        return $this->statusCode;
    }

    /**
     * Get additional error details
     * 
     * @return array Error details
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * Create exception from API response
     * 
     * @param array $responseData API response data
     * @param int $statusCode HTTP status code
     * @return MarzPayException
     */
    public static function fromResponse(array $responseData, int $statusCode): self
    {
        $message = $responseData['message'] ?? 'API request failed';
        $errorCode = $responseData['code'] ?? 'API_ERROR';
        $details = $responseData['details'] ?? [];

        return new self($message, $errorCode, $statusCode, $details);
    }

    /**
     * Create network error exception
     * 
     * @param string $message Network error message
     * @return MarzPayException
     */
    public static function networkError(string $message = 'Network request failed'): self
    {
        return new self($message, 'NETWORK_ERROR', 0);
    }

    /**
     * Create validation error exception
     * 
     * @param string $message Validation error message
     * @param array $details Validation details
     * @return MarzPayException
     */
    public static function validationError(string $message, array $details = []): self
    {
        return new self($message, 'VALIDATION_ERROR', 400, $details);
    }

    /**
     * Create authentication error exception
     * 
     * @param string $message Authentication error message
     * @return MarzPayException
     */
    public static function authenticationError(string $message = 'Authentication failed'): self
    {
        return new self($message, 'AUTHENTICATION_ERROR', 401);
    }

    /**
     * Create authorization error exception
     * 
     * @param string $message Authorization error message
     * @return MarzPayException
     */
    public static function authorizationError(string $message = 'Authorization failed'): self
    {
        return new self($message, 'AUTHORIZATION_ERROR', 403);
    }

    /**
     * Create not found error exception
     * 
     * @param string $message Not found error message
     * @return MarzPayException
     */
    public static function notFoundError(string $message = 'Resource not found'): self
    {
        return new self($message, 'NOT_FOUND_ERROR', 404);
    }

    /**
     * Create rate limit error exception
     * 
     * @param string $message Rate limit error message
     * @return MarzPayException
     */
    public static function rateLimitError(string $message = 'Rate limit exceeded'): self
    {
        return new self($message, 'RATE_LIMIT_ERROR', 429);
    }

    /**
     * Create server error exception
     * 
     * @param string $message Server error message
     * @return MarzPayException
     */
    public static function serverError(string $message = 'Internal server error'): self
    {
        return new self($message, 'SERVER_ERROR', 500);
    }

    /**
     * Convert exception to array
     * 
     * @return array Exception as array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->errorCode,
            'status' => $this->statusCode,
            'details' => $this->details,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }

    /**
     * Convert exception to JSON string
     * 
     * @return string Exception as JSON string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * Set the error code
     * 
     * @param string $errorCode Error code
     * @return void
     */
    public function setErrorCode(string $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    /**
     * Set the HTTP status code
     * 
     * @param int $statusCode HTTP status code
     * @return void
     */
    public function setStatus(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    /**
     * Set response data
     * 
     * @param array $responseData Response data
     * @return void
     */
    public function setResponseData(array $responseData): void
    {
        $this->details = $responseData;
    }

    /**
     * Get response data
     * 
     * @return array Response data
     */
    public function getResponseData(): array
    {
        return $this->details;
    }

    /**
     * Create exception from API response
     * 
     * @param array $apiResponse API response data
     * @return MarzPayException
     */
    public static function fromApiResponse(array $apiResponse): self
    {
        $message = $apiResponse['message'] ?? 'API request failed';
        $errorCode = $apiResponse['error_code'] ?? 'API_ERROR';
        $status = $apiResponse['status'] ?? 'error';
        $data = $apiResponse['data'] ?? [];

        $exception = new self($message);
        $exception->setErrorCode($errorCode);
        $exception->setStatus($status === 'error' ? 400 : 200);
        $exception->setResponseData($data);

        return $exception;
    }
}
