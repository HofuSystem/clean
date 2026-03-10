<?php

namespace Core\PaymentGateways\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\PaymentGateways\Requests\PaymentTransactionsRequest;
use Core\PaymentGateways\Requests\ImportPaymentTransactionsRequest;
use Core\PaymentGateways\Exports\PaymentTransactionsExport;
use Core\PaymentGateways\Services\PaymentTransactionsService;

class PaymentTransactionsController extends Controller
{
    use ApiResponse;
    public function __construct(protected PaymentTransactionsService $paymentTransactionsService) {}

    public function index()
    {
        $title      = trans('payment transactions index');
        $screen     = 'payment-transactions-index';
        $total      = $this->paymentTransactionsService->totalCount();
        $trash      = $this->paymentTransactionsService->trashCount();

        return view('payment-gateways::pages.payment-transactions.list', compact('title', 'screen', "total", "trash"));
    }


    public function createOrEdit(Request $request, $id = null)
    {
        $item       = isset($id)    ? $this->paymentTransactionsService->get($id) : null;
        $screen     = isset($item)  ? 'payment transactions-edit'          : 'payment transactions-create';
        $title      = isset($item)  ? trans("payment transactions  edit")  : trans("payment transactions  create");


        return view('payment-gateways::pages.payment-transactions.edit', compact('item', 'title', 'screen'));
    }

    public function storeOrUpdate(PaymentTransactionsRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record             = $this->paymentTransactionsService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl  = route('dashboard.payment-transactions.delete', $record->id);
            $record->updateUrl  = route('dashboard.payment-transactions.edit', $record->id);
            DB::commit();
            return $this->returnData(trans('payment transactions saved'), ['entity' => $record->itemData]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }


    public function show($id)
    {
        $title      = trans('payment transactions show');
        $screen     = 'payment-transactions-show';
        $item       = $this->paymentTransactionsService->get($id);;
        $comments   = $item->comments()->where('parent_id', null)->get();
        return view('payment-gateways::pages.payment-transactions.show', compact('title', 'screen', 'item', 'comments'));
    }


    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $record             = $this->paymentTransactionsService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('payment transactions deleted'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $data             = $this->paymentTransactionsService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
    public function importView(Request $request)
    {
        $title      = trans('payment transactions import');
        $screen     = 'payment transactions-import';
        $url        = route('dashboard.payment-transactions.import');
        $exportUrl  = route('dashboard.payment-transactions.export', ['headersOnly' => 1]);
        $backUrl    = route('dashboard.payment-transactions.index');
        $cols       = ['transaction_id' => 'transaction id', 'for' => 'for', 'status' => 'status', 'request_data' => 'request data', 'payment_method' => 'payment method', 'payment_data' => 'payment data', 'payment_response' => 'payment response'];
        return view('settings::views.import', compact('title', 'screen', 'url', 'exportUrl', 'backUrl', 'cols'));
    }
    public function import(ImportPaymentTransactionsRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->paymentTransactionsService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('payment transactions saved'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
    public function export(Request $request)
    {
        $filename = $request->headersOnly ? 'payment-transactions-template.xlsx' : 'payment-transactions.xlsx';
        return Excel::download(new PaymentTransactionsExport($request->headersOnly, $request->cols), $filename);
    }
    public function comment(CommentRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $comment = $this->paymentTransactionsService->comment($id, $request->content, $request->parent_id);
            DB::commit();
            return $this->returnData(trans('comment created'), ['comment' => new CommentResource($comment)]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
    public function restore(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $record             = $this->paymentTransactionsService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('PaymentTransaction restored'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
