<?php

namespace Core\Categories\Services;

use Core\Comments\Services\CommentingService;
use Core\Categories\Models\CategorySetting;
use Core\Categories\DataResources\CategorySettingsResource;
use Core\Settings\Helpers\ToolHelper;

class CategorySettingsService
{
    public function __construct(protected CommentingService $commentingService,protected PricesService $pricesService){}

    public function selectable(string $key,string $value,$parent = false){
        $selected = ['id'];
        if(!in_array($key,["name"])){
            $selected[] = $key;
        }
        if(!in_array($value,["name"])){
            $selected[] = $value;
        }
        return CategorySetting::select($selected)
        ->when($parent,function($parentQuery){
            $parentQuery->whereNull('parent_id');
        })
        ->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData         = array_filter($data,fn($key) => in_array($key, ['category_id','addon_price','cost','parent_id','status','translations']),ARRAY_FILTER_USE_KEY);
        $slug               = \Str::slug($data['translations']['en']['name']);
        $slug               = ToolHelper::generateUniqueSlug(CategorySetting::class,$slug,$id);
        $recordData['slug'] = $slug;
        $record             = CategorySetting::updateOrCreate(['id' => $id],$recordData);
        
        if(!isset($id)){
            //saving on create the related pricesItems
            $pricesItems            = $data['addon_prices'] ?? [];
            foreach ($pricesItems as $index => $itemValues) {
                $itemValues['priceable_type']   = CategorySetting::class;
                $itemValues['priceable_id']     = $record->id;
                $this->pricesService->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
            //saving on create the related categorySettingsItems
            $categorySettingsItems            = $data['category_settings'] ?? [];
            foreach ($categorySettingsItems as $index => $itemValues) {
                $itemValues['parent_id'] = $record->id;
                $this->storeOrUpdate($itemValues,$itemValues['id'] ?? null);
            }
        }
           
        return $record;
    }

    public function get(int $id){
        return  CategorySetting::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = CategorySetting::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw,$type){

        $recordsTotal       = CategorySetting::count();
        $recordsFiltered    = CategorySetting::search($type)->count();
        $records            = CategorySetting::select(['id','slug','category_id','addon_price','parent_id','status','cost'])
        ->with(['category','parent','categorySettings'])
        ->search($type)->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CategorySettingsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            CategorySetting::find($value['id'])->update([$orderBy=>$value['order']]);
        }
    }
    public function import(array $items){
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item,$item['id'] ?? null);
        }
        return $items;
    }
    public function comment(int $id,string $content,int | null $parent_id){
       return $this->commentingService->comment(
         CategorySetting::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount($type){
        return CategorySetting::search($type)->count();
    }
    public function trashCount($type){
        return CategorySetting::search($type)->onlyTrashed()->count();
    }
}
