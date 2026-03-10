<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Core\Pages\Models\Page;
use Core\Pages\Models\Section;
use Illuminate\Support\Facades\DB;

class PagesAndSectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Pages based on: resources/views/pages/
     * Sections based on: resources/views/landing/sections/
     */
    public function run(): void
    {
        $this->command->info('Starting Pages and Sections seeding...');

        // Clear existing data
        $this->command->info('Clearing existing data...');
        DB::table('section_translations')->delete();
        DB::table('page_translations')->delete();
        DB::table('sections')->delete();
        DB::table('pages')->delete();

        // Create pages with their sections
        $this->command->info('Creating Home page...');
        $this->createHomePage();

        $this->command->info('Creating Services page...');
        $this->createServicesPage();

        $this->command->info('Creating B2B page...');
        $this->createB2BPage();

        $this->command->info('Creating Blog page...');
        $this->createBlogPage();

        $this->command->info('Creating Contact page...');
        $this->createContactPage();

        $this->command->info('Creating FAQ page...');
        $this->createFaqPage();

        $this->command->info('Creating Why Us page...');
        $this->createWhyUsPage();

        $this->command->info('Creating App page...');
        $this->createAppPage();

        $this->command->info('Pages and Sections seeding completed successfully!');
    }

    /**
     * Home Page (resources/views/pages/home.blade.php)
     * Main landing page with hero, services, app-features, why-us, b2b, blogs, testimonials, contact
     */
    private function createHomePage()
    {
        $page = Page::create([
            'slug' => 'home',
            'image' => 'newWeb/logo.svg',
            'is_active' => true,
        ]);

        $page->translations()->create([
            'locale' => 'ar',
            'title' => 'الرئيسية',
            'description' => 'كلين ستيشن - التطبيق رقم 1 للعناية بالملابس',
            'meta_title' => 'كلين ستيشن | خدمات الغسيل والتنظيف',
            'meta_description' => 'كلين ستيشن يعود إليكم بحلته الجديدة، جامعًا بين التقنية والراحة في تجربة واحدة لا مثيل لها.'
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'Home',
            'description' => 'Clean Station - #1 Laundry Care App',
            'meta_title' => 'Clean Station | Laundry & Cleaning Services',
            'meta_description' => 'Clean Station returns to you in its new look, combining technology and comfort in an unparalleled experience.'
        ]);

        // Sections used in landing/index.blade.php and landing/welcome.blade.php
        $sections = [
            [
                'template' => 'hero',
                'images' => 'newWeb/logo.svg',
                'translations' => [
                    'ar' => [
                        'title' => 'غسيلك.. أذكى وأنظف!',
                        'small_title' => 'التطبيق رقم #1 للعناية بالملابس',
                        'description' => 'اطلب خدمة غسيل ملابسك باحترافية عالية من بين يديك في دقائق معدودة. استمتع بتجربة توصيل وتتبع سهلة ومريحة.'
                    ],
                    'en' => [
                        'title' => 'Your Laundry.. Smarter!',
                        'small_title' => '#1 Laundry App',
                        'description' => 'Order your laundry professionally in just a few minutes. Enjoy an easy and convenient delivery and tracking experience.'
                    ]
                ]
            ],
            [
                'template' => 'services',
                'images' => 'newWeb/clouds.png',
                'translations' => [
                    'ar' => [
                        'title' => 'خدمات ذكية لحياة أسهل',
                        'small_title' => 'Smart Services',
                        'description' => 'نجمع بين أحدث تقنيات الغسيل والعناية الفائقة بالتفاصيل.'
                    ],
                    'en' => [
                        'title' => 'Smart Services, Easier Life',
                        'small_title' => 'Smart Services',
                        'description' => 'Combining latest tech with extreme care.'
                    ]
                ]
            ],
            [
                'template' => 'app-features',
                'images' => 'newWeb/app-screenshot.png',
                'translations' => [
                    'ar' => [
                        'title' => 'تطبيق كلين ستيشن',
                        'small_title' => 'حمّل التطبيق الآن',
                        'description' => 'تطبيق متكامل للعناية بملابسك وكل ما يخص منزلك'
                    ],
                    'en' => [
                        'title' => 'Clean Station App',
                        'small_title' => 'Download the app now',
                        'description' => 'A comprehensive app for taking care of your clothes and everything related to your home'
                    ]
                ]
            ],
            [
                'template' => 'why-us',
                'images' => 'newWeb/serv.jpg',
                'translations' => [
                    'ar' => [
                        'title' => 'لماذا تختارنا؟',
                        'small_title' => 'Why Choose Us',
                        'description' => 'نقدم لك أفضل تجربة غسيل وتنظيف بجودة عالية وأسعار منافسة'
                    ],
                    'en' => [
                        'title' => 'Why Choose Us?',
                        'small_title' => 'Why Choose Us',
                        'description' => 'We provide you with the best laundry and cleaning experience with high quality and competitive prices'
                    ]
                ]
            ],
            [
                'template' => 'b2b',
                'images' => 'newWeb/b2b-bg.jpg',
                'translations' => [
                    'ar' => [
                        'title' => 'حلول متكاملة للشركات',
                        'small_title' => 'Enterprise Solutions',
                        'description' => 'نقدم حلول غسيل وتنظيف متكاملة للفنادق والشركات والمؤسسات'
                    ],
                    'en' => [
                        'title' => 'Comprehensive Business Solutions',
                        'small_title' => 'Enterprise Solutions',
                        'description' => 'We provide comprehensive laundry and cleaning solutions for hotels, companies and institutions'
                    ]
                ]
            ],
            [
                'template' => 'blogs',
                'images' => 'newWeb/bl.jpg',
                'translations' => [
                    'ar' => [
                        'title' => 'أحدث المقالات والنصائح',
                        'small_title' => 'Our Blog',
                        'description' => 'تابع أحدث المقالات والنصائح في مجال العناية بالملابس والتنظيف'
                    ],
                    'en' => [
                        'title' => 'Latest Articles',
                        'small_title' => 'Our Blog',
                        'description' => 'Follow the latest articles and tips in clothing care and cleaning'
                    ]
                ]
            ],
            [
                'template' => 'testimonials',
                'translations' => [
                    'ar' => [
                        'title' => 'ماذا قال عملاؤنا؟',
                        'small_title' => 'آراء العملاء',
                        'description' => 'تعرف على آراء عملائنا الكرام'
                    ],
                    'en' => [
                        'title' => 'Testimonials',
                        'small_title' => 'Customer Reviews',
                        'description' => 'See what our valued customers say'
                    ]
                ]
            ],
            [
                'template' => 'contact',
                'images' => 'newWeb/call-ringing.svg',
                'translations' => [
                    'ar' => [
                        'title' => 'تواصل معنا',
                        'small_title' => 'نحن هنا لخدمتك',
                        'description' => 'نحن هنا لخدمتك دائماً'
                    ],
                    'en' => [
                        'title' => 'Contact Us',
                        'small_title' => 'We are here to help',
                        'description' => 'We are here to help you'
                    ]
                ]
            ]
        ];

        $this->createSectionsForPage($page, $sections);
    }

    /**
     * Services Page (resources/views/pages/services.blade.php)
     * Includes: landing/sections/services.blade.php
     */
    private function createServicesPage()
    {
        $page = Page::create([
            'slug' => 'services',
            'image' => 'newWeb/abrr.jpg',
            'is_active' => true,
        ]);

        $page->translations()->create([
            'locale' => 'ar',
            'title' => 'خدماتنا',
            'description' => 'تعرف على أهم الخدمات التي نقدمها',
            'meta_title' => 'خدماتنا | كلين ستيشن',
            'meta_description' => 'تعرف على مجموعة خدماتنا المتكاملة للعناية بالملابس والتنظيف'
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'Our Services',
            'description' => 'Learn about the most important services we offer',
            'meta_title' => 'Our Services | Clean Station',
            'meta_description' => 'Learn about our comprehensive clothing care and cleaning services'
        ]);

        $sections = [
            [
                'template' => 'services',
                'images' => 'newWeb/clouds.png',
                'translations' => [
                    'ar' => [
                        'title' => 'خدمات ذكية لحياة أسهل',
                        'small_title' => 'Smart Services',
                        'description' => 'نجمع بين أحدث تقنيات الغسيل والعناية الفائقة بالتفاصيل.'
                    ],
                    'en' => [
                        'title' => 'Smart Services, Easier Life',
                        'small_title' => 'Smart Services',
                        'description' => 'Combining latest tech with extreme care.'
                    ]
                ]
            ]
        ];

        $this->createSectionsForPage($page, $sections);
    }

    /**
     * B2B Page (resources/views/pages/b2b.blade.php)
     * Includes: landing/sections/b2b.blade.php
     */
    private function createB2BPage()
    {
        $page = Page::create([
            'slug' => 'b2b',
            'image' => 'newWeb/abrr.jpg',
            'is_active' => true,
        ]);

        $page->translations()->create([
            'locale' => 'ar',
            'title' => 'قطاع الأعمال',
            'description' => 'حلول متكاملة للشركات والمؤسسات',
            'meta_title' => 'قطاع الأعمال | كلين ستيشن',
            'meta_description' => 'نقدم حلول غسيل وتنظيف متكاملة للفنادق والشركات والمؤسسات'
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'Business Solutions',
            'description' => 'Comprehensive solutions for companies and institutions',
            'meta_title' => 'Business Solutions | Clean Station',
            'meta_description' => 'We provide comprehensive laundry and cleaning solutions for hotels, companies and institutions'
        ]);

        $sections = [
            [
                'template' => 'b2b',
                'images' => 'newWeb/b2b-bg.jpg',
                'translations' => [
                    'ar' => [
                        'title' => 'حلول متكاملة للشركات',
                        'small_title' => 'Enterprise Solutions',
                        'description' => 'نقدّم حلول غسيل متكاملة بالتعاون مع الفنادق والمطاعم والنوادي الصحية ومراكز التجميل.'
                    ],
                    'en' => [
                        'title' => 'Comprehensive Business Solutions',
                        'small_title' => 'Enterprise Solutions',
                        'description' => 'We provide comprehensive laundry solutions in cooperation with hotels, restaurants, health clubs and beauty centers.'
                    ]
                ]
            ]
        ];

        $this->createSectionsForPage($page, $sections);
    }

    /**
     * Blog Page (resources/views/pages/blog.blade.php)
     * Includes: landing/sections/blogs.blade.php
     */
    private function createBlogPage()
    {
        $page = Page::create([
            'slug' => 'blog',
            'image' => 'newWeb/bl.jpg',
            'is_active' => true,
        ]);

        $page->translations()->create([
            'locale' => 'ar',
            'title' => 'المدونة',
            'description' => 'تابع أحدث الأخبار والنصائح في مجال التنظيف والغسيل',
            'meta_title' => 'المدونة | كلين ستيشن',
            'meta_description' => 'تابع أحدث المقالات والنصائح في مجال العناية بالملابس والتنظيف'
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'Blog',
            'description' => 'Follow the latest news and tips in cleaning and laundry',
            'meta_title' => 'Blog | Clean Station',
            'meta_description' => 'Follow the latest articles and tips in clothing care and cleaning'
        ]);

        $sections = [
            [
                'template' => 'blogs',
                'images' => 'newWeb/bl.jpg',
                'translations' => [
                    'ar' => [
                        'title' => 'أحدث المقالات والنصائح',
                        'small_title' => 'Our Blog',
                        'description' => 'تعرف على أحدث المقالات والنصائح في مجال التنظيف والغسيل'
                    ],
                    'en' => [
                        'title' => 'Latest Articles',
                        'small_title' => 'Our Blog',
                        'description' => 'Learn about the latest articles and tips in the field of cleaning and laundry'
                    ]
                ]
            ]
        ];

        $this->createSectionsForPage($page, $sections);
    }

    /**
     * Contact Page (resources/views/pages/contact.blade.php)
     * Includes: landing/sections/contact.blade.php
     */
    private function createContactPage()
    {
        $page = Page::create([
            'slug' => 'contact',
            'image' => 'newWeb/call-ringing.svg',
            'is_active' => true,
        ]);

        $page->translations()->create([
            'locale' => 'ar',
            'title' => 'تواصل معنا',
            'description' => 'تواصل معنا للحصول على خدمات التنظيف والغسيل',
            'meta_title' => 'تواصل معنا | كلين ستيشن',
            'meta_description' => 'تواصل معنا الآن وسوف نقوم بالرد عليك بسرعة'
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'Contact Us',
            'description' => 'Contact us for cleaning and laundry services',
            'meta_title' => 'Contact Us | Clean Station',
            'meta_description' => 'Contact us now and we will respond to you quickly'
        ]);

        $sections = [
            [
                'template' => 'contact',
                'images' => 'newWeb/call-ringing.svg',
                'translations' => [
                    'ar' => [
                        'title' => 'تواصل معنا',
                        'small_title' => 'نحن هنا لخدمتك',
                        'description' => 'نحن هنا لخدمتك دائماً'
                    ],
                    'en' => [
                        'title' => 'Contact Us',
                        'small_title' => 'We are here to help',
                        'description' => 'We are here to help you'
                    ]
                ]
            ]
        ];

        $this->createSectionsForPage($page, $sections);
    }

    /**
     * FAQ Page (resources/views/pages/faq.blade.php)
     * Includes: landing/sections/faq.blade.php
     */
    private function createFaqPage()
    {
        $page = Page::create([
            'slug' => 'faq',
            'image' => 'newWeb/logo.svg',
            'is_active' => true,
        ]);

        $page->translations()->create([
            'locale' => 'ar',
            'title' => 'الأسئلة الشائعة',
            'description' => 'إجابات لأكثر استفساراتكم شيوعاً',
            'meta_title' => 'الأسئلة الشائعة | كلين ستيشن',
            'meta_description' => 'إجابات لأكثر استفساراتكم شيوعاً حول خدمات كلين ستيشن'
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'FAQ',
            'description' => 'Answers to your most common questions',
            'meta_title' => 'FAQ | Clean Station',
            'meta_description' => 'Answers to your most common questions about Clean Station services'
        ]);

        $sections = [
            [
                'template' => 'faq',
                'translations' => [
                    'ar' => [
                        'title' => 'الأسئلة الشائعة',
                        'small_title' => 'Support',
                        'description' => 'إجابات لأكثر استفساراتكم شيوعاً.'
                    ],
                    'en' => [
                        'title' => 'FAQs',
                        'small_title' => 'Support',
                        'description' => 'Answers to your most common questions.'
                    ]
                ]
            ]
        ];

        $this->createSectionsForPage($page, $sections);
    }

    /**
     * Why Us Page (resources/views/pages/why-us.blade.php)
     * Includes: landing/sections/why-us.blade.php
     */
    private function createWhyUsPage()
    {
        $page = Page::create([
            'slug' => 'why-us',
            'image' => 'newWeb/serv.jpg',
            'is_active' => true,
        ]);

        $page->translations()->create([
            'locale' => 'ar',
            'title' => 'لماذا نحن',
            'description' => 'تعرف على ما يميزنا عن غيرنا',
            'meta_title' => 'لماذا نحن | كلين ستيشن',
            'meta_description' => 'تعرف على ما يميز كلين ستيشن عن غيرها من خدمات الغسيل والتنظيف'
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'Why Us',
            'description' => 'Learn about what makes us different',
            'meta_title' => 'Why Us | Clean Station',
            'meta_description' => 'Learn what makes Clean Station different from other laundry and cleaning services'
        ]);

        $sections = [
            [
                'template' => 'why-us',
                'images' => 'newWeb/serv.jpg',
                'translations' => [
                    'ar' => [
                        'title' => 'لماذا تختارنا؟',
                        'small_title' => 'Why Choose Us',
                        'description' => 'نقدم لك أفضل تجربة غسيل وتنظيف بجودة عالية وأسعار منافسة'
                    ],
                    'en' => [
                        'title' => 'Why Choose Us?',
                        'small_title' => 'Why Choose Us',
                        'description' => 'We provide you with the best laundry and cleaning experience with high quality and competitive prices'
                    ]
                ]
            ]
        ];

        $this->createSectionsForPage($page, $sections);
    }

    /**
     * App Page (resources/views/pages/app.blade.php)
     * Includes: landing/sections/app-features.blade.php
     */
    private function createAppPage()
    {
        $page = Page::create([
            'slug' => 'app',
            'image' => 'newWeb/app-screenshot.png',
            'is_active' => true,
        ]);

        $page->translations()->create([
            'locale' => 'ar',
            'title' => 'تطبيق كلين ستيشن',
            'description' => 'حمّل التطبيق الآن واستمتع بتجربة فريدة',
            'meta_title' => 'تطبيق كلين ستيشن | حمّله الآن',
            'meta_description' => 'تطبيق كلين ستيشن متكامل للعناية بملابسك وكل ما يخص منزلك'
        ]);

        $page->translations()->create([
            'locale' => 'en',
            'title' => 'Clean Station App',
            'description' => 'Download the app now and enjoy a unique experience',
            'meta_title' => 'Clean Station App | Download Now',
            'meta_description' => 'Clean Station comprehensive app for taking care of your clothes and everything related to your home'
        ]);

        $sections = [
            [
                'template' => 'app-features',
                'images' => 'newWeb/app-screenshot.png',
                'translations' => [
                    'ar' => [
                        'title' => 'تطبيق كلين ستيشن',
                        'small_title' => 'حمّل التطبيق الآن',
                        'description' => 'تطبيق متكامل للعناية بملابسك وكل ما يخص منزلك'
                    ],
                    'en' => [
                        'title' => 'Clean Station App',
                        'small_title' => 'Download the app now',
                        'description' => 'A comprehensive app for taking care of your clothes and everything related to your home'
                    ]
                ]
            ]
        ];

        $this->createSectionsForPage($page, $sections);
    }

    /**
     * Helper method to create sections for a page
     */
    private function createSectionsForPage(Page $page, array $sections)
    {
        foreach ($sections as $sectionData) {
            $section = Section::create([
                'page_id' => $page->id,
                'template' => $sectionData['template'],
                'images' => $sectionData['images'] ?? null,
                'video' => $sectionData['video'] ?? null,
            ]);

            // Add translations for the section
            foreach ($sectionData['translations'] as $locale => $translation) {
                $section->translations()->create([
                    'locale' => $locale,
                    'title' => $translation['title'],
                    'small_title' => $translation['small_title'] ?? null,
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }
    }
}
