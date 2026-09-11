<?php

namespace Tests\Unit\Audit;

use Core\Products\Services\ProductsService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\Support\IsolatedAuditTestCase;

/**
 * Product-card compatibility against synthetic translated categories.
 * Does not inspect production data or validate the MySQL cleanup migration.
 */
class CategoryTranslationsDuplicatesTest extends IsolatedAuditTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        app()->setLocale('en');
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('clothes');
        });
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('locale');
            $table->string('name');
            $table->unique(['category_id', 'locale']);
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku');
            $table->string('image')->default('');
            $table->decimal('price')->default(10);
            $table->decimal('cost')->default(2);
            $table->integer('points')->default(1);
            $table->string('type')->default('clothes');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->softDeletes();
        });
        Schema::create('product_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('locale');
            $table->string('name');
        });
        DB::table('categories')->insert([['id' => 1], ['id' => 2]]);
        foreach (['en', 'ar'] as $locale) {
            foreach ([1, 2] as $id) {
                DB::table('category_translations')->insert(['category_id' => $id, 'locale' => $locale, 'name' => $locale.' category '.$id]);
            }
        }
        foreach ([1, 2, 3, 4] as $id) {
            DB::table('products')->insert([
                'id' => $id, 'sku' => 'SKU-'.$id, 'category_id' => 1, 'sub_category_id' => 2,
                'status' => $id === 3 ? 'inactive' : 'active',
                'deleted_at' => $id === 4 ? '2026-01-01 00:00:00' : null,
            ]);
            foreach (['en', 'ar'] as $locale) {
                DB::table('product_translations')->insert(['product_id' => $id, 'locale' => $locale, 'name' => $locale.' product '.$id]);
            }
        }
    }

    public function test_translated_category_joins_return_each_active_product_once(): void
    {
        $cards = app(ProductsService::class)->getProductsCard();
        $this->assertSame([1, 2], $cards->pluck('id')->sort()->values()->all());
        $this->assertSame(['en category 1'], $cards->pluck('category')->unique()->values()->all());
        $this->assertSame(['en category 2'], $cards->pluck('sub_category')->unique()->values()->all());
    }

    public function test_locale_changes_labels_without_changing_product_ids(): void
    {
        $english = app(ProductsService::class)->getProductsCard();
        app()->setLocale('ar');
        $arabic = app(ProductsService::class)->getProductsCard();
        $this->assertSame($english->pluck('id')->all(), $arabic->pluck('id')->all());
        $this->assertSame('ar product 1', $arabic->first()['name']);
        $this->assertSame('ar category 1', $arabic->first()['category']);
    }

    public function test_missing_category_translation_keeps_product_visible(): void
    {
        DB::table('category_translations')->where('locale', 'en')->delete();
        $cards = app(ProductsService::class)->getProductsCard();
        $this->assertCount(2, $cards);
        $this->assertNull($cards->first()['category']);
        $this->assertNull($cards->first()['sub_category']);
    }

    public function test_missing_product_translation_does_not_inflate_count(): void
    {
        DB::table('product_translations')->where('product_id', 2)->where('locale', 'en')->delete();
        $this->assertSame([1], app(ProductsService::class)->getProductsCard()->pluck('id')->all());
    }

    public function test_product_card_keys_and_numeric_types_remain_compatible(): void
    {
        $card = app(ProductsService::class)->getProductsCard()->first();
        $this->assertSame(['id', 'sku', 'image', 'name', 'price', 'points', 'cost', 'type', 'category', 'category_id', 'sub_category', 'sub_category_id', 'in_contract'], array_keys($card));
        $this->assertSame(10.0, $card['price']);
        $this->assertSame(2.0, $card['cost']);
        $this->assertSame(1.0, $card['points']);
        $this->assertSame(0, $card['in_contract']);
    }
}
