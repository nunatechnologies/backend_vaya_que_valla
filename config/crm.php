<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CRM Backend Base URL
    |--------------------------------------------------------------------------
    |
    | This value is the base URL of your CRM backend, which will be used when
    | the application needs to make requests to the CRM backend services.
    |
    */
    'base_url' => env('CRM_BACKEND_BASE_URL', 'https://crm-back.vayaquevalla.com'),

    'user_email' => env('CRM_USER_EMAIL'),
    'user_password' => env('CRM_USER_PASSWORD'),

];
