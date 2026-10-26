<?php
/** Google Merchant Center feed: one item per bundle. Add this URL in Merchant Center > Products > Feeds. */
$clean = fn($s) => trim(preg_replace('/\[TODO[^\]]*\]/', '', (string)$s));
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
<channel>
<title>Mars Pillow</title>
<link><?= e(url('/')) ?></link>
<description>Contoured memory foam pillows</description>
<?php foreach (offers() as $slug => $o):
  $img = is_file(MP_PUBLIC . '/' . $o['image']) ? url('/' . $o['image']) : (is_file(MP_PUBLIC . '/assets/img/product-1.jpg') ? url('/assets/img/product-1.jpg') : og_image()); ?>
<item>
  <g:id><?= e($o['id']) ?></g:id>
  <g:title><?= e('Mars Pillow ' . ($o['id'] === 'single' ? '' : $o['name'] . ' – ') . 'Contour Memory Foam Pillow for Neck Support, Side & Back Sleepers') ?></g:title>
  <g:description><?= e($o['short'] . ' Two heights in one contoured memory foam pillow, washable cover, ' . (int)cfg('site.trial_nights') . '-night trial, ' . (int)cfg('site.warranty_years') . '-year warranty, free US shipping.') ?></g:description>
  <g:link><?= e(url('/product/' . $slug)) ?></g:link>
  <g:image_link><?= e($img) ?></g:image_link>
  <g:availability>in_stock</g:availability>
  <g:price><?= number_format($o['price'], 2, '.', '') ?> USD</g:price>
  <g:brand>Mars Pillow</g:brand>
  <g:condition>new</g:condition>
  <g:identifier_exists>no</g:identifier_exists>
  <g:google_product_category>Home &amp; Garden &gt; Linens &amp; Bedding &gt; Bedding &gt; Pillows</g:google_product_category>
  <g:item_group_id>MARS-PILLOW</g:item_group_id>
  <g:shipping><g:country>US</g:country><g:price>0.00 USD</g:price></g:shipping>
</item>
<?php endforeach; ?>
</channel>
</rss>
