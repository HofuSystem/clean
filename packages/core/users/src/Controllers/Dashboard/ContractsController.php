<?php

namespace Core\Users\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Users\Requests\ContractsRequest; 
use Core\Users\Requests\ImportContractsRequest; 
use Core\Users\Exports\ContractsExport; 
use Core\Users\Services\ContractsService;
use Core\Users\Services\UsersService;
use Core\Products\Services\ProductsService;

class ContractsController extends Controller
{
    use ApiResponse;
    public function __construct(protected ContractsService $contractsService,protected UsersService $usersService,protected ProductsService $productsService){}

    public function index(){
        $title      = trans('contracts index');
        $screen     = 'contracts-index';
        $total      = $this->contractsService->totalCount();
        $trash      = $this->contractsService->trashCount();
		$clients = $this->usersService->selectable('id','fullname',[],'company');
		$contracts = $this->contractsService->selectable(['id','title']);
		$products = $this->productsService->selectable('id','name');

        return view('users::pages.contracts.list', compact('title','screen','clients','contracts','products',"total","trash"));
    }


    public function createOrEdit(Request $request,$id = null){
        $item       = isset($id)    ? $this->contractsService->get($id) : null;
        $screen     = isset($item)  ? 'contracts-edit'          : 'contracts-create';
        $title      = isset($item)  ? trans("contracts  edit")  : trans("contracts  create");
		$clients = $this->usersService->selectable('id','fullname',[],'company');
		$contracts = $this->contractsService->selectable(['id','title']);
		$products = $this->productsService->selectable('id','name',['category_id','sub_category_id'],['category','subCategory'])->map(function($item){
			$item->name = $item->category?->name . ' -> ' . $item->subCategory?->name . ' -> ' . $item->name;
			return $item;
		});


        return view('users::pages.contracts.edit', compact('item','title','screen','clients','contracts','products') );
    }

    public function storeOrUpdate(ContractsRequest $request, $id = null){
        try {
            DB::beginTransaction();
            $record             = $this->contractsService->storeOrUpdate($request->all(),$id);
            $record->deleteUrl  = route('dashboard.contracts.delete',$record->id);
            $record->updateUrl  = route('dashboard.contracts.edit',$record->id);
            DB::commit();
            return $this->returnData(trans('contracts saved'),['entity'=>$record->itemData]);
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
        $title      = trans('contracts show');
        $screen     = 'contracts-show';
        $item       = $this->contractsService->get($id);;
        $comments   = $item->comments()->where('parent_id',null)->get();
        return view('users::pages.contracts.show', compact('title','screen','item','comments'));
    }


    public function delete(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->contractsService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('contracts deleted'));
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
            $data             = $this->contractsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'),$data);
        }catch(ValidationException $e){
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function importView(Request $request){
        $title      = trans('contracts import');
        $screen     = 'contracts-import';
        $url        = route('dashboard.contracts.import') ;
        $exportUrl  = route('dashboard.contracts.export',['headersOnly' => 1]) ;
        $backUrl    = route('dashboard.contracts.index') ;
        $cols       = ['title'=>'title','months_count'=>'months count','month_fees'=>'month fees','unlimited_days'=>'unlimited days','number_of_days'=>'number of days','contract'=>'contract','start_date'=>'start date','end_date'=>'end date','client_id'=>'client'];
        return view('settings::views.import', compact('title','screen','url','exportUrl','backUrl','cols'));
    }
    public function import(ImportContractsRequest $request){
        try {
            DB::beginTransaction();
            $this->contractsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('contracts saved'));
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
        $filename = $request->headersOnly ? 'contracts-template.xlsx' : 'contracts.xlsx';
        return Excel::download(new ContractsExport($request->headersOnly,$request->cols), $filename);
    }
    public function comment(CommentRequest $request,$id){
        try {
            DB::beginTransaction();
            $comment = $this->contractsService->comment($id,$request->content,$request->parent_id);
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
            $record             = $this->contractsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Contract restored'));
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
    public function qrCodesForm(Request $request){
        $title      = trans('Generate QR Codes');
        $screen     = 'contracts-qr-codes-form';
        $contracts  = $this->contractsService->selectable(['id','title','client_id'],['client']);
        
        return view('users::pages.contracts.qr-codes-form', compact('title','screen','contracts'));
    }

    public function generateQrCodes(Request $request){
        $validated = $request->validate([
            'contract_id' => 'required|exists:contracts,id',
            'count' => 'required_if:has_titles,0|nullable|integer|min:1|max:100',
            'has_titles' => 'required|in:0,1',
            'titles' => 'required_if:has_titles,1|nullable|array',
            'titles.*' => 'required_if:has_titles,1|nullable|string',
        ]);

        $contract = $this->contractsService->get($request->contract_id);
        $qrCodes = [];

        if ($request->has_titles == '1') {
            foreach ($request->titles as $title) {
                $data = [
                    'user_id' => $contract->client_id,
                    'title' => $title 
                ];
                $qrCodes[] = [
                    'data' => base64_encode(json_encode($data)),
                    'title' => $title,
                    'owner' => $contract->client?->fullname ?? 'N/A'
                ];
            }
        } else {
            for ($i = 0; $i < $request->count; $i++) {
                $data = [
                    'user_id' => $contract->client_id,
                    'title' => $contract->title 
                ];
                $qrCodes[] = [
                    'data' => base64_encode(json_encode($data)),
                    'title' => $contract->title ?? 'N/A',
                    'owner' => $contract->client?->fullname ?? 'N/A'
                ];
            }
        }

        $title = trans('Generated QR Codes');
        $screen = 'contracts-qr-codes-display';
        
        return view('users::pages.contracts.qr-codes-display', compact('title','screen','qrCodes'));
    }
    
}
