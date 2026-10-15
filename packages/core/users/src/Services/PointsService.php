<?php

namespace Core\Users\Services;

use Core\Comments\Services\CommentingService;
use Core\Users\Models\Point;
use Core\Users\DataResources\PointsResource;
use Core\Users\Models\User;

class PointsService
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
        return Point::select($selected)->get();
    }

    public function storeOrUpdate(array $data = [],$id = null){
        $recordData = array_filter($data,fn($key) => in_array($key, ['title','amount','operation','expire_at','user_id','translations']),ARRAY_FILTER_USE_KEY);
        $record     = Point::updateOrCreate(['id' => $id],$recordData);
        
        
        return $record;
    }

    public function get(int $id){
        $point = Point::with(['user', 'creator', 'order'])->findOrFail($id);
        if (!$point->order) {
            if (preg_match('/(?:for order|used in order)\s*:\s*(.+)$/i', $point->title, $matches)) {
                $order = \Core\Orders\Models\Order::where('reference_id', trim($matches[1]))->first();
            } elseif (preg_match('/order cancellation\s*(\d+)$/i', $point->title, $matches)) {
                $order = \Core\Orders\Models\Order::find((int) $matches[1]);
            } else {
                $order = null;
            }
            if ($order) {
                $point->setRelation('order', $order);
            }
        }
        return $point;
    }

    public function delete(int $id,$final = false){
        $record             = Point::findOrFail($id);
        if($final){
            $record->forceDelete();
        }else{
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw){

        $recordsTotal       = Point::count();
        $recordsFiltered    = Point::search()->count();
        $records            = Point::select(['id','title','amount','operation','expire_at','created_at','user_id','creator_id','order_id'])
        ->with(['user:id,fullname,phone', 'creator:id,fullname', 'order:id,reference_id'])
        ->search()->dataTable()->get();

        // Older entries kept the order reference in their title rather than in
        // a dedicated column. Resolve those references for display when possible.
        $legacyReferences = [];
        $legacyIds = [];
        foreach ($records as $record) {
            if ($record->order_id) {
                continue;
            }

            if (preg_match('/(?:for order|used in order)\s*:\s*(.+)$/i', $record->title, $matches)) {
                $legacyReferences[] = trim($matches[1]);
            } elseif (preg_match('/order cancellation\s*(\d+)$/i', $record->title, $matches)) {
                $legacyIds[] = (int) $matches[1];
            }
        }

        $legacyOrders = collect();
        if ($legacyReferences || $legacyIds) {
            $legacyOrders = \Core\Orders\Models\Order::query()
                ->where(function ($query) use ($legacyReferences, $legacyIds) {
                    if ($legacyReferences) {
                        $query->whereIn('reference_id', array_unique($legacyReferences));
                    }
                    if ($legacyIds) {
                        $method = $legacyReferences ? 'orWhereIn' : 'whereIn';
                        $query->{$method}('id', array_unique($legacyIds));
                    }
                })
                ->get(['id', 'reference_id'])
                ->keyBy('id');

            foreach ($records as $record) {
                if ($record->order_id) {
                    continue;
                }
                if (preg_match('/(?:for order|used in order)\s*:\s*(.+)$/i', $record->title, $matches)) {
                    $reference = trim($matches[1]);
                    $order = $legacyOrders->firstWhere('reference_id', $reference);
                } elseif (preg_match('/order cancellation\s*(\d+)$/i', $record->title, $matches)) {
                    $order = $legacyOrders->get((int) $matches[1]);
                } else {
                    $order = null;
                }
                if ($order) {
                    $record->setRelation('order', $order);
                }
            }
        }
        
        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => PointsResource::collection($records)
        ];
    }

    public function order(array $list,$orderBy='order'){
        foreach ($list as  $value) {
            Point::find($value['id'])->update([$orderBy=>$value['order']]);
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
         Point::class,
         $id,
         $content,
         request()->user()->id,
         $parent_id
       );
    }
    public function totalCount(){
        return Point::count();
    }
    public function trashCount(){
        return Point::onlyTrashed()->count();
    }

    public function summary(): array
    {
        $summary = Point::query()->selectRaw(
            "COUNT(*) as movements,
             COALESCE(SUM(CASE WHEN operation = 'deposit' THEN amount ELSE 0 END), 0) as added,
             COALESCE(SUM(CASE WHEN operation = 'withdraw' THEN amount ELSE 0 END), 0) as deducted"
        )->first();

        return [
            'movements' => (int) $summary->movements,
            'added' => (float) $summary->added,
            'deducted' => (float) $summary->deducted,
            'net' => (float) $summary->added - (float) $summary->deducted,
        ];
    }
    public function restore(int $id){
        $record = Point::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
    public static function updateUserPoints($userId){
        $depositPoints = Point::where('user_id', $userId)->where('operation', 'deposit')->sum('amount');
        $withdrawPoints = Point::where('user_id', $userId)->where('operation', 'withdraw')->sum('amount');
        $user = User::find($userId);
        $currentPoints = $depositPoints - $withdrawPoints; 
        if ($user) {
            $user->update(['points_balance' =>$currentPoints]);
        }
        return $currentPoints;
    }
}
