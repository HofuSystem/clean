<?php

namespace Core\Settings\Helpers;

use Core\Settings\Services\SettingsService;
use Core\Users\Models\User;


class ToolHelper
{
    public static function fromMobile()
    {
        return (request()->header('device-unique-id') or request()->header('accept') == 'application/json');
    }
    public static function getBooleanValue($string)
    {
        if (is_bool($string)) {
            return $string;
        } else {
            return (($string === 'true') or ($string === '1') or ($string === 1));
        }
    }
    public static function isJson($string)
    {
        if (!is_string($string))
            return false;
        $string = trim(preg_replace('/\s\s+/', ' ', $string));
        return ((is_string($string) &&
            (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
    public static function getUser()
    {
        try {
            return auth()->user() ?? request()->user("sanctum") ?? request()->user() ?? null;
        } catch (\Exception $e) {
            report($e);
            return json_decode(json_encode(["id" => null, "name" => null]));
        }
    }
    public static function getUserId()
    {
        try {
            return self::getUser()->id;
        } catch (\Exception $e) {
            report($e);
            return null;
        }
    }
    public static function formatString($content, array $data)
    {
        return preg_replace_callback('/{{\s*(.*?)\s*}}/', function ($matches) use ($data) {
            $key = $matches[1];
            return $data[$key] ?? '';
        }, $content);
    }
    public static function formatStringWithAvailable($content, array $data)
    {
        return preg_replace_callback('/{{\s*(.*?)\s*}}/', function ($matches) use ($data) {
            $key = $matches[1];
            return $data[$key] ??  '{{' . $key . '}}';
        }, $content);
    }
    public static function strRandom($length = 16)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
    public static function hasSpecialChar($str)
    {
        return preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $str) ? true : false;
    }
    public static function setMultiLevelArray(&$array, $path, $newValue)
    {

        $pathArray = explode('.', $path);
        $current = &$array;
        foreach ($pathArray as $index => $value) {
            $current = !is_array($current) ? [] : $current;
            $current = &$current[$value] ?? null;
            if ($index == count($pathArray) - 1) {
                $current = $newValue;
            }
        }
    }
    public static function getMultiLevelArray(&$array, $path)
    {
        $pathArray = explode('.', $path);
        $current = &$array;
        foreach ($pathArray as $index => $value) {
            $current = &$current[$value] ?? null;
            if ($index == count($pathArray) - 1) {
                return $current;
            }
        }
    }
    public static function dirToArray($dir, $deep = false)
    {
        if (!file_exists($dir))
            return [];
        $result = array();
        $cdir = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value) and $deep) {
                    $result[$value] = self::dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else if (! is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[] = $value;
                }
            }
        }

        return $result;
    }
    public static function logException($ex)
    {
        $array = [];
        $array['message']     = $ex->getMessage();
        $array['file']         = $ex->getFile();
        $array['line']         = $ex->getLine();
        logger($array);
    }
    public static  function  formatNumber($number)
    {
        $units = ['', 'k', 'M', 'B', 'T'];
        $unitIndex = 0;

        while ($number >= 1000 && $unitIndex < count($units) - 1) {
            $number /= 1000;
            $unitIndex++;
        }

        return number_format($number, ($unitIndex > 0 ? 1 : 0)) . $units[$unitIndex];
    }
    public static function arrayToPhpCode($array)
    {

        $php_code = "";
        $php_code .= str_replace("array (", "[", var_export($array, true));
        $php_code = str_replace(")", "]", $php_code);
        $php_code = preg_replace('/\b\d+\s*=>\s*/', '', $php_code);
        $php_code .= ";\n";
        return $php_code;
    }
    public static function formatArrayLineToCode($rules)
    {
        // Check if all keys are numeric
        $allKeysAreNumeric = true;
        foreach ($rules as $key => $rule) {
            if (!is_numeric($key)) {
                $allKeysAreNumeric = false;
                break;
            }
        }

        $array = '[';
        foreach ($rules as $key => $rule) {
            if ($allKeysAreNumeric) {
                $array .= "'{$rule}',";
            } else {
                $array .= "'{$key}'=>'{$rule}',";
            }
        }
        $array = substr($array, 0, -1); // Remove the last comma
        if (empty($array)) {
            $array = '[';
        }
        $array .= ']';
        return $array;
    }
    public static function only($rules)
    {
        $array = '[';
        foreach ($rules as $key => $rule) {
            $array .= "'{$rule}',";
        }
        $array  = substr($array, 0, -1);
        if (empty($array)) {
            $array = '[';
        }
        $array .= ']';
        return $array;
    }
    public static function generateUniqueSlug(string $modelClass, string $baseSlug, $id = null)
    {
        $slug = $baseSlug;
        $count = 1;

        while (
            $modelClass::where('slug', $slug)
            ->withTrashed()
            ->when($id, fn($query) => $query->where('id', '!=', $id))
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $count++;
        }

        return $slug;
    }
    static function convertArabicNumbers($string)
    {
        $eastern_arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $western_arabic = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        return str_replace($eastern_arabic, $western_arabic, $string);
    }
    static function getPriceBasedOnCurrentWeekDay($price)
    {
        $weekDay = strtolower(date('l'));
        $weekDaysPrices = SettingsService::getDataBaseSetting('week_prices');
        $weekDay = collect($weekDaysPrices)->firstWhere('day', $weekDay);
        if ($weekDay) {
            $price = $price * $weekDay->percentage;
        }

        // Round to nearest 0.25 (x, x.25, x.50, x.75)
        $fraction = $price - floor($price);
        if ($fraction < 0.125) {
            $rounded = floor($price);
        } elseif ($fraction < 0.375) {
            $rounded = floor($price) + 0.25;
        } elseif ($fraction < 0.625) {
            $rounded = floor($price) + 0.50;
        } elseif ($fraction < 0.875) {
            $rounded = floor($price) + 0.75;
        } else {
            $rounded = ceil($price);
        }

        return (float) number_format($rounded, 2);
    }
    public static function sectionsData()
    {
        /*
        hero
        services
        b2b
        blogs
        faq
        contact
        testimonials
        why-us
        app-features
         */
        return [
            "hero" => [
                'has' => ['title', 'small_title', 'description', 'image'],
                'entity' => []
            ],
            "services" => [
                'has' => ['title', 'small_title', 'description'],
                'entity' => []
            ],
            "app-features" => [
                'has' => ['title','image'],
                'entity' => []
            ],
            "why-us" => [
                'has' => ['title', 'small_title', 'description'],
                'entity' => []
            ],
            "faq" => [
                'has' => ['title', 'small_title', 'description'],
                'entity' => []
            ],
            "contact" => [
                'has' => ['title', 'small_title', 'description', 'image'],
                'entity' => []
            ],
            "blogs" => [
                'has' => ['title', 'small_title', 'description', 'image'],
                'entity' => []
            ],
            "b2b" => [
                'has' => ['title', 'small_title', 'description', 'image'],
                'entity' => []
            ],
            "testimonials" => [
                'has' => ['title', 'small_title', 'description', 'image'],
                'entity' => []
            ],
        ];
    }
}
