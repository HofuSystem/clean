<?php

namespace Core\Orders\Services;

use Carbon\Carbon;
use Core\B2B\Models\CompanyBranch;
use Core\Categories\Models\Category;
use Core\Categories\Models\CategoryDateTime;
use Core\Categories\Models\CategoryOffer;
use Core\Categories\Models\CategorySetting;
use Core\Categories\Models\CategoryType;
use Core\Comments\Services\CommentingService;
use Core\Coupons\Models\ClientCoupon;
use Core\Coupons\Models\Coupon;
use Core\Coupons\Services\CouponsService;
use Core\Orders\Models\Order;
use Core\Orders\Models\OrderAnalyticsContext;
use Core\Orders\DataResources\OrdersResource;
use Core\Orders\Models\DeliveryPrice;
use Core\Orders\Models\OrderItem;
use Core\Orders\Models\OrderReport;
use Core\Orders\Models\OrderRepresentative;
use Core\Products\Models\Product;
use Core\Products\Models\ProductSetting;
use Core\Settings\Helpers\ToolHelper;
use Core\Settings\Models\Setting;
use Core\Settings\Services\SettingsService;
use Core\Users\Models\Address;
use Core\Users\Models\Point;
use Core\Wallet\Models\WalletTransaction;
use RuntimeException;
use Core\Notification\Services\TelegramNotificationService;
use Core\Orders\Models\Cart;
use Core\Orders\Models\OrderTransaction;
use Core\Orders\Support\OrderPaymentMath;
use Core\Products\Services\ProductsService;
use Core\B2B\Models\Contract;
use Core\PaymentGateways\Services\MyFatoorahService;
use Illuminate\Validation\ValidationException;
use Core\Financials\Services\InvoiceService;
use Illuminate\Support\Facades\DB;

class OrdersService
{
    public function __construct(
        protected CommentingService $commentingService,
        protected OrderItemsService $orderItemsService,
        protected OrderRepresentativesService $orderRepresentativesService,
        protected OrderReportsService $orderReportsService,
        protected OrderTypesOfDatasService $orderTypesOfDatasService,
        protected TelegramNotificationService $telegramNotificationService,
        protected MyFatoorahService $myfatoorahService,
        protected OrderHistoryService $orderHistoryService,
        protected InvoiceService $invoiceService

    ) {}

    public function selectable(string $key, string $value)
    {
        $selected = ['id'];
        if (!in_array($key, [])) {
            $selected[] = $key;
        }
        if (!in_array($value, [])) {
            $selected[] = $value;
        }
        return Order::select($selected)->get();
    }
    public function storeOrUpdate(array $data = [], $id = null)
    {
        $recordData = array_filter($data, fn($key) => in_array($key, ['reference_id', 'type', 'status', 'client_id', 'company_id', 'branch_id', 'b2b_type', 'pay_type', 'transaction_id', 'order_status_times', 'days_per_week', 'days_per_week_names', 'days_per_month_dates', 'note', 'coupon_id', 'coupon_data', 'order_price', 'delivery_price', 'total_price', 'paid', 'is_admin_accepted', 'admin_cancel_reason', 'wallet_used', 'wallet_amount_used', 'translations', 'perfume', 'starch_level']), ARRAY_FILTER_USE_KEY);
        $record     = Order::updateOrCreate(['id' => $id], $recordData);

        if (!isset($id)) {
            //saving on create the related orderItemsItems
            $orderItemsItems            = $data['items'] ?? [];
            foreach ($orderItemsItems as $index => $itemValues) {
                $itemValues['order_id'] = $record->id;
                $this->orderItemsService->storeOrUpdate($itemValues, $itemValues['id'] ?? null);
            }
            //saving on create the related orderRepresentativesItems
            $orderRepresentativesItems            = $data['representatives'] ?? [];
            foreach ($orderRepresentativesItems as $index => $itemValues) {
                $itemValues['order_id'] = $record->id;
                $this->orderRepresentativesService->storeOrUpdate($itemValues, $itemValues['id'] ?? null);
            }
            //saving on create the related orderReportsItems
            $orderReportsItems            = $data['reports'] ?? [];
            foreach ($orderReportsItems as $index => $itemValues) {
                $itemValues['order_id'] = $record->id;
                $this->orderReportsService->storeOrUpdate($itemValues, $itemValues['id'] ?? null);
            }
            //saving on create the related orderTypesOfDatasItems
            $orderTypesOfDatasItems            = $data['more_data'] ?? [];
            foreach ($orderTypesOfDatasItems as $index => $itemValues) {
                $itemValues['order_id'] = $record->id;
                $this->orderTypesOfDatasService->storeOrUpdate($itemValues, $itemValues['id'] ?? null);
            }
        }
        $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
        if ($record->wasRecentlyCreated && !in_array($record->client_id, $testAccounts)) {
            $this->telegramNotificationService->sendMessage("@cleanstationneworders", $this->telegramNotificationService->formatNewOrderMessage($record));
        }
        return $record;
    }
    public function storeOrder($type, $clientId, $orderData, $items, $couponId, $walletUsed)
    {

        $referenceId = 'O';
        if ($type == 'clothes') {
            $referenceId = 'L';
        } else if ($type == 'services') {
            $referenceId = 'S';
        }
        $referenceId .= rand(100000000, 999999999);

        $orderData     = array_merge($orderData, [
            'order_price'        => 0,
            'wallet_used'        => $walletUsed,
            'status'             => 'pending',
            'order_status_times' => ['pending' => date("Y-m-d H:i")],
            'is_admin_accepted'  => true,
            'reference_id'       => $referenceId,
            'client_id'          => $clientId,
        ]);
        $order = Order::create($orderData);
        foreach ($items as $item) {
            $this->addItem($order->id, $item['product_id'], $item['quantity'], $item['height'] ?? null, $item['width'] ?? null);
        }
        $productIds    = $order->items->pluck('product_id')->toArray();
        $subTotal      = $order->items->sum('total_price');
        $freeDeliveryMin = SettingsService::getDataBaseSetting('free_delivery_min');

        if (!($freeDeliveryMin and $subTotal >= $freeDeliveryMin)) {
            $categoriesIds    = [];
            foreach ($order->items as $item) {
                $categoriesIds[] = $item->product?->category_id;
                $categoriesIds[] = $item->product?->sub_category_id;
            }
            $deliveryCharge   =  DeliveryPrice::whereIn('category_id', $categoriesIds)
                ->orWhere(function ($areaQuery) use ($order) {
                    $areaQuery->where('city_id', $order->city_id)->where('district_id', $order->district_id);
                })->orderByDesc('price')->first()?->price;
            if (!isset($deliveryCharge)) {
                $deliveryCharge =  SettingsService::getDataBaseSetting('delivery_charge');
            }
            $order->update(['delivery_price' => $deliveryCharge]);
        }
        if ($couponId) {
            $coupon = Coupon::findMatching(applying: "manual", userId: $clientId, orderType: $type, productsIds: $productIds, orderValue: $subTotal)->where('id', $couponId)->exists();
            if (!$coupon) {
                app(AnalyticsOutboxService::class)->recordUserEvent(
                    'coupon_rejected',
                    (int) $clientId,
                    is_array($orderData['analytics_context'] ?? null) ? $orderData['analytics_context'] : [],
                    ['reason_category' => 'not_matching'],
                    'coupon-rejected:' . (string) \Illuminate\Support\Str::uuid()
                );
                throw new RuntimeException(trans('the selected Coupon is not matching'));
            }
        } else {
            $coupon = Coupon::findMatching(applying: "auto", userId: $clientId, orderType: $type, productsIds: $productIds, orderValue: $subTotal)->where('id', $couponId)->exists();
        }
        $order = $this->reCalcOrder($order->id);
        if ($order->pay_type == 'card') {
            $order->update([
                'paid' => $order->total_price
            ]);
        } else {
            if ($walletUsed) {
                $client     = $order->client;
                $usedAmount = 0;
                if ($client->wallet) {
                    if ($client->wallet >= $order->total_price) {
                        $usedAmount = $order->total_price;
                        $order->update([
                            'pay_type' => 'wallet'
                        ]);
                    } else {
                        $usedAmount = $client->wallet;
                    }
                    $order->update([
                        'paid' => $usedAmount,
                        'wallet_amount_used' => $usedAmount,
                    ]);
                    $this->createWalletOrderPaymentTransactions(
                        $client,
                        $order,
                        (float) $usedAmount,
                        'pay order wallet payment for order : ' . $order->reference_id
                    );
                }
            }
            $order = $this->reCalcOrder($order->id);
        }
        $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
        if (!in_array($clientId, $testAccounts)) {
            $this->telegramNotificationService->sendMessage("@cleanstationneworders", $this->telegramNotificationService->formatNewOrderMessage($order));
        }
        if ($order->status !== 'pending_payment') {
            app(AnalyticsOutboxService::class)->record('order_created', $order);
        }
        if ($order->coupon_id) {
            app(AnalyticsOutboxService::class)->record('coupon_accepted', $order, [
                'coupon_type' => (string) ($order->coupon?->type ?? 'unknown'),
                'discount_amount' => round((float) ($order->total_coupon ?? 0), 2),
            ], 'coupon-accepted');
        }
        return $order;
    }

