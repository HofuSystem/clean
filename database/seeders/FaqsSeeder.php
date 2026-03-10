<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Pages\Models\Faq;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FaqsSeeder extends Seeder
{
  /**
   * Run the database seeds.
   * 
   * Seeds FAQs for Clean Station laundry and cleaning services
   */
  public function run(): void
  {
    $this->command->info('Starting FAQs seeding...');

    // Clear existing data
    $this->command->info('Clearing existing FAQs...');
    DB::table('faq_translations')->delete();
    DB::table('faqs')->delete();

    $faqs = [
      // Core Service Questions
      [

        'translations' => [
          'ar' => [
            'question' => 'هل تضمنون عدم خلط الملابس؟',
            'answer' => 'نعم، وبشكل قاطع. نخصص غسالة مستقلة لكل طلب، ولا نخلط ملابس أي عميل مع آخر نهائياً.'
          ],
          'en' => [
            'question' => 'Do you mix clothes?',
            'answer' => 'Yes, separate machine for everyone. We never mix clothes from different customers.'
          ]
        ]
      ],
      [

        'translations' => [
          'ar' => [
            'question' => 'كيف يعمل الطلب السريع؟',
            'answer' => 'ببساطة، ضع ملابسك في كيس واطلب المندوب. سيقوم فريقنا بالفرز والعد وإرسال الفاتورة لك.'
          ],
          'en' => [
            'question' => 'How does Quick Order work?',
            'answer' => 'Simply put your clothes in a bag and request pickup. Our team will sort, count, and send you the invoice.'
          ]
        ]
      ],
      [

        'translations' => [
          'ar' => [
            'question' => 'هل يجب أن أكون متواجداً في المنزل؟',
            'answer' => 'لا، مع خدمة "من الباب للباب" يمكنك تعليق الملابس على المقبض وسنقوم باستلامها.'
          ],
          'en' => [
            'question' => 'Do I need to be home?',
            'answer' => 'No, with our "Door-to-Door" service you can hang clothes on the handle and we will pick them up.'
          ]
        ]
      ],
      [

        'translations' => [
          'ar' => [
            'question' => 'ما هي مدة التوصيل المعتادة؟',
            'answer' => 'للغسيل العادي 24 ساعة. للسجاد 5 أيام.'
          ],
          'en' => [
            'question' => 'What is the standard delivery time?',
            'answer' => '24 hours for normal laundry. 5 days for carpets.'
          ]
        ]
      ],

    ];

    $this->command->info('Creating FAQs...');

    foreach ($faqs as $faqData) {
      $faq = Faq::create([]);

      foreach ($faqData['translations'] as $locale => $translation) {
        $faq->translations()->create([
          'locale' => $locale,
          'question' => $translation['question'],
          'answer' => $translation['answer'],
        ]);
      }

    }

  }
}
