<?php
 
namespace Core\Orders\DataResources\Api\Client\Order;
 
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class OrderTransactionsResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            
            "id"                    => $this->id,
            "type"                  => $this->type,
            "online_payment_method" => $this->online_payment_method,
            "amount"                => $this->amount,
            "transaction_id"        => $this->transaction_id,
           
        ];
    }
}
