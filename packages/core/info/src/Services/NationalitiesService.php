<?php

namespace Core\Info\Services;

use Core\Comments\Services\CommentingService;
use Core\Info\Models\Nationality;
use Core\Info\DataResources\NationalitiesResource;

class NationalitiesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key = 'id', string $value = 'name'){
        $locale = app()->getLocale();
        return \Illuminate\Support\Facades\DB::table('nationalities')
            ->join('nationality_translations', function($join) use ($locale) {
                $join->on('nationalities.id', '=', 'nationality_translations.nationality_id')
                     ->where('nationality_translations.locale', '=', $locale);
            })
            ->whereNull('nationalities.deleted_at')
            ->select('nationalities.id', 'nationality_translations.name')
            ->orderBy('nationality_translations.name', 'asc')
            ->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['arranging','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Nationality::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Nationality::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Nationality::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Nationality::count();
        $recordsFiltered    = Nationality::search()->count();
        $records            = Nationality::select(['id','arranging'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => NationalitiesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Nationality::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Nationality::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Nationality::count();
    }
    public function trashCount(){
        return Nationality::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Nationality::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
