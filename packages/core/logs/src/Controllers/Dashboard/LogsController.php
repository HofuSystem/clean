<?php

namespace Core\Logs\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use Illuminate\Http\Request;
use Core\Admin\Models\Language;
use Core\Logs\Models\Log;

class LogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['logs'] = Log::search()->paginate(config('app.itemPerPage'));
        $data['screen'] = 'logs-index';

        return view('logs::pages.logs.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['screen'] = 'logs-create';
        return view('logs::pages.logs.edit',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token');

        try {
            Log::create($data);
            toastr()->success('New logs added');
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
        $data['log']    = Log::find($id);
        $data['screen'] = 'logs-edit';

        return view('logs::pages.logs.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->except('_token', '_method');
        try {
            Log::find($id)->update($data);
            toastr()->success('logs updated');
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
            Log::destroy($id);
            toastr()->success('the log was deleted');
            return redirect()->back();
        } catch (\Exception $ex) {
            report($ex);
            toastr()->error($ex->getMessage());
            return redirect()->back();
        }
    }

}