    public static function issues()
    {
        return [
            'requested by customer' => 'requested by customer',
            'customer failed to collect / receive' => 'customer failed to collect / receive',
            'reorder' => 'reorder',
            'managements test' => 'mangements test',
            'out of delivery area' => 'out of delivery area',
            'minimum order condition was unfulfilled' => 'minimum order condition was unfulfilled',
            'missing / incoorect customer info' => 'missing / incoorect customer info',
            'tech issue' => 'tech issue',
            'delay' => 'delay',
            'delay during delivery' => 'delay during delivery',
            'driver unable to locate address' => 'driver unable to locate address',
            'fraudulent order' => 'fraudulent order',
            'other' => 'other',
        ];
    }
    /* public function changeStatus(int $orderId, string $status, $notes = null)
   {
      $order = Order::findOrFail($orderId);
      if ($status == 'accept') {
         $order->update(['is_admin_accepted' => true]);
      } else {
         $order->update(['status' => $status, 'order_status_times' => [$status => [date("Y-m-d H:i"), auth()->user()->email, $notes]], 'admin_cancel_reason' => request()->admin_cancel_reason]);
      }
   } */

    public function changeStatus(int $orderId, string $status, $notes = null)
    {
        $order = Order::findOrFail($orderId);
        $client = $order->client;

        if ($status === 'accept') {
            $order->update([
                'is_admin_accepted' => true
            ]);
            return;
        }
        $ogStatus = $order->status;
        if ($status === 'finished') {
            $this->completeOrderFinancials($order->id);
            $order->refresh();
        }
        $order->update([
            'status' => $status,
            'order_status_times' => [
                $status => [now()->format('Y-m-d H:i'), auth()->user()->email, $notes ??  request()->admin_cancel_reason]
            ],
            'admin_cancel_reason' => request()->admin_cancel_reason
        ]);
        
 
        if ($status === 'canceled' && $client && !in_array($ogStatus, ['pending_payment', 'cancel_payment','failed_payment'])) {
            $paidWithWallet = $order->wallet_amount_used;
            $paidInPoints   = $order->points_amount_used;
            $paidInCard     = $order->card_amount_used;
            if ($order->pay_type === 'card' && $paidInCard > 0) {
                $this->refundToWallet(
                    user: $client,
                    amount: $paidInCard,
                    notes: 'Order used electronic payment, value returned to wallet due to cancellation',
                    transaction_type: 'remaining_amount',
                    order: $order
                );
            }

            if ($order->wallet_used && $paidWithWallet > 0) {
                $this->refundToWallet(
                    user: $client,
                    amount: $paidWithWallet,
                    notes: 'Order used wallet, value returned due to cancellation',
                    transaction_type: 'remaining_amount',
                    order: $order
                );
            }

            if ($order->points_used && $order->points_amount > 0) {
                Point::create([
                    'title' => 'Points returned due to order cancellation' . $orderId,
                    'amount' => $order->points_amount,
                    'operation' => 'deposit',
                    'user_id' => $order->client_id,
                    'order_id' => $order->id,
                ]);
                 //add order transaction 
                $order->transactions()->create([
                    'type'                  => 'points',
                    'amount'                => -$paidInPoints,
                    'notes'                 => 'Points returned due to order cancellation ' . $order->reference_id."total points: ".$order->points_amount,
                ]);
            }
        }
    }

    /**
     * Apply completion money movements exactly once for every completion path.
     * Locking the order serializes simultaneous admin/driver/technician requests.
     */
    public function completeOrderFinancials(int $orderId): void
    {
        DB::transaction(function () use ($orderId) {
            $order = Order::query()->lockForUpdate()->findOrFail($orderId);
            $this->applyCompletionEffects($order);

            $overpayment = OrderPaymentMath::remainingForCustomer($order->total_price, $order->paid);
            if (OrderPaymentMath::isCollectable($overpayment)
                && ! WalletTransaction::query()->where('order_id', $order->id)
                    ->where('transaction_type', 'remaining_amount')->exists()) {
                $wallet = WalletTransaction::create([
                    'title' => 'remaining amount for order : ' . $order->reference_id,
                    'amount' => $overpayment, 'type' => 'deposit', 'status' => 'accepted',
                    'transaction_type' => 'remaining_amount', 'wallet_bucket' => 'funded',
                    'user_id' => $order->client_id, 'order_id' => $order->id,
                ]);
                $order->transactions()->create([
                    'type' => 'wallet', 'amount' => -$overpayment,
                    'wallet_transaction_id' => $wallet->id,
                    'notes' => 'remaining amount for order : ' . $order->reference_id,
                ]);
            }
        });
    }

    /** Apply the same financial effects for every actor that finishes an order. */
    private function applyCompletionEffects(Order $order): void
    {
        $order->items()->update(['is_delivered' => true]);

        $cash = OrderPaymentMath::remainingToCollect($order->total_price, $order->paid);
        if (OrderPaymentMath::isCollectable($cash)
            && ! $order->transactions()->where('type', 'cash')->exists()) {
            $order->transactions()->create([
                'type' => 'cash', 'amount' => $cash,
                'notes' => 'cash amount for order : ' . $order->reference_id,
            ]);
        }

        if ((float) $order->cashback > 0
            && ! WalletTransaction::query()->where('order_id', $order->id)->where('transaction_type', 'cashback')->exists()) {
            WalletTransaction::create([
                'title' => 'cashback : ' . $order->client?->fullname,
                'amount' => round((float) $order->total_price * (float) $order->cashback / 100, 2),
                'type' => 'deposit', 'status' => 'accepted', 'transaction_type' => 'cashback',
                'wallet_bucket' => 'promotional', 'user_id' => $order->client_id,
                'expired_at' => now()->addDays(3), 'order_id' => $order->id,
            ]);
        }

        $firstOrderId = Order::query()->where('client_id', $order->client_id)
            ->orderBy('created_at')->orderBy('id')->value('id');
        $referrer = $order->client?->registerBy;
        if ($firstOrderId === $order->id && $referrer) {
            $referralPoints = (int) (SettingsService::getDataBaseSetting('referral_points') ?? 0);
            if ($referralPoints > 0 && ! Point::query()->where('order_id', $order->id)->where('user_id', $referrer->id)->exists()) {
                Point::create([
                    'title' => 'referral gift points for user : ' . $order->client->fullname,
                    'amount' => $referralPoints, 'operation' => 'deposit',
                    'user_id' => $referrer->id, 'order_id' => $order->id,
                ]);
                $referrer->increment('earned_referral_points', $referralPoints);
            }

            $referralRiyals = round((float) (SettingsService::getDataBaseSetting('referral_riyals') ?? 0), 2);
            if ($referralRiyals > 0 && ! WalletTransaction::query()->where('order_id', $order->id)
                ->where('user_id', $referrer->id)->where('transaction_type', 'promotional_add')->exists()) {
                WalletTransaction::create([
                    'title' => 'referral gift riyals for user : ' . $order->client->fullname,
                    'amount' => $referralRiyals, 'type' => 'deposit', 'status' => 'accepted',
                    'user_id' => $referrer->id, 'wallet_bucket' => 'promotional',
                    'transaction_type' => 'promotional_add', 'order_id' => $order->id,
                ]);
                $referrer->increment('earned_referral_riyals', $referralRiyals);
            }
        }
    }

