<?php

namespace Core\Categories\Services;

use Carbon\Carbon;
use Core\Categories\DataResources\Api\CategoryTimeResource;
use Core\Comments\Services\CommentingService;
use Core\Categories\Models\CategoryDateTime;
use Core\Categories\DataResources\CategoryDateTimesResource;
use Core\Orders\Helpers\OrderHelper;
class CategoryDateTimesService
{
    public function __construct(protected CommentingService $commentingService) {}

    public function selectable(string $key, string $value)
    {
        $selected = ['id'];
        if (!in_array($key, [])) {
            $selected[] = $key;
        }
        if (!in_array($value, [])) {
            $selected[] = $value;
        }
        return CategoryDateTime::select($selected)->get();
    }

    public function update($type, $date,$categoryId,$cityId,$times,$newType,$newCategoryId,$newCityId,$newDate = null)
    {
        $record = $this->getForEditQuery($type, $date,$categoryId,$cityId)->get();
        foreach($record as $item){
            $item->delete();
        }
        foreach ($times as $time) {
            CategoryDateTime::create([
                'type'            => $newType,
                'category_id'     => $newCategoryId,
                'city_id'         => $newCityId,
                'date'            => $newDate ?? $date,
                'from'            => $time['from'],
                'to'              => $time['to'],
                'order_count'     => $time['receiver_count'] + $time['delivery_count'],
                'receiver_count'  => $time['receiver_count'],
                'delivery_count'  => $time['delivery_count'],
            ]);
        }
       
    }
    public function delete($type, $date,$categoryId,$cityId)
    {
        $record = $this->getForEditQuery($type, $date,$categoryId,$cityId)->get();
        foreach($record as $item){
            $item->delete();
        }
       
    }

    public function duplicate($type, $date, $categoryId, $cityId, $fromDate, $toDate)
    {
        // Get the original date times for the specified date
        $originalDateTimes = $this->getForEdit($type, $date, $categoryId, $cityId);
        
        if ($originalDateTimes->isEmpty()) {
            throw new \Exception(trans('No date times found for the specified date'));
        }

        // Convert dates to Carbon instances
        $startDate = Carbon::parse($fromDate);
        $endDate = Carbon::parse($toDate);
        $duplicatedCount = 0;

        // Iterate through each date in the range
        while ($startDate->lte($endDate)) {
            $currentDate = $startDate->format('Y-m-d');
            
            // Skip if it's the same as the original date
            if (!Carbon::parse($currentDate)->isSameDay(Carbon::parse($date))) {
                // Delete existing date times for this date if any
                CategoryDateTime::where('type', $type)
                    ->where('date', $currentDate)
                    ->where('category_id', $categoryId)
                    ->where('city_id', $cityId)
                    ->delete();
                
                // Duplicate each time slot to the new date
                foreach ($originalDateTimes as $original) {
                    CategoryDateTime::updateOrCreate([
                        'type'          => $type,
                        'category_id'   => $categoryId,
                        'city_id'       => $cityId,
                        'date'          => $currentDate,
                        'from'          => $original->from,
                        'to'            => $original->to,
                    ], [
                        'order_count'       => $original->order_count,
                        'receiver_count'    => $original->receiver_count,
                        'delivery_count'    => $original->delivery_count,
                    ]);
                }
                
                $duplicatedCount++;
            }
            
            // Move to next day
            $startDate->addDay();
        }

        return $duplicatedCount;
    }

