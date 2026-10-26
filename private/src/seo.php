<?php
declare(strict_types=1);

/**
 * SEO metadata for every page in one place.
 * Rules used (on-page SEO checklist): title 50–60 characters with the main keyword near the start,
 * description 150–160 characters ending with a call to action, one H1 per page, short keyword URLs.
 */
function page_meta(string $key): array
{
    $m = [
        'home' => [
            'title' => 'Orthopedic Memory Foam Pillow for Neck Support | Mars Pillow',
            'description' => 'A contoured memory foam pillow designed to support your neck and help keep your spine aligned. 60-night trial, 3-year warranty, free US shipping. Shop now.',
            'path' => '/',
        ],
        'product' => [
            'title' => 'Contour Memory Foam Pillow, 60-Night Trial | Mars Pillow',
            'description' => 'Shop the Mars Pillow, a contour memory foam pillow for back and side sleepers. Bundle and save up to 17%, with free US shipping and a 60-night trial. Order now.',
            'path' => '/product/mars-pillow',
        ],
        'faq' => [
            'title' => 'Orthopedic Pillow FAQ: Trial, Care, Shipping | Mars Pillow',
            'description' => 'Answers about the Mars Pillow orthopedic pillow: which side to sleep on, washing the cover, the 60-night trial, the 3-year warranty and shipping. Read the FAQ.',
            'path' => '/faq',
        ],
        'about' => [
            'title' => 'Our Story: Designing a Better Neck Pillow | Mars Pillow',
            'description' => 'Mars Pillow started with one question: why do so many pillows leave a gap under the neck? See how we designed our contoured memory foam pillow. Read our story.',
            'path' => '/about',
        ],
        'contact' => [
            'title' => 'Contact Us: Orders, Returns and Pillow Help | Mars Pillow',
            'description' => 'Questions about your Mars Pillow, an order, or the 60-night trial? Send us a message and our team will get back to you within one business day. Contact us now.',
            'path' => '/contact',
        ],
        'privacy' => [
            'title' => 'Privacy Policy: How We Handle Your Data | Mars Pillow',
            'description' => 'Read how Mars Pillow collects, uses and protects your personal information when you visit our store or place an order, and how to make a privacy request today.',
            'path' => '/privacy-policy',
        ],
        'terms' => [
            'title' => 'Terms of Service: Rules for Using Our Store | Mars Pillow',
            'description' => 'The terms that apply when you browse the Mars Pillow website or buy our contoured memory foam pillow, including orders, pricing and warranty. Read the terms.',
            'path' => '/terms-of-service',
        ],
        'shipping' => [
            'title' => 'Shipping Policy: Free Shipping Across the US | Mars Pillow',
            'description' => 'Every Mars Pillow order ships free within the United States. See processing times, delivery estimates, tracking and what to do if a package is late. Learn more.',
            'path' => '/shipping-policy',
        ],
        'returns' => [
            'title' => 'Returns and Refunds: Your 60-Night Sleep Trial | Mars Pillow',
            'description' => 'Sleep on your Mars Pillow for up to 60 nights. If it is not right for you, here is how returns, refunds and the 3-year warranty work. Start a return today.',
            'path' => '/returns',
        ],
        'accessibility' => [
            'title' => 'Accessibility Statement: Our WCAG Commitment | Mars Pillow',
            'description' => 'Mars Pillow aims to meet WCAG 2.1 AA so everyone can shop our store. Read what we do for accessibility and how to tell us about a barrier you find. Contact us.',
            'path' => '/accessibility',
        ],
        'cart'      => ['title' => 'Your Cart | Mars Pillow', 'description' => 'Review the items in your cart.', 'path' => '/cart', 'noindex' => true],
        'checkout'  => ['title' => 'Checkout | Mars Pillow', 'description' => 'Secure checkout.', 'path' => '/checkout', 'noindex' => true],
        'success'   => ['title' => 'Order Confirmed | Mars Pillow', 'description' => 'Thank you for your order.', 'path' => '/order/success', 'noindex' => true],
        'cancelled' => ['title' => 'Payment Cancelled | Mars Pillow', 'description' => 'Your payment was cancelled.', 'path' => '/order/cancelled', 'noindex' => true],
        '404'       => ['title' => 'Page Not Found | Mars Pillow', 'description' => 'This page drifted out of orbit.', 'path' => '/404', 'noindex' => true],
        'admin'     => ['title' => 'Admin | Mars Pillow', 'description' => 'Admin', 'path' => '/admin', 'noindex' => true],
    ];
    return ($m[$key] ?? $m['404']) + ['key' => $key, 'schema' => [], 'scripts' => [], 'noindex' => false];
}

function og_image(): string { return url('/assets/img/og-image.jpg'); }

function schema_organization(): array
{
    return [
        '@context' => 'https://schema.org', '@type' => 'Organization',
        'name' => cfg('site.name'), 'url' => url('/'), 'logo' => url('/assets/img/logo-square.png'),
        'email' => cfg('site.support_email'),
    ];
}