    private function refundToWallet($user, float $amount, string $notes, string $transaction_type,$order)
    {
        $transaction = WalletTransaction::create([
            'type'          => 'deposit',
            'amount'        => $amount,
            'order_id'      => $order->id,
            'status'        => 'accepted',
            'user_id'       => $user->id,
            'added_by_id'   => auth()->id(),
            'notes'          => $notes,
            'transaction_type' => $transaction_type
        ]);
        //add order transaction 
        $order->transactions()->create([
            'type'                  => 'wallet',
            'amount'                => -$amount,
            'wallet_transaction_id' => $transaction->id,
            'notes'                 => $notes,
        ]);
    }

    public function returnOrderContinue(int $orderId)
    {
        $order = Order::findOrFail($orderId);
        if ($order->status === 'issue' && $order->status_before_issue) {
            $previousStatus = $order->status_before_issue;
            $order->update([
                'status' => $previousStatus,
                'status_before_issue' => null,
                'driver_has_problem' => false,
                'order_status_times' => [
                    $previousStatus => [now()->format('Y-m-d H:i'), auth()->user()?->email],
                ],
            ]);
        }
        OrderReport::where('order_id', $order->id)->forceDelete();
    }
    public function getDateTimes(array $productIds)
    {
        $categoryDataTimes = CategoryDateTime::whereHas('category', function ($categoryQuery) use ($productIds) {
            $categoryQuery
                ->whereHas('products', function ($productQuery) use ($productIds) {
                    $productQuery->whereIn('id', $productIds);
                })
                ->orWhereHas('productsSub', function ($productQuery) use ($productIds) {
                    $productQuery->whereIn('id', $productIds);
                });
        })
            ->whereDate('date', '>=', Carbon::now())
            ->get();
        $dateTimes         = [];
        foreach ($categoryDataTimes as $categoryDataTime) {
            if (!isset($dateTimes[$categoryDataTime->date])) {
                $dateTimes[$categoryDataTime->date]  = [];
            }
            $dateTimes[$categoryDataTime->date][] = [
                "key"    => $categoryDataTime->from . "-" . $categoryDataTime->to,
                "value"  => Carbon::parse($categoryDataTime->from)->format('h:i A') . '-' . Carbon::parse($categoryDataTime->to)->format('h:i A')
            ];
        }
        return $dateTimes;
    }
    public function update($orderId, $items)
    {
        foreach ($items as $item) {
            $this->addItem($orderId, $item['id'], $item['quantity'], $item['height'] ?? null, $item['width'] ?? null);
        }
    }
    public function addItem($orderId, $productId, $quantity, $height = null, $width = null)
    {
        $order                  = Order::where('id', $orderId)->firstOrFail();
        $product                = Product::findOrFail($productId);
        $orderItem              = OrderItem::where('order_id', $orderId)
            ->where('product_id', $productId)
            ->where('width', $width)
            ->where('height', $height)
            ->first();

        $orderItemFromTrash    = OrderItem::onlyTrashed()->where('order_id', $orderId)
            ->where('product_id', $productId)
            ->where('width', $width)
            ->where('height', $height)
            ->first();
        ProductsService::setCurrentContract($order->company);
        $productData = ProductsService::getProductData($order->company,$order->b2b_type,$order->client?->profile?->city_id,$product);
        $cost          = $productData['cost'];
        $price         = $productData['price'];
        if ($orderItem) {
            $orderItem->qtyUpdates()->create([
                'from'            => $orderItem->quantity,
                'to'              => ($orderItem->quantity + $quantity),
                'updater_email'   => auth()->user()->email,
                'updater_id'      => auth()->user()->id,
            ]);
            $orderItem->increment('quantity', $quantity);

            $orderItem->update([
                'width'              => $width,
                'height'             => $height,
                'update_by_admin'    =>  auth()->user()->email,
                'product_price'      =>  $price,
                'product_cost'       =>  $cost,
                'product_data'       =>  $product->toJson(),
                'wash_type'          =>  $product->wash_type,
            ]);
        } elseif ($orderItemFromTrash) {
            $orderItem = $orderItemFromTrash;
            $orderItemFromTrash->qtyUpdates()->create([
                'from'            => $orderItemFromTrash->quantity,
                'to'              => $quantity,
                'updater_email'   => auth()->user()->email,
                'updater_id'      => auth()->user()->id,
            ]);

            $orderItemFromTrash->update([
                'deleted_at'            => null,
                'final_delete'          => false,
                'quantity'              => $quantity,
                'width'                 => $width,
                'height'                => $height,
                'update_by_admin'       =>  auth()->user()->email,
                'product_price'         =>  $price,
                'product_cost'          =>  $cost,
                'product_data'          =>  $product->toJson(),
                'wash_type'             =>  $product->wash_type,
            ]);
        } else {

            $orderItem = OrderItem::create([
                'order_id'                  =>   $order->id,
                'product_id'                =>   $product->id,
                'product_price'             =>   $price,
                'product_cost'             =>    $cost,
                'quantity'                  =>   $quantity,
                'width'                     =>   $width,
                'height'                    =>   $height,
                'product_data'              =>   $product->toJson(),
                'add_by_admin'              =>   auth()->user()->email,
                'wash_type'                 =>   $product->wash_type,
            ]);
        }
        if (($quantity != 0) and  json_decode($orderItem->product_data)->type == 'sales') {
            $product = Product::where('id', json_decode($orderItem->product_data)->id)->first();
            $product->update(['quantity' => $product->quantity - $quantity]);
        }
        $this->reCalcOrder($orderId);
    }
    public function updateItem($orderId, $itemId, $quantity, $height, $width, $wash_type = null)
    {
        $orderItem    = OrderItem::withTrashed()->where('order_id', $orderId)->where('id', $itemId)->firstOrFail();
        $product      = $orderItem->product;
        if ($quantity == $orderItem->quantity and $height == $orderItem->height and $width == $orderItem->width and ($wash_type === null || $wash_type == $orderItem->wash_type)) {
            return;
        }
        if ($quantity < 1) {
            return $this->destroyItem($orderItem->id);
        }
        $quantityDiff =  ($orderItem->quantity - $quantity);
        if ($quantity != $orderItem->quantity) {
            $orderItem->qtyUpdates()->create([
                'from'            => $orderItem->quantity,
                'to'              => $quantity,
                'updater_email'   => auth()->user()->email,
                'updater_id'      => auth()->user()->id,
            ]);
        }

        $updateData = [
            'width'                 => $width,
            'height'                => $height,
            'quantity'              => $quantity,
            'update_by_admin'       => auth()->user()->email,
            'deleted_at'            => null,
            'final_delete'          => false,
        ];
        if ($wash_type !== null) {
            $updateData['wash_type'] = $wash_type;
        }
        $orderItem->update($updateData);
        if (($quantityDiff != 0) and  json_decode($orderItem->product_data)->type == 'sales') {
            $product = Product::where('id', json_decode($orderItem->product_data)->id)->first();
            $product->update(['quantity' => $product->quantity - $quantityDiff]);
        }
        $this->reCalcOrder($orderId);
    }

