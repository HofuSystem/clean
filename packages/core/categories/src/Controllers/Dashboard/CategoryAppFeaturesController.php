<?php

namespace Core\Categories\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Core\Categories\Requests\CategoryAppFeaturesRequest;
use Core\Categories\Services\CategoryAppFeaturesService;

class CategoryAppFeaturesController extends Controller
{
    use ApiResponse;

    public function __construct(protected CategoryAppFeaturesService $categoryAppFeaturesService) {}

    /**
     * Store or update an app feature.
     *
     * @param CategoryAppFeaturesRequest $request
     * @param int|null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeOrUpdate(CategoryAppFeaturesRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record = $this->categoryAppFeaturesService->storeOrUpdate($request->all(), $id);
            DB::commit();
            return $this->returnData(trans('App Feature saved'), ['entity' => $record->itemData]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    /**
     * Delete an app feature.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $this->categoryAppFeaturesService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('App Feature deleted'));
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
}
