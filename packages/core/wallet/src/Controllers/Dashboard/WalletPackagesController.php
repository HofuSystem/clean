<?php

namespace Core\Wallet\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Wallet\Requests\WalletPackagesRequest; 
use Core\Wallet\Requests\ImportWalletPackagesRequest; 
use Core\Wallet\Exports\WalletPackagesExport; 
use Core\Wallet\Services\WalletPackagesService;

class WalletPackagesController extends Controller
{
    use ApiResponse;
    public function __construct(protected WalletPackagesService $walletPackagesService){}

    public function index(){
        $title      = trans('WalletPackage index');
        $screen     = 'wallet-packages-index';
        $total      = $this->walletPackagesService->totalCount();
        $trash      = $this->walletPackagesService->trashCount();

        return view('wallet::pages.wallet-packages.list', compact('title','screen',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->walletPackagesService->get($id) : null;
        $screen     = isset($item)  ? 'WalletPackage-edit'          : 'WalletPackage-create';
        $title      = isset($item)  ? trans("WalletPackage  edit")  : trans("WalletPackage  create");


        return view('wallet::pages.wallet-packages.edit', compact('item','title','screen') );
    }

    public function storeOrUpdate(WalletPackagesRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->walletPackagesService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.wallet-packages.delete',$record->id);
            $record->updateUrl  = route('dashboard.wallet-packages.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('WalletPackage saved'),['entity'=>$record->itemData]);
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
        $title      = trans('WalletPackage index');
        $screen     = 'wallet-packages-index';
        $item       = $this->walletPackagesService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('wallet::pages.wallet-packages.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->walletPackagesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('WalletPackage deleted'));
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
            $data             = $this->walletPackagesService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('WalletPackage import');
        $screen     = 'WalletPackage-import';
        $url        = route('dashboard.wallet-packages.import') ;
        $exportUrl  = route('dashboard.wallet-packages.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.wallet-packages.index') ;
        $cols       = ['image'=>'image','price'=>'price','value'=>'value','status'=>'status'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportWalletPackagesRequest $request){
        try {
            DB::beginTransaction();
            $this->walletPackagesService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('WalletPackage saved'));
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
        $filename = $request->headersOnly ? 'wallet-packages-template.xlsx' : 'wallet-packages.xlsx';
        return Excel::download(new WalletPackagesExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->walletPackagesService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->walletPackagesService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('WalletPackage restored'));
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
