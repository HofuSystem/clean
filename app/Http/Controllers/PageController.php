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
        $pageData = Page::with(['sections'])
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
        $pageData = Page::with(['sections'])
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
        $pageData = Page::with(['sections'])
            ->where('slug', 'why-us')->where('is_active',true)
            ->first();

        if (!$pageData) {
            return abort(404, 'Service not found');
        }
        $counters = Counter::get();
        $features = Feature::get();
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
        $pageData = Page::with(['sections'])
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
        $pageData = Page::with(['sections'])
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
        $pageData = Page::with(['sections'])
            ->where('slug', 'contact')->where('is_active',true)
            ->first();

        if (!$pageData) {
            return abort(404, 'Service not found');
        }
        $settings = Setting::get()->keyBy('key')->map(function($item){
            return $item->value;
        });
        $services = Category::where('status','active')->whereNull('parent_id')->get();
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
        $pageData = Page::with(['sections'])
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
    public function servicePost(Request $request,$slug)
    {
        $service = Category::where('slug',$slug)->first();
        if(!$service){
            return abort(404, 'Service not found');
        }
        return view('pages.single-service', [
            'service' => $service,
            'title' => $service->name,
            'description'=> $service->description,
            'metaTitle' => $service->meta_title,
            'metaDescription' => $service->meta_description,
        ]);
    }
    public function blog()
    {
        $pageData = Page::with(['sections'])
            ->where('slug', 'blogs')->where('is_active',true)
            ->first();
        $posts = Blog::published()->latest()->paginate(9);

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
        $blog = Blog::published()->where('slug',$slug)->first();
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
            'pages' => Page::where('is_active', true)->get(),
            'services' => Category::where('status','active')->whereNull('parent_id')->get(),
            'blogs' => Blog::published()->get(),
        ])->render();

        return response($content, 200)
            ->header('Content-Type', 'text/xml');
    }

    public function terms()
    {
        $pageData = Page::with(['sections'])
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
        $pageData = Page::with(['sections'])
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
