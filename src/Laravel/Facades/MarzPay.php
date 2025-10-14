<?php

namespace MarzPay\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use MarzPay\Classes\CollectionsAPI;
use MarzPay\Classes\DisbursementsAPI;
use MarzPay\Classes\AccountsAPI;
use MarzPay\Classes\BalanceAPI;
use MarzPay\Classes\TransactionsAPI;
use MarzPay\Classes\ServicesAPI;
use MarzPay\Classes\WebhooksAPI;
use MarzPay\Classes\PhoneVerificationAPI;

/**
 * MarzPay Facade
 * 
 * @method static CollectionsAPI collections()
 * @method static DisbursementsAPI disbursements()
 * @method static AccountsAPI accounts()
 * @method static BalanceAPI balance()
 * @method static TransactionsAPI transactions()
 * @method static ServicesAPI services()
 * @method static WebhooksAPI webhooks()
 * @method static PhoneVerificationAPI phoneVerification()
 * @method static array getInfo()
 * @method static array testConnection()
 * @method static void setCredentials(string $apiKey, string $apiSecret)
 * @method static string getAuthHeader()
 */
class MarzPay extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \MarzPay\MarzPay::class;
    }
}

