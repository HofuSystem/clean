<?php

namespace Tests\Unit;

use Core\MediaCenter\Helpers\MediaCenterHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaCenterHelperTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_save_media_converts_jpg_to_webp()
    {
        $file = UploadedFile::fake()->image('test_photo.jpg', 500, 500);

        $media = MediaCenterHelper::saveMedia($file, 'image');

        $this->assertNotNull($media);
        $this->assertStringEndsWith('.webp', $media->file_name);
        $this->assertFileExists(storage_path('app/public/' . $media->file_name));

        // Check image header/type is WEBP
        $imageInfo = getimagesize(storage_path('app/public/' . $media->file_name));
        $this->assertEquals(IMAGETYPE_WEBP, $imageInfo[2]);
    }

    public function test_save_media_converts_png_to_webp_for_gallery()
    {
        $file = UploadedFile::fake()->image('test_gallery.png', 800, 600);

        $media = MediaCenterHelper::saveMedia($file, 'gallery');

        $this->assertNotNull($media);
        $this->assertStringEndsWith('.webp', $media->file_name);
        $this->assertStringStartsWith('gallery/', $media->file_name);
        $this->assertFileExists(storage_path('app/public/' . $media->file_name));

        $imageInfo = getimagesize(storage_path('app/public/' . $media->file_name));
        $this->assertEquals(IMAGETYPE_WEBP, $imageInfo[2]);
    }

    public function test_save_media_converts_avatar_to_webp()
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 300, 300);

        $media = MediaCenterHelper::saveMedia($file, 'avatar');

        $this->assertNotNull($media);
        $this->assertStringEndsWith('.webp', $media->file_name);
        $this->assertStringStartsWith('avatars/', $media->file_name);

        $imageInfo = getimagesize(storage_path('app/public/' . $media->file_name));
        $this->assertEquals(IMAGETYPE_WEBP, $imageInfo[2]);
    }

    public function test_save_media_converts_gif_to_webp()
    {
        $file = UploadedFile::fake()->image('animation.gif', 200, 200);

        $media = MediaCenterHelper::saveMedia($file, 'media');

        $this->assertNotNull($media);
        $this->assertStringEndsWith('.webp', $media->file_name);
        $this->assertStringStartsWith('media/', $media->file_name);

        $imageInfo = getimagesize(storage_path('app/public/' . $media->file_name));
        $this->assertEquals(IMAGETYPE_WEBP, $imageInfo[2]);
    }

    public function test_save_media_preserves_non_image_files()
    {
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $media = MediaCenterHelper::saveMedia($file, 'pdf');

        $this->assertNotNull($media);
        $this->assertStringEndsWith('.pdf', $media->file_name);
        $this->assertStringStartsWith('pdf/', $media->file_name);
    }
}
