<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>https://museemusical.shop/</loc>
    </url>
@if (isset($pages) && !empty($pages))
@foreach ($pages as $page)
    <url>
        <loc>{{ $page }}</loc>
    </url>
@endforeach
@endif
@if (isset($brands) && !empty($brands))
@foreach ($brands as $brand)
    <url>
        <loc>https://museemusical.shop/#/brands/{{ $brand->slug }}</loc>
    </url>
@endforeach
@endif
@foreach ($products as $items)
    <url>
        <loc>https://museemusical.shop/#/product/{{ $items->product_url }}</loc>
        <lastmod>{{ $items->created_at->tz('UTC')->toAtomString() }}</lastmod>
    </url>
@endforeach
</urlset>