    public function destroyItem($itemId)
    {
        $item    = OrderItem::withTrashed()->where('id', $itemId)->firstOrFail();
        $product = Product::where('id', json_decode($item->product_data)->id)->first();
        if (json_decode($item->product_data)->type == 'sales') {
            $product->update(['quantity' => $product->quantity + $item['quantity']]);
        }
        $isFinal = request()->final ?? false;
        if ($item->quantity > 0) {
            $item->qtyUpdates()->create([
                'from'            => $item->quantity,
                'to'              => 0,
                'updater_email'   => auth()->user()->email,
                'updater_id'      => auth()->user()->id,
            ]);
            $item->update([
                'quantity'              => 0,
                'update_by_admin'       =>  auth()->user()->email,
            ]);
        }
        if ($isFinal) {
            $item->update([
                'final_delete' => true,
            ]);
            $item->delete();
        } else {
            $item->delete();
        }
        $this->reCalcOrder($item->order_id);
    }
    public function reCalcOrder($id)
    {
        $order         = Order::findOrFail($id);
        $subTotal      = $order->items->sum('total_price');
        $costTotal     = $order->items->sum('total_cost');
        $couponPresent = 0;
        $couponValue   = 0;
        $couponData    = json_decode($order->coupon_data);
        $coupon        = $order->coupon;
        $deliveryPrice = floatval($order->delivery_price);
        if (!isset($couponData) and isset($coupon)) {
            $order->update(['coupon_data' => $coupon->toJson()]);
        }
        $cashBack = null;
        if (isset($couponData) or isset($coupon)) {
            $couponType       =  $couponData?->type ?? $coupon?->type;
            $couponMin        =  $couponData?->order_minimum ?? $coupon?->order_minimum ?? 0;
            if (in_array($couponType, ['percentage', 'value','cashback']) and $subTotal >= $couponMin) {
                $couponValue    = $couponData->discount_percentage ?? $couponData->value ?? $coupon->value;
                $couponPresent  = $couponData->discount_percentage ?? $couponData->value ?? $coupon->value;
                if ($couponType == 'percentage') {
                    $couponValue   = $subTotal * $couponPresent / 100;
                }
                if ($couponType == 'cashback') {
                    $cashBack         = $couponValue;
                    $couponValue      = 0;
                }
            }
        }
        $deliveryCharge = SettingsService::getDataBaseSetting('delivery_charge');
        $freeDelivery = SettingsService::getDataBaseSetting('free_delivery');
        if (($subTotal - $couponValue) >= $freeDelivery || $order->b2b_type == 'company') {
            $deliveryPrice = 0;
        } elseif ($deliveryPrice == 0) {
            $deliveryPrice = $deliveryCharge;
        }
        $total = $subTotal + $deliveryPrice - $couponValue;
      
        
        $washTypes     = $order->items->pluck('wash_type')->unique()->filter()->toArray();
        $wash_type    = null;
        if (count($washTypes) > 1) {
            $wash_type = 'mixed';
        } elseif (count($washTypes) == 1) {
            $wash_type = reset($washTypes);
        }

        $order->update([
            'wash_type'             => $wash_type,
            'order_price'           => $subTotal,
            'cashback'              => $cashBack,
            'total_coupon'          => $couponValue,
            'total_price'           => $total,
            'delivery_price'        => $deliveryPrice,
        ]);
        $this->calcOriginalProductsTotal($order->id);
        return $order;
    }
    public function calcOriginalProductsTotal($orderId)
    {
        $order = Order::findOrFail($orderId);
        $items = OrderItem::where('order_id', $orderId)->with('product')->get();
        $originalProductsTotal = 0;
        foreach ($items as $item) {
            $product = isset($item->product) ? $item->product : (isset($item->product_data) ? json_decode($item->product_data) : null);
            $prices = ProductsService::getProductOutOfContractPriceData($product, $order->city_id);
            $originalProductsTotal += $prices['price'] * $item->quantity;
        }
        $b2bPofit = $order->order_price - $originalProductsTotal;
       $order->update(['original_products_total' => $originalProductsTotal,'b2b_profit' => $b2bPofit]);
        return $order;
    }
    public function get($id)
    {
        return Order::with([
            'client.profile.city.translations',
            'client.profile.district.translations',
            'company',
            'branch',
            'moreDatas',
            'transactions',
            'followUp.admin',
            'items' => fn($q) => $q->withTrashed()->with([
                'product.translations',
                'product.category.translations',
                'product.subCategory.translations',
                'qtyUpdates'
            ]),
            'orderRepresentatives.representative',
            'orderRepresentatives.order',
            'orderRepresentatives.items.product.translations',
            'orderRepresentatives.address',
            'operator',
            'comments',
            'reports.reportReason.translations',
            'city.translations',
            'district.translations',
            'coupon',
            'electronicInvoice'
        ])->where(function($q) use ($id) {
            $q->where('id', $id)->orWhere('reference_id', $id);
        })->firstOrFail();
    }

    public function delete(int $id, $final = false)
    {
        $record             = Order::findOrFail($id);
        if ($final) {
            $record->forceDelete();
        } else {
            $record->delete();
        }
        return true;
    }
    public function assignRepresentatives(string $type, int $repId, array $orderIds)
    {
        foreach ($orderIds as $orderId) {
            $assignment = OrderRepresentative::query()->where([
                'type'      => $type,
                'order_id'  => $orderId,
            ])->first();
            if ($assignment) {
                $assignment->update(['representative_id' => $repId]);
            } else {
                $assignment = OrderRepresentative::create([
                    'type' => $type,
                    'order_id' => $orderId,
                    'representative_id' => $repId,
                ]);
            }
            $order  = Order::where('id', $orderId)->first();
            if($order?->status == "pending" and $order->type == 'sales' and $type == 'delivery'){
                $order->update([
                    'status'    => 'order_has_been_delivered_to_admin',
                ]);
           }
        }
    }
    public function assignOperators(int $operatorId, array $orderIds)
    {
        foreach ($orderIds as $orderId) {
            Order::where('id', $orderId)->first()?->update([
                'operator_id' => $operatorId,
            ]);
        }
    }
    public function applyCoupon(int $couponId, array $orderIds)
    {
        $coupon = Coupon::find($couponId);
        foreach ($orderIds as $orderId) {
            $order          = Order::where('id', $orderId)->first();
            $oldCoupon      = $order->coupon;
            $oldCouponValue = $order->total_coupon;
            $newCouponValue = 0;
            if ($coupon->type == 'cashback') {
                $newCouponValue = 0;
            } elseif ($coupon->type == 'percentage') {
                $newCouponValue = $order->order_price * $coupon->value / 100;
                if ($coupon->max_value > 0 and $newCouponValue > $coupon->max_value) {
                    $newCouponValue = $coupon->max_value;
                }
            } else {
                $newCouponValue = $coupon->value;
            }

            $orderTotalPrice  = $order->total_price + $oldCouponValue - $newCouponValue;
            $order->update([
                'coupon_id'    => $couponId,
                'coupon_data'  => $coupon->toJson(),
                'total_coupon' => $newCouponValue,
                'total_price'  => $orderTotalPrice,
            ]);
            app(AnalyticsOutboxService::class)->record('coupon_accepted', $order->fresh(), [
                'coupon_type' => (string) ($coupon->type ?? 'unknown'),
                'discount_amount' => round((float) $newCouponValue, 2),
            ], 'coupon-applied:' . (string) \Illuminate\Support\Str::uuid());
            
            // Log coupon change
            $this->orderHistoryService->logCouponChange($orderId, $oldCoupon, $coupon);
        }
    }
    public function updateDeliveryPrice(array $orderIds, $price)
    {
        foreach ($orderIds as $orderId) {
            $order = Order::where('id', $orderId)->first();
            if ($order) {
                $oldDeliveryPrice = $order->delivery_price;
                $deliveryDifference = $order->delivery_price - $price;
                $order->update([
                    'delivery_price'    => $price,
                    'total_price'       => $order->total_price - $deliveryDifference,
                ]);
                
               
            }
        }
    }

    public function updateCost(array $orderIds, $washerCost, $labCost)
    {
        foreach ($orderIds as $orderId) {
            $order = Order::where('id', $orderId)->first();
            if ($order) {
                $order->update([
                    'washer_cost' => $washerCost ?? 0,
                    'lab_cost'    => $labCost ?? 0,
                ]);
            }
        }
    }

