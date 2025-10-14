<?php

namespace MarzPay\Utils;

class PhoneNumberUtils
{
    /**
     * Format phone number to international format
     */
    public static function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Handle different formats
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            // Local format: 0759983853 -> 256759983853
            return '256' . substr($phone, 1);
        } elseif (strlen($phone) === 12 && substr($phone, 0, 3) === '256') {
            // Already in international format
            return $phone;
        } elseif (strlen($phone) === 9) {
            // Without leading zero: 759983853 -> 256759983853
            return '256' . $phone;
        }
        
        // Return as-is if format is not recognized
        return $phone;
    }
    
    /**
     * Validate phone number format
     */
    public static function isValidPhoneNumber(string $phoneNumber): bool
    {
        if (empty($phoneNumber)) {
            return false;
        }
        
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Check if it's a valid Ugandan mobile number
        // Format: 256XXXXXXXXX (12 digits starting with 256)
        if (strlen($phone) === 12 && substr($phone, 0, 3) === '256') {
            $mobilePrefix = substr($phone, 3, 2);
            // Valid Ugandan mobile prefixes: 70, 71, 72, 74, 75, 76, 77, 78, 79
            return in_array($mobilePrefix, ['70', '71', '72', '74', '75', '76', '77', '78', '79']);
        }
        
        // Check local format: 0XXXXXXXXX (10 digits starting with 0)
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $mobilePrefix = substr($phone, 1, 2);
            // Valid Ugandan mobile prefixes: 70, 71, 72, 74, 75, 76, 77, 78, 79
            return in_array($mobilePrefix, ['70', '71', '72', '74', '75', '76', '77', '78', '79']);
        }
        
        // Check without leading zero: XXXXXXXXX (9 digits)
        if (strlen($phone) === 9) {
            $mobilePrefix = substr($phone, 0, 2);
            // Valid Ugandan mobile prefixes: 70, 71, 72, 74, 75, 76, 77, 78, 79
            return in_array($mobilePrefix, ['70', '71', '72', '74', '75', '76', '77', '78', '79']);
        }
        
        return false;
    }
    
    /**
     * Get mobile network operator from phone number
     */
    public static function getNetworkOperator(string $phoneNumber): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        if (strlen($phone) >= 9) {
            $prefix = substr($phone, -9, 2);
            
            switch ($prefix) {
                case '70':
                case '71':
                case '77':
                case '78':
                    return 'MTN';
                case '75':
                case '76':
                    return 'Airtel';
                case '72':
                case '79':
                    return 'Africell';
                case '74':
                    return 'K2';
                default:
                    return null;
            }
        }
        
        return null;
    }
}
