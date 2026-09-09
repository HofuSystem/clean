<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<?php
// Canonical service slugs allowed on the sitemap — CS-02/CS-07
$allowedServiceSlugs = ['wash-and-iron', 'dry-cleaning', 'carpet-upholstery-cleaning', 'shoe-care'];

// Static content has no reliable modification timestamp; omit lastmod.
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">

    {{-- ===== Homepage ===== --}}
    <url>
        <loc>https://cleanstation.app/ar</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en"/>
        <changefreq>weekly</changefreq>
        <priority>1.00</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en"/>
        <changefreq>weekly</changefreq>
        <priority>0.90</priority>
    </url>

    {{-- ===== Services Hub ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/services</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/services"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/services"/>
        <changefreq>weekly</changefreq>
        <priority>0.90</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/services</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/services"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/services"/>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>

    {{-- ===== Individual Service Pages (canonical 4 only) ===== --}}
    @foreach ($allowedServiceSlugs as $serviceSlug)
        <url>
            <loc>https://cleanstation.app/ar/services/{{ $serviceSlug }}</loc>
            <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/services/{{ $serviceSlug }}"/>
            <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/services/{{ $serviceSlug }}"/>
            <changefreq>weekly</changefreq>
            <priority>0.85</priority>
        </url>
        <url>
            <loc>https://cleanstation.app/en/services/{{ $serviceSlug }}</loc>
            <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/services/{{ $serviceSlug }}"/>
            <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/services/{{ $serviceSlug }}"/>
            <changefreq>weekly</changefreq>
            <priority>0.80</priority>
        </url>
    @endforeach

    {{-- ===== Pricing ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/pricing</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/pricing"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/pricing"/>
        <changefreq>weekly</changefreq>
        <priority>0.85</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/pricing</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/pricing"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/pricing"/>
        <changefreq>weekly</changefreq>
        <priority>0.80</priority>
    </url>

    {{-- ===== Coverage ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/riyadh</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/riyadh"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/riyadh"/>
        <changefreq>weekly</changefreq>
        <priority>0.80</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/riyadh</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/riyadh"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/riyadh"/>
        <changefreq>weekly</changefreq>
        <priority>0.75</priority>
    </url>

    {{-- ===== B2B ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/b2b</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/b2b"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/b2b"/>
        <changefreq>monthly</changefreq>
        <priority>0.75</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/b2b</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/b2b"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/b2b"/>
        <changefreq>monthly</changefreq>
        <priority>0.70</priority>
    </url>

    {{-- ===== Why Us ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/why-us</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/why-us"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/why-us"/>
        <changefreq>monthly</changefreq>
        <priority>0.70</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/why-us</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/why-us"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/why-us"/>
        <changefreq>monthly</changefreq>
        <priority>0.65</priority>
    </url>

    {{-- ===== FAQ ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/faq</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/faq"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/faq"/>
        <changefreq>monthly</changefreq>
        <priority>0.65</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/faq</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/faq"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/faq"/>
        <changefreq>monthly</changefreq>
        <priority>0.60</priority>
    </url>

    {{-- ===== Contact ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/contact-us</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/contact-us"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/contact-us"/>
        <changefreq>monthly</changefreq>
        <priority>0.65</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/contact-us</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/contact-us"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/contact-us"/>
        <changefreq>monthly</changefreq>
        <priority>0.60</priority>
    </url>

    {{-- ===== Blog Hub ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/blogs</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/blogs"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/blogs"/>
        <changefreq>weekly</changefreq>
        <priority>0.70</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/blogs</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/blogs"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/blogs"/>
        <changefreq>weekly</changefreq>
        <priority>0.65</priority>
    </url>

    {{-- ===== Blog Posts (dynamic from DB) ===== --}}
    @foreach ($blogs as $blog)
    <url>
        <loc>https://cleanstation.app/ar/blogs/{{ $blog->slug }}</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/blogs/{{ $blog->slug }}"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/blogs/{{ $blog->slug }}"/>
        @if($blog->updated_at)<lastmod>{{ $blog->updated_at->toISOString() }}</lastmod>@endif
        <changefreq>monthly</changefreq>
        <priority>0.60</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/blogs/{{ $blog->slug }}</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/blogs/{{ $blog->slug }}"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/blogs/{{ $blog->slug }}"/>
        @if($blog->updated_at)<lastmod>{{ $blog->updated_at->toISOString() }}</lastmod>@endif
        <changefreq>monthly</changefreq>
        <priority>0.55</priority>
    </url>
    @endforeach

    {{-- ===== Legal Pages ===== --}}
    <url>
        <loc>https://cleanstation.app/ar/terms</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/terms"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/terms"/>
        <changefreq>yearly</changefreq>
        <priority>0.40</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/terms</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/terms"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/terms"/>
        <changefreq>yearly</changefreq>
        <priority>0.35</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/ar/privacy</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/privacy"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/privacy"/>
        <changefreq>yearly</changefreq>
        <priority>0.40</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/privacy</loc>
        <xhtml:link rel="alternate" hreflang="ar-SA" href="https://cleanstation.app/ar/privacy"/>
        <xhtml:link rel="alternate" hreflang="en-SA" href="https://cleanstation.app/en/privacy"/>
        <changefreq>yearly</changefreq>
        <priority>0.35</priority>
    </url>

</urlset>
