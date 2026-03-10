<?php

namespace Core\Wallet\Services;

use Core\Comments\Services\CommentingService;
use Core\Wallet\DataResources\Api\WalletPackageResource;
use Core\Wallet\Models\WalletPackage;
use Core\Wallet\DataResources\WalletPackagesResource;

class WalletPackagesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key,string $value){
        $selected = ['id'];
        if(!in_array($key,[])){
            $selected[] = $key;
        }
        if(!in_array($value,[])){
            $selected[] = $value;
        }
        return WalletPackage::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['image','price','value','status','translations']),ARRAY_FILTER_USE_KEY);
        $record     = WalletPackage::updateOrCreate(['id' => $id],$recordData);


        return $record;
    }

    public function get(int $id){
        return  WalletPackage::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = WalletPackage::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = WalletPackage::count();
        $recordsFiltered    = WalletPackage::search()->count();
        $records            = WalletPackage::select(['id','image','price','value','status'])
        ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => WalletPackagesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            WalletPackage::find($value['id'])->update([$orderBy=>$value['order']]);
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
         WalletPackage::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return WalletPackage::count();
    }
    public function trashCount(){
        return WalletPackage::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = WalletPackage::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
    public function packages()
    {
        $wallet_packages=WalletPackage::where('status','active')->get();
        return WalletPackageResource::collection($wallet_packages);
    }
}
