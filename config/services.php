<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
      'key' => env('AWS_ACCESS_KEY_ID'),
      'secret' => env('AWS_SECRET_ACCESS_KEY'),
      'region' => env('SES_REGION'),
    ],

    'stripe' => [
      'model' => App\Models\User::class,
      'key' => config('app.stripe_key'),
      'secret' => config('app.stripe_secret'),
    ],

    'freeeaccounting' => [
        'template_id' => env('FREEE_TEMPLATE_ID'),
        'client_id' => env('FREEE_ACCOUNTING_CLIENT_ID'),
        'client_secret' => env('FREEE_ACCOUNTING_CLIENT_SECRET'),
        'account_item_id' => env('FREEE_ACCOUNT_ITEM_ID', ''),
        'discount_item_id' => env('FREEE_DISCOUNT_ITEM_ID', ''),
        'special_discount_item_id' => env('FREEE_SPECIAL_DISCOUNT_ITEM_ID', ''),
        'shipping_item_id' => env('FREEE_SHIPPING_ITEM_ID', ''),
        'other_item_id' => env('FREEE_OTHER_ITEM_ID', ''),
        // HP掲載料
        'listing_fee_account_item_id' => env('FREEE_LISTING_FEE_ACCOUNT_ITEM_ID', ''),
        'listing_fee_item_id' => env('FREEE_LISTING_FEE_ITEM_ID', ''),
        // ブランド使用料
        'brand_fee_item_id' => env('FREEE_BRAND_FEE_ITEM_ID', ''),
        'advance_payment_amount_id' => env('FREEE_ADVANCE_PAYMENT_AMOUNT_ID', ''),
        'advance_payment_tax_code' => env('FREEE_ADVANCE_PAYMENT_CODE', ''),
        // 税区分コード
        'tax_code' => env('FREEE_TAX_CODE', ''),
        'redirect' => env('FREEE_CALLBACK'),
    ],

    'slack' => [
      'web_hook_url' => env('SLACK_WEBHOOK_URL'),
    ]
];
