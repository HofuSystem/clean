<?php
 
namespace Core\B2B\DataResources;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Core\Admin\Helpers\DashboardDataTableFormatter;
class B2BOrderResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request): array
    {
        $receiver = $this->orderRepresentatives->where('type', 'receiver')->first();
        $delivery = $this->orderRepresentatives->where('type', 'delivery')->first();
        
        // Status color and text mapping
        $statusColor = 'amber-400';
        $statusText = __($this->status);
        if ($this->status == 'finished' || $this->status == 'delivered') {
            $statusColor = 'green-500';
            $statusText .= ' ✔️';
        } elseif ($this->status == 'canceled') {
            $statusColor = 'red-500';
        }

        // Type styling
        $isGuest = $this->b2b_type === 'client';
        $typeClass = $isGuest ? 'bg-purple-50 text-purple-700' : 'bg-blue-50 text-blue-700';
        $typeName = $isGuest ? trans('guest_order') : trans('contract_order');
        
        // Extract room number if guest
        if ($isGuest && $this->note) {
            if (preg_match('/Room: (\w+)/', $this->note, $matches)) {
                $typeName .= ' (' . $matches[1] . ')';
            }
        }

        $buildingIcon = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>';
        $userIcon = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>';
        $typeIcon = $isGuest ? $userIcon : $buildingIcon;

        return [
            'id' => $this->id,
            'reference_id' => $this->reference_id,
            'actions' => '
                <button onclick="viewOrderDetails(\'' . $this->reference_id . '\')" class="bg-white border border-gray-200 text-gray-600 hover:text-[#1c75bc] hover:border-[#1c75bc] px-5 py-2 rounded-xl text-xs font-black shadow-sm transition-all flex items-center gap-2">
                    ' . trans('order_details') . '
                    <svg class="w-3.5 h-3.5 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                </button>',
            'total' => '
                <span class="inline-block bg-white text-gray-900 px-4 py-1.5 rounded-xl font-black text-sm border border-gray-100 shadow-sm">
                    ' . number_format($this->total_price, 2) . ' ' . trans('client.SAR') . '
                </span>',
            'delivery_date' => $delivery ? '
                <div class="dir-dependent-text">
                    <div class="font-bold text-gray-800">' . $delivery->date . '</div>
                    <div class="text-xs text-gray-400 font-medium mt-1" dir="ltr">' . $delivery->time_12_hours_format . ' - ' . $delivery->to_time_12_hours_format . '</div>
                </div>' : '<div class="text-gray-400">---</div>',
            'pickup_date' => $receiver ? '
                <div class="dir-dependent-text">
                    <div class="font-bold text-gray-800">' . $receiver->date . '</div>
                    <div class="text-xs text-gray-400 font-medium mt-1" dir="ltr">' . $receiver->time_12_hours_format . ' - ' . $receiver->to_time_12_hours_format . '</div>
                </div>' : '<div class="text-gray-400">---</div>',
            'order_info' => '
                <div class="flex flex-col gap-1.5 dir-dependent-items">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-900 font-black font-mono text-base tracking-tight">' . $this->reference_id . '</span>
                        <span class="w-2 h-2 rounded-full shadow-sm bg-' . $statusColor . '"></span>
                    </div>
                    ' . ($this->branch ? '<div class="text-[10px] font-bold text-gray-500 mb-0.5"><strong>' . trans('branch') . ':</strong> ' . $this->branch->name . '</div>' : '') . '
                    <div class="flex items-center">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md ' . $typeClass . ' text-[10px] font-black uppercase tracking-wider dir-dependent-flex">
                            <span>' . $typeName . '</span> ' . $typeIcon . '
                        </span>
                        <span class="text-[10px] font-black text-' . explode('-', $statusColor)[0] . '-600 bg-' . explode('-', $statusColor)[0] . '-50 px-2 py-0.5 rounded border border-' . explode('-', $statusColor)[0] . '-100 mr-2 ml-2">' . $statusText . '</span>
                    </div>
                </div>'
        ];
    }
}
