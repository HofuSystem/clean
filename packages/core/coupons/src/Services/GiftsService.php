<?php

namespace Core\Coupons\Services;

use Core\Coupons\Models\Gift;
use Core\Coupons\DataResources\GiftsResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



class GiftsService
{
    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter($data, fn($key) => in_array($key, [
            'status', 'from', 'to', 'coupon_code', 'order_type', 
            'register_from', 'register_to', 'orders_from', 'orders_to', 
            'orders_min', 'orders_max', 'translations',
            'type', 'value', 'max_value'
        ]), ARRAY_FILTER_USE_KEY);
        if (isset($recordData['order_type']) && is_array($recordData['order_type'])) {
            $recordData['order_type'] = implode(',', $recordData['order_type']);
        }else{
            $recordData['order_type'] = null;
        }

        
        $record = Gift::updateOrCreate(['id' => $id], $recordData);
        
        return $record;
    }

    public function get(int $id)
    {
        return Gift::findOrFail($id);
    }

    public function delete(int $id, $final = false)
    {
        $record = Gift::findOrFail($id);
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }

    public function dataTable($draw)
    {
        $recordsTotal    = Gift::count();
        $recordsFiltered = Gift::search()->count();
        $records         = Gift::select([
            'id', 'status', 'from', 'to', 'coupon_code', 'order_type'
        ])->search()->dataTable()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => GiftsResource::collection($records)

        ];
    }

    public function totalCount()
    {
        return Gift::count();
    }

    public function trashCount()
    {
        return Gift::onlyTrashed()->count();
    }

    public function restore(int $id)
    {
        $record = Gift::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }

    /**
     * Get the gift that matches the user the most which he didn't use its coupon
     */
    public function getMatchingGift($user,$orderType = null)
    {
        $now            = Carbon::now();
        $userId         = $user->id;
        $userCreatedAt  = $user->created_at;

        return Gift::where(function ($query) use ($now) {
                $query->whereNull('from')->orWhere('from', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('to')->orWhere('to', '>=', $now);
            })
            ->when($orderType, function ($query) use ($orderType) {
                $query->where(function($q) use ($orderType){
                    $q->whereRaw("FIND_IN_SET(?, order_type)", [$orderType]);
                    $q->orWhereNull('order_type');
                });
            })
            ->when(!$orderType, function ($query) {
                $query->whereNull('order_type');
            })
            // Check registration date range
            ->where(function ($query) use ($userCreatedAt) {
                $query->whereNull('register_from')->orWhere('register_from', '<=', $userCreatedAt);
            })
            ->where(function ($query) use ($userCreatedAt) {
                $query->whereNull('register_to')->orWhere('register_to', '>=', $userCreatedAt);
            })
            // Check if coupon already used
            ->where(function ($query) use ($userId) {
                $query->whereNull('coupon_id')
                    ->orWhereNotExists(function ($subQuery) use ($userId) {
                        $subQuery->select(DB::raw(1))
                            ->from('orders')
                            ->where('client_id', $userId)
                            ->whereColumn('orders.coupon_id', 'gifts.coupon_id');
                    });
            })
            // Check order count criteria using a subquery
            ->where(function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $orderCountSubquery = DB::table('orders')
                        ->selectRaw('count(*)')
                        ->where('client_id', $userId)
                        ->where(function ($sub) {
                            $sub->whereRaw('gifts.orders_from IS NULL OR orders.created_at >= gifts.orders_from')
                                ->whereRaw('gifts.orders_to IS NULL OR orders.created_at <= gifts.orders_to');
                        });

                    $q->where(function ($inner) use ($orderCountSubquery) {
                        $inner->whereNull('orders_min')
                            ->orWhereRaw('orders_min <= (' . $orderCountSubquery->toSql() . ')', $orderCountSubquery->getBindings());
                    })
                    ->where(function ($inner) use ($orderCountSubquery) {
                        $inner->whereNull('orders_max')
                            ->orWhereRaw('orders_max >= (' . $orderCountSubquery->toSql() . ')', $orderCountSubquery->getBindings());
                    });
                });
            })
            ->orderBy('status')
            ->first();
    }

    public function attachToUser($giftId, $userId)
    {
        $gift = Gift::findOrFail($giftId);
        $gift->users()->syncWithoutDetaching([$userId]);
        return $gift;
    }

    public function getMyMatchingGifts($user, $orderType = null)
    {
        $now            = Carbon::now();
        $userId         = $user->id;
        $userCreatedAt  = $user->created_at;

        return $user->gifts()->where(function ($query) use ($now) {
                $query->whereNull('from')->orWhere('from', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('to')->orWhere('to', '>=', $now);
            })
            ->when($orderType, function ($query) use ($orderType) {
                $query->where(function($q) use ($orderType){
                    $q->whereRaw("FIND_IN_SET(?, order_type)", [$orderType]);
                    $q->orWhereNull('order_type');
                });
            })
            ->when(!$orderType, function ($query) {
                $query->whereNull('order_type');
            })
            // Check registration date range
            ->where(function ($query) use ($userCreatedAt) {
                $query->whereNull('register_from')->orWhere('register_from', '<=', $userCreatedAt);
            })
            ->where(function ($query) use ($userCreatedAt) {
                $query->whereNull('register_to')->orWhere('register_to', '>=', $userCreatedAt);
            })
            // Check if coupon already used
            ->where(function ($query) use ($userId) {
                $query->whereNull('coupon_id')
                    ->orWhereNotExists(function ($subQuery) use ($userId) {
                        $subQuery->select(DB::raw(1))
                            ->from('orders')
                            ->where('client_id', $userId)
                            ->whereColumn('orders.coupon_id', 'gifts.coupon_id');
                    });
            })
            // Check order count criteria using a subquery
            ->where(function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $orderCountSubquery = DB::table('orders')
                        ->selectRaw('count(*)')
                        ->where('client_id', $userId)
                        ->where(function ($sub) {
                            $sub->whereRaw('gifts.orders_from IS NULL OR orders.created_at >= gifts.orders_from')
                                ->whereRaw('gifts.orders_to IS NULL OR orders.created_at <= gifts.orders_to');
                        });

                    $q->where(function ($inner) use ($orderCountSubquery) {
                        $inner->whereNull('orders_min')
                            ->orWhereRaw('orders_min <= (' . $orderCountSubquery->toSql() . ')', $orderCountSubquery->getBindings());
                    })
                    ->where(function ($inner) use ($orderCountSubquery) {
                        $inner->whereNull('orders_max')
                            ->orWhereRaw('orders_max >= (' . $orderCountSubquery->toSql() . ')', $orderCountSubquery->getBindings());
                    });
                });
            })
            ->orderBy('status')
            ->first();
    }
}


