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
            'title' => 'Adjustable Memory Foam Pillow for Neck Support | Mars Pillow',
            'description' => 'An adjustable memory foam pillow with 3 removable layers, 4 heights and a soft and a firm side. 60-night trial, 3-year warranty, free US shipping. Shop now.',
            'path' => '/',
        ],
        'product' => [
            'title' => 'Adjustable Memory Foam Pillow, 60-Night Trial | Mars Pillow',
            'description' => 'Shop the Mars Pillow, an adjustable memory foam pillow for side, back and stomach sleepers. Bundle and save up to 17%, free US shipping and a 60-night trial.',
            'path' => '/product/mars-pillow',
        ],
        'faq' => [
            'title' => 'Orthopedic Pillow FAQ: Trial, Care, Shipping | Mars Pillow',
            'description' => 'Answers about the Mars Pillow: which height and side to use, caring for the cover, the 60-night trial, the 3-year warranty and US shipping. Read the full FAQ.',
            'path' => '/faq',
        ],
        'about' => [
            'title' => 'Our Story: Designing an Adjustable Pillow | Mars Pillow',
            'description' => 'Mars Pillow started with one question: why do pillows come in one height when people come in every shape? See how we designed our adjustable pillow. Read more.',
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
            'description' => 'The terms that apply when you browse the Mars Pillow website or buy our adjustable memory foam pillow, including orders, pricing and warranty. Read the terms.',
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
        'quiz' => [
            'title' => 'Pillow Finder: Which Pillow Height Fits You? | Mars Pillow',
            'description' => 'Answer three quick questions about how you sleep and find the pillow height and side of the Mars Pillow that fit you best. Takes 30 seconds. Find your fit now.',
            'path' => '/find-your-fit',
        ],
        'lp'        => ['title' => 'The Pillow You Adjust to Your Neck | Mars Pillow', 'description' => 'An adjustable memory foam pillow with 4 heights, a soft and a firm side, a 60-night trial and free US shipping.', 'path' => '/lp/neck-pillow', 'noindex' => true],
        'review'    => ['title' => 'Review Your Mars Pillow | Mars Pillow', 'description' => 'Tell us how your Mars Pillow is working for you.', 'path' => '/review', 'noindex' => true],
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
    $stats = reviews_stats();
    $extra = [];
    if ($stats['count'] > 0) { // only real, approved reviews
        $extra['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => $stats['avg'], 'reviewCount' => $stats['count'], 'bestRating' => 5];
        $extra['review'] = array_map(fn($r) => ['@type' => 'Review', 'author' => ['@type' => 'Person', 'name' => $r['name']],
            'reviewRating' => ['@type' => 'Rating', 'ratingValue' => (int)$r['rating'], 'bestRating' => 5], 'name' => $r['title'], 'reviewBody' => $r['body']], reviews_approved(5));
    }
    return $extra + [
        '@context' => 'https://schema.org', '@type' => 'Product',
        'name' => 'Mars Pillow', 'brand' => ['@type' => 'Brand', 'name' => 'Mars Pillow'],
        'description' => 'An adjustable memory foam pillow with three removable layers, four heights and a soft and a firm side, designed to support the neck for side, back and stomach sleepers.',
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
        ['cat' => 'Using your orthopedic pillow', 'q' => 'How do I adjust the height?', 'a' => 'Unzip the cover and add or remove layers. The pillow has two 2.4 in memory foam layers and one 0.8 in fine-tune layer, so you can set it to 2.4, 3.1, 4.7 or 5.5 in. Stomach sleepers usually need the lowest height, back sleepers a medium height and side sleepers the most. Our pillow finder suggests a starting point.'],
        ['cat' => 'Using your orthopedic pillow', 'q' => 'What is the difference between the soft side and the firm side?', 'a' => 'The soft side gently sinks under your head. The firm side holds your head higher and moves less. Flip the pillow to switch. Side sleepers often prefer the firm side, and back and stomach sleepers often prefer the soft side.'],
        ['cat' => 'Using your orthopedic pillow', 'q' => 'How long does it take to get used to a new pillow?', 'a' => 'Many sleepers need a few nights to a couple of weeks. Give each setting at least three nights before you change it. That is why you get ' . $n . ' nights to decide.'],
        ['cat' => 'Using your orthopedic pillow', 'q' => 'Is the Mars Pillow good for stomach sleepers?', 'a' => 'Yes. Use one 2.4 in layer with the soft side on top. That keeps the pillow low, so your head does not tip back.'],
        ['cat' => 'Using your orthopedic pillow', 'q' => 'Does a new memory foam pillow smell?', 'a' => 'New memory foam can have a light smell for a day or two. Let it air out in a ventilated room.'],
        ['cat' => 'Care', 'q' => 'How do I wash the Mars Pillow?', 'a' => 'Unzip the cover and machine wash it cold on a gentle cycle, then air dry. Do not wash the foam layers. Air them out instead. [TODO confirm]'],
        ['cat' => 'Care', 'q' => 'Will a standard pillowcase fit?', 'a' => 'The pillow is 23.6 x 15.7 in, so a standard pillowcase usually fits. [TODO confirm]'],
        ['cat' => 'Trial, returns and warranty', 'q' => 'How does the ' . $n . '-night trial work?', 'a' => 'Sleep on your Mars Pillow for up to ' . $n . ' nights. If it is not right for you, contact us and we will arrange a return. [TODO return terms]'],
        ['cat' => 'Trial, returns and warranty', 'q' => 'What does the ' . $w . '-year warranty cover?', 'a' => 'The pillow comes with a ' . $w . '-year warranty against manufacturing defects. [TODO what is covered]'],
        ['cat' => 'Orders and shipping', 'q' => 'How fast does my order ship?', 'a' => 'Every order ships free within the US and arrives in ' . $d . ' business days.'],
        ['cat' => 'Orders and shipping', 'q' => 'Which payment methods do you accept?', 'a' => 'You can pay with all major credit and debit cards through Stripe, or with PayPal. Your card details are entered on the payment provider\'s secure page, not on our site.'],
        ['cat' => 'Orders and shipping', 'q' => 'Do you ship outside the United States?', 'a' => 'Not yet. We currently ship to addresses in the United States only.'],
    ];
}

/** Pillow finder: turns 3 answers into a recommendation. Returns null until all are answered. */
function quiz_result(array $a): ?array
{
    $pos = ['side', 'back', 'stomach', 'both']; $build = ['narrow', 'average', 'broad']; $who = ['me', 'two'];
    if (!in_array($a['pos'] ?? '', $pos, true) || !in_array($a['build'] ?? '', $build, true) || !in_array($a['who'] ?? '', $who, true)) return null;
    // index into product.heights: 0 = 2.4 in, 1 = 3.1 in, 2 = 4.7 in, 3 = 5.5 in
    $idx = match ($a['pos']) {
        'stomach' => 0,
        'back'    => $a['build'] === 'broad' ? 2 : 1,
        'side'    => $a['build'] === 'narrow' ? 2 : 3,
        default   => $a['build'] === 'narrow' ? 1 : 2,
    };
    $heights = cfg('product.heights');
    $h = $heights[$idx];
    $feel = in_array($a['pos'], ['side'], true) ? 'firm' : 'soft';
    $why = match ($a['pos']) {
        'stomach' => 'On your stomach, a low pillow keeps your head from tipping back. Use one thick layer with the soft side on top.',
        'back'    => 'On your back, your head should rest level without being pushed forward. A medium height with the soft side on top supports the curve of your neck.',
        'side'    => 'On your side, the pillow needs to fill the space between your shoulder and your head. ' . ($a['build'] === 'narrow' ? 'With narrower shoulders, two thick layers are usually enough.' : 'With your build, start with all three layers.') . ' The firm side keeps your head from sinking.',
        default   => 'You change positions during the night, so start in the middle and adjust after a few nights.',
    };
    $note = match (true) {
        $a['pos'] === 'side' && $a['build'] === 'broad' => 'If it still feels low after a week, tell us. You have the full trial to decide.',
        $a['pos'] === 'both' => 'If you mostly end up on your side, add the fine-tune layer. If you mostly end up on your back, remove it.',
        default => 'Give each setting at least three nights before you change it. Your neck needs a little time to get used to a new pillow.',
    };
    $offerSlug = $a['who'] === 'two' ? 'mars-twin-pack' : 'mars-sleep-pack';
    return ['height' => $h, 'feel' => $feel, 'why' => $why, 'note' => $note, 'offer' => offer_by_slug($offerSlug), 'key' => "{$a['pos']}-{$a['build']}-{$a['who']}"];
}
