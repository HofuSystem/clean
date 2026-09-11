<?php

namespace Tests\Unit\Audit;

use PHPUnit\Framework\TestCase;

class MediaUploadCompressionPolicyTest extends TestCase
{
    private function projectPath(string $path): string
    {
        return dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . $path;
    }

    public function test_uploaded_images_use_high_quality_responsive_compression(): void
    {
        $helper = file_get_contents($this->projectPath('packages/core/media-center/src/Helpers/MediaCenterHelper.php'));

        $this->assertStringContainsString("saveMedias(\$filesOrUrls, \$type = 'media', \$quality = 82", $helper);
        $this->assertStringContainsString("saveMedia(\$fileOrUrl, \$type = 'media', \$quality = 82", $helper);
        $this->assertStringContainsString("compressImage(\$image, \$extension = null, \$quality = 82", $helper);
        $this->assertStringContainsString('$newWidth = 1920;', $helper);
        $this->assertStringContainsString('$newHeight = 1920;', $helper);
        $this->assertStringContainsString('$constraint->aspectRatio();', $helper);
        $this->assertStringContainsString('$constraint->upsize();', $helper);
        $this->assertStringContainsString('$image->encode(\'webp\', $quality);', $helper);
    }

    public function test_landing_templates_do_not_depend_on_downloaded_static_replacements(): void
    {
        $hero = file_get_contents($this->projectPath('resources/views/landing/sections/hero.blade.php'));
        $features = file_get_contents($this->projectPath('resources/views/landing/sections/app-features.blade.php'));

        $this->assertStringNotContainsString('assets/images/optimized/', $hero . $features);
        $this->assertStringContainsString('{{ $section->image_url }}', $hero);
    }
}
