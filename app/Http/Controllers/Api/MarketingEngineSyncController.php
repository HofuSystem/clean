<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Core\Users\Models\User;
use Core\Orders\Models\Order;
use Carbon\Carbon;

class MarketingEngineSyncController extends Controller
{
    public function sync(Request $request)
    {
        $since = $request->query('since');

        // Fetch customers (users)
        $usersQuery = User::query();
        if ($since) {
            $usersQuery->where('updated_at', '>=', Carbon::parse($since));
        }
        
        $users = $usersQuery->with(['profile.city', 'profile.district'])->get()->map(function ($user) {
            return [
                'external_id' => (string) $user->id,
                'fullName' => $user->fullname,
                'phone' => $user->phone,
                'email' => $user->email,
                'city' => $user->city_name,
                'district' => $user->district_name,
                'registrationDate' => $user->created_at ? $user->created_at->format('Y-m-d') : null,
            ];
        });

        // Fetch orders
        $ordersQuery = Order::query();
        if ($since) {
            $ordersQuery->where('updated_at', '>=', Carbon::parse($since));
        }
        
        $orders = $ordersQuery->with(['client', 'city', 'district', 'orderRepresentatives'])->get()->map(function ($order) {
            $receiver = $order->orderRepresentatives->where('type', 'receiver')->first();
            $delivery = $order->orderRepresentatives->where('type', 'delivery')->first();
            
            return [
                'orderNumber' => $order->reference_id ?? (string) $order->id,
                'customerPhone' => $order->client ? $order->client->phone : null,
                'createdAt' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
                'status' => $order->status,
                'orderTotal' => (float) $order->total_price,
                'paymentMethod' => $order->pay_type,
                'pickupDate' => $receiver ? $receiver->date : null,
                'deliveryDate' => $delivery ? $delivery->date : null,
                'city' => $order->city ? $order->city->name : null,
                'district' => $order->district ? $order->district->name : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'customers' => $users,
                'orders' => $orders,
            ]
        ]);
    }
}
