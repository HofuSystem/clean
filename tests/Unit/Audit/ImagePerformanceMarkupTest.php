<?php

namespace Tests\Unit\Audit;

use PHPUnit\Framework\TestCase;

class ImagePerformanceMarkupTest extends TestCase
{
    private function projectPath(string $path): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . $path;
    }

    public function test_hero_keeps_the_primary_image_high_priority_and_defers_secondary_images(): void
    {
        $hero = file_get_contents($this->projectPath('resources/views/landing/sections/hero.blade.php'));
        $this->assertStringContainsString('fetchpriority="high" decoding="async"', $hero);
        $this->assertStringContainsString('width="300" height="600"', $hero);
        $this->assertSame(6, substr_count($hero, 'loading="lazy" decoding="async"'));
        $this->assertSame(0, substr_count($hero, '?auto=format&fit=crop&q=80&w=400'));
        $this->assertSame(4, substr_count($hero, 'width="400" height="400"'));
    }

    public function test_below_fold_feature_and_blog_images_have_dimensions_and_alt_text(): void
    {
        $features = file_get_contents($this->projectPath('resources/views/landing/sections/app-features.blade.php'));
        $blogs = file_get_contents($this->projectPath('resources/views/landing/sections/blogs.blade.php'));
        $this->assertStringContainsString('width="300" height="600" loading="lazy" decoding="async"', $features);
        $this->assertStringContainsString("\$defaultFeatureImage = \$section->image_url ?: (\$appFeatures->first()?->image_url ?? '');", $features);
        $this->assertStringContainsString('alt="{{ $appFeatures->first()?->title ?? trans(\'app feature\') }}"', $features);
        $this->assertGreaterThanOrEqual(2, substr_count($features, 'loading="lazy" decoding="async"'));
        $this->assertGreaterThanOrEqual(2, substr_count($blogs, 'loading="lazy" decoding="async"'));
        $this->assertStringContainsString('alt="{{ $posts[0]->title }}"', $blogs);
    }

    public function test_upload_and_blog_image_changes_keep_explicit_alt_text(): void
    {
        $blogs = file_get_contents($this->projectPath('resources/views/landing/sections/blogs.blade.php'));
        $this->assertStringNotContainsString('<img src="{{ $posts[0]->image_url }}" alt=""', $blogs);
        $this->assertStringNotContainsString('<img src="{{ $post->image_url }}" alt=""', $blogs);
    }
}
