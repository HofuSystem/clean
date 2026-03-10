<?php

namespace Core\Pages\Services;

use Core\Comments\Services\CommentingService;
use Core\Pages\Models\Testimonial;
use Core\Pages\DataResources\TestimonialsResource;

class TestimonialsService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable($cols = [],$with = [],$scopes = []){
        $query = Testimonial::underMyControl()->select($cols)->with($with);
        foreach($scopes as $scope){
            $query = $query->$scope();
        } 
        return $query->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['avatar','rating','is_active','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Testimonial::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        return  Testimonial::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Testimonial::underMyControl()->where('id',$id)->first();
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Testimonial::underMyControl()->count();
        $recordsFiltered    = Testimonial::underMyControl()->search()->count();
        $records            = Testimonial::underMyControl()->select(['id','avatar','rating','is_active'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => TestimonialsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Testimonial::underMyControl()->find($value['id'])->update([$orderBy=>$value['order']]);
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
         Testimonial::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Testimonial::underMyControl()->count();
    }
    public function trashCount(){
        return Testimonial::underMyControl()->onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Testimonial::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
