<?php

namespace Tests\Feature\Audit;

use Carbon\Carbon;
use Core\Blog\Models\Blog;
use Tests\Support\IsolatedAuditTestCase;

class BlogDateDisplayTest extends IsolatedAuditTestCase
{
    private function articleFixture(?string $published, ?string $created = '2025-01-01'): Blog
    {
        $post = new Blog;
        $post->status = 'publish';
        $post->slug = 'test-post';
        $post->published_at = $published;
        $post->created_at = $created;
        $post->translateOrNew('en')->title = 'Test article';
        $post->translateOrNew('en')->content = 'Content';
        return $post;
    }

    public function test_new_badge_uses_publication_date_and_excludes_old_future_and_unknown_dates(): void
    {
        $this->travelTo(Carbon::parse('2026-09-09 12:00:00'));
        $this->assertTrue($this->articleFixture('2026-09-01')->is_recently_published);
        $this->assertFalse($this->articleFixture('2025-10-21')->is_recently_published);
        $this->assertFalse($this->articleFixture('2026-09-10')->is_recently_published);
        $this->assertFalse($this->articleFixture('2026-08-30 12:00:00')->is_recently_published);
        $this->assertTrue($this->articleFixture('2026-08-30 12:00:01')->is_recently_published);
        $this->assertTrue($this->articleFixture(null, '2026-09-08')->is_recently_published);
        $this->assertFalse($this->articleFixture(null, null)->is_recently_published);
        $draft = $this->articleFixture('2026-09-08'); $draft->status = 'pending';
        $this->assertFalse($draft->is_recently_published);
    }

    public function test_dates_include_year_and_localized_month_using_publication_date(): void
    {
        $post = $this->articleFixture('2025-10-21', '2024-01-01');
        app()->setLocale('en');
        $english = view('partials.blog-date', compact('post'))->render();
        $this->assertStringContainsString('21 October 2025', $english);
        $this->assertStringContainsString('datetime="2025-10-21', $english);
        app()->setLocale('ar');
        $arabic = view('partials.blog-date', compact('post'))->render();
        $this->assertStringContainsString('21 أكتوبر 2025', $arabic);
        $this->assertStringNotContainsString('2024', $arabic);
        $this->assertSame('2025-10-21', $post->publication_date->format('Y-m-d'));
    }

    public function test_rendered_cards_only_show_new_badge_for_recent_articles(): void
    {
        app()->setLocale('en');
        $this->travelTo(Carbon::parse('2026-09-09 12:00:00'));
        foreach (['2025-10-21' => 0, '2026-09-08' => 1] as $date => $expectedBadges) {
            $html = view('landing.sections.blogs', [
                'posts' => collect([$this->articleFixture('2025-01-01'), $this->articleFixture($date)]),
                'section' => (object) ['title' => 'Blog'],
            ])->render();
            $dom = new \DOMDocument;
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);
            $this->assertSame($expectedBadges, $xpath->query('//div[contains(@class, "absolute top-4")]')->length);
            $this->assertStringContainsString(Carbon::parse($date)->locale('en')->translatedFormat('j F Y'), $html);
        }
    }
}
