<?php

namespace MarzPay\Utils;

use Ramsey\Uuid\Uuid;

class GeneralUtils
{
    /**
     * Generate a unique reference
     */
    public static function generateReference(): string
    {
        return Uuid::uuid4()->toString();
    }
    
    /**
     * Generate a unique reference with prefix
     */
    public static function generateReferenceWithPrefix(string $prefix = 'REF'): string
    {
        $uuid = Uuid::uuid4()->toString();
        return strtoupper($prefix) . '_' . str_replace('-', '', $uuid);
    }
    
    /**
     * Generate a short reference (8 characters)
     */
    public static function generateShortReference(): string
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }
    
    /**
     * Format amount to cents
     */
    public static function formatAmountToCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
    
    /**
     * Format amount from cents
     */
    public static function formatAmountFromCents(int $cents): float
    {
        return $cents / 100;
    }
    
    /**
     * Format currency amount
     */
    public static function formatCurrency(float $amount, string $currency = 'UGX'): string
    {
        return number_format($amount, 0, '.', ',') . ' ' . $currency;
    }
    
    /**
     * Validate email address
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString(string $input): string
    {
        return trim(strip_tags($input));
    }
    
    /**
     * Generate random string
     */
    public static function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        return $randomString;
    }
    
    /**
     * Convert array to query string
     */
    public static function arrayToQueryString(array $params): string
    {
        return http_build_query($params);
    }
    
    /**
     * Convert query string to array
     */
    public static function queryStringToArray(string $queryString): array
    {
        parse_str($queryString, $params);
        return $params;
    }
    
    /**
     * Get current timestamp
     */
    public static function getCurrentTimestamp(): int
    {
        return time();
    }
    
    /**
     * Get current date in ISO format
     */
    public static function getCurrentDateISO(): string
    {
        return date('c');
    }
    
    /**
     * Get current date in MySQL format
     */
    public static function getCurrentDateMySQL(): string
    {
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Check if string is JSON
     */
    public static function isJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    
    /**
     * Pretty print JSON
     */
    public static function prettyPrintJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
