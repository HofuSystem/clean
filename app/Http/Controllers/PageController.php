<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Core\Admin\Models\NewsLetter;
use Core\Blog\Models\Blog;
use Core\Categories\Models\Category;
use Core\MediaCenter\Helpers\MediaCenterHelper;
use Core\Pages\Models\Business;
use Core\Pages\Models\Counter;
use Core\Pages\Models\Faq;
use Core\Pages\Models\Feature;
use Core\Pages\Models\Page;
use Core\Pages\Models\Reason;
use Core\Pages\Models\Testimonial;
use Core\Pages\Requests\ContactRequestsRequest;
use Core\Pages\Services\ContactRequestsService;
use Core\Services\Models\ExtraService;
use Core\Services\Models\Plan;
use Core\Services\Models\PlansFeature;
use Core\Services\Models\Service;
use Core\Services\Requests\PlaceOrderRequest;
use Core\Settings\Models\Setting;
use Core\Settings\Traits\ApiResponse;
use Core\Users\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    use ApiResponse;
    public function __construct(protected ContactRequestsService $contactRequestsService){}
    /**
     * Display the home page content
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'home')->where('is_active',true)
            ->first();


        if (!$pageData) {
            return abort(404, 'Page not found');
        }

        return view('pages.home', [
            'title'           => $pageData->title,
            'description'     => $pageData->description,
            'metaTitle'       => $pageData->meta_title,
            'metaDescription' => $pageData->meta_description,
            'page'            => $pageData,

        ]);
    }
    /**
     * Display the home page content
     *
     * @return \Illuminate\Http\Response
     */
    public function b2b()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'b2b')->where('is_active',true)
            ->first();

       
        if (!$pageData) {
            return abort(404, 'Page not found');
        }

        return view('pages.b2b', [
            'title'           => $pageData->title,
            'description'     => $pageData->description,
            'metaTitle'       => $pageData->meta_title,
            'metaDescription' => $pageData->meta_description,
            'page'            => $pageData

        ]);
    }

    /**
     * Display the about-us page content
     *
     * @return \Illuminate\Http\Response
     */
    public function whyUs()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'why-us')->where('is_active',true)
            ->first();

        if (!$pageData) {
            return abort(404, 'Service not found');
        }
        $counters = Counter::with('translations')->get();
        $features = Feature::with('translations')->get();
        return view('pages.why-us', [
            'page'              => $pageData,
            'counters'          => $counters,
            'features'          => $features,
            'title'             => $pageData->title,
            'description'       => $pageData->description,
            'metaTitle'         => $pageData->meta_title,
            'metaDescription'   => $pageData->meta_description,
        ]);
    }

     /**
     * Display the about-us page content
     *
     * @return \Illuminate\Http\Response
     */
    public function appFeatures()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'app')->where('is_active',true)
            ->first();

        if (!$pageData) {
            return abort(404, 'Service not found');
        }

        return view('pages.app', [
            'page'              => $pageData,
            'title'             => $pageData->title,
            'description'       => $pageData->description,
            'metaTitle'         => $pageData->meta_title,
            'metaDescription'   => $pageData->meta_description,
        ]);
    }
      /**
     * Display the about-us page content
     *
     * @return \Illuminate\Http\Response
     */
    public function faq()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'faq')->where('is_active',true)
            ->first();

        if (!$pageData) {
            return abort(404, 'Service not found');
        }
 
        return view('pages.faq', [
            'page'              => $pageData,
            'title'             => $pageData->title,
            'description'       => $pageData->description,
            'metaTitle'         => $pageData->meta_title,
            'metaDescription'   => $pageData->meta_description,
        ]);
    }
    /**
     * Display the contact-us page content
     *
     * @return \Illuminate\Http\Response
     */
    public function contactUs()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'contact')->where('is_active',true)
            ->first();

        if (!$pageData) {
            return abort(404, 'Service not found');
        }
        $settings = Setting::get()->keyBy('key')->map(function($item){
            return $item->value;
        });
        $services = Category::with('translations')->where('status','active')->whereNull('parent_id')->get();
        return view('pages.contact', [
            'page'              => $pageData,
            'services'          => $services,
            'settings'          => $settings,
            'title'             => $pageData->title,
            'description'       => $pageData->description,
            'metaTitle'         => $pageData->meta_title,
            'metaDescription'   => $pageData->meta_description,
        ]);
    }
    public function contactUsRequest(ContactRequestsRequest $request)
    {
        try {
            DB::beginTransaction();
            $record             = $this->contactRequestsService->storeOrUpdate($request->validated());
            DB::commit();
            return redirect()->back()->with('success', trans('contact requests saved'));
        } catch (\Throwable $e) {     
            DB::rollback();
            report($e);
            return redirect()->back()->with('error', trans('system Error please try again later'));
        }
    }
      /**
     * Display the services page content
     *
     * @return \Illuminate\Http\Response
     */
    public function services()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'services')->where('is_active',true)
            ->first();

        if (!$pageData) {
            return abort(404, 'Service not found');
        }

        return view('pages.services', [
            'page' => $pageData,
            'title' => $pageData->title,
            'description'     => $pageData->description,
            'metaTitle' => $pageData->meta_title,
            'metaDescription' => $pageData->meta_description,
        ]);
    }
    public function servicePost(Request $request, $slug)
    {
        $lang = app()->getLocale();
        $isRtl = $lang === 'ar';

        $allServicesData = [
            'wash-and-iron' => [
                'icon' => 'fa-solid fa-shirt',
                'ar' => [
                    'title' => 'غسيل وكي الملابس في الرياض | كلين ستيشن',
                    'meta_description' => 'خدمة غسيل وكي الملابس اليومية في الرياض من كلين ستيشن. غسيل منفصل 100% لكل عميل، كي بالبخار، مع استلام وتوصيل مجاني وتتبع عبر التطبيق.',
                    'name' => 'غسيل وكي',
                    'tagline' => 'خدمات الغسيل اليومي بالرياض',
                    'headline' => 'غسيل وكي الملابس باحترافية',
                    'description' => 'تقدم كلين ستيشن خدمة غسيل وكي الملابس اليومية الشاملة للعملاء في مدينة الرياض، مصممة خصيصاً لتوفير وقتك وجهدك وضمان أقصى درجات النظافة والأناقة لملابسك. نعتمد سياسة الغسيل المنفصل 100% لكل طلب على حدة لتجنب اختلاط الملابس، مع استخدام مساحيق منظفة ومعطرات فاخرة وآمنة على الأنسجة. يتم الكي بأحدث أجهزة البخار الاحترافية للمحافظة على ألوان وشكل قطعك المفضلة، من الثياب والقمصان إلى الملابس الكاجوال واليومية، مع خدمة استلام وتوصيل مجاني حتى باب منزلك عبر تطبيق كلين ستيشن الذكي.',
                    'suitable_title' => 'القطع المناسبة لخدمة الغسيل والكي',
                    'suitable_items' => [
                        'الثياب البيضاء والملونة.',
                        'القمصان الرسمية والعملية.',
                        'الملابس القطنية والكاجوال.',
                        'البناطيل وملابس الرياضة.',
                    ],
                    'sections' => [
                        [
                            'title' => 'كيف نعالج ونفرز الطلبات؟',
                            'description' => 'يتم استلام غسيلك في حقائب مغلقة ومحفورة برقم طلبك. في المغسلة، يقوم فريق متخصص بفرز الملابس حسب اللون ونوع الأنسجة والتأكد من إرشادات العناية بالقطعة. تُغسل كل شحنة عميل بشكل منفصل تماماً في دورة غسيل خاصة، ثم تنتقل لمرحلة التجفيف والكي بالبخار والتعطير والتغليف الأنيق.'
                        ],
                        [
                            'title' => 'الاستلام والتوصيل والتتبع',
                            'description' => 'بمجرد إرسال الطلب عبر التطبيق، يصلك مندوب كلين ستيشن للاستلام في الوقت المحدد. يمكنك تتبع حالة الطلب مرحلة بمرحلة من الاستلام والتغسيل وحتى التوصيل لباب بيتك.'
                        ]
                    ],
                    'faqs' => [
                        [
                            'q' => 'هل يتم غسيل ملابسي بشكل منفصل؟',
                            'a' => 'نعم، تلتزم كلين ستيشن بسياسة الغسيل المنفصل 100% لكل عميل على حدة لضمان الخصوصية والنظافة التامة.'
                        ],
                        [
                            'q' => 'كم يستغرق وقت تسليم الطلب؟',
                            'a' => 'تستغرق خدمة الغسيل والكي العادية عادة من 24 إلى 48 ساعة حسب مواعيد الاستلام المحددة في التطبيق.'
                        ]
                    ]
                ],
                'en' => [
                    'title' => 'Wash & Iron Laundry in Riyadh | Clean Station',
                    'meta_description' => 'Professional wash and iron laundry in Riyadh with separate order handling, garment care, pickup, delivery and app tracking.',
                    'name' => 'Wash & Iron',
                    'tagline' => 'Daily Laundry Service in Riyadh',
                    'headline' => 'Professional Wash & Iron Laundry Services',
                    'description' => 'Clean Station provides a complete daily Wash & Iron laundry service for customers across Riyadh, engineered to save your time while maintaining the highest hygiene and garment care standards. We operate under a strict 100% separate wash per customer policy to ensure complete hygiene and privacy. Premium detergent and fabric softeners protect your clothing colors and fibers. Garments are professionally steam pressed, folded or hung according to your preference, with seamless door-to-door pickup and delivery requested right from the Clean Station app.',
                    'suitable_title' => 'Items Suitable for Wash & Iron',
                    'suitable_items' => [
                        'Traditional Thobes & Shirts.',
                        'Work Shirts & Trousers.',
                        'Cotton & Casual Everyday Wear.',
                        'Activewear & T-Shirts.',
                    ],
                    'sections' => [
                        [
                            'title' => 'Sorting & Care Process',
                            'description' => 'Your laundry is collected in sealed bags labeled with your unique order ID. Our team sorts garments by color and fabric type according to care label instructions. Each order is washed independently in dedicated machines, followed by steam pressing, refreshing, and crisp packaging.'
                        ],
                        [
                            'title' => 'Pickup, Delivery & Real-Time Tracking',
                            'description' => 'Simply schedule your pickup time in the app. Our driver arrives at your doorstep, collects your items, and keeps you updated step-by-step until your clean laundry is delivered back.'
                        ]
                    ],
                    'faqs' => [
                        [
                            'q' => 'Are my clothes washed separately?',
                            'a' => 'Yes, Clean Station enforces a 100% separate wash policy for every customer order to guarantee complete hygiene and privacy.'
                        ],
                        [
                            'q' => 'What is the standard turnaround time?',
                            'a' => 'Standard Wash & Iron service typically takes 24 to 48 hours depending on your selected pickup window in the app.'
                        ]
                    ]
                ]
            ],
            'dry-cleaning' => [
                'icon' => 'fa-solid fa-wand-magic-sparkles',
                'ar' => [
                    'title' => 'تنظيف جاف ودراي كلين في الرياض | كلين ستيشن',
                    'meta_description' => 'خدمة دراي كلين في الرياض للأقمشة الحساسة والبدل والفساتين، مع معالجة مناسبة للحالة واستلام وتوصيل من الباب.',
                    'name' => 'التنظيف الجاف',
                    'tagline' => 'العناية بالقطع الفاخرة بالرياض',
                    'headline' => 'التنظيف الجاف ودراي كلين احترافي',
                    'description' => 'توفر كلين ستيشن خدمة التنظيف الجاف الاحترافية (Dry Cleaning) المخصصة للملابس الفاخرة والأنسجة الحساسة التي تتطلب معالجة خاصة بدون استخدام الماء التقليدي. نستخدم تقنيات معالجة البقع المتطورة بالمذيبات العضوية الآمنة للمحافظة على ألياف الحرير، الصوف، الفرو، والبدل الرسمية والفساتين. يضمن خبراء العناية لدينا فحص كل قطعة بشكل مستقل وتحديد أسلوب التنظيف المناسب لها لمنع التلف أو انكماش الأقمشة، مع التغليف الفاخر والتوصيل المباشر لمنزلك في الرياض.',
                    'suitable_title' => 'القطع الموصى بها للتنظيف الجاف',
                    'suitable_items' => [
                        'البدل الرسمية والسترات (البلايزر).',
                        'فساتين السهرة والمناسبات.',
                        'الملابس المصنوعة من الحرير والصوف.',
                        'المعاطف الشتوية الثقيلة والبشت.',
                    ],
                    'sections' => [
                        [
                            'title' => 'معالجة البقع والفرز الدقيق',
                            'description' => 'تخضع كل قطعة لمعاينة أولية لتحديد مواضع البقع وإرشادات المصنع. يتم إزالة البقع يدوياً باستخدام مركبات آمنة مخصصة قبل إدخال القطعة في آلات التنظيف الجاف المتقدمة، متبوعة بالكبس والتشكيل بالبخار لإعادة رونق القطعة.'
                        ]
                    ],
                    'faqs' => [
                        [
                            'q' => 'ما الفرق بين التنظيف الجاف والغسيل العادي؟',
                            'a' => 'التنظيف الجاف يستخدم مذيبات سائلة خاصة بدلاً من الماء لحماية الأنسجة الحساسة والبدل من الانكماش أو تلف الألوان.'
                        ]
                    ]
                ],
                'en' => [
                    'title' => 'Dry Cleaning in Riyadh | Clean Station',
                    'meta_description' => 'Professional dry cleaning in Riyadh for suits, dresses and delicate fabrics with appropriate care, pickup and delivery.',
                    'name' => 'Dry Cleaning',
                    'tagline' => 'Delicate Garment Care in Riyadh',
                    'headline' => 'Expert Dry Cleaning & Garment Care',
                    'description' => 'Clean Station offers specialized Dry Cleaning services for luxury garments, suits, evening gowns, and delicate fabrics requiring water-free care across Riyadh. We utilize eco-safe organic solvent technology to lift tough stains without shrinking or damaging sensitive fibers like silk, wool, velvet, and intricate embroideries. Every garment undergoes individual pre-inspection and custom finishing to maintain original texture and shape, delivered straight to your door.',
                    'suitable_title' => 'Recommended Items for Dry Cleaning',
                    'suitable_items' => [
                        'Business Suits & Blazers.',
                        'Evening Dresses & Formal Gowns.',
                        'Silk, Cashmere & Wool Items.',
                        'Heavy Winter Coats & Traditional Cloaks.',
                    ],
                    'sections' => [
                        [
                            'title' => 'Precision Stain Treatment & Sorting',
                            'description' => 'Every garment undergoes pre-inspection for spot treatment and care label verification. Stains are manually treated with safe agents before entering advanced dry cleaning machines, followed by specialized steam finishing.'
                        ]
                    ],
                    'faqs' => [
                        [
                            'q' => 'What is the main benefit of dry cleaning?',
                            'a' => 'Dry cleaning uses specialized fluids instead of water, protecting delicate fabrics and suit structures from shrinking or fading.'
                        ]
                    ]
                ]
            ],
            'carpet-upholstery-cleaning' => [
                'icon' => 'fa-solid fa-couch',
                'ar' => [
                    'title' => 'تنظيف السجاد والمفروشات في الرياض | كلين ستيشن',
                    'meta_description' => 'خدمة تنظيف السجاد والمفروشات في الرياض مع معالجة البقع والأتربة حسب حالة القطعة والاستلام والتوصيل.',
                    'name' => 'تنظيف السجاد والمفروشات',
                    'tagline' => 'العناية بالسجاد والمفروشات بالرياض',
                    'headline' => 'تنظيف وتعقيم السجاد والمفروشات بالبخار',
                    'description' => 'تقدم كلين ستيشن خدمة تنظيف السجاد والموكيت والمفروشات المنزلية والتجارية المتخصصة في مدينة الرياض. تعتمد الخدمة على أجهزة غسيل مركزي حديثة ومساحيق تنظيف عميقة تساعد في إزالة الغبار والروائح الكريهة والبقع المستعصية دون التأثير على جودة الأنسجة أو ألوان السجاد. نضمن معالجة دقيقة للسجاد العجمي والحريري والتركستاني بأساليب آمنة، مع توفير الاستلام والتوصيل من وإلى منزلك بسهولة تامة.',
                    'suitable_title' => 'الأنواع والقطع المشمولة بالخدمة',
                    'suitable_items' => [
                        'السجاد الفاخر واليدوي والسجاد العجمي.',
                        'الموكيت والسجاد الخفيف والسميك.',
                        'أغطية المفروشات والكنب والستائر.',
                        'البطانيات والمفارش الشتوية.',
                    ],
                    'sections' => [
                        [
                            'title' => 'طريقة الغسيل والتعقيم الحراري',
                            'description' => 'يمر السجاد بمراحل متعددة تشمل نفض الغبار، الغسيل بالشامبو المخصص، الشطف الآلي، ثم التجفيف في غرف حرارية مخصصة تضمن القضاء على البكتيريا وحفظ جودة الصوف والألياف.'
                        ]
                    ],
                    'faqs' => [
                        [
                            'q' => 'كم يستغرق غسيل وتجفيف السجاد؟',
                            'a' => 'يستغرق غسيل وتجفيف السجاد عادة من 3 إلى 5 أيام عمل لضمان الجفاف التام في غرف تجفيف مخصصة.'
                        ]
                    ]
                ],
                'en' => [
                    'title' => 'Carpet & Upholstery Cleaning in Riyadh | Clean Station',
                    'meta_description' => 'Carpet, rug and upholstery cleaning in Riyadh with condition-appropriate cleaning, stain care, pickup and delivery.',
                    'name' => 'Carpet & Upholstery',
                    'tagline' => 'Carpet & Rug Care in Riyadh',
                    'headline' => 'Deep Carpet & Upholstery Cleaning',
                    'description' => 'Clean Station provides professional deep carpet, rug, and upholstery cleaning services across Riyadh. Utilizing advanced industrial washing and drying technology, we eliminate deep-seated dust, stubborn stains, and unpleasant odors while preserving carpet pile density and colors. From luxury Persian and oriental rugs to everyday home carpets and sofa covers, our process ensures deep hygiene and complete freshness with convenient door-to-door pickup & delivery.',
                    'suitable_title' => 'Items Handled',
                    'suitable_items' => [
                        'Oriental, Silk & Persian Rugs.',
                        'Area Rugs & Living Room Carpets.',
                        'Curtains & Upholstery Covers.',
                        'Blankets & Comforters.',
                    ],
                    'sections' => [
                        [
                            'title' => 'Deep Washing & Thermal Sanitization',
                            'description' => 'Rugs undergo multi-stage deep cleaning including dust extraction, shampoo washing, automated rinsing, and climate-controlled thermal drying to eliminate allergens.'
                        ]
                    ],
                    'faqs' => [
                        [
                            'q' => 'How long does carpet cleaning take?',
                            'a' => 'Carpet washing and thorough climate-controlled drying typically take 3 to 5 business days.'
                        ]
                    ]
                ]
            ],
            'shoe-care' => [
                'icon' => 'fa-solid fa-shoe-prints',
                'ar' => [
                    'title' => 'تنظيف والعناية بالأحذية في الرياض | كلين ستيشن',
                    'meta_description' => 'خدمة تنظيف والعناية بالأحذية الرياضية والجلدية والشمواه في الرياض مع الاستلام والتوصيل.',
                    'name' => 'العناية بالأحذية',
                    'tagline' => 'خدمة العناية بالأحذية بالرياض',
                    'headline' => 'تنظيف والعناية الفائقة بالأحذية',
                    'description' => 'تقدم كلين ستيشن خدمة تنظيف وتلميع الأحذية المتخصصة في مدينة الرياض للأحذية الرياضية، الجلدية الرسمية، والأحذية القماشية والشمواه. نعتمد تقنيات التنظيف اليدوي الدقيق باستخدام فرش ومحاليل متخصصة لإزالة الأوساخ والبقع دون الإضرار بالمواد الأصلية أو نعل الحذاء، متبوعة بالتعقيم الداخلي والتلميع لإعادة رونق أحذيتك المفضلة مع خدمة استلام وتوصيل مريحة.',
                    'suitable_title' => 'أنواع الأحذية المشمولة',
                    'suitable_items' => [
                        'الأحذية الرياضية (Sneakers).',
                        'الأحذية الجلدية الرسمية.',
                        'أحذية الشمواه والقماش.',
                        'الأحذية النسائية والصنادل الفاخرة.',
                    ],
                    'sections' => [
                        [
                            'title' => 'العناية اليدوية والتعقيم',
                            'description' => 'يتم تنظيف كل حذاء يدوياً خطوة بخطوة مع استخدام قوالب الحفظ، ومستحضرات ترطيب الجلد، والتعقيم بالأشعة لحماية الحذاء وإطالة عمره.'
                        ]
                    ],
                    'faqs' => [
                        [
                            'q' => 'كيف يتم تنظيف أحذية الشمواه؟',
                            'a' => 'يتم تنظيف الشمواه يدويًا بواسطة فرش خاصة ومحاليل جافة مخصصة لتجنب بقع الماء والتلف.'
                        ]
                    ]
                ],
                'en' => [
                    'title' => 'Shoe Cleaning & Care in Riyadh | Clean Station',
                    'meta_description' => 'Professional shoe cleaning and care in Riyadh for sneakers, leather and suede with pickup and delivery.',
                    'name' => 'Shoe Care',
                    'tagline' => 'Shoe Cleaning Service in Riyadh',
                    'headline' => 'Premium Footwear Cleaning & Care',
                    'description' => 'Clean Station offers detailed shoe cleaning, conditioning, and polishing services in Riyadh for sneakers, formal leather shoes, canvas, and suede footwear. Our shoe care specialists utilize handcrafted techniques, specialized brushes, and premium cleaning solutions to safely remove dirt, stains, and scuffs without compromising material integrity. Internal sanitization and deodorization restore freshness to your favorite footwear with easy doorstep pickup and delivery.',
                    'suitable_title' => 'Types of Footwear Serviced',
                    'suitable_items' => [
                        'Athletic & Luxury Sneakers.',
                        'Formal Leather Dress Shoes.',
                        'Suede & Canvas Shoes.',
                        'Women’s Heels & Designer Footwear.',
                    ],
                    'sections' => [
                        [
                            'title' => 'Hand Restoration & Sanitization',
                            'description' => 'Each shoe is meticulously hand-cleaned, shaped, conditioned with leather balm, and sanitized internally to restore pristine comfort and aesthetics.'
                        ]
                    ],
                    'faqs' => [
                        [
                            'q' => 'How are suede shoes cleaned safely?',
                            'a' => 'Suede is cleaned dry using specialized brass and bristle brushes along with waterless foam cleaners to preserve texture.'
                        ]
                    ]
                ]
            ]
        ];

        $serviceConfig = $allServicesData[$slug][$lang] ?? null;

        // Try to load Category model as well
        $category = Category::with('translations')->where('slug', $slug)->first();

        if (!$serviceConfig && !$category) {
            return abort(404, 'Service not found');
        }

        $title = $serviceConfig['title'] ?? ($category ? $category->name : 'Service');
        $metaDescription = $serviceConfig['meta_description'] ?? ($category ? $category->description : '');

        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', $slug)->where('is_active', true)
            ->first();

        return view('pages.single-service', [
            'page' => $pageData,
            'slug' => $slug,
            'serviceConfig' => $serviceConfig,
            'category' => $category,
            'title' => $title,
            'description' => $metaDescription,
            'metaTitle' => $title,
            'metaDescription' => $metaDescription,
            'icon' => $allServicesData[$slug]['icon'] ?? 'fa-solid fa-shirt',
        ]);
    }

    public function pricing()
    {
        $settings = Setting::get()->keyBy('key')->map(function($item){
            return $item->value;
        });

        // Load active categories and their active products
        $categories = Category::with(['products' => function($q) {
            $q->where('status', 'active');
        }, 'translations', 'products.translations', 'products.subCategory'])
        ->where('status', 'active')
        ->where(function($q) {
            $q->whereIn('type', ['clothes'])
              ->orWhere('slug', 'carpets-furnishings');
        })
        ->whereNull('parent_id')
        ->get();

        $lang = app()->getLocale();
        $title = $lang === 'ar' ? 'قائمة أسعار الغسيل والتنظيف الجاف | كلين ستيشن' : 'Laundry & Dry Cleaning Prices | Clean Station';
        $metaDescription = $lang === 'ar' 
            ? 'اطلع على أسعار الغسيل والكي، التنظيف الجاف، السجاد والمفروشات والعناية بالأحذية. الاستلام والتوصيل مجاني للطلبات 100 ريال فأكثر وفق السياسة المعتمدة.'
            : 'View Clean Station prices for laundry, ironing, dry cleaning, carpets, upholstery and shoe care. Free pickup and delivery applies to orders of SAR 100+ under the approved policy.';

        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'pricing')->where('is_active', true)
            ->first();

        return view('pages.pricing', [
            'page' => $pageData,
            'categories' => $categories,
            'settings' => $settings,
            'title' => $title,
            'metaTitle' => $title,
            'metaDescription' => $metaDescription,
            'description' => $metaDescription,
        ]);
    }

    public function coverage()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->whereIn('slug', ['coverage', 'riyadh'])->where('is_active', true)
            ->first();

        // Load Riyadh and Al-Mubarraz active cities and their active districts
        $cities = \Core\Info\Models\City::whereIn('id', [2, 13])
            ->with(['districts' => function($q) {
                $q->where('status', 'active');
            }, 'translations', 'districts.translations'])
            ->orderByRaw('FIELD(id, 2, 13)')
            ->get();

        $lang = app()->getLocale();
        $title = $lang === 'ar' ? 'تغطية الأحياء والمدن | كلين ستيشن' : 'Coverage & Active Districts | Clean Station';
        $metaDescription = $lang === 'ar'
            ? 'اكتشف المدن والأحياء المغطاة بخدمات كلين ستيشن لاستلام وتوصيل الغسيل في السعودية.'
            : 'Discover the cities and districts covered by Clean Station laundry pickup and delivery services in Saudi Arabia.';

        return view('pages.riyadh', [
            'page' => $pageData,
            'cities' => $cities,
            'title' => $title,
            'metaTitle' => $title,
            'metaDescription' => $metaDescription,
            'description' => $metaDescription,
        ]);
    }
    public function blog()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
            ->where('slug', 'blogs')->where('is_active',true)
            ->first();
        $posts = Blog::published()->with('translations')->latest()->paginate(9);

        if (!$pageData) {
            return abort(404, 'Service not found');
        }

        return view('pages.blog', [
            'page' => $pageData,
            'title' => $pageData->title,
            'description' => $pageData->content,
            'metaTitle' => $pageData->meta_title,
            'metaDescription' => $pageData->meta_description,
            'posts' => $posts,
            'has_pagination' => true,
        ]);
    }
    public function blogPost(Request $request,$slug)
    {
        $blog = Blog::published()->with('translations')->where('slug',$slug)->first();
        if(!$blog){
            return abort(404, 'Blog not found');
        }
        return view('pages.single-blog', [
            'blog' => $blog,
            'title' => $blog->title,
            'description' => \Illuminate\Support\Str::limit(strip_tags($blog->content), 300),
            'metaTitle' => $blog->meta_title,
            'metaDescription' => $blog->meta_description,
        ]);
    }
    public function siteMap(){
         $content = view('sitemap', [
            'pages' => Page::with('translations')->where('is_active', true)->get(),
            'services' => Category::with('translations')->where('status','active')->whereNull('parent_id')->get(),
            'blogs' => Blog::published()->with('translations')->get(),
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function terms()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
        ->where('slug', 'terms')->where('is_active',true)
        ->first();

    if (!$pageData) {
        return abort(404, 'Service not found');
    }

    return view('terms', [
        'page' => $pageData,
        'title' => $pageData->title,
        'description' => $pageData->content,
        'metaTitle' => $pageData->meta_title,
        'metaDescription' => $pageData->meta_description,
    ]);
    }
    public function privacy()
    {
        $pageData = Page::with(['translations', 'sections.translations'])
        ->where('slug', 'privacy')->where('is_active',true)
        ->first();
        if (!$pageData) {
            return abort(404, 'Service not found');
        }
        return view('privacy', [
            'page' => $pageData,
            'title' => $pageData->title,
            'description' => $pageData->content,
            'metaTitle' => $pageData->meta_title,
            'metaDescription' => $pageData->meta_description,
        ]);
    }


}
