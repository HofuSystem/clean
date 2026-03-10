<?php

namespace App\Http\Controllers;

use Core\Admin\Models\Page;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request )
    {
      
        $page = ($request->segment(1) !== null) ? $request->segment(1) : 'home';
        $page = Page::with(['sections' => function ($secQuery)  {
            $secQuery->orderBy('order')->where('status','active');
        }])->where('name',$page)->where('status','active')->firstOrFail();
        $data['title']  = $page->name;
        $data['page']   = $page;
        return view("pages.home",$data);
    }
}
