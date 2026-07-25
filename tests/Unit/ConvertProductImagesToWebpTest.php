<?php

namespace Tests\Unit;

use Core\Products\Models\Product;
use Core\Products\Services\ProductsService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ConvertProductImagesToWebpTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_convert_product_images_to_webp_and_compress_sales_products()
    {
        // Setup dummy image file in storage
        $salesImageFile = UploadedFile::fake()->image('sales_product.png', 1200, 1200);
        $salesImagePath = $salesImageFile->store('images', 'public');

        $regularImageFile = UploadedFile::fake()->image('regular_product.jpg', 600, 600);
        $regularImagePath = $regularImageFile->store('images', 'public');

        // Create product records
        $salesProduct = Product::create([
            'type' => 'sales',
            'sku' => 'SALES-TEST-001',
            'image' => $salesImagePath,
            'status' => 'active',
            'price' => 100,
            'cost' => 50,
            'quantity' => 10,
        ]);

        $regularProduct = Product::create([
            'type' => 'clothes',
            'sku' => 'CLOTHES-TEST-001',
            'image' => $regularImagePath,
            'status' => 'active',
            'price' => 50,
            'cost' => 25,
            'quantity' => 5,
        ]);

        $service = app(ProductsService::class);
        $result = $service->convertProductImagesToWebp(60);

        $this->assertGreaterThanOrEqual(2, $result['total']);
        $this->assertGreaterThanOrEqual(2, $result['converted']);
        $this->assertEquals(0, $result['failed']);

        // Refresh models
        $salesProduct->refresh();
        $regularProduct->refresh();

        // Check extensions in DB
        $this->assertStringEndsWith('.webp', $salesProduct->image);
        $this->assertStringEndsWith('.webp', $regularProduct->image);

        // Check files exist on disk
        $salesFullPath = Storage::disk('public')->path($salesProduct->image);
        $regularFullPath = Storage::disk('public')->path($regularProduct->image);

        $this->assertFileExists($salesFullPath);
        $this->assertFileExists($regularFullPath);

        // Verify WebP image type
        $salesImageInfo = getimagesize($salesFullPath);
        $this->assertEquals(IMAGETYPE_WEBP, $salesImageInfo[2]);

        $regularImageInfo = getimagesize($regularFullPath);
        $this->assertEquals(IMAGETYPE_WEBP, $regularImageInfo[2]);

        // Sales product image should have been compressed/resized to max 1024 dimension
        $this->assertLessThanOrEqual(1024, $salesImageInfo[0]);
        $this->assertLessThanOrEqual(1024, $salesImageInfo[1]);
    }
}
