<?php

namespace Tests\Feature\Audit;

use App\Http\Controllers\PageController;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\IsolatedAuditTestCase;

class SeoOutputTest extends IsolatedAuditTestCase
{
    public function test_sitemap_contains_four_bilingual_services_without_categories_or_fake_dates(): void
    {
        $xml = view('sitemap', ['blogs' => collect()])->render();
        $dom = new \DOMDocument;
        $this->assertTrue($dom->loadXML($xml));
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('s', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        foreach (['wash-and-iron', 'dry-cleaning', 'carpet-upholstery-cleaning', 'shoe-care'] as $slug) {
            foreach (['ar', 'en'] as $locale) {
                $url = 'https://cleanstation.app/'.$locale.'/services/'.$slug;
                $this->assertSame(1, $xpath->query('//s:url[s:loc="'.$url.'"]')->length);
            }
        }
        $this->assertSame(0, $xpath->query('//s:lastmod')->length);
        $urls = array_map(fn ($node) => $node->textContent, iterator_to_array($xpath->query('//s:loc')));
        $this->assertSame(count($urls), count(array_unique($urls)));
    }

    public function test_sitemap_queries_only_published_posts_and_uses_record_modification_time(): void
    {
        Schema::create('blogs', function (Blueprint $t) {
            $t->id(); $t->string('slug'); $t->string('status'); $t->timestamp('published_at')->nullable();
            $t->timestamps(); $t->softDeletes();
        });
        foreach ([['public-post', 'publish', null], ['draft-post', 'pending', '2000-01-01'], ['future-post', 'publish', '2999-01-01']] as [$slug, $status, $date]) {
            DB::table('blogs')->insert(['slug' => $slug, 'status' => $status, 'published_at' => $date, 'updated_at' => '2025-01-02 03:04:05']);
        }
        // siteMap does not use the controller's contact-form dependency.
        $controller = (new \ReflectionClass(PageController::class))->newInstanceWithoutConstructor();
        DB::enableQueryLog(); DB::flushQueryLog();
        $response = $controller->siteMap();
        $this->assertCount(1, DB::getQueryLog());
        $xml = $response->getContent();
        $this->assertStringContainsString('/ar/blogs/public-post', $xml);
        $this->assertStringNotContainsString('draft-post', $xml);
        $this->assertStringNotContainsString('future-post', $xml);
        $this->assertStringContainsString(Carbon::parse('2025-01-02 03:04:05')->toISOString(), $xml);
    }

    public function test_article_json_is_valid_and_cannot_close_its_script_element(): void
    {
        config(['app.url' => 'https://cleanstation.app']);
        app()->setLocale('en');
        $this->app->instance('request', Request::create('/en/blogs/guide?utm_source=test'));
        $blog = (object) ['title' => 'Guide </script><script>alert(1)</script>', 'meta_description' => 'Useful guide',
            'content' => '<p>Text</p>', 'published_at' => '2025-01-01 10:00:00', 'updated_at' => '2025-01-02 10:00:00'];
        $html = view('partials.blog-structured-data', compact('blog'))->render();
        $this->assertSame(1, substr_count($html, '</script>'));
        preg_match('/<script[^>]*>(.*?)<\/script>/s', $html, $match);
        $data = json_decode($match[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('BlogPosting', $data['@type']);
        $this->assertSame($blog->title, $data['headline']);
        $this->assertSame('https://cleanstation.app/en/blogs/guide', $data['url']);
        $this->assertSame('en', $data['inLanguage']);
        $this->assertSame('2025-01-01T10:00:00+03:00', $data['datePublished']);
        $this->assertArrayNotHasKey('author', $data);
    }

    public function test_article_does_not_invent_missing_dates(): void
    {
        $blog = (object) ['title' => 'Guide', 'meta_description' => null, 'content' => '<p>Article text</p>',
            'published_at' => null, 'updated_at' => null];
        $html = view('partials.blog-structured-data', ['blog' => $blog, 'resolvedCanonicalUrl' => 'https://cleanstation.app/ar/blogs/guide'])->render();
        preg_match('/<script[^>]*>(.*?)<\/script>/s', $html, $match);
        $data = json_decode($match[1], true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('Article text', $data['description']);
        $this->assertArrayNotHasKey('datePublished', $data);
        $this->assertArrayNotHasKey('dateModified', $data);
    }
    public function test_blog_index_has_one_accessible_primary_heading(): void
    {
        // Render the actual page body without the layout's unrelated database widgets.
        $template = str_replace("@extends('layouts.landing')", '', file_get_contents(resource_path('views/pages/blog.blade.php')));
        $html = \Illuminate\Support\Facades\Blade::render($template."\n@yield('content')", [
            'page' => (object) ['sections' => collect()], 'title' => 'Laundry articles',
        ]);
        $dom = new \DOMDocument;
        @$dom->loadHTML($html);
        $headings = $dom->getElementsByTagName('h1');
        $this->assertSame(1, $headings->length);
        $this->assertSame('Laundry articles', $headings->item(0)->textContent);
    }
}
