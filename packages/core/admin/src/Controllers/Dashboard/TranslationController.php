<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Admin\Helpers\TransHelper;
use Illuminate\Http\Request;
use Core\Admin\Models\Language;

class TranslationController extends Controller
{
    public function index()
    {
        $data['keys']       = array_keys(TransHelper::getDataOfKey('temp'));
        $data['title']      = 'trans-index';
        $data['screen']     = 'trans-index';
        $data['prefixes']   = ['en','ar'];
        $data['langs']      = [];
        foreach ($data['prefixes'] as $prefix) {
            $data['langs'][$prefix] = TransHelper::getDataOfKey($prefix);
        }

        return view('admin::pages.translations.list', $data);
    }
    public function store(Request $request)
    {
        try {

            $data                   = TransHelper::getDataOfKey('temp');
            $data[$request->key]    = '';
            file_put_contents(base_path('lang/temp.json'), json_encode($data,JSON_PRETTY_PRINT));
            foreach ($request->langs as $prefix => $value) {
                $data                   = TransHelper::getDataOfKey($prefix);
                $data[$request->key]    = $value;
                TransHelper::setDataOfKey($prefix, $data);
            }

            toastr()->success("Translarion Stored sucseefuly");
            return redirect()->back();
        } catch (\Exception $ex) {
            report($ex);
            toastr()->error($ex->getMessage());
            return redirect()->back();
        }
    }
    public function storeMultiple(Request $request)
    {
        try {
            // save all data
            $data           = [];
            $data['temp']   = TransHelper::getDataOfKey('temp');
            $prefixes       = ['en','ar'];
            foreach ($prefixes as $prefix) {
                $data[$prefix] = TransHelper::getDataOfKey($prefix);
            }
            foreach ($request->translations as $translation) {
                $data['temp'][$translation['key']] = "";
                foreach ($prefixes as $prefix) {
                    $data[$prefix][$translation['key']] = $translation['langs'][$prefix];
                }
                // save to temp.json
            }
            foreach ($data as $key => $value) {
                TransHelper::setDataOfKey($key, $value);
            }
            toastr()->success("Translarion Stored sucseefuly");
            return redirect()->back();
        } catch (\Exception $ex) {
            report($ex);
            toastr()->error($ex->getMessage());
            return redirect()->back();
        }
    }

    public function destroy(Request $request)
    {
        $key = $request->key ?? '';
        try {

            $data = file_exists(base_path('lang/temp.json')) ? json_decode(file_get_contents(base_path('lang/temp.json')), true) : [];
            unset($data[$key]);
            file_put_contents(base_path('lang/temp.json'), json_encode($data));

            $data['prefixes'] = ['en','ar'];
            foreach ($data['prefixes'] as $prefix) {
                $data = file_exists(base_path('lang/' . $prefix . '.json')) ? json_decode(file_get_contents(base_path('lang/' . $prefix . '.json')), true) : [];
                unset($data[$key]);
                file_put_contents(base_path('lang/' . $prefix . '.json'), json_encode($data));
            }

            toastr()->success("Translarion deleted sucseefuly");
            return redirect()->back();
        } catch (\Exception $ex) {
            report($ex);
            toastr()->error($ex->getMessage());
            return redirect()->back();
        }
    }
}
