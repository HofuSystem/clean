<?php

namespace Database\Seeders;

use Core\Pages\Models\Page;
use Core\Pages\Models\Section;
use Illuminate\Database\Seeder;

/**
 * Adds the website pages and section slots used by the public landing pages.
 * Safe to run repeatedly: existing translations and media are left untouched.
 */
class WebsitePageCmsSeeder extends Seeder
{
    public function run(): void
    {
        $home = $this->page('home', 'Home', 'الرئيسية', 'Clean Station | Laundry & Cleaning Services', 'كلين ستيشن | خدمات الغسيل والعناية');
        $gifts = $this->page('gifts', 'Flowers & Gifts', 'الورود والهدايا', 'Flower & Gift Delivery in Riyadh | Clean Station', 'توصيل الورود والهدايا في الرياض | كلين ستيشن');

        $this->sections($home, [
            'hero' => ['Your laundry, smarter and cleaner', 'غسيلك أذكى وأنظف', 'Order laundry care with pickup and delivery at a time that suits you.', 'اطلب خدمات العناية بملابسك مع الاستلام والتوصيل في الوقت الذي يناسبك.'],
            'order-modes' => ['Two ways to order in the app', 'طريقتان للطلب من التطبيق', '', ''],
            'pricing' => ['Value laundry bags', 'الحقائب الاقتصادية', '', ''],
            'why-us' => ['Why Clean Station?', 'لماذا كلين ستيشن؟', '', ''],
            'services' => ['Complete care journey', 'رحلة العناية المتكاملة', '', ''],
            'app-features' => ['Everything from your phone', 'كل شيء من جوالك', '', ''],
            'testimonials' => ['What our customers say', 'ماذا يقول عملاؤنا؟', '', ''],
            'faq' => ['Before you order', 'قبل أن تطلب', '', ''],
        ]);

        $this->sections($gifts, [
            'gifts-hero' => ['Flowers and gifts, delivered with care', 'ورود وهدايا تصل بعناية', 'Choose a bouquet or vase and schedule delivery in the app.', 'اختر باقة ورود أو فازة وحدد موعد التوصيل من التطبيق.'],
            'gifts-collections' => ['Our collection', 'تشكيلتنا', 'Explore bouquets and elegant flower vases.', 'تعرّف على باقات الورود والفازات الأنيقة.'],
            'gifts-occasions' => ['For every occasion', 'لكل مناسبة', '', ''],
            'gifts-benefits' => ['Gift it your way', 'أهدِ بالطريقة التي تناسبك', '', ''],
            'gifts-steps' => ['Your gift in four steps', 'هديتك بأربع خطوات', '', ''],
            'gifts-faq' => ['Before you order', 'قبل أن تطلب', '', ''],
            'gifts-cta' => ['Surprise someone with an elegant gift', 'فاجئ من تحب بهدية أنيقة', 'Order from the Gifts section in the app.', 'اطلب من قسم الهدايا في التطبيق.'],
        ]);
    }

    private function page(string $slug, string $titleEn, string $titleAr, string $metaEn, string $metaAr): Page
    {
        $page = Page::firstOrCreate(
            ['slug' => $slug],
            ['image' => 'newWeb/logo.svg', 'is_active' => true]
        );

        foreach (['en' => [$titleEn, $metaEn], 'ar' => [$titleAr, $metaAr]] as $locale => [$title, $metaTitle]) {
            if (!$page->translate($locale)) {
                $page->translations()->create([
                    'locale' => $locale,
                    'title' => $title,
                    'description' => $title,
                    'meta_title' => $metaTitle,
                    'meta_description' => $title,
                ]);
            }
        }

        return $page;
    }

    private function sections(Page $page, array $definitions): void
    {
        foreach ($definitions as $template => [$titleEn, $titleAr, $descriptionEn, $descriptionAr]) {
            $section = Section::firstOrCreate(
                ['page_id' => $page->id, 'template' => $template],
                ['order' => Section::where('page_id', $page->id)->max('order') + 1]
            );

            foreach (['en' => [$titleEn, $descriptionEn], 'ar' => [$titleAr, $descriptionAr]] as $locale => [$title, $description]) {
                if (!$section->translate($locale)) {
                    $section->translations()->create([
                        'locale' => $locale,
                        'title' => $title,
                        'small_title' => null,
                        'description' => $description,
                    ]);
                }
            }
        }
    }
}
