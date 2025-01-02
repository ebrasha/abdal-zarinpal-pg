<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Zarinpal Merchant ID
    |--------------------------------------------------------------------------
    |
    | This value is the merchant ID provided by Zarinpal for your account.
    | It is used to authenticate your requests to the Zarinpal API.
    |
    */
    'merchant_id' => env('ZARINPAL_MERCHANT_ID', '00000000-0000-0000-0000-000000000000'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This value defines the default currency for your transactions.
    | You can override this value in your code if needed.
    |
    */
    'currency' => env('ZARINPAL_CURRENCY', 'IRT'),

];
