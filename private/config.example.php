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

    // Products and bundles. Prices are ALWAYS taken from here on the server,
    // never from the browser. Prices are in whole US dollars.
    'offers' => [
        'mars-pillow' => [
            'id' => 'single', 'name' => 'Mars Pillow', 'price' => 99, 'compare_at_price' => null,
            'short' => 'One contoured memory foam pillow with a washable cover.',
            'includes' => ['1 Mars Pillow', 'Washable cover included'],
            'units' => ['p'], 'badge' => null, 'image' => 'assets/img/product-single.jpg',
            'amazon_url' => '', // [TODO] paste the Amazon listing link; leave empty to hide the button
        ],
        'mars-sleep-pack' => [
            'id' => 'sleep-pack', 'name' => 'Mars Sleep Pack', 'price' => 129, 'compare_at_price' => 138,
            'short' => 'One pillow plus a spare Mars Cover, so one is always fresh.',
            'includes' => ['1 Mars Pillow', '1 extra Mars Cover'],
            'units' => ['p', 'c'], 'badge' => null, 'image' => 'assets/img/product-sleep-pack.jpg',
            'amazon_url' => '',
        ],
        'mars-twin-pack' => [
            'id' => 'twin-pack', 'name' => 'Mars Twin Pack', 'price' => 179, 'compare_at_price' => 198,
            'short' => 'Two pillows, one for each side of the bed.',
            'includes' => ['2 Mars Pillows', 'One for each side of the bed'],
            'units' => ['p', 'p'], 'badge' => 'Most Popular', 'image' => 'assets/img/product-twin-pack.jpg',
            'amazon_url' => '',
        ],
        'interstellar-suite' => [
            'id' => 'interstellar', 'name' => 'Interstellar Suite', 'price' => 229, 'compare_at_price' => 276,
            'short' => 'Two pillows and two spare covers for the whole bed.',
            'includes' => ['2 Mars Pillows', '2 extra Mars Covers'],
            'units' => ['p', 'p', 'c', 'c'], 'badge' => 'Best Value', 'image' => 'assets/img/product-interstellar.jpg',
            'amazon_url' => '',
        ],
    ],
    'default_offer' => 'mars-twin-pack',

    // Product details shown on the product page. Fill in to match the real product.
    'product' => [
        'layers' => [
            ['name' => 'Breathable cover', 'text' => 'Soft knit you can zip off and wash. [TODO fabric]'],
            ['name' => 'Cooling layer',    'text' => 'Helps move heat away from your face and neck. [TODO]'],
            ['name' => 'Memory foam core', 'text' => 'Slow-recovery foam that softens under your head and stays firm under your neck.'],
            ['name' => 'Contour base',     'text' => 'The wave shape that gives the pillow its two heights. [TODO]'],
        ],
        'size'      => '[TODO] 24 x 14 in, 4.3 in / 3.5 in contour heights',
        'weight'    => '[TODO] 3.3 lb',
        'materials' => '[TODO] Cover: 60% polyester, 40% viscose. Core: CertiPUR-US® certified memory foam (only if certified).',
        'care'      => 'Unzip the cover and machine wash it cold on a gentle cycle, then air dry. Do not wash the foam core. Air it out instead. [TODO confirm]',
    ],

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

    // "Mars vs. the big brand" table on the product page and the ad landing page.
    // Only facts you can prove. Update the date and prices whenever you check them.
    'comparison' => [
        'brand'      => 'TEMPUR-Neck',
        'checked_on' => '2026-10-26',
        'source_url' => 'https://www.tomsguide.com/mattresses/pillows-bedding/tempur-pedic-tempur-neck-pillow-review',
        'rows' => [
            ['Price (one pillow)', '$99', '$119–$159 list price'],
            ['Sleep trial', '60 nights', 'No trial'],
            ['Warranty', '3 years', '5 years'],
            ['Heights', 'Two heights in one pillow', 'Choose one of 3 sizes'],
            ['Cover', 'Zip-off, machine washable', 'Removable knit cover'],
            ['Shipping', 'Free in the US', 'Free in the US'],
        ],
    ],
];
