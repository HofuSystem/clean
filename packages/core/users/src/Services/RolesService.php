<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\Permission;
use Core\Users\Models\Role;
use Core\Users\DataResources\RolesResource;
use Spatie\Permission\Models\Role as SpatieRole;

class RolesService
{
    public function __construct(protected CommentingService $commentingService){}

    public function selectable(string $key = 'id', string $value = 'title'){
        $locale = app()->getLocale();
        return \Illuminate\Support\Facades\DB::table('roles')
            ->leftJoin('role_translations', function($join) use ($locale) {
                $join->on('roles.id', '=', 'role_translations.role_id')
                     ->where('role_translations.locale', '=', $locale);
            })
            ->whereNull('roles.deleted_at')
            ->select('roles.id', 'roles.name', \Illuminate\Support\Facades\DB::raw('COALESCE(role_translations.title, roles.name) as title'))
            ->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['name','translations']),ARRAY_FILTER_USE_KEY);
        if(!isset($data['guard_name'])){
            $recordData['guard_name'] = 'web';
        }
        $record         = Role::updateOrCreate(['id' => $id],$recordData);
        $record         = SpatieRole::findOrFail($record->id);
        $permissions    = $data['permissions'] ?? [];
        // $permissions    = Permission::select('id')->whereIn('id',$permissions)->orWhereIn('name',$permissions)->get()->pluck('id')->toArray();
        $record->syncPermissions($permissions);
        
        
        return $record;
    }

    public function get(int $id){
        return  Role::findOrFail($id);
    }

    public function delete(int $id,$final = false){
        $record             = Role::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Role::count();
        $recordsFiltered    = Role::search()->count();
        $records            = Role::select(['id','name'])
        ->search()->dataTable()->get();
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => RolesResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Role::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Role::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Role::count();
    }
    public function trashCount(){
        return Role::onlyTrashed()->count();
    }
    public function restore(int $id){
        $record = Role::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
