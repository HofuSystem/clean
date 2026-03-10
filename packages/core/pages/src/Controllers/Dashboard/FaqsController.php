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
use Core\Pages\Requests\FaqsRequest;
use Core\Pages\Requests\ImportFaqsRequest;
use Core\Pages\Exports\FaqsExport;
use Core\Pages\Services\FaqsService;

class FaqsController extends Controller
{
    use ApiResponse;
    public function __construct(protected FaqsService $faqsService){}

    public function index(){
        $title      = trans('faqs index');
        $screen     = 'faqs-index';
        $total      = $this->faqsService->totalCount();
        $trash      = $this->faqsService->trashCount();

        return view('pages::pages.faqs.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->faqsService->get($id) : null;
        $screen     = isset($item)  ? 'faqs-edit'          : 'faqs-create';
        $title      = isset($item)  ? trans("faqs  edit")  : trans("faqs  create");


        return view('pages::pages.faqs.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(FaqsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->faqsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.faqs.delete',$record->id);
            $record->updateUrl  = route('dashboard.faqs.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('faqs saved'),['entity'=>$record->itemData]);
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
        $title      = trans('faqs show');
        $screen     = 'faqs-show';
        $item       = $this->faqsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('pages::pages.faqs.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->faqsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('faqs deleted'));
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
            $data             = $this->faqsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('faqs import');
        $screen     = 'faqs-import';
        $url        = route('dashboard.faqs.import') ;
        $exportUrl  = route('dashboard.faqs.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.faqs.index') ;
        $cols       = ['translations.en.question'=>'question en','translations.ar.question'=>'question ar','translations.en.answer'=>'answer en','translations.ar.answer'=>'answer ar'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportFaqsRequest $request){
        try {
            DB::beginTransaction();
            $this->faqsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('faqs saved'));
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
        $filename = $request->headersOnly ? 'faqs-template.xlsx' : 'faqs.xlsx';
        return Excel::download(new FaqsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->faqsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->faqsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Faq restored'));
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
