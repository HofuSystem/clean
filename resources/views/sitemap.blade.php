<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>https://cleanstation.app/ar</loc>
        <lastmod>2024-07-20T04:38:00+00:00</lastmod>
        <priority>1.00</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en</loc>
        <lastmod>2024-07-20T04:38:00+00:00</lastmod>
        <priority>0.80</priority>
    </url>
    <!-- Dynamic Pages -->
    @foreach ($pages as $page)
        @if ($page->slug && $page->slug !== 'home')
            <url>
                <loc>{{ url('en/' . $page->slug) }}</loc>
                <lastmod>{{ $page->updated_at->toISOString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.7</priority>
            </url>
            <url>
                <loc>{{ url('ar/' . $page->slug) }}</loc>
                <lastmod>{{ $page->updated_at->toISOString() }}</lastmod>
                <changefreq>weekly</changefreq>
                <priority>0.7</priority>
            </url>
        @endif
    @endforeach

    <!-- Services -->
    @foreach ($services as $service)
        <url>
            <loc>{{ url('en/services/' . $service->slug) }}</loc>
            <lastmod>{{ $service->updated_at->toISOString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
        <url>
            <loc>{{ url('ar/services/' . $service->slug) }}</loc>
            <lastmod>{{ $service->updated_at->toISOString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    <!-- Blogs -->
    @foreach ($blogs as $blog)
        <url>
            <loc>{{ url('en/blog/' . $blog->slug) }}</loc>
            <lastmod>{{ $blog->updated_at->toISOString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>
        <url>
            <loc>{{ url('ar/blog/' . $blog->slug) }}</loc>
            <lastmod>{{ $blog->updated_at->toISOString() }}</lastmod>
            <changefreq>monthly</changefreq>
            <priority>0.6</priority>
        </url>
    @endforeach
    <url>
        <loc>https://cleanstation.app/ar/terms</loc>
        <lastmod>2024-07-20T04:38:00+00:00</lastmod>
        <priority>0.80</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/ar/privacy</loc>
        <lastmod>2024-07-20T04:38:00+00:00</lastmod>
        <priority>0.80</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/terms</loc>
        <lastmod>2024-07-20T04:38:00+00:00</lastmod>
        <priority>0.64</priority>
    </url>
    <url>
        <loc>https://cleanstation.app/en/privacy</loc>
        <lastmod>2024-07-20T04:38:00+00:00</lastmod>
        <priority>0.64</priority>
    </url>
</urlset>
