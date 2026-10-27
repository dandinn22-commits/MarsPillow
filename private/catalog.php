<?php
/**
 * Mars Pillow catalog: bundles, prices, pillow details and the comparison table.
 * Not secret, so it lives here (in git) instead of config.php. Values here override config.php.
 */
return [
    // Products and bundles. Prices are ALWAYS taken from here on the server,
    // never from the browser. Prices are in whole US dollars.
    'offers' => [
        'mars-pillow' => [
            'id' => 'single', 'name' => 'Mars Pillow', 'price' => 99, 'compare_at_price' => null,
            'short' => 'One adjustable 3-layer memory foam pillow with a soft side and a firm side.',
            'includes' => ['1 Mars Pillow (3 removable layers)', 'Bamboo-viscose cover included'],
            'units' => ['p'], 'badge' => null, 'image' => 'assets/img/product-single.jpg',
            'amazon_url' => '', // [TODO] paste the Amazon listing link; leave empty to hide the button
        ],
        'mars-sleep-pack' => [
            'id' => 'sleep-pack', 'name' => 'Mars Sleep Pack', 'price' => 129, 'compare_at_price' => 138,
            'short' => 'One pillow plus a spare Mars Cover, so one is always fresh.',
            'includes' => ['1 Mars Pillow (3 removable layers)', '1 extra Mars Cover'],
            'units' => ['p', 'c'], 'badge' => null, 'image' => 'assets/img/product-sleep-pack.jpg',
            'amazon_url' => '',
        ],
        'mars-twin-pack' => [
            'id' => 'twin-pack', 'name' => 'Mars Twin Pack', 'price' => 179, 'compare_at_price' => 198,
            'short' => 'Two pillows, so each sleeper sets their own height and feel.',
            'includes' => ['2 Mars Pillows (3 removable layers each)', 'Each sleeper sets their own height'],
            'units' => ['p', 'p'], 'badge' => 'Most Popular', 'image' => 'assets/img/product-twin-pack.jpg',
            'amazon_url' => '',
        ],
        'interstellar-suite' => [
            'id' => 'interstellar', 'name' => 'Interstellar Suite', 'price' => 229, 'compare_at_price' => 276,
            'short' => 'Two pillows and two spare covers for the whole bed.',
            'includes' => ['2 Mars Pillows (3 removable layers each)', '2 extra Mars Covers'],
            'units' => ['p', 'p', 'c', 'c'], 'badge' => 'Best Value', 'image' => 'assets/img/product-interstellar.jpg',
            'amazon_url' => '',
        ],
    ],
    'default_offer' => 'mars-twin-pack',

    // Product details shown on the product page. Fill in to match the real product.
    'product' => [
        // The three foam layers, top to bottom. 'in' is thickness in inches (used by the 3D model and the copy).
        'layers' => [
            ['name' => 'Soft layer',    'in' => 2.4, 'text' => 'Memory foam that slowly sinks under your head. Sleep on this side for a softer feel. [TODO confirm foam type]'],
            ['name' => 'Fine-tune layer', 'in' => 0.8, 'text' => 'A thin layer you add or remove to change the height by 0.8 in.'],
            ['name' => 'Firm layer',    'in' => 2.4, 'text' => 'Denser support foam. Flip the pillow to sleep on this side for a firmer feel. [TODO confirm foam type]'],
        ],
        // Heights you can build from the layers (inches), with the layers used.
        'heights' => [
            ['in' => 2.4, 'cm' => 6,  'how' => 'One 2.4 in layer',              'for' => 'Stomach sleepers'],
            ['in' => 3.1, 'cm' => 8,  'how' => 'One 2.4 in layer + fine-tune layer', 'for' => 'Back sleepers, smaller builds'],
            ['in' => 4.7, 'cm' => 12, 'how' => 'Soft + firm layers',            'for' => 'Back sleepers, side sleepers with narrow shoulders'],
            ['in' => 5.5, 'cm' => 14, 'how' => 'All three layers',              'for' => 'Side sleepers, broad shoulders'],
        ],
        'size'      => '23.6 x 15.7 in (60 x 40 cm), up to 5.5 in (14 cm) high',
        'weight'    => '[TODO] lb',
        'materials' => 'Cover: viscose from bamboo and polyester (35% / 65%). Core: three memory foam layers. [TODO confirm foam and any certification, e.g. CertiPUR-US®, only if certified]',
        'care'      => 'Unzip the cover and machine wash it cold on a gentle cycle, then air dry. Do not wash the foam layers. Air them out instead. [TODO confirm]',
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
            ['Height', '4 heights, adjust at home', 'Pick 1 of 3 sizes when you buy'],
            ['Feel', 'Soft side and firm side', 'One feel'],
            ['Cover', 'Zip-off, machine washable', 'Removable knit cover'],
            ['Shipping', 'Free in the US', 'Free in the US'],
        ],
    ],
];
