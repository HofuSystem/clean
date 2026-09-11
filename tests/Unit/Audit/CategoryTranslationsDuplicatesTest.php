<?php

namespace Tests\Unit\Audit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * CategoryTranslationsDuplicatesTest
 *
 * يثبت:
 *  1. لا توجد ترجمات مكررة لنفس (category_id, locale).
 *  2. UNIQUE index موجود فعلاً في قاعدة البيانات.
 *  3. محاولة إدراج ترجمة مكررة تُنتج خطأ DB.
 *  4. المنتج يظهر مرة واحدة فقط في getProductsCard().
 *  5. Pagination وTotal Count صحيحان (لا تضخيم بسبب JOIN).
 */
class CategoryTranslationsDuplicatesTest extends TestCase
{
    // ─── 1. لا توجد تكرارات ───────────────────────────────────────────────

    public function test_no_duplicate_category_translations_exist(): void
    {
        $duplicates = DB::select("
            SELECT category_id, locale, COUNT(*) as cnt
            FROM category_translations
            GROUP BY category_id, locale
            HAVING COUNT(*) > 1
        ");

        $this->assertEmpty(
            $duplicates,
            'توجد ترجمات مكررة في category_translations: ' .
            json_encode(array_map(fn($r) => (array)$r, $duplicates), JSON_UNESCAPED_UNICODE)
        );
    }

    // ─── 2. UNIQUE index موجود ────────────────────────────────────────────

    public function test_unique_index_exists_on_category_translations(): void
    {
        $indexName = 'category_translations_category_id_locale_unique';

        $indexes = DB::select("
            SHOW INDEX FROM category_translations WHERE Key_name = ?
        ", [$indexName]);

        $this->assertNotEmpty(
            $indexes,
            "UNIQUE index '{$indexName}' غير موجود في category_translations."
        );
    }

    // ─── 3. DB ترفض إدراج ترجمة مكررة ───────────────────────────────────

    public function test_cannot_insert_duplicate_translation_for_same_locale(): void
    {
        // نجد category_id وlocale موجودَين فعلاً
        $existing = DB::selectOne("
            SELECT category_id, locale FROM category_translations LIMIT 1
        ");

        if (!$existing) {
            $this->markTestSkipped('لا يوجد تصنيف في قاعدة البيانات لإجراء الاختبار.');
        }

        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('category_translations')->insert([
            'category_id' => $existing->category_id,
            'locale'      => $existing->locale,
            'name'        => 'اختبار تكرار',
        ]);
    }

    // ─── 4. المنتج يظهر مرة واحدة فقط في getProductsCard() ──────────────

    public function test_each_product_appears_once_in_products_card(): void
    {
        /** @var \Core\Products\Services\ProductsService $service */
        $service  = app(\Core\Products\Services\ProductsService::class);
        $products = $service->getProductsCard();

        $ids      = $products->pluck('id');
        $uniqueIds = $ids->unique();

        $this->assertCount(
            $uniqueIds->count(),
            $ids->toArray(),
            'توجد منتجات مكررة في نتيجة getProductsCard()! الـ IDs: ' .
            implode(', ', $ids->duplicates()->toArray())
        );
    }

    // ─── 5. Total Count لا يتضخم بسبب JOIN ──────────────────────────────

    public function test_products_card_count_matches_active_products_count(): void
    {
        $activeCount = DB::table('products')
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->count();

        /** @var \Core\Products\Services\ProductsService $service */
        $service      = app(\Core\Products\Services\ProductsService::class);
        $productsCard = $service->getProductsCard();

        $this->assertEquals(
            $activeCount,
            $productsCard->count(),
            "عدد المنتجات في getProductsCard() ({$productsCard->count()}) " .
            "لا يساوي عدد المنتجات الفعلية ({$activeCount}). " .
            "ربما بسبب JOIN duplication أو منتج ليس له ترجمة."
        );
    }
}