    public function updateB2bFinancialNote(array $orderIds, $note)
    {
        Order::whereIn('id', $orderIds)->update(['b2b_financial_note' => $note]);
    }

    public function changePayType(string $payType, array $orderIds)
    {
        $orders = Order::whereIn('id', $orderIds)->get();
        foreach ($orders as $order) {
            $oldPayType = $order->pay_type;
            $order->update([
                'pay_type' => $payType,
            ]);
            
            
        }
    }

    public function dataTable($draw)
    {

        $recordsTotal       = Order::count();
        $recordsFiltered    = Order::search()->count();
        $records            = Order::select(['id', 'reference_id', 'type', 'status', 'client_id', 'company_id', 'branch_id', 'b2b_type', 'operator_id', 'pay_type', 'note', 'coupon_id', 'total_price','total_provider_invoice', 'paid', 'is_admin_accepted', 'admin_cancel_reason', 'wallet_used', 'wallet_amount_used', 'online_payment_method', 'city_id', 'district_id', 'created_at', 'updated_at'])
            ->with(['client.profile.city.translations', 'client.profile.district.translations', 'company', 'branch', 'operator', 'coupon', 'city.translations', 'district.translations', 'orderRepresentatives', 'electronicInvoice'])
            ->search()->dataTable()->get();

        return [
            'draw'              => $draw,
            'recordsTotal'      => $recordsTotal,
            'recordsFiltered'   => $recordsFiltered,
            'data'              => OrdersResource::collection($records)
        ];
    }

