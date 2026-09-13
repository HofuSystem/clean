<?php

namespace Tests\Feature\Audit;

use App\Support\WebsiteImages;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Tests\Support\IsolatedAuditTestCase;

class WebsiteImagesTest extends IsolatedAuditTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.url' => 'https://cleanstation.app', 'filesystems.disks.public.driver' => 'local']);
        Storage::fake('public');
    }

    private function original(): string
    {
        $image = (new ImageManager(['driver' => 'gd']))->canvas(1200, 1800, '#136a9e');
        $contents = (string) $image->encode('png');
        Storage::disk('public')->put('gallery/example.png', $contents);
        $image->destroy();
        return $contents;
    }

    public function test_variants_preserve_original_and_offer_smaller_correctly_sized_images(): void
    {
        $original = $this->original();
        $this->assertTrue(WebsiteImages::prepare('gallery/example.png'));
        $data = WebsiteImages::attributes('https://cleanstation.app/storage/gallery/example.png');
        $this->assertSame($original, Storage::disk('public')->get('gallery/example.png'));
        $this->assertSame(1200, $data['width']);
        $this->assertSame(1800, $data['height']);
        $this->assertStringContainsString('1200w', $data['srcset']);
        foreach (Storage::disk('public')->allFiles('_website') as $file) {
            if (! str_ends_with($file, '.webp')) {
                continue;
            }
            $variant = Storage::disk('public')->get($file);
            $size = getimagesizefromstring($variant);
            $this->assertSame(IMAGETYPE_WEBP, $size[2]);
            $this->assertLessThan(strlen($original), strlen($variant));
            $this->assertLessThan(1200, $size[0]);
            $this->assertEqualsWithDelta(1.5, $size[1] / $size[0], 0.01);
        }
        $files = Storage::disk('public')->allFiles();
        $this->assertTrue(WebsiteImages::prepare('gallery/example.png'));
        $this->assertSame($files, Storage::disk('public')->allFiles());
    }

    public function test_rendering_is_read_only_and_falls_back_until_copies_exist(): void
    {
        $this->original();
        $before = Storage::disk('public')->allFiles();
        $html = Blade::render('<x-website-image src="https://cleanstation.app/storage/gallery/example.png" width="300" height="450" alt="Test" sizes="300px" />');
        $this->assertStringContainsString('src="https://cleanstation.app/storage/gallery/example.png"', $html);
        $this->assertStringNotContainsString('srcset=', $html);
        $this->assertSame($before, Storage::disk('public')->allFiles());
        WebsiteImages::prepare('gallery/example.png');
        $html = Blade::render('<x-website-image src="https://cleanstation.app/storage/gallery/example.png" alt="Test" sizes="300px" />');
        $this->assertStringContainsString('srcset=', $html);
        $this->assertStringContainsString('width="1200"', $html);
        $this->assertStringContainsString('sizes="300px"', $html);
    }

    public function test_missing_copies_and_changed_originals_fall_back_without_broken_urls(): void
    {
        $this->original();
        WebsiteImages::prepare('gallery/example.png');
        foreach (Storage::disk('public')->allFiles('_website') as $file) {
            if (str_ends_with($file, '.webp')) {
                Storage::disk('public')->delete($file);
                break;
            }
        }
        $this->assertSame([], WebsiteImages::attributes('gallery/example.png'));
        $this->assertTrue(WebsiteImages::prepare('gallery/example.png'));
        Storage::disk('public')->put('gallery/example.png', 'changed');
        clearstatcache();
        $this->assertSame([], WebsiteImages::attributes('gallery/example.png'));
    }

    public function test_external_invalid_and_unsupported_paths_are_not_read_or_written(): void
    {
        foreach (['https://other.example/storage/test.png', '../private/test.png', '/etc/test.png', 'gallery/missing.png', 'gallery/animation.gif', '_website/old.png'] as $path) {
            $this->assertFalse(WebsiteImages::prepare($path));
            $this->assertSame([], WebsiteImages::attributes($path));
        }
        $this->assertSame([], Storage::disk('public')->allFiles());
    }
    public function test_prepare_command_is_registered_and_preserves_original(): void
    {
        $original = $this->original();
        $this->artisan('website:prepare-images', ['--path' => ['gallery/example.png']])->assertExitCode(0);
        $this->assertSame($original, Storage::disk('public')->get('gallery/example.png'));
        $this->assertNotEmpty(WebsiteImages::attributes('gallery/example.png'));
    }

    public function test_updated_landing_templates_compile_without_php_errors(): void
    {
        foreach (['layouts/landing', 'layouts/partials/navbar', 'layouts/partials/footer', 'landing/sections/hero', 'landing/sections/app-features'] as $view) {
            $compiled = Blade::compileString(file_get_contents(resource_path('views/'.$view.'.blade.php')));
            try {
                $this->assertNotEmpty(token_get_all($compiled, TOKEN_PARSE));
            } catch (\ParseError $error) {
                file_put_contents(storage_path('framework/failed-landing-template.php'), $compiled);
                $this->fail($view.': '.$error->getMessage());
            }
        }
    }

}
