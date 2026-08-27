<?php

return [
    /*
    |--------------------------------------------------------------------------
    | M-Pesa API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your M-Pesa API settings.
    | Use sandbox for testing and production for live transactions.
    |
    */

    'public_key' => env('MPESA_PUBLIC_KEY', ''),
    
    'api_host' => env('MPESA_API_HOST', 'https://api.sandbox.vm.co.mz'),
    
    'api_key' => env('MPESA_API_KEY', ''),
    
    'origin' => env('MPESA_ORIGIN', 'developer.mpesa.vm.co.mz'),
    
    'service_provider_code' => env('MPESA_SERVICE_PROVIDER_CODE', ''),
    
    'initiator_identifier' => env('MPESA_INITIATOR_IDENTIFIER', ''),
    
    'security_credential' => env('MPESA_SECURITY_CREDENTIAL', ''),
];