    public function order(array $list, $orderBy = 'order')
    {
        foreach ($list as  $value) {
            Order::find($value['id'])->update([$orderBy => $value['order']]);
        }
    }
    public function import(array $items)
    {
        foreach ($items as  $index => $item) {
            $items[$index] = $this->storeOrUpdate($item, $item['id'] ?? null);
        }
        return $items;
    }
    public function comment(int $id, string $content, int | null $parent_id)
    {
        return $this->commentingService->comment(
            Order::class,
            $id,
            $content,
            request()->user()->id,
            $parent_id
        );
    }
    public function totalCount()
    {
        return Order::underMyControl()->count();
    }
    public function trashCount()
    {
        return Order::underMyControl()->onlyTrashed()->count();
    }
    //api function
    public function createOrder($data, $products = [], $user = null)
    {
        // Keep analytics metadata out of the financial/order attribute mass assignment.
        // This field is optional so app versions that predate schema v2 remain compatible.
        $analyticsContext = $data['analytics_context'] ?? null;
        unset($data['analytics_context']);

        if ($data['type'] == 'clothes' || $data['type'] == 'fastorder') {
            $reference_id = 'L' . rand(100000000, 999999999);
        } else if ($data['type'] == 'services') {
            $reference_id = 'S' . rand(100000000, 999999999);
        } else if ($data['type'] == 'host' || $data['type'] == 'care' || $data['type'] == 'selfcare') {
            $reference_id = 'H' . rand(100000000, 999999999);
        } else if ($data['type'] == 'maidflex' || $data['type'] == 'maidscheduled' || $data['type'] == 'maidPackage' || $data['type'] == 'maidoffer') {
            $reference_id = 'C' . rand(100000000, 999999999);
        } else if ($data['type'] == 'sales') {
            $reference_id = 'G' . rand(100000000, 999999999);
        }
        if($data['type'] == 'maidflex' || $data['type'] == 'maidscheduled'){
            if(!isset($data['nationality_id'])){
               throw ValidationException::withMessages([
                   'nationality_id' => trans('please update the app to the latest version'),
               ]);
            }
        }
        if($data['type'] == 'maidscheduled'){
            if(!isset($data['contract_duration_id'])){
               throw ValidationException::withMessages([
                   'contract_duration_id' => trans('please update the app to the latest version'),
               ]);
            }
        }
        $orderData           = [];
        if (isset($data['service_id']) && $data['service_id']) {
            if ($data['type'] == 'maidoffer') {
                $offer   = CategoryOffer::find($data['service_id']);
                $nameAr  = $offer?->translate('ar')?->name;
                $nameEn  = $offer?->translate('en')?->name;
                $offer = ['id' => $offer->id, 'name_ar' => $nameAr, 'name_en' => $nameEn, 'sale_price' => $offer->sale_price, 'price' => $offer->price, 'hours_num' => $offer->hours_num, 'workers_num' => $offer->workers_num, 'type' => $offer->type];
                $orderData['service_data'] =  $offer  != null  ?  json_encode($offer)  : null;
            } else {
                $service = Category::find($data['service_id']);
                $nameAr  = $service?->translate('ar')?->name;
                $nameEn  = $service?->translate('en')?->name;
                $service = ['id' => $service->id, 'name_ar' => $nameAr, 'name_en' => $nameEn, 'parent_id' => $service->parent_id];
                $orderData['service_data'] =  $service  != null  ? json_encode($service) : null;
            }
        }
        if (isset($data['service_type_id']) && $data['service_type_id']) {
            $serviceType   = CategoryType::find($data['service_type_id']);
            $nameAr        = $serviceType?->translate('ar')?->name;
            $nameEn        = $serviceType?->translate('en')?->name;
            $serviceType   = ['id' => $serviceType->id, 'name_ar' => $nameAr, 'name_en' => $nameEn, 'custom_service_id' => $serviceType->category_id];
            $orderData['service_type_data'] =  $serviceType  != null  ?  json_encode($serviceType) : null;
        }
        if (isset($data['uniform_id']) && $data['uniform_id']) {
            $uniform = CategorySetting::find($data['uniform_id']);
            $nameAr  = $uniform?->translate('ar')?->name;
            $nameEn  = $uniform?->translate('en')?->name;
            $uniform = ['id' => $uniform->id, 'name' => $nameAr, 'name_en' => $nameEn, 'price' => $uniform->addon_price, 'custom_service_id' => $uniform->category_id, 'parent_id' => $uniform->parent_id];
            $orderData['uniform_data'] =  $uniform  != null  ? json_encode($uniform) : null;
        }
         if (isset($data['nationality_id']) && $data['nationality_id']) {
            $nationality = CategorySetting::find($data['nationality_id']);
            $nameAr      = $nationality?->translate('ar')?->name;
            $nameEn      = $nationality?->translate('en')?->name;
            $nationality = ['id' => $nationality->id, 'name' => $nameAr, 'name_en' => $nameEn, 'price' => $nationality->addon_price, 'custom_service_id' => $nationality->category_id, 'parent_id' => $nationality->parent_id];
            $orderData['nationality_data'] =  $nationality  != null  ? json_encode($nationality) : null;
        }
        if (isset($data['contract_duration_id']) && $data['contract_duration_id']) {
            $contractDuration = CategorySetting::find($data['contract_duration_id']);
            $nameAr      = $contractDuration?->translate('ar')?->name;
            $nameEn      = $contractDuration?->translate('en')?->name;
            $contractDuration = ['id' => $contractDuration->id, 'name' => $nameAr, 'name_en' => $nameEn, 'price' => $contractDuration->addon_price, 'custom_service_id' => $contractDuration->category_id, 'parent_id' => $contractDuration->parent_id];
            $orderData['contract_duration_data'] =  $contractDuration  != null  ? json_encode($contractDuration) : null;
        }
        if (isset($data['worker_count_id']) && $data['worker_count_id']) {
            $workerCount = CategorySetting::find($data['worker_count_id']);
            $nameAr      = $workerCount?->translate('ar')?->name;
            $nameEn      = $workerCount?->translate('en')?->name;
            $workerCount = ['id' => $workerCount->id, 'name' => $nameAr, 'name_en' => $nameEn, 'price' => $workerCount->addon_price, 'custom_service_id' => $workerCount->category_id, 'parent_id' => $workerCount->parent_id];
            $orderData['worker_count_data'] =  $workerCount  != null  ? json_encode($workerCount) : null;
        }
        if (isset($data['hours_count_id']) && $data['hours_count_id']) {
            $hoursCount  = CategorySetting::find($data['hours_count_id']);
            $nameAr      = $hoursCount?->translate('ar')?->name;
            $nameEn      = $hoursCount?->translate('en')?->name;
            $hoursCount  = ['id' => $hoursCount->id, 'name' => $nameAr, 'name_en' => $nameEn, 'price' => $hoursCount->addon_price, 'custom_service_id' => $hoursCount->category_id, 'parent_id' => $hoursCount->parent_id];
            $orderData['hours_count_data'] =  $hoursCount  != null  ? json_encode($hoursCount) : null;
        }
        if (isset($data['period_id']) && $data['period_id']) {
            $period = CategorySetting::find($data['period_id']);
            $nameAr = $period?->translate('ar')?->name;
            $nameEn = $period?->translate('en')?->name;
            $period = ['id' => $period->id, 'name' => $nameAr, 'name_en' => $nameEn, 'price' => $period->addon_price, 'custom_service_id' => $period->category_id, 'parent_id' => $period->parent_id];
            $orderData['period_data'] =  $period  != null  ? json_encode($period) : null;
        }
        if (isset($data['duration_id']) && $data['duration_id']) {
            $duration = CategorySetting::find($data['duration_id']);
            $nameAr   = $duration?->translate('ar')?->name;
            $nameEn   = $duration?->translate('en')?->name;
            $duration = ['id' => $duration->id, 'name' => $nameAr, 'name_en' => $nameEn, 'price' => $duration->addon_price, 'custom_service_id' => $duration->category_id, 'parent_id' => $duration->parent_id];
            $orderData['duration_data'] =  $duration  != null  ? json_encode($duration) : null;
        }
        if (isset($data['additional_id']) && $data['additional_id']) {
            $additional = CategorySetting::find($data['additional_id']);
            $nameAr     = $additional?->translate('ar')?->name;
            $nameEn     = $additional?->translate('en')?->name;
            $additional = ['id' => $additional->id, 'name' => $nameAr, 'name_en' => $nameEn, 'price' => $additional->addon_price, 'custom_service_id' => $additional->category_id];
            $orderData['additional_data'] =  $additional  != null  ? json_encode($additional) : null;
        }
        $data['wallet_used'] = ToolHelper::getBooleanValue($data['wallet_used'] ?? false);
        $data['points_used'] = ToolHelper::getBooleanValue($data['points_used'] ?? false);
        $address = Address::where('id', $data['receiving_address_id'] ?? null)
            ->orWhere('id', $data['delivery_address_id'] ?? null)
            ->orWhere('id', $data['execute_address_id'] ?? null)
            ->first();
        $branch = CompanyBranch::find($data['branch_id'] ?? null);
 

        if (isset($data['days_per_week_names'])) {
            $data['days_per_week_names'] = json_encode(explode(',', $data['days_per_week_names']));
        }
        if (isset($data['days_per_month_dates'])) {
            $data['days_per_month_dates'] = json_encode(explode(',', $data['days_per_month_dates']));
        }

        $user    = $user ?? auth('api')->user();
        $coupon  =  Coupon::find($data['coupon_id'] ?? null);
        if (isset($coupon->type) and $coupon->type == 'cashback') {
            $data['cashback'] = $coupon->value;
        }
        $data['coupon_data']  = isset($data['coupon_id']) ?  $coupon?->toJson() : json_encode([]);
        $data['status']       = $data['status'] ?? 'pending';
        $data  = array_merge($data, [
            'city_id'            => $data['city_id'] ?? $branch?->city_id ?? $address?->city_id ?? auth('api')->user()?->city_id,
            'district_id'        => $data['district_id'] ?? $branch?->district_id ?? $address?->district_id ?? auth('api')->user()?->district_id,
            'client_id'          => $user->id,
            'order_status_times' => [$data['status'] => date("Y-m-d H:i")],
            'is_admin_accepted'  => true,
            'reference_id'       => $reference_id,
        ]);


        $createOrderData  = array_merge($data, [
            'paid'               => 0,
            'wallet_amount_used' => 0,
            'points_amount_used' => 0,
        ]);

        if (($createOrderData['type'] ?? null) === 'clothes') {
            $serviceTypeValues = ['iron_only', 'wash_only', 'wash_iron', 'wash_and_iron', 'dry_clean', 'كوي فقط', 'غسيل فقط', 'غسيل وكوي', 'تنظيف جاف'];
            if (isset($createOrderData['desc']) && in_array(trim((string)$createOrderData['desc']), $serviceTypeValues, true)) {
                $createOrderData['desc'] = null;
            }
        }

        $noteParts = array_filter([$createOrderData['desc'] ?? null, $user->contract_note ?? null], function ($part) {
            return !is_null($part) && trim((string)$part) !== '';
        });
        $createOrderData['note'] = !empty($noteParts) ? implode(' - ', array_map('trim', $noteParts)) : null;
        $order = Order::create($createOrderData);

        if (is_array($analyticsContext)) {
            $branchKeys = ['channel', 'feature', 'campaign', 'click_id', 'clicked_branch_link', 'click_timestamp'];
            $branchSnapshot = static function (string $key) use ($analyticsContext, $branchKeys) {
                $snapshot = $analyticsContext[$key] ?? null;
                if (!is_array($snapshot)) {
                    return null;
                }

                return array_intersect_key($snapshot, array_flip($branchKeys));
            };

            OrderAnalyticsContext::create([
                'order_id' => $order->id,
                'schema_version' => 2,
                'app_instance_id' => $analyticsContext['app_instance_id'] ?? null,
                'app_version' => $analyticsContext['app_version'] ?? null,
                'session_id' => isset($analyticsContext['session_id']) ? (int) $analyticsContext['session_id'] : null,
                'platform' => in_array($analyticsContext['platform'] ?? null, ['ios', 'android'], true)
                    ? $analyticsContext['platform']
                    : null,
                'branch_first' => $branchSnapshot('branch_first'),
                'branch_latest' => $branchSnapshot('branch_latest'),
            ]);
        }


        if ($data['pay_type'] == 'card') {
            $order->paid             = $data['paid'];
            $order->card_amount_used = $data['paid'];
            $order->update();
            if ($data['status'] != 'pending_payment') {
                //add order transaction for card payment
                $order->transactions()->create([
                    'type'                  => 'card',
                    'amount'                => $data['paid'],
                    'notes'                 => 'pay normal order card payment for order : ' . $order->reference_id,
                ]);
            }
        }
        if (($data['wallet_used'] == true and  $data['wallet_amount_used'] > 0) and $user->wallet >= $data['wallet_amount_used']) {
            $order->paid                  = ($order->paid + $data['wallet_amount_used']);
            $order->wallet_amount_used    = ($data['wallet_amount_used']);
            $order->update();
            if ($data['status'] != 'pending_payment') {
                $this->createWalletOrderPaymentTransactions(
                    $user,
                    $order,
                    (float) $data['wallet_amount_used'],
                    'pay normal order wallet payment for order : ' . $order->reference_id
                );
            }
        }
        if (ToolHelper::getBooleanValue($data['points_used'] ?? false)) {
            $redemption = app(PointsRedemptionService::class)->calculate($user, $order, $data['points_amount'] ?? 0);
            $user                      = $user ?? auth('api')->user();
            $order->paid               = round($order->paid + $redemption['value'], 2);
            $order->points_amount_used = $redemption['value'];
            $order->points_amount      = $redemption['points'];
            $order->update();
            if ($data['status'] != 'pending_payment') {
                $pointsData                = ['title' => 'used in order : ' . $order->reference_id, 'amount' => $redemption['points'], 'operation' => 'withdraw', 'order_id' => $order->id, 'creator_id' => $user->id];
                $pointModel = $user->points()->create($pointsData);
                //add to the order transactions
                $order->transactions()->create([
                    'type'                  => 'points',
                    'amount'                => $redemption['value'],
                    'point_id'              => $pointModel->id,
                    'notes'                 => 'pay normal order points payment for order : ' . $order->reference_id,
                ]);
            }
        }

        foreach ($orderData ?? [] as $key => $value) {
            $order->moreDatas()->create([
                'key'    => $key,
                'value'  => $value,
            ]);
        }
        //orderRepresentatives
        //receiving data
        if (
            isset($data['receiving_date']) and !empty($data['receiving_date'])
            and isset($data['receiving_time']) and !empty($data['receiving_time'])
            and isset($data['receiving_to_time']) and !empty($data['receiving_to_time'])
        ) {
            $address = Address::where('id', $data['receiving_address_id'] ?? null)->first();
            $order->orderRepresentatives()->create([
                'type'          => 'receiver',
                'date'          => $data['receiving_date']      ?? null,
                'time'          => $data['receiving_time']      ?? null,
                'to_time'       => $data['receiving_to_time']   ?? null,
                'lat'           => $branch->lat ?? $address?->lat ?? $data['receiving_lat'] ?? null,
                'lng'           => $branch->lng ?? $address?->lng ?? $data['receiving_lng'] ?? null,
                'location'      => $branch->location ?? $address?->location ?? $data['receiving_location'] ?? null,
                'has_problem'   => false,
                'for_all_items' => true,
                'address_id'    => $address?->id,
                'branch_id'     => $branch?->id,
            ]);
        }
        if (
            isset($data['execute_date']) and !empty($data['execute_date'])
            and isset($data['execute_time']) and !empty($data['execute_time'])
        ) {
            $address = Address::where('id', $data['receiving_address_id'])->first();

            $order->orderRepresentatives()->create([
                'type'          => 'technical',
                'date'          => $data['execute_date']      ?? $data['receiving_date']      ?? null,
                'time'          => $data['execute_time']      ?? $data['receiving_time']      ?? null,
                'to_time'       => $data['execute_to_time']   ?? $data['receiving_to_time']   ?? null,
                'lat'           => $branch?->lat             ?? $address?->lat             ?? $data['execute_lat']       ?? null,
                'lng'           => $branch?->lng             ?? $address?->lng             ?? $data['execute_lng']       ?? null,
                'location'      => $branch?->location        ?? $address?->location        ?? $data['execute_location']  ?? null,
                'has_problem'   => false,
                'for_all_items' => true,
                'address_id'    => $address?->id,
                'branch_id'     => $branch?->id,

            ]);
        }
        if (
            isset($data['delivery_date']) and !empty($data['delivery_date'])
            and isset($data['delivery_time']) and !empty($data['delivery_time'])
            and isset($data['delivery_to_time']) and !empty($data['delivery_to_time'])
        ) {
            $address = Address::where('id', $data['delivery_address_id'] ?? null)->first();

            $order->orderRepresentatives()->create([
                'type'          => 'delivery',
                'date'          => $data['delivery_date']       ?? null,
                'time'          => $data['delivery_time']       ?? null,
                'to_time'       => $data['delivery_to_time']    ?? null,
                'lat'           => $branch?->lat               ?? $address?->lat               ?? $data['delivery_lat']       ?? null,
                'lng'           => $branch?->lng               ?? $address?->lng               ?? $data['delivery_lng']       ?? null,
                'location'      => $branch?->location          ?? $address?->location          ?? $data['delivery_location']  ?? null,
                'has_problem'   => false,
                'for_all_items' => true,
                'address_id'    => $address?->id,
                'branch_id'     => $branch?->id,

            ]);
        }

        if (in_array($data['type'], ['clothes', 'services', 'sales'])) {
            $this->storeOrderItems($order->id, $products);
        }
        $this->calcOriginalProductsTotal($order->id);
        $testAccounts = SettingsService::getDataBaseSetting('testing_accounts') ?? [];
        if (!in_array($order->client_id, $testAccounts)) {
            $this->telegramNotificationService->sendMessage("@cleanstationneworders", $this->telegramNotificationService->formatNewOrderMessage($order));
        }
        if ($order->status !== 'pending_payment') {
            app(AnalyticsOutboxService::class)->record('order_created', $order);
        }
        if ($order->coupon_id) {
            app(AnalyticsOutboxService::class)->record('coupon_accepted', $order, [
                'coupon_type' => (string) ($order->coupon?->type ?? 'unknown'),
                'discount_amount' => round((float) ($order->total_coupon ?? 0), 2),
            ], 'coupon-accepted');
        }
        return $order;
    }
    public function storeOrderItems($orderId, $products)
    {
        $order = Order::findOrFail($orderId);
        foreach ($products as $item) {
            $product = Product::where('id', $item['id'])->first();
            ProductsService::setCurrentContract($order->company);
            $productData = ProductsService::getProductData($order->company,$order->b2b_type,$order->client?->profile?->city_id,$product);
            $price = $productData['price'];
            $cost = $productData['cost'];
            $processedCustomizations = null;
            $customizationsPriceSum = 0;
            if (!empty($item['customizations']) && is_array($item['customizations'])) {
                $processedCustomizations = [];
                foreach ($item['customizations'] as $customization) {
                    $setting = ProductSetting::find($customization['setting_id'] ?? null);
                    $option  = ProductSetting::find($customization['options_id'] ?? null);
                   
                 
                    
                    $processedCustomizations[] = [
                        'setting_id'      => $customization['setting_id'] ?? null,
                        'setting_name_en' => $setting?->translate('en')?->name ?? '',
                        'setting_name_ar' => $setting?->translate('ar')?->name ?? '',
                        'options_id'      => $customization['options_id'] ?? null,
                        'option_name_en'  => $option?->translate('en')?->name ?? '',
                        'option_name_ar'  => $option?->translate('ar')?->name ?? '',
                        'price'           => $option?->price,
                    ];
                }
            }
            $price += $customizationsPriceSum;
            $order->items()->create([
                'order_id'             =>  $order->id,
                'product_id'           =>  $product->id,
                'product_price'        =>  $price,
                'product_cost'         =>  $cost,
                'quantity'             =>  $item['quantity'] ?? 0,
                'width'                =>  $item['width']    ?? 0,
                'height'               =>  $item['height']   ?? 0,
                'product_data'         =>  $product->toJson(),
                'is_picked'            =>  false,
                'is_delivered'         =>  false,
                'customizations'       =>  $processedCustomizations,
                'card_note'            =>  $item['card_note'] ?? null,
            ]);
            if ($product->type == 'sales') {
                $product->update(['quantity' => $product->quantity - $item['quantity']]);
            }
        }
    }
    public function payFastOrder($orderId, $data,$user = null)
    {
        $order               = Order::whereId($orderId)->firstOrFail();
        $user                = $user ?? auth('api')->user();
        $alreadyCardPaid     = $order->paid - ($order->wallet_amount_used + $order->points_amount_used);
        $alreadyWalletPaid   = $order->wallet_amount_used;
        $alreadyPointsPaid   = $order->points_amount_used;
        $alreadyPointsAmount = $order->points_amount;
        $newCardPaid         = $data['paid'] ?? 0;
        $alreadyCardPaid     += $newCardPaid;
        if(
            $newCardPaid == 0 
            and ToolHelper::getBooleanValue($data['wallet_used'] ?? false) == false 
            and ToolHelper::getBooleanValue($data['points_used'] ?? false) == false
        ){
            return null;
        }
        if ($newCardPaid > 0) {
            //add to the order transactions
            $order->transactions()->create([
                'type'                  => 'card',
                'amount'                => $newCardPaid,
                'transaction_id'        => $data['transaction_id'] ?? null,
                'online_payment_method' => $data['pay_type'] ?? 'card',
                'notes'                 => 'pay fast order card payment for order : ' . $order->reference_id,
            ]);
        }

        $walletUsed        = ToolHelper::getBooleanValue($data['wallet_used'] ?? false);
        if (($walletUsed == true and  $data['wallet_amount_used'] > 0) and $user->wallet >= $data['wallet_amount_used']) {
            $alreadyWalletPaid  += $data['wallet_amount_used'];
            $this->createWalletOrderPaymentTransactions(
                $user,
                $order,
                (float) $data['wallet_amount_used'],
                'pay fast order wallet payment for order : ' . $order->reference_id
            );
        }

        $pointsUsed        = ToolHelper::getBooleanValue($data['points_used'] ?? false);
        if ($pointsUsed) {
            $redemption = app(PointsRedemptionService::class)->calculate($user, $order, $data['points_amount'] ?? 0);
            $order->paid         = round($order->paid + $redemption['value'], 2);
            $alreadyPointsPaid   += $redemption['value'];
            $alreadyPointsAmount += $redemption['points'];

            $pointsData = ['title' => 'used in order : ' . $order->reference_id, 'amount' => $redemption['points'], 'operation' => 'withdraw', 'order_id' => $order->id, 'creator_id' => $user->id];
            $pointModel = $user->points()->create($pointsData);
            //add to the order transactions
            $order->transactions()->create([
                'type'      => 'points',
                'amount'    => $redemption['value'],
                'point_id'  => $pointModel->id,
                'notes'     => 'pay fast order points payment for order : ' . $order->reference_id,
            ]);
        }
        $data['wallet_used']        = ($walletUsed || $order->wallet_used);
        $data['points_used']        = ($pointsUsed || $order->points_used);

        $data['wallet_amount_used'] = $alreadyWalletPaid;
        $data['points_amount_used'] = $alreadyPointsPaid;
        $data['points_amount']      = $alreadyPointsAmount;
        $data['paid']               = ($alreadyCardPaid + $alreadyWalletPaid + $alreadyPointsPaid);
        if ($data['transaction_id'] ?? null == null) {
            unset($data['transaction_id']);
        }
        $payType = $order->pay_type;
        if ($data['pay_type'] == 'card') {
            $payType = 'card';
        } else if ($data['pay_type'] == 'wallet' and $order->pay_type != 'card') {
            $payType = 'wallet';
        }
        $data['pay_type']            = $payType;
        // $data['hide_payment_option'] = true;
        $order->update($data);
    }
    public function updateStatus($orderId, $data)
    {
        $order = Order::where('status','pending')->whereId($orderId)->first();
        if($order){
            return $order;
        }
        $order = Order::whereStatus('pending_payment')->whereId($orderId)->firstOrFail();
        if(isset($data['status']) and $data['status'] == 'pending'){
            Cart::where('user_id',$order->client_id)->delete();
        }
        $ogStatus = $order->status;
        $order->update($data);

        if ($ogStatus === 'pending_payment' && $order->status === 'pending') {
            app(AnalyticsOutboxService::class)->record('order_created', $order);
        }
        

        
        if ($order->client and $order->status == 'pending') {
            $pointsUsedAmount = $order->points_amount_used;
            $walletUsedAmount = $order->wallet_amount_used;
            $cardPaidAmount   = $order->paid - ($walletUsedAmount + $pointsUsedAmount);
            if ($cardPaidAmount > 0) {
                //add to the order transactions
                $order->transactions()->create([
                    'type'                  => 'card',
                    'amount'                => $cardPaidAmount,
                    'transaction_id'        => $order->transaction_id,
                    'online_payment_method' => $order->online_payment_method,
                    'notes'                 => 'pay normal order card payment for order : ' . $order->reference_id,
                ]);
            }
            if ($pointsUsedAmount > 0) {
                //add to the points records
                $pointsData = ['title' => 'used in order : ' . $order->reference_id, 'amount' => $order->points_amount, 'operation' => 'withdraw', 'order_id' => $order->id, 'creator_id' => $order->client->id];
                $pointModel = $order->client?->points()->create($pointsData);

                //add to the order transactions
                $order->transactions()->create([
                    'type'      => 'points',
                    'amount'    => $pointsUsedAmount,
                    'point_id'  => $pointModel->id,
                    'notes'     => 'pay normal order points payment for order : ' . $order->reference_id,
                ]);
            }
            if ($walletUsedAmount > 0) {
                //add to the wallet records
                $this->createWalletOrderPaymentTransactions(
                    $order->client,
                    $order,
                    (float) $walletUsedAmount,
                    'pay normal order wallet payment for order : ' . $order->reference_id
                );
            }
        }
        if(isset($data['status']) and $data['status'] != $ogStatus){
            $order->update([
                'order_status_times' => [
                    $data['status'] => [now()->format('Y-m-d H:i')]
                ],
            ]);
        }
        return $order;
    }
    /**
     * Card amount the gateway must charge to settle a fast order.
     *
     * Computed from the order itself (total minus what is already paid, minus the
     * wallet/points the client will use) instead of trusting the amount sent by the
     * client app. Wallet and points are only deducted when payFastOrder() would
     * actually apply them, so the card always covers the true outstanding balance.
     */
    public function fastPaymentCardAmount(Order $order, array $data, $user = null): float
    {
        $user         = $user ?? auth('api')->user();
        $walletAmount = 0.0;
        $pointsAmount = 0.0;

        if (ToolHelper::getBooleanValue($data['wallet_used'] ?? false)) {
            $requested = (float) ($data['wallet_amount_used'] ?? 0);
            if ($requested > 0 && (float) ($user?->wallet ?? 0) >= $requested) {
                $walletAmount = $requested;
            }
        }

        if (ToolHelper::getBooleanValue($data['points_used'] ?? false)) {
            $points           = (int) ($data['points_amount'] ?? 0);
            $balance          = (float) ($user?->points_balance ?? 0);
            $minAllowedPoints = (float) (SettingsService::getDataBaseSetting('minium_points_to_use') ?? 0);
            $rate = max(0, (float) (SettingsService::getDataBaseSetting('riyal_per_point_redeem') ?? 0));
            if ($points > 0 && $points >= $minAllowedPoints && $balance >= $points) {
                $serverValue = round($points * $rate, 2);
                $dueAfterWallet = OrderPaymentMath::remainingToCollect(
                    $order->total_price,
                    (float) $order->paid + $walletAmount
                );
                if ($serverValue <= $dueAfterWallet) {
                    $pointsAmount = $serverValue;
                }
            }
        }

        return OrderPaymentMath::remainingToCollect(
            $order->total_price,
            (float) $order->paid + $walletAmount + $pointsAmount
        );
    }