    public function get(int $id)
    {
        return  CategoryDateTime::findOrFail($id);
    }

  
    public function dataTable($draw)
    {

        $recordsTotal       = CategoryDateTime::count();
        $recordsFiltered    = CategoryDateTime::search()->count();
        $records            = CategoryDateTime::select(['type', 'category_id', 'city_id', 'date'])
            ->groupBy('type','date','category_id','city_id')
            ->with(['category', 'city'])
            ->selectRaw('COUNT(*) as count')
            ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => CategoryDateTimesResource::collection($records)
        ];
    }

    public function order(array $list, $orderBy = 'order')
    {
        foreach ($list as  $value) {
            CategoryDateTime::find($value['id'])->update([$orderBy => $value['order']]);
        }
    }
    public function comment(int $id, string $content, int | null $parent_id)
    {
        return $this->commentingService->comment(
            CategoryDateTime::class,
            $id,
            $content,
            request()->user()->id,
            $parent_id
        );
    }
    public function totalCount()
    {
        return CategoryDateTime::count();
    }
    public function trashCount()
    {
        return CategoryDateTime::onlyTrashed()->count();
    }
    public function getForEditQuery($type, $date,$categoryId,$cityId)
    {
        return CategoryDateTime::where('type', $type)->where('date', $date)->where('category_id', $categoryId)->where('city_id', $cityId);
    }
    public function getForEdit($type, $date,$categoryId,$cityId)
    {
        return $this->getForEditQuery($type, $date,$categoryId,$cityId)->get();
    }
    function createDateTimes($type, $categoryId, $cityId, $dateFrom, $dateTo, $weekends, $offDates, $times)
    {
        $yesterday = Carbon::yesterday()->endOfDay(); // Get yesterday at 23:59:59
        CategoryDateTime::whereDate('date', '<=', $yesterday)->forceDelete();
        // Convert input dates to Carbon instances
        $startDate = Carbon::parse($dateFrom);
        $endDate = Carbon::parse($dateTo);

        // Convert weekends to lowercase for case-insensitive comparison
        $weekends = collect($weekends)->map(function ($day) {
            return strtolower($day);
        });

        // Convert off_dates to a collection of Carbon instances
        $offDates = collect($offDates)->map(function ($date) {
            return Carbon::parse($date);
        });

        // Initialize a collection to store the final date-time slots

        // Iterate through each date in the range
        while ($startDate->lte($endDate)) {
            // Check if the current date is a weekend
            $isWeekend = $weekends->contains(strtolower($startDate->englishDayOfWeek));

            // Check if the current date is in the off_dates collection
            $isOffDate = $offDates->contains(function ($offDate) use ($startDate) {
                return $offDate->isSameDay($startDate);
            });

            // If the date is not a weekend and not an off day, process it
            if (!$isWeekend && !$isOffDate) {
                // Loop through each time slot and assign it to the current date
                foreach ($times as $time) {
                    CategoryDateTime::updateOrCreate([
                        'type'          =>  $type,
                        'category_id'   =>  $categoryId,
                        'city_id'       =>  $cityId,
                        'date'          =>  $startDate->toDateString(),
                        'from'          =>  $time['from_time'],
                        'to'            =>  $time['to_time'],
                    ], [
                        'order_count'       =>  $time['receiver_count'] + $time['delivery_count'],
                        'receiver_count'    =>  $time['receiver_count'],
                        'delivery_count'    =>  $time['delivery_count'],
                    ]);
                }
            }

            // Move to the next day
            $startDate->addDay();
        }
    }
    public static function getDateTimes($type, $categoryIds, $address = null)
    {
        if (!isset($address)) {
            $address = auth('api')->user()?->profile;
        }
        $lastDate = Carbon::now()->addDays(14);
        $categoryDates = CategoryDateTime::where('date', '<=', $lastDate)
            ->where('type',OrderHelper::getOrderType($type) )
            ->where(function ($cityQuery) use ($address) {
                $cityQuery->where('city_id', $address->city_id)
                    ->orWhereNull('city_id');
            })
            ->when($categoryIds, function ($query) use ($categoryIds) {
                $query->when(is_array($categoryIds), function ($query) use ($categoryIds) {
                    $query->whereIn('category_id', $categoryIds);
                }, function ($query) use ($categoryIds) {
                    $query->where('category_id', $categoryIds);
                });
            })
            ->when(!isset($categoryIds), function ($query)  {
                $query->whereNull('category_id');
            })
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->get();
        if ($categoryDates->isEmpty()) {
            $categoryDates = CategoryDateTime::where('date', '<=', $lastDate)
                ->where('type', OrderHelper::getOrderType($type))
                ->where(function ($cityQuery) use ($address) {
                    $cityQuery->where('city_id', $address->city_id)
                        ->orWhereNull('city_id');
                })
                ->whereNull('category_id')
                ->where('date', '>=', now()->format('Y-m-d'))
                ->orderBy('date', 'asc')
                ->get();
        }
        return $categoryDates;
    }
    public static function getDateTimesFormatted($for = 'all', $categoryDates){
        $dates =  $categoryDates->pluck('date')->unique();
        $dateTimes  =   [];
        CategoryTimeResource::$for = $for;
        foreach ($dates as $date) {
            $times          = $categoryDates->sortBy('from')->filter(function ($item) use ($date) {
                return $item->date == $date;
            });
            $datetime    = [
                'day'   =>  strtolower(Carbon::parse($date)->format('l')),
                'date'  =>  Carbon::parse($date)->format('Y-m-d'),
                'times' =>  array_values(CategoryTimeResource::collection($times)->toArray(request())),
            ];
            $times = $datetime['times'] ?? [];
            $hasAvaliavle = false;
            foreach ($times as $time) {
                if($time['isAvailable']){
                    $hasAvaliavle = true;
                    break;
                }
            }
            if($hasAvaliavle){
                $dateTimes[] = $datetime;
            }
        }
        return $dateTimes;
    }

    public function restore(int $id){
        $record = CategoryDateTime::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
