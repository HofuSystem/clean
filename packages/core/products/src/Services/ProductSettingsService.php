<?php

namespace Core\Products\Services;

use Core\Comments\Services\CommentingService;
use Core\Products\Models\ProductSetting;
use Core\Products\DataResources\ProductSettingsResource;
use Core\Settings\Helpers\ToolHelper;
use Core\Categories\Services\PricesService;

class ProductSettingsService
{
    public function __construct(protected CommentingService $commentingService, protected PricesService $pricesService){}

    public function selectable(string $key, string $value, $parent = false)
    {
        $selected = ['id'];
        if(!in_array($key, ["name"])){
            $selected[] = $key;
        }
        if(!in_array($value, ["name"])){
            $selected[] = $value;
        }
        return ProductSetting::select($selected)
        ->when($parent, function($parentQuery){
            $parentQuery->whereNull('parent_id');
        })
        ->get();
    }

    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter($data, fn($key) => in_array($key, ['addon_price', 'cost', 'discount_percent', 'parent_id', 'status', 'color', 'icon', 'translations', 'general']), ARRAY_FILTER_USE_KEY);
        $slug = null;
        if ($id) {
            $existingRecord = ProductSetting::find($id);
            if ($existingRecord && $existingRecord->slug) {
                $slug = $existingRecord->slug;
            }
        }
        if (!$slug) {
            $slug = \Str::slug($data['translations']['en']['name'] ?? '');
            $slug = ToolHelper::generateUniqueSlug(ProductSetting::class, $slug, $id);
        }
        $recordData['slug'] = $slug;
        $record             = ProductSetting::updateOrCreate(['id' => $id], $recordData);
        
        if(!isset($id)){
            //saving on create the related pricesItems
            $pricesItems            = $data['addon_prices'] ?? [];
            foreach ($pricesItems as $index => $itemValues) {
                $itemValues['priceable_type']   = ProductSetting::class;
                $itemValues['priceable_id']     = $record->id;
                $this->pricesService->storeOrUpdate($itemValues, $itemValues['id'] ?? null);
            }
            //saving on create the related productSettingsItems
            $productSettingsItems            = $data['product_settings'] ?? [];
            foreach ($productSettingsItems as $index => $itemValues) {
                $itemValues['parent_id'] = $record->id;
                $this->storeOrUpdate($itemValues, $itemValues['id'] ?? null);
            }
        }
           
        return $record;
    }

    public function get(int|string $id)
    {
        return ProductSetting::findOrFail($id);
    }

    public function delete(int|string $id, $final = false)
    {
        $record             = ProductSetting::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw, $type)
    {
        $recordsTotal       = ProductSetting::count();
        $recordsFiltered    = ProductSetting::search($type)->count();
        $records            = ProductSetting::select(['id', 'slug', 'addon_price', 'parent_id', 'status', 'cost'])
        ->with(['products', 'parent', 'productSettings'])
        ->search($type)->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => ProductSettingsResource::collection($records)
        ];
    }

    public function order(array $list, $orderBy='order')
    {
        foreach ($list as  $value) {
            ProductSetting::find($value['id'])->update([$orderBy=>$value['order']]);
        }
    }

    public function import(array $items)
    {
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item, $item['id'] ?? null);
        }
        return $items;
    }

    public function comment(int|string $id, string $content, int | null $parent_id)
    {
       return $this->commentingService->comment(
         ProductSetting::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }

    public function totalCount($type)
    {
        return ProductSetting::search($type)->count();
    }

    public function trashCount($type)
    {
        return ProductSetting::search($type)->onlyTrashed()->count();
    }

    public function restore($id)
    {
        $record = ProductSetting::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
