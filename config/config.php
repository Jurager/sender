<?php


return [

    /*
    |--------------------------------------------------------------------------
    | UserAgent to communicate with sms-assistent.by
    |--------------------------------------------------------------------------
    */
    'agent' => env('SENDER_CONNECTION', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)'),

    /*
    |--------------------------------------------------------------------------
    | Username to authenticate at sms-assistent.by
    |--------------------------------------------------------------------------
    */
    'username' => env('SENDER_USERNAME', 'example'),

    /*
    |--------------------------------------------------------------------------
    | Password to authenticate at sms-assistent.by
    |--------------------------------------------------------------------------
    */
    'password' => env('SENDER_PASSWORD', 'qwerty'),

    /*
    |--------------------------------------------------------------------------
    | Name will be displayed to recipients
    |--------------------------------------------------------------------------
    */
    'name' => env('SENDER_NAME', 'John Doe'),

];