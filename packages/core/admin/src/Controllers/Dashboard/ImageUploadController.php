<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function index()
    {
        $title = "رفع الصور";
        
        // Read links mapping
        $linksMapping = [];
        if (Storage::disk('public')->exists('image-uploader/links.json')) {
            $linksMapping = json_decode(Storage::disk('public')->get('image-uploader/links.json'), true) ?? [];
        }

        // Fetch all images from the specific directory
        $files = Storage::disk('public')->files('image-uploader');
        
        $images = [];
        foreach ($files as $file) {
            // Skip the JSON file itself
            if (basename($file) === 'links.json') {
                continue;
            }

            $fileName = basename($file);
            $url = $linksMapping[$fileName] ?? asset('storage/' . $file);

            $images[] = [
                'name' => $fileName,
                'url' => $url,
                'time' => Storage::disk('public')->lastModified($file)
            ];
        }

        // Sort by newest first
        usort($images, function($a, $b) {
            return $b['time'] <=> $a['time'];
        });

        return view('admin::pages.image-upload.index', compact('title', 'images'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // توليد اسم مختصر جداً (6 أحرف فقط) مع التأكد من عدم تكراره
            do {
                $imageName = \Illuminate\Support\Str::random(6) . '.' . $file->extension();  
            } while (Storage::disk('public')->exists('image-uploader/' . $imageName));

            $file->storeAs('image-uploader', $imageName, 'public');
            $originalUrl = asset('storage/image-uploader/' . $imageName);
            $shortUrl = $originalUrl;

            try {
                // Request a short URL from TinyURL
                $response = @file_get_contents('https://tinyurl.com/api-create.php?url=' . urlencode($originalUrl));
                if ($response) {
                    $shortUrl = $response;

                    // Save to mapping
                    $linksMapping = [];
                    if (Storage::disk('public')->exists('image-uploader/links.json')) {
                        $linksMapping = json_decode(Storage::disk('public')->get('image-uploader/links.json'), true) ?? [];
                    }
                    $linksMapping[$imageName] = $shortUrl;
                    Storage::disk('public')->put('image-uploader/links.json', json_encode($linksMapping, JSON_UNESCAPED_SLASHES));
                }
            } catch (\Exception $e) {
                // If it fails, fallback to original URL silently
            }

            return back()
                ->with('success', trans('تم رفع الصورة بنجاح!'))
                ->with('image_url', $shortUrl);
        }

        return back()->with('error', trans('حدث خطأ أثناء رفع الصورة.'));
    }
}
