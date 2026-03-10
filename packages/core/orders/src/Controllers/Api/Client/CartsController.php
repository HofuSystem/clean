<?php

namespace Core\Orders\Controllers\Api\Client;

use App\Http\Controllers\Controller;
use Core\Orders\Requests\Api\CartsRequest;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Orders\Services\CartsService;

class CartsController extends Controller
{
    use ApiResponse;
    public function __construct(protected CartsService $cartsService){}

    public function save(CartsRequest $request){
        $hasNewData = $this->cartsService->carHasNewData($request->user()?->id, $request->data);
        if(!$hasNewData){
            return $this->returnSuccessMessage(trans('Cart is up to date'));
        }
        try {
            DB::beginTransaction();
            $cartData = [
                'user_id'   => $request->user()?->id,
                'phone'     => $request->user()?->phone,
                'data'      => json_encode($request->data),
            ];
            $record   = $this->cartsService->storeOrUpdate($cartData);
            DB::commit();
            return $this->returnSuccessMessage(trans('Cart updated'));
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
