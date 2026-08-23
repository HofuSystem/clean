<?php

namespace Core\Categories\DataResources\Api;

use Carbon\Carbon;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderRepresentative;
use Core\Settings\Services\SettingsService;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryTimeResource extends JsonResource
{
    public static $for;
    public static $preloadedCounts = null;
    
    /**
     * Static array to cache order counts grouped by type
     * Key format: "{date}_{from}_{to}" -> ['receiver' => count, 'delivery' => count, 'all' => count]
     */
    protected static $dateCounts = [];
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $isAvailable = true;
        $date = $this->date;
        $from = $this->from;
        $to = $this->to;
        $maxOrderCount = 0;
        $type = null;
        
      
        // Create base cache key (without type)
        $baseKey = "{$date}_{$from}_{$to}";
        
        // Get all counts grouped by type in a single query or from preloaded collection
        if (!isset(self::$dateCounts[$baseKey])) {
            if (self::$preloadedCounts !== null) {
                // Read from preloaded collection in memory
                $formattedFrom = Carbon::parse($from)->format('H:i:s');
                $formattedTo = Carbon::parse($to)->format('H:i:s');

                $matching = self::$preloadedCounts->filter(function ($item) use ($date, $formattedFrom, $formattedTo) {
                    return $item->date == $date
                        && $item->time <= $formattedFrom
                        && $item->to_time >= $formattedTo;
                });

                $receiverCount = (int) $matching->where('type', 'receiver')->sum('count');
                $deliveryCount = (int) $matching->where('type', 'delivery')->sum('count');

                self::$dateCounts[$baseKey] = [
                    'receiver' => $receiverCount,
                    'delivery' => $deliveryCount,
                    'all'      => $receiverCount + $deliveryCount,
                ];
            } else {
                // Fallback for isolated single calls
                $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
                
                $countsByType = OrderRepresentative::where('date', $date)
                    ->whereTime('time', '<=', $from)
                    ->whereTime('to_time', '>=', $to)
                    ->whereHas('order', function($orderQuery) use ($testAccounts) {
                        if (!empty($testAccounts)) {
                            $orderQuery->whereNotIn('client_id', $testAccounts);
                        }
                    })
                    ->selectRaw('type, COUNT(DISTINCT order_id) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray();

                self::$dateCounts[$baseKey] = [
                    'receiver' => $countsByType['receiver'] ?? 0,
                    'delivery' => $countsByType['delivery'] ?? 0,
                    'all' => ($countsByType['receiver'] ?? 0) + ($countsByType['delivery'] ?? 0),
                ];
            }
        }
         // Determine type based on self::$for
         if (self::$for == 'receiver') {
            $orderCount = self::$dateCounts[$baseKey]['receiver'] ?? 0;
            $maxOrderCount = $this->receiver_count;
        } elseif (self::$for == 'delivery') {
            $orderCount = self::$dateCounts[$baseKey]['delivery'] ?? 0;
            $maxOrderCount = $this->delivery_count;
        } else {
            $orderCount = self::$dateCounts[$baseKey]['all'] ?? 0;
            $maxOrderCount = $this->order_count;
        }
        

        if ($orderCount >= $maxOrderCount) {
            $isAvailable = false ;
        }

        $timeAgo = false;
        if (Carbon::parse($this->date)->isToday()) {
            $currentTime = Carbon::now()->format('H:i');
            if (Carbon::parse($this->to)->format('H:i') < $currentTime || 
                Carbon::parse($this->from)->format('H:i') < $currentTime) {
                $timeAgo = true;
            }
        }
        if($timeAgo){
            $isAvailable = false;
        }
        return [
            'id'            => $this->id,
            'from'          => Carbon::parse($this->from)->format('H:i'),
            'to'            => Carbon::parse($this->to)->format('H:i'),
            'isAvailable'   => $isAvailable,
            'timeAgo'       => $timeAgo,
        ];
    }
}
