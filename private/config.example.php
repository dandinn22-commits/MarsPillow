<?php
/**
 * Mars Pillow — site configuration.
 *
 * Copy this file to config.php (same folder) and fill in your values.
 * This folder ("private") must stay OUTSIDE public_html so nobody can download it.
 *
 * Switching from test to live: change 'mode' => 'test' to 'mode' => 'live'
 * and make sure the live keys below are filled in. Nothing else needs to change.
 */
return [

    // 'test' = Stripe test keys + PayPal sandbox + errors shown on screen.
    // 'live' = real payments, errors only written to the log file.
    'mode' => 'test',

    'site' => [
        'name'             => 'Mars Pillow',
        'base_url'         => 'https://marspillows.com',   // your domain, no trailing slash
        'support_email'    => 'support@marspillows.com',    // shown to customers on the site
        'business_name'    => 'Mars Pillow LLC',            // [TODO] legal business name
        'business_address' => '[TODO] Street, City, State ZIP, USA',
        'trial_nights'     => 60,
        'warranty_years'   => 3,
        'shipping_days'    => '[TODO] 3–7',                 // business days, shown on the site
        'currency'         => 'USD',
    ],

    // Products, bundles, prices, pillow details and the comparison table are in private/catalog.php

    'db' => [
        // Hostinger: hPanel > Databases > MySQL Databases
        'dsn'  => 'mysql:host=localhost;dbname=[TODO]_marspillow;charset=utf8mb4',
        'user' => '[TODO]',
        'pass' => '[TODO]',
    ],

    'stripe' => [
        'test' => [
            'secret_key'     => 'sk_test_[TODO]',
            'webhook_secret' => 'whsec_[TODO]',
        ],
        'live' => [
            'secret_key'     => 'sk_live_[TODO]',
            'webhook_secret' => 'whsec_[TODO]',
        ],
    ],

    'paypal' => [
        'test' => [ // sandbox app from developer.paypal.com
            'client_id' => '[TODO]',
            'secret'    => '[TODO]',
        ],
        'live' => [
            'client_id' => '[TODO]',
            'secret'    => '[TODO]',
        ],
    ],

    'mail' => [
        'from_email'  => 'orders@marspillows.com',   // alias of the Google Workspace user
        'from_name'   => 'Mars Pillow',
        'admin_email' => 'dan@marspillows.com',      // where new-order and contact emails go
        // SMTP via Google Workspace. 'user' is the real Workspace user (not the alias).
        // 'pass' is an App Password: myaccount.google.com > Security > 2-Step Verification > App passwords.
        // If 'enabled' is false, PHP mail() is used instead.
        'smtp' => [
            'enabled'  => true,
            'host'     => 'smtp.gmail.com',
            'port'     => 587,
            'secure'   => 'tls', // 'ssl' for 465, 'tls' for 587
            'user'     => 'dan@marspillows.com',
            'pass'     => '[TODO] 16-letter app password',
        ],
    ],

    'admin' => [
        // Create a hash: run  php -r "echo password_hash('YourStrongPassword', PASSWORD_DEFAULT);"
        // or open /admin/make-hash once (only works in test mode). Paste the result here.
        'password_hash' => '[TODO]',
    ],

    'tracking' => [
        // Loaded only after the visitor accepts cookies. Leave empty to turn off.
        'ga4_id'           => 'G-R0J9GHL9SM',
        'meta_pixel_id' => '',  // e.g. 1234567890
        // Meta Conversions API: sends purchases from the server too, so Meta still counts them
        // when the browser blocks the pixel. Events Manager > your pixel > Settings > Generate access token.
        'meta_capi_token' => '',
        'meta_test_event_code' => '', // e.g. TEST12345 while testing in Events Manager; empty when live
        // Google Ads purchase conversion (with enhanced conversions). Google Ads > Goals > Conversions.
        'google_ads_id'    => 'AW-18539204074',
        'google_ads_label' => '',  // [TODO] label of the Purchase conversion action
    ],

    // Discount codes. 'percent' off the order. The popup gives out 'popup_code'.
    'coupons' => [
        'WELCOME10' => ['percent' => 10, 'label' => '10% off your first order'],
    ],
    'popup_code' => 'WELCOME10',

    // Emails sent by the scheduled task (cron), see DEPLOY.md step 11
    'automations' => [
        'review_request_days' => 21,   // days after shipping to ask for a review
        'cart_reminder_hours' => 3,    // hours after someone leaves a cart to remind them
    ],

];
