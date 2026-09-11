<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fix: Category Translations Duplicates
 *
 * الهدف:
 *  1. إثبات وعرض الصفوف المكررة في category_translations قبل الحذف.
 *  2. الاحتفاظ بالصف الأقدم (أصغر id) لكل زوج (category_id, locale).
 *  3. حذف الصفوف الزائدة.
 *  4. إضافة UNIQUE index على (category_id, locale) إن لم يكن موجوداً.
 *
 * السبب:
 *  الـ JOIN في getProductsCard() على category_translations يضاعف صفوف المنتجات
 *  عندما يوجد أكثر من ترجمة لنفس التصنيف واللغة.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. اكتشاف وتسجيل التكرارات قبل الحذف ──────────────────────────
        $duplicates = DB::select("
            SELECT
                t1.id,
                t1.category_id,
                t1.locale,
                t1.name
            FROM category_translations t1
            INNER JOIN (
                SELECT category_id, locale
                FROM category_translations
                GROUP BY category_id, locale
                HAVING COUNT(*) > 1
            ) dupes ON t1.category_id = dupes.category_id
                    AND t1.locale     = dupes.locale
            ORDER BY t1.category_id, t1.locale, t1.id
        ");

        if (!empty($duplicates)) {
            \Illuminate\Support\Facades\Log::warning(
                '[Migration] category_translations duplicates found before cleanup',
                ['rows' => array_map(fn($r) => (array)$r, $duplicates)]
            );
        }

        // ── 2. حذف الصفوف المكررة (يبقى الأقدم: أصغر id) ──────────────────
        //
        //  إذا كانت الترجمتان مختلفتي الـ name، نبقي الأقدم (id أصغر)
        //  لأنها الأولى التي أُدخلت ومن المرجح أنها الصحيحة.
        //  الصفوف الزائدة يتم حذفها بناءً على id > الـ id الأصغر لنفس الزوج.
        //
        DB::statement("
            DELETE t1
            FROM category_translations t1
            INNER JOIN category_translations t2
                ON  t1.category_id = t2.category_id
                AND t1.locale      = t2.locale
                AND t1.id          > t2.id
        ");

        // ── 3. إضافة UNIQUE index إن لم يكن موجوداً ────────────────────────
        $indexName = 'category_translations_category_id_locale_unique';

        $indexExists = collect(DB::select("
            SHOW INDEX FROM category_translations WHERE Key_name = '{$indexName}'
        "))->isNotEmpty();

        if (!$indexExists) {
            Schema::table('category_translations', function (Blueprint $table) {
                $table->unique(['category_id', 'locale']);
            });
        }
    }

    public function down(): void
    {
        // لا يمكن استعادة الصفوف المحذوفة — الـ down يزيل الـ unique index فقط
        $indexName = 'category_translations_category_id_locale_unique';

        $indexExists = collect(DB::select("
            SHOW INDEX FROM category_translations WHERE Key_name = '{$indexName}'
        "))->isNotEmpty();

        if ($indexExists) {
            Schema::table('category_translations', function (Blueprint $table) {
                $table->dropUnique(['category_id', 'locale']);
            });
        }
    }
};
