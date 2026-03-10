<?php

namespace Core\Pages\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Pages\Requests\TestimonialsRequest;
use Core\Pages\Requests\ImportTestimonialsRequest;
use Core\Pages\Exports\TestimonialsExport;
use Core\Pages\Services\TestimonialsService;

class TestimonialsController extends Controller
{
    use ApiResponse;
    public function __construct(protected TestimonialsService $testimonialsService){}

    public function index(){
        $title      = trans('Testimonials index');
        $screen     = 'testimonials-index';
        $total      = $this->testimonialsService->totalCount();
        $trash      = $this->testimonialsService->trashCount();

        return view('pages::pages.testimonials.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->testimonialsService->get($id) : null;
        $screen     = isset($item)  ? 'Testimonials-edit'          : 'Testimonials-create';
        $title      = isset($item)  ? trans("Testimonials  edit")  : trans("Testimonials  create");


        return view('pages::pages.testimonials.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(TestimonialsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->testimonialsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.testimonials.delete',$record->id);
            $record->updateUrl  = route('dashboard.testimonials.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('Testimonials saved'),['entity'=>$record->itemData]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }


    public function show($id){
        $title      = trans('Testimonials show');
        $screen     = 'testimonials-show';
        $item       = $this->testimonialsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.testimonials.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->testimonialsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Testimonials deleted'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    public function dataTable(Request $request){
        try {
            $data             = $this->testimonialsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('Testimonials import');
        $screen     = 'Testimonials-import';
        $url        = route('dashboard.testimonials.import') ;
        $exportUrl  = route('dashboard.testimonials.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.testimonials.index') ;
        $cols       = ['translations.en.name'=>'name en','translations.ar.name'=>'name fr','avatar'=>'avatar','translations.en.title'=>'title en','translations.ar.title'=>'title fr','translations.en.body'=>'body en','translations.ar.body'=>'body fr','translations.en.location'=>'location en','translations.ar.location'=>'location fr'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportTestimonialsRequest $request){
        try {
            DB::beginTransaction();
            $this->testimonialsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Testimonials saved'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function export(Request $request)
    {
        $filename = $request->headersOnly ? 'testimonials-template.xlsx' : 'testimonials.xlsx';
        return Excel::download(new TestimonialsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->testimonialsService->comment($id,$request->content,$request->parent_id);
            DB::commit();
            return $this->returnData(trans('comment created'),['comment'=>new CommentResource($comment)]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function restore(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->testimonialsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Testimonial restored'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

}
