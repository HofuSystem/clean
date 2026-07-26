<?php

namespace Core\MediaCenter\Helpers;

use Core\MediaCenter\Models\Media;
use Core\Settings\Helpers\ToolHelper;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class MediaCenterHelper
{
    /**
     * Allowed file extensions for media files.
     *
     * @var array
     */
    protected static $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'mp4', 'mp3', 'wav', 'jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    protected static $imageExtensions   = ['gif', 'webp', 'jpg', 'jpeg', 'png','svg'];

    /**
     * Save a media file to storage.
     *
     * @param array $filesOrUrls
     * @param string $type
     * @param int $quality
     * @param string $disk
     * @return string|false
     */
    public static function saveMedias($filesOrUrls, $type = 'media', $quality = 60, $disk = 'public')
    {
        $data = collect();
        foreach ($filesOrUrls as  $fileOrUrl) {
            $result = self::saveMedia($fileOrUrl, $type, $quality, $disk);
            $data->push($result);
        }

        return  $data->filter();
    }
    /**
     * Save a media file to storage.
     *
     * @param UploadedFile|string $fileOrUrl
     * @param string $type
     * @param int $quality
     * @param string $disk
     * @return string|false
     */
    public static function saveMedia($fileOrUrl, $type = 'media', $quality = 60, $disk = 'public')
    {
        if (is_string($fileOrUrl)) {
            $file = self::downloadFile($fileOrUrl);
        } else {
            $file = $fileOrUrl;
        }

        if (!$file) {
            return null;
        }

        $extension      = strtolower($file->getClientOriginalExtension());
        $originalName   = $file->getClientOriginalName();

        if (!in_array($extension, self::$allowedExtensions)) {
            return null;
        }

        $mimeType = $file->getMimeType();
        $isImage  = (str_starts_with($mimeType, 'image/') || in_array($extension, self::$imageExtensions)) && $extension !== 'svg' && $mimeType !== 'image/svg+xml';

        if ($isImage) {
            $fileName = pathinfo($file->hashName(), PATHINFO_FILENAME) . '.webp';
        } else {
            $fileName = $file->hashName();
        }

        switch ($type) {
            case 'media':
                if ($isImage) {
                    $fileObj = Image::make($file);
                    if ($fileObj->width() > 1024 || $fileObj->height() > 1024) {
                        $fileObj = self::compressImage($fileObj, $extension, $quality);
                    } else {
                        $fileObj->encode('webp', $quality);
                    }
                    $path = storage_path('app/public/media/');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0755, true, true);
                    }
                    $fileObj->save($path . $fileName);
                    $filePath       = 'media/' . $fileName;
                    $sizeInBytes    = filesize($path . $fileName);
                } else {
                    $filePath       = $file->storeAs('media', $fileName, $disk);
                    $sizeInBytes    = $file->getSize();
                }
                break;
            case 'pdf':
                $filePath       = $file->storeAs('pdf', $fileName, $disk);
                $sizeInBytes    = $file->getSize();
                break;
            case 'excel':
                $filePath       = $file->storeAs('excel', $fileName, $disk);
                $sizeInBytes    = $file->getSize();
                break;
            case 'word':
                $filePath       = $file->storeAs('word', $fileName, $disk);
                $sizeInBytes    = $file->getSize();
                break;
            case 'gallery':
                if ($isImage) {
                    $fileObj = Image::make($file);
                    if ($fileObj->width() > 1024 || $fileObj->height() > 1024) {
                        $fileObj = self::compressImage($fileObj, $extension, $quality);
                    } else {
                        $fileObj->encode('webp', $quality);
                    }
                    $path = storage_path('app/public/gallery/');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0755, true, true);
                    }
                    $fileObj->save($path . $fileName);
                    $filePath    = 'gallery/' . $fileName;
                    $sizeInBytes = filesize($path . $fileName);
                } else {
                    // Non-image files
                    $filePath    = $file->storeAs('gallery', $fileName, $disk);
                    $sizeInBytes = $file->getSize();
                }
                break;
            case 'image':
                $fileObj = Image::make($file);
                if ($fileObj->width() > 1024 || $fileObj->height() > 1024) {
                    $fileObj = self::compressImage($fileObj, $extension, $quality);
                } else {
                    $fileObj->encode('webp', $quality);
                }
                $path = storage_path('app/public/images/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true, true);
                }
                $fileObj->save($path . $fileName);
                $filePath       = 'images/' . $fileName;
                $sizeInBytes    = filesize($path . $fileName);
                break;
            case 'avatar':
                $fileObj = Image::make($file)->fit(200);
                $fileObj->encode('webp', $quality);
                $path = storage_path('app/public/avatars/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true, true);
                }
                $fileObj->save($path . $fileName);
                $filePath       = 'avatars/' . $fileName;
                $sizeInBytes    = filesize($path . $fileName);
                break;
            case 'thumbnail':
                $fileObj = Image::make($file)->fit(100);
                $fileObj->encode('webp', $quality);
                $path = storage_path('app/public/thumbnails/');
                if (!File::exists($path)) {
                    File::makeDirectory($path, 0755, true, true);
                }
                $fileObj->save($path . $fileName);
                $filePath       = 'thumbnails/' . $fileName;
                $sizeInBytes    = filesize($path . $fileName);
                break;
            case 'file':
                if ($isImage) {
                    $fileObj = Image::make($file);
                    if ($fileObj->width() > 1024 || $fileObj->height() > 1024) {
                        $fileObj = self::compressImage($fileObj, $extension, $quality);
                    } else {
                        $fileObj->encode('webp', $quality);
                    }
                    $path = storage_path('app/public/files/');
                    if (!File::exists($path)) {
                        File::makeDirectory($path, 0755, true, true);
                    }
                    $fileObj->save($path . $fileName);
                    $filePath       = 'files/' . $fileName;
                    $sizeInBytes    = filesize($path . $fileName);
                } else {
                    $filePath       = $file->storeAs('files', $fileName, $disk);
                    $sizeInBytes    = $file->getSize();
                }
                break;
            default:
                return null;
        }

        if (is_string($fileOrUrl) && isset($file) && file_exists($file->getPathname())) {
            @unlink($file->getPathname());
        }

        $sizeInKB       = round($sizeInBytes / 1024, 2);
        $media          = Media::create([
            'file_type'     =>  $type,
            'file_name'     =>  $filePath,
            'size'          =>  $sizeInKB,
            'title'         =>  $originalName,
            'alt'           =>  $originalName,
        ]);
        return $media;
    }

    /**
     * Compress and resize an image while maintaining quality.
     *
     * @param mixed $image
     * @param string|null $extension
     * @param int $quality
     * @return mixed
     */
    public static function compressImage($image, $extension = null, $quality = 60)
    {
        // Get the original image dimensions.
        $width = $image->width();
        $height = $image->height();

        // Calculate the new image dimensions based on the aspect ratio.
        if ($width > $height) {
            $newWidth = 1024;
            $newHeight = null;
        } else {
            $newWidth = null;
            $newHeight = 1024;
        }

        // Resize the image.
        $image->resize($newWidth, $newHeight, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        // Convert the image to WebP format.
        $image->encode('webp', $quality);

        return $image;
    }

    /**
     * Download a file from a URL and return an UploadedFile object.
     *
     * @param string $url
     * @return UploadedFile|false
     */
    public static function downloadFile($url)
    {
        $tempFile = tempnam(sys_get_temp_dir(), '');
        $success = copy($url, $tempFile);

        if ($success) {
            return new UploadedFile($tempFile, basename($url));
        } else {
            return null;
        }
    }

    /**
     * Delete a file from storage.
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function deleteFile($id)
    {
        $media = Media::where('id',$id)->orWhere('file_name',$id)->first();
        Storage::disk('public')->delete($media->file_name);
        return $media->delete();
    }

    /**
     * Get a list of media files based on the type.
     *
     * @param string $type
     * @return array
     */
    public static function getMediaList($type = null)
    {
        $list = Media::where('file_type', $type)->orderBy('id', 'desc')->paginate()->items();
        return $list;
    }
    public static function getImagesUrl($value)
    {
        if (str_contains($value, ',')) {
            $values  = explode(',', $value);
            $newValues = [];
            foreach ($values as $key => $value) {
                $newValues[] = self::getImageUrl($value);
            }
            return $newValues;
        } else {
            return self::getImageUrl($value);
        }
    }
    public static function getImageUrl($value)
    {
        if(!isset($value) or empty($value)){
            return null;
        }
        $nameArray  = explode('.', $value);
        $ext        = strtolower(end($nameArray));
        if (!in_array($ext, self::$imageExtensions)) {
            return url('control/icons/' . $ext . '.png');
        }
        return url("storage/" . $value);
    }
    public static function getUrls($value)
    {
        if (str_contains($value, ',')) {
            $values  = explode(',', $value);
            foreach ($values as $key => $val) {
                $values[$key] = self::getUrl($val);
            }
            return $values;
        } else {
            return self::getUrl($value);
        }
    }
    public static function getUrl($value)
    {
        if(!isset($value) or empty($value)){
            return null;
        }
        return url("storage/" . $value);
    }
    public static function getImagesHtml($value)
    {

        if (str_contains($value, ',')) {
            $values  = explode(',', $value);
            foreach ($values as $key => $val) {
                $values[$key] = self::getImageHtml($val);
            }
            $values = implode('', $values);
            return $values;
        } else {
            return self::getImageHtml($value);
        }
    }
    public static function getImageHtml($value)
    {
        if(isset($value) and !empty($value)){
            $nameArray = explode('.', $value);
            $ext        = strtolower(end($nameArray));
            if (!in_array($ext, self::$imageExtensions)) {
                $iconUrl = url('control/icons/' . $ext . '.png');
                $fileUrl = url("storage/" . $value);
                return "<a href=\"{$fileUrl}\" target=\"_blank\"><img src=\"{$iconUrl}\"/></a>";
            }
            $url = url("storage/" . $value);

            return "<a href=\"{$url}\" target=\"_blank\"><img src=\"{$url}\"/></a>";
        }
    }
}
