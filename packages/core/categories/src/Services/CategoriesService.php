<?php

namespace Core\Categories\Services;

use Core\Comments\Services\CommentingService;
use Core\Categories\Models\Category;
use Core\Categories\DataResources\CategoriesResource;
use Carbon\Carbon;
use Core\Categories\DataResources\CategoriesSelectResource;
use Core\Categories\Models\CategoryDateTime;
use Core\Settings\Helpers\ToolHelper;
use Illuminate\Support\Collection;
use Core\Products\Services\ProductsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class CategoriesService
{
    public function __construct(protected CommentingService $commentingService,protected CategoryTypesService $categoryTypesService,protected CategorySettingsService $categorySettingsService,protected CategoryDateTimesService $categoryDateTimesService){}

    public function selectable(string $key,string $value,$where = [],$hasProducts = false,$types = []){
        $selected = ['id','type','parent_id'];
        if(!in_array($key,["name","intro","desc"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name","intro","desc"])){
            $selected[] = $value;
        }
        return Category::with('translations')->select($selected)->where($where)
        ->when(!empty($types),function($typesQuery)use($types){$typesQuery->whereIn('type',$types);})
        ->when($hasProducts,fn($query) => $query->where(fn($query)=> $query->has('products')->orHas('subCategories.products')->orHas('productsSub')))
        ->get();
    }
    public function getSelect(string $key,string $value,$where = [],$hasProducts = false){
       $categories = $this->selectable($key,$value,$where,$hasProducts);
       return CategoriesSelectResource::collection($categories);
    }

    public function duplicateAction(array $data = [],$id){
        $productsService = app(ProductsService::class);
        $category = Category::findOrFail($id);
        $newCategory = $this->storeOrUpdate($data);

        $oldProducts = $category->productsSub;
        foreach($oldProducts as $oldProduct){
            $newProductData = $oldProduct->toArray();
            unset($newProductData['id']);
            $newProductData['sub_category_id'] = $newCategory->id;
            $newProductData['translations'] = $oldProduct->translations->keyBy('locale')->map(function($translation){
                $translation->id = null;
                $translation->product_id = null;
                return $translation;
            })->toArray();
            
            $newProduct = $productsService->storeOrUpdate($newProductData);
        }
        return $newCategory;
    }
    public function storeOrUpdate(array $data = [],$id = null){
        $recordData         = array_filter($data,fn($key) => in_array($key, ['slug','image','type','delivery_price','discount_percent','sort','is_package','status','parent_id','for_all_cities','cities','translations']),ARRAY_FILTER_USE_KEY);
        $record             = Category::updateOrCreate(['id' => $id],$recordData);
        $record->cities()->sync($data['cities'] ?? []);
        if(!isset($id)){
            //saving on create the related categoriesItems
            $categoriesItems            = $data['sub_categories'] ?? [];
            foreach ($categoriesItems as $index => $itemValues) {
                $itemValues['parent_id'] = $record->id;
                $this->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
            //saving on create the related categoryTypesItems
            $categoryTypesItems            = $data['category_types'] ?? [];
            foreach ($categoryTypesItems as $index => $itemValues) {
                $itemValues['category_id'] = $record->id;
                $this->categoryTypesService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
            //saving on create the related categorySettingsItems
            $categorySettingsItems            = $data['category_settings'] ?? [];
            foreach ($categorySettingsItems as $index => $itemValues) {
                $itemValues['parent_id'] = $record->id;
                $this->categorySettingsService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
            //saving on create the related categoryDateTimesItems
            $categoryDateTimesItems            = $data['date_times'] ?? [];
            foreach ($categoryDateTimesItems as $index => $itemValues) {
                $itemValues['category_id'] = $record->id;
                $this->categoryDateTimesService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }

        return $record;
    }

    public function get(int $id){
        return  Category::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Category::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw,$type, $isSales = false){

        $recordsTotal       = Category::search($type, $isSales)->count();
        $recordsFiltered    = Category::search($type, $isSales)->count();
        $records            = Category::select(['id','image','type','delivery_price','sort','is_package','status','parent_id'])
        ->with(['parent','cities'])
        ->search($type, $isSales)
        ->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CategoriesResource::collection($records)
        ];
    }

    public function order(array $list, $orderBy = 'order')
    {
        $cases = [];
        $ids = [];
        foreach ($list as $value) {
            $id = (int)$value['id'];
            $order = (int)$value['order'];
            $cases[] = "WHEN {$id} THEN {$order}";
            $ids[] = $id;
        }

        if (!empty($ids)) {
            $idsList = implode(',', $ids);
            $casesList = implode(' ', $cases);
            DB::update("UPDATE categories SET `{$orderBy}` = (CASE id {$casesList} END) WHERE id IN ({$idsList})");
        }

        Cache::tags(['categories_api'])->flush();
    }
    public function import(array $items){
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item,$item['id'] ?? null);
        }
        return $items;
    }
    public function comment(int $id,string $content,int | null $parent_id){
       return $this->commentingService->comment(
         Category::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount($type = null, $isSales = false){
        return Category::search($type, $isSales)->count();
    }
    public function trashCount($type = null, $isSales = false){
        return Category::search($type, $isSales)->onlyTrashed()->count();
    }

    //create date times
    public function restore(int $id){
        $record = Category::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