function schema_website(): array
{
    return ['@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => cfg('site.name'), 'url' => url('/')];
}

/** Product + one Offer per bundle. No ratings until there are real, verified reviews. */
function schema_product(): array
{
    $offers = [];
    foreach (offers() as $slug => $o) {
        $offers[] = [
            '@type' => 'Offer', 'name' => $o['name'], 'sku' => $o['id'],
            'url' => url('/product/' . $slug), 'price' => number_format($o['price'], 2, '.', ''), 'priceCurrency' => 'USD',
            'availability' => 'https://schema.org/InStock', 'itemCondition' => 'https://schema.org/NewCondition',
            'shippingDetails' => [
                '@type' => 'OfferShippingDetails',
                'shippingRate' => ['@type' => 'MonetaryAmount', 'value' => '0', 'currency' => 'USD'],
                'shippingDestination' => ['@type' => 'DefinedRegion', 'addressCountry' => 'US'],
            ],
            'hasMerchantReturnPolicy' => [
                '@type' => 'MerchantReturnPolicy', 'applicableCountry' => 'US',
                'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
                'merchantReturnDays' => (int)cfg('site.trial_nights', 60),
            ],
        ];
    }
    $prices = array_column(offers(), 'price');
    return [
        '@context' => 'https://schema.org', '@type' => 'Product',
        'name' => 'Mars Pillow', 'brand' => ['@type' => 'Brand', 'name' => 'Mars Pillow'],
        'description' => 'A contoured memory foam pillow designed to support the neck and help keep the spine aligned for back and side sleepers.',
        'image' => [url('/assets/img/og-image.jpg')],
        'sku' => 'MARS-PILLOW',
        'offers' => [
            '@type' => 'AggregateOffer', 'priceCurrency' => 'USD',
            'lowPrice' => number_format(min($prices), 2, '.', ''), 'highPrice' => number_format(max($prices), 2, '.', ''),
            'offerCount' => count($offers), 'offers' => $offers,
        ],
    ];
}

function schema_breadcrumbs(array $items): array
{
    $list = [];
    foreach ($items as $i => [$name, $path]) $list[] = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $name, 'item' => url($path)];
    return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list];
}

function schema_faq(array $faqs): array
{
    $q = [];
    foreach ($faqs as $f) $q[] = ['@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags(preg_replace('/\[TODO[^\]]*\]/', '', $f['a']))]];
    return ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $q];
}

/** Shared FAQ content (used on the FAQ page, the product page and the home page) */
function faqs(): array
{
    $n = (int)cfg('site.trial_nights', 60); $w = (int)cfg('site.warranty_years', 3); $d = (string)cfg('site.shipping_days');
    return [
        ['cat' => 'Using your orthopedic pillow', 'q' => 'Which side of the pillow should I sleep on?', 'a' => 'Back sleepers usually prefer the lower edge under the neck. Side sleepers usually prefer the higher edge, which fills the space between the shoulder and the head. Try both during your first few nights.'],
        ['cat' => 'Using your orthopedic pillow', 'q' => 'How long does it take to get used to a contour pillow?', 'a' => 'Many sleepers need a few nights to a couple of weeks to get used to a new shape. That is why you get ' . $n . ' nights to decide.'],
        ['cat' => 'Using your orthopedic pillow', 'q' => 'Is the Mars Pillow good for stomach sleepers?', 'a' => 'The Mars Pillow is designed for back and side sleepers. Most stomach sleepers prefer a thinner, softer pillow.'],
        ['cat' => 'Using your orthopedic pillow', 'q' => 'Does a new memory foam pillow smell?', 'a' => 'New memory foam can have a light smell for a day or two. Let it air out in a ventilated room.'],
        ['cat' => 'Care', 'q' => 'How do I wash the Mars Pillow?', 'a' => 'Unzip the cover and machine wash it cold on a gentle cycle, then air dry. Do not wash the foam core. Air it out instead. [TODO confirm]'],
        ['cat' => 'Trial, returns and warranty', 'q' => 'How does the ' . $n . '-night trial work?', 'a' => 'Sleep on your Mars Pillow for up to ' . $n . ' nights. If it is not right for you, contact us and we will arrange a return. [TODO return terms]'],
        ['cat' => 'Trial, returns and warranty', 'q' => 'What does the ' . $w . '-year warranty cover?', 'a' => 'The pillow comes with a ' . $w . '-year warranty against manufacturing defects. [TODO what is covered]'],
        ['cat' => 'Orders and shipping', 'q' => 'How fast does my order ship?', 'a' => 'Every order ships free within the US and arrives in ' . $d . ' business days.'],
        ['cat' => 'Orders and shipping', 'q' => 'Which payment methods do you accept?', 'a' => 'You can pay with all major credit and debit cards through Stripe, or with PayPal. Your card details are entered on the payment provider\'s secure page, not on our site.'],
        ['cat' => 'Orders and shipping', 'q' => 'Do you ship outside the United States?', 'a' => 'Not yet. We currently ship to addresses in the United States only.'],
    ];
}
