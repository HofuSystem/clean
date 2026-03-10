<?php

namespace Core\Categories\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Settings\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

use Core\Comments\Requests\CommentRequest;
use Core\Comments\DataResources\CommentResource;
use Core\Categories\Requests\SlidersRequest;
use Core\Categories\Requests\ImportSlidersRequest;
use Core\Categories\Exports\SlidersExport;
use Core\Categories\Services\SlidersService;
use Core\Categories\Services\CategoriesService;
use Core\Info\Services\CitiesService;

class SlidersController extends Controller
{
    use ApiResponse;
    public function __construct(protected SlidersService $slidersService, protected CategoriesService $categoriesService, protected CitiesService $citiesService)
    {
    }

    public function index()
    {
        $title = trans('Slider index');
        $screen = 'sliders-index';
        $total = $this->slidersService->totalCount();
        $trash = $this->slidersService->trashCount();
        $categories = $this->categoriesService->selectable('id', 'name');
        $cities = $this->citiesService->selectable('id', 'name');

        return view('categories::pages.sliders.list', compact('title', 'screen', 'categories', 'cities', "total", "trash"));
    }


    public function createOrEdit(Request $request, $id = null)
    {
        $item = isset($id) ? $this->slidersService->get($id) : null;
        $screen = isset($item) ? 'Slider-edit' : 'Slider-create';
        $title = isset($item) ? trans("Slider  edit") : trans("Slider  create");
        $categories = $this->categoriesService->selectable('id', 'name', ['parent_id' => null]);
        $cities = $this->citiesService->selectable('id', 'name');

        $sliderView = isset($item) ? $this->slidersService->getSliderView($item->id) : null;

        return view('categories::pages.sliders.edit', compact('item', 'title', 'screen', 'categories', 'cities', 'sliderView'));
    }

    public function storeOrUpdate(SlidersRequest $request, $id = null)
    {
        try {
            DB::beginTransaction();
            $record = $this->slidersService->storeOrUpdate($request->all(), $id);
            $record->deleteUrl = route('dashboard.sliders.delete', $record->id);
            $record->updateUrl = route('dashboard.sliders.edit', $record->id);
            DB::commit();
            return $this->returnData(trans('Slider saved'), ['entity' => $record->itemData]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }


    public function show($id)
    {
        $title = trans('Slider index');
        $screen = 'sliders-index';
        $item = $this->slidersService->get($id);
        ;
        $comments = $item->comments()->where('parent_id', null)->get();
        return view('categories::pages.sliders.show', compact('title', 'screen', 'item', 'comments'));
    }


    public function delete(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $record = $this->slidersService->delete($id, $request->final);
            DB::commit();
            return $this->returnSuccessMessage(trans('Slider deleted'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }

    public function dataTable(Request $request)
    {
        try {
            $data = $this->slidersService->dataTable($request->draw);
            return $this->returnData(trans('data founded'), $data);
        } catch (ValidationException $e) {
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
    public function importView(Request $request)
    {
        $title = trans('Slider import');
        $screen = 'Slider-import';
        $url = route('dashboard.sliders.import');
        $exportUrl = route('dashboard.sliders.export', ['headersOnly' => 1]);
        $backUrl = route('dashboard.sliders.index');
        $cols = ['category_id' => 'category', 'type' => 'type', 'status' => 'status', 'city_id' => 'city'];
        return view('settings::views.import', compact('title', 'screen', 'url', 'exportUrl', 'backUrl', 'cols'));
    }
    public function import(ImportSlidersRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->slidersService->import($request->data);
            DB::commit();
            return $this->returnSuccessMessage(trans('Slider saved'));
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
    public function export(Request $request)
    {
        $filename = $request->headersOnly ? 'sliders-template.xlsx' : 'sliders.xlsx';
        return Excel::download(new SlidersExport($request->headersOnly, $request->cols), $filename);
    }
    public function comment(CommentRequest $request, $id)
    {
        try {
            DB::beginTransaction();
            $comment = $this->slidersService->comment($id, $request->content, $request->parent_id);
            DB::commit();
            return $this->returnData(trans('comment created'), ['comment' => new CommentResource($comment)]);
        } catch (ValidationException $e) {
            DB::rollback();
            return $this->returnErrorMessage($e->getMessage(), $e->errors(), [], 422);
        } catch (\Throwable $e) {
            DB::rollback();
            report($e);
            return $this->returnErrorMessage(trans('system Error please try again later'), [], [], 422);
        }
    }
    public function restore(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $record = $this->slidersService->restore($id);
            DB::commit();
            return $this->returnSuccessMessage(trans('Slider restored'));
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
