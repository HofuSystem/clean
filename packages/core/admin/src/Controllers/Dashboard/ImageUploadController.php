<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function index()
    {
        $title = "رفع الصور";
        return view('admin::pages.image-upload.index', compact('title'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = \Illuminate\Support\Str::random(40) . '.' . $file->extension();  
            $file->storeAs('public/images', $imageName);
            $url = asset('storage/images/' . $imageName);
            return back()
                ->with('success', trans('تم رفع الصورة بنجاح!'))
                ->with('image_url', $url);
        }

        return back()->with('error', trans('حدث خطأ أثناء رفع الصورة.'));
    }
}
