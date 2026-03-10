<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use Illuminate\Http\Request;
use Core\Admin\Models\Language;

class LangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['langs'] = Language::search()->ordered()->paginate(config('app.itemPerPage'));
        $data['screen'] = 'langs-index';

        return view('dashboard.pages.Languages.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['screen'] = 'langs-create';
        return view('dashboard.pages.Languages.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LanguageRequest $request)
    {
        $data = $request->except('_token');
        $data['active'] = $request->has('active');

        try {
            Language::create($data);
            toastr()->success('New lanuage added');
            return redirect()->back();
        } catch (\Exception $ex) {
            report($ex);
            toastr()->error($ex->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['lang'] = Language::find($id);
        $data['screen'] = 'langs-edit';

        return view('dashboard.pages.Languages.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(LanguageRequest $request, $id)
    {
        $data = $request->except('_token', '_method');
        $data['active'] = $request->has('active');
        try {
            Language::find($id)->update($data);
            toastr()->success('lanuage updated');
            return redirect()->back();
        } catch (\Exception $ex) {
            report($ex);
            toastr()->error($ex->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            Language::destroy($id);
            toastr()->success('the lang was deleted');
            return redirect()->back();
        } catch (\Exception $ex) {
            report($ex);
            toastr()->error($ex->getMessage());
            return redirect()->back();
        }
    }
    public function toggle(Request $request, $id)
    {
        try {
            return  Language::find($id)->update(['active' => $request->input('status')]);
        } catch (\Exception $ex) {
            report($ex);
            return $ex->getMessage();
        }
    }
}
