<?php

namespace Core\Users\Controllers\Api;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Core\Users\DataResources\Api\AddressesResource;
use Core\Users\Models\Address;
use Core\Users\Requests\Api\AddressesRequest;
use Core\Users\Services\AddressesService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AddressesController extends Controller
{
    use ApiResponse;
    /**
     * Display a listing of the user's addresses.
     */
    public function __construct(protected AddressesService $addressesService ){}

    public function index()
    {
        try {
            $user = Auth::user();
            $addresses = $user->addresses()->get();

            return $this->returnData(trans('addresses'),['data'=>AddressesResource::collection($addresses)]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }

    /**
     * Store a newly created address for the user.
     */
    public function store(AddressesRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        try {
            $address = Address::where('name',$request->name)
                ->where('user_id',$data['user_id'])
                ->first();
            DB::beginTransaction();
            $record = $this->addressesService->storeOrUpdate($data,$address?->id);
            DB::commit();
            return $this->returnData(trans('address add successfully'),['data'=>new AddressesResource($record)]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }
    }
/**
     * Update the specified address.
     */
    public function update(AddressesRequest $request,$id)
    {
        $data = $request->validated();
        try {
            DB::beginTransaction();
            $record = $this->addressesService->storeOrUpdate($data,$id);
            DB::commit();
            return $this->returnData(trans('address update successfully'),['data'=>new AddressesResource($record)]);
        }catch(ValidationException $e){
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(),$e->errors(),[],422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'),[],[],422);
        }

       /*  // If setting as default, unset other defaults
        if ($request->has('is_default') && $request->is_default) {
            $address->user->addresses()
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        } */
    }


    /**
     * Remove the specified address.
     */
    public function destroy(Request $request,$id){
        try {
            DB::beginTransaction();
            $record             = $this->addressesService->delete($id,$request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('address deleted'));
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
