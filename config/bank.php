<?php

return [
    'name' => env('BANK_NAME', 'CentralBank'),
    'brand_initials' => env('BANK_BRAND_INITIALS', 'CB'),
    'country_code' => env('BANK_COUNTRY_CODE', 'BD'),
    'currency' => env('BANK_CURRENCY', 'BDT'),
    'support_email' => env('BANK_SUPPORT_EMAIL', 'support@centralbank.com'),
    'minimum_transfer_balance' => env('BANK_MINIMUM_TRANSFER_BALANCE', 500),
];