    private function createWalletOrderPaymentTransactions($user, Order $order, float $amount, string $notes): void
    {
        if ($amount <= 0 || ! $user) {
            return;
        }

        $allocations = app(\Core\Wallet\Services\WalletBucketAllocationService::class)
            ->allocate((int) $user->id, $amount);
        $walletTransactions = [];

        foreach ($allocations as $bucket => $bucketAmount) {
            $walletTransactions[] = $user->walletTransactions()->create([
                'amount' => $bucketAmount,
                'type' => 'withdraw',
                'status' => 'accepted',
                'transaction_type' => 'order_payment',
                'wallet_bucket' => $bucket,
                'order_id' => $order->id,
            ]);
        }

        // Persist every wallet ledger row before creating order transactions:
        // the latter trigger paid-total recalculation and may emit purchase.
        foreach ($walletTransactions as $walletTransaction) {
            $order->transactions()->create([
                'type' => 'wallet',
                'amount' => $walletTransaction->amount,
                'wallet_transaction_id' => $walletTransaction->id,
                'notes' => $notes,
            ]);
        }
    }

    public function createPaymentUrl(int $orderId,$amount,$data,$type = 'order_payment'): ?string
    {
        $order = Order::whereId($orderId)->firstOrFail();
        $previousTransaction = OrderTransaction::where('order_id',$orderId)
            ->where('type','card')->count();
        if($previousTransaction > 0){
            $multiplePaymentFees = SettingsService::getDataBaseSetting('multiple_payment_fees');
            $amount = $amount + $multiplePaymentFees;
        }
        $transactionNumber = $previousTransaction + 1;
        return $this->myfatoorahService->createTransaction($amount, $order->id, $data, $order->client_id, $type,$order->reference_id.'-'.$transactionNumber.'-');
    }
    public function restore(int $id){
        $record = Order::onlyTrashed()->findOrFail($id);
        $record->restore();
        return $record;
    }
}
