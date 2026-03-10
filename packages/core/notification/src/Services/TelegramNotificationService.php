<?php

namespace Core\Notification\Services;

use Core\Orders\Models\Order;
use Core\Products\Models\Product;
use Core\Settings\Helpers\ToolHelper;
use Core\Users\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    protected $botToken;
    protected $chatId;

    public function __construct()
    {
        $this->botToken = "7970295502:AAHmfUgGNGPyHp8RoDKiEZ4G6vdrdiMg0B0";
    }

    public function sendMessage(string $chatId, string $message): void
    {
        if (app()->environment() == 'local' || app()->environment() == 'Local') {
            return;
        }

        if (empty($this->botToken) || empty($chatId)) {
            Log::warning('Telegram bot token or chat ID not configured');
            return;
        }

        try {
            $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";
            $respnse = Http::timeout(5)->post($url, [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to send Telegram notification: ' . $e->getMessage());
        }
    }

    public function formatExceptionMessage(\Throwable $exception): string
    {
        $domain     =   $_SERVER['HTTP_HOST'] ?? null;
        $message    =   "<b>⚠️ حدث خطأ ⚠️ (" . $domain . ")</b>\n\n";
        $message    .=  "<b>📱 التطبيق:</b> " . config('app.name') . "\n";
        $message    .=  "<b>🌍 البيئة:</b> " . app()->environment() . "\n";

        // Add request URL if available
        if (request() && method_exists(request(), 'fullUrl')) {
            $message .= "<b>🔗 الرابط:</b> " . request()->fullUrl() . "\n";
        }

        $message .= "<b>❌ الخطأ:</b> " . $exception->getMessage() . "\n";
        $message .= "<b>📄 الملف:</b> " . $exception->getFile() . "\n";
        $message .= "<b>📝 السطر:</b> " . $exception->getLine() . "\n";
        $message .= "<b>🔢 الكود:</b> " . $exception->getCode() . "\n";
        $message .= "<b>🕐 الوقت:</b> " . now()->toDateTimeString() . "\n\n";

        // Add authenticated user if available
        if (request()->user()) {
            $message .= "<b>👤 المستخدم:</b> " . request()->user()->email . " (ID: " . request()->user()->id . ")\n";
        }

        if ($exception instanceof \Illuminate\Database\QueryException) {
            $message .= "<b>💾 استعلام SQL:</b> " . $exception->getSql() . "\n";
            $message .= "<b>🔗 المتغيرات:</b> " . json_encode($exception->getBindings()) . "\n";
        }

        // Add request data if available
        if (request() && !empty(request()->all())) {
            $message .= "\n<b>📋 بيانات الطلب:</b>\n<pre>" .
                json_encode(request()->except(['password', 'password_confirmation']), JSON_PRETTY_PRINT) .
                "</pre>\n";
        }

        // Add stack trace (first 3 lines to identify the source)
        $trace = collect($exception->getTrace())->take(5)->map(function ($trace) {
            $file = $trace['file'] ?? 'unknown';
            $line = $trace['line'] ?? '?';
            $function = ($trace['class'] ?? '') . ($trace['type'] ?? '') . ($trace['function'] ?? '');
            
            // Shorten file path
            $file = str_replace(base_path(), '', $file);
            
            return "→ {$function}\n  {$file}:{$line}";
        })->join("\n");

        if ($trace) {
            $message .= "\n<b>🔍 Stack Trace (Top 5):</b>\n<pre>{$trace}</pre>";
        }

        return $message;
    }

    /**
     * Report an exception to Telegram from within a try-catch block
     * Use this when you catch an exception but still want to notify about it
     */
    public function reportException(\Throwable $exception, string $chatId = '@itcleanstation'): void
    {
        try {
            $formattedMessage = $this->formatExceptionMessage($exception);
            $this->sendMessage($chatId, $formattedMessage);
        } catch (\Throwable $e) {
            Log::error('Failed to report exception to Telegram: ' . $e->getMessage());
        }
    }
    //chanel @cleanstationneworders
    public function formatNewOrderMessage(Order $order)
    {
        $reviving = $order->orderRepresentatives()->where('type', 'receiver')->first();
        $delivery = $order->orderRepresentatives()->where('type', 'delivery')->first();
        $ordersCount = $order->client->orders()->whereNotIn('orders.status', ['pending_payment', 'failed_payment','cancel_payment'])->count();
        $message = "<a href='".route('dashboard.orders.edit', $order->reference_id)."'> طلب جديد</a>  🔵\n\n";
        $message .= "<b>رقم الطلب:</b> " . $order->reference_id . "\n";
        $message .= "<b>العميل:</b> " . $order->client?->fullname . "\n";
        $message .= "<b>الهاتف:</b> " . $order->client?->phone . "\n";
        $message .= "<b>عدد الطلبات:</b> " . $ordersCount . "\n";
        if ($ordersCount  == 1) {
            $message .= "<b>|------------------------|</b>\n";
            $message .= "<b>| أول طلب للعميل |</b>\n";
            $message .= "<b>|------------------------|</b>\n";
        }
        $message .= "<b>حالة الطلب:</b> " . trans($order->status, [], 'ar') . " " . trans($order->status, [], 'en') . "\n";
        $message .= "<b>إجمالي الطلب:</b> " . $order->total_price . " ريال\n";
        $message .= "<b>عدد القطع:</b> " . $order->items->count() . "\n";
        $message .= "<b>المدينة:</b> " . $order->city?->name . " | ";
        $message .= "<b>الحي:</b> " . $order->district?->name . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "<b>الاستلام:</b> " . $reviving?->date . " " . $reviving?->time . " - " . $reviving?->to_time . "\n";
        $message .= "<b>التسليم:</b> " . $delivery?->date . " " . $delivery?->time . " - " . $delivery?->to_time . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= route('dashboard.orders.edit', $order->reference_id);
        return $message;
    }
    public function formatOrderIsPendingPaymentForTenMinutesMessage($order)
    {
        $message = "<b>⏰ طلب في انتظار الدفع لمدة عشر دقائق</b>\n\n";
        $message .= "<b>🔢 رقم الطلب:</b> " . $order->reference_id . "\n";
        $message .= "<b>👤 العميل:</b> " . $order->client?->fullname . "\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $order->client?->phone . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "💳 اضغط هنا لدفع ثمن الطلب: " . route('dashboard.orders.edit', $order->reference_id);
        return $message;
    }
    public function formatOrderIsLateForPickupMessage($order)
    {
        $reviving = $order->orderRepresentatives()->where('type', 'receiver')->first();
        $message = "<b>🚨 طلب متأخر عن موعد الاستلام</b>\n\n";
        $message .= "<b>🔢 رقم الطلب:</b> " . $order->reference_id . "\n";
        $message .= "<b>👤 العميل:</b> " . $order->client?->fullname . "\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $order->client?->phone . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "<b>📅 موعد الاستلام:</b> " . $reviving?->date . " " . $reviving?->time . " - " . $reviving?->to_time . "\n";
        $message .= "<b>🚚 المستلم:</b> " . ($reviving?->fullname ?? "لا يوجد مستلم بعد") . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "🔗 اضغط هنا لتعديل الطلب: " . route('dashboard.orders.edit', $order->reference_id);
        return $message;
    }
    public function formatOrderIsLateForDeliveryMessage($order)
    {
        $delivery = $order->orderRepresentatives()->where('type', 'delivery')->first();
        $message = "<b>🚨 طلب متأخر عن موعد التسليم</b>\n\n";
        $message .= "<b>🔢 رقم الطلب:</b> " . $order->reference_id . "\n";
        $message .= "<b>👤 العميل:</b> " . $order->client?->fullname . "\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $order->client?->phone . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "<b>🚚 موعد التسليم:</b> " . $delivery?->date . " " . $delivery?->time . " - " . $delivery?->to_time . "\n";
        $message .= "<b>🚚 المُسلم:</b> " . ($delivery?->fullname ?? "لا يوجد مُسلم بعد") . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "🔗 اضغط هنا لتعديل الطلب: " . route('dashboard.orders.edit', $order->reference_id);
        return $message;
    }

    // chanel @cleanstationsupport
    public function formatClientIsTryingToLoginWithOldLoginMethodMessage($phone)
    {
        $user = User::where('phone', $phone)->first();
        $message = "<b>⚠️ عميل يحاول تسجيل الدخول بالطريقة القديمة</b>\n\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $phone . "\n";
        $message .= "<b>--------------------------------</b>\n";
        if ($user) {
            $message .= "<b>👤 المستخدم:</b> " . $user->fullname . "\n";
            $message .= "<b>📧 البريد الإلكتروني:</b> " . $user->email . "\n";
            $message .= "<b>--------------------------------</b>\n";
            $message .= "🔗 اضغط هنا لتعديل المستخدم: " . route('dashboard.users.edit', $user->id);
        }
        return $message;
    }
    public function formatCartHasBeenLeftForMoreThanTenMinutesMessage($cart)
    {
        $cartItems =  json_decode($cart->data, true);
        $cartItems = ToolHelper::isJson($cartItems) ? json_decode($cartItems, true) : $cartItems;
        $productIds = array_map(fn($product) => $product['id'], $cartItems);
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $totalPieces = 0;
        $totalPrice = 0;
        foreach ($cartItems ?? [] as $cartItem) {
            if($products[$cartItem['id']]){
                $totalPieces += $cartItem['quantity'];
                $totalPrice += $products[$cartItem['id']]->price * $cartItem['quantity'];
            }
            
        }
        $districtName = $cart->user?->profile?->other_city_name ?? $cart->user?->profile?->district?->name;
        $message = "<b>🛒 سلة متروكة لأكثر من عشر دقيقة</b>\n\n";
        $message .= "<b>👤 العميل:</b> " . $cart->user?->fullname . "\n";
        $message .= "<b>🏢 المدينة:</b> " . $cart->user?->profile?->city?->name . "\n";
        $message .= "<b>🏢 الحي:</b> " . $districtName . "\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $cart->user?->phone . "\n";
        $message .= "<b>🆔 رقم السلة:</b> " . $cart->id . "\n";

        if($cart->updated_at){
            $message .= "<b>🆔 منذ:</b> " . $cart->updated_at->format('Y-m-d h:i A') . "\n";
        }else if($cart->created_at){
            $message .= "<b>🆔 عند الإنشاء:</b> " . $cart->created_at->format('Y-m-d h:i A') . "\n";
        }
        $message .= "<b>--------------------------------</b>\n";
        $message .= "<b>🔢 عدد القطع:</b> " . $totalPieces . "\n";
        $message .= "<b>💰 إجمالي السعر:</b> " . $totalPrice . " ريال\n";

        $message .= "<b>--------------------------------</b>\n";
        $message .= "🔗 اضغط هنا لتعديل السلة: " . route('dashboard.carts.create-order', $cart->id);
        return $message;
    }
    public function formatClientNewInAppMessage($user)
    {
        $message = "<b>🎉 عميل جديد في التطبيق</b>\n\n";
        $message .= "<b>👤 العميل:</b> " . $user->fullname . "\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $user->phone . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "🔗 اضغط هنا لتعديل المستخدم: " . route('dashboard.users.edit', $user->id);
        return $message;
    }
    public function clientChangeNotificationSettingsMessage($user, $newStatus)
    {
        $message = "<b>🔔 عميل غيّر إعدادات الإشعارات</b>\n\n";
        $message .= "<b>👤 العميل:</b> " . $user->fullname . "\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $user->phone . "\n";
        $message .= "<b>🔄 الحالة الجديدة:</b> " . $newStatus . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "🔗 اضغط هنا لتعديل المستخدم: " . route('dashboard.users.edit', $user->id);
        return $message;
    }

    // chanel @cleanstationreports
    public function formatShortPerformanceReportMessage($dateFrom, $dateTo, $type, $ordersCount, $ordersTotal, $ordersCost, $revenue)
    {
        $message = "<b>📊 تقرير أداء مختصر</b>\n\n";
        $message .= "<b>📅 من تاريخ:</b> " . $dateFrom . "\n";
        $message .= "<b>📅 إلى تاريخ:</b> " . $dateTo . "\n";
        $message .= "<b>📋 النوع:</b> " . $type . "\n";
        $message .= "<b>📊 عدد الطلبات:</b> " . $ordersCount . "\n";
        $message .= "<b>💰 إجمالي الطلبات:</b> " . $ordersTotal . " ريال\n";
        $message .= "<b>💸 تكلفة الطلبات:</b> " . $ordersCost . " ريال\n";
        $message .= "<b>💵 الإيرادات:</b> " . $revenue . " ريال\n";
        $message .= "<b>--------------------------------</b>\n";
        return $message;
    }

    // chanel @cleanstationnoperation

    public function formatOrderStartsPickupInOneHourMessage($order)
    {
        $pickup = $order->orderRepresentatives()->where('type', 'receiver')->first();
        $message = "<b>⏰ طلب يبدأ الاستلام خلال ساعة</b>\n\n";
        $message .= "<b>🔢 رقم الطلب:</b> " . $order->reference_id . "\n";
        $message .= "<b>👤 العميل:</b> " . $order->client?->fullname . "\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $order->client?->phone . "\n";
        $message .= "<b>💳 نوع الدفع:</b> " . ($order->pay_type ?? "لا يوجد نوع دفع بعد") . "\n";
        $message .= "<b>📝 ملاحظة الطلب:</b> " . ($order->note ?? "لا توجد ملاحظة بعد") . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "<b>📅 موعد الاستلام:</b> " . $pickup?->date . " " . $pickup?->time . " - " . $pickup?->to_time . "\n";
        $message .= "<b>📍 الموقع:</b> " . ($pickup?->location ?? "لا يوجد موقع بعد") . "\n";
        $message .= "<b>--------------------------------</b>\n";
        return $message;
    }

    public function formatOrderStartsDeliveryInOneHourMessage($order)
    {
        $delivery = $order->orderRepresentatives()->where('type', 'delivery')->first();
        $message = "<b>⏰ طلب يبدأ التسليم خلال ساعة</b>\n\n";
        $message .= "<b>🔢 رقم الطلب:</b> " . $order->reference_id . "\n";
        $message .= "<b>👤 العميل:</b> " . $order->client?->fullname . "\n";
        $message .= "<b>📱 رقم الهاتف:</b> " . $order->client?->phone . "\n";
        $message .= "<b>💳 نوع الدفع:</b> " . ($order->pay_type ?? "لا يوجد نوع دفع بعد") . "\n";
        $message .= "<b>📝 ملاحظة الطلب:</b> " . ($order->note ?? "لا توجد ملاحظة بعد") . "\n";
        $message .= "<b>--------------------------------</b>\n";
        $message .= "<b>🚚 موعد التسليم:</b> " . $delivery?->date . " " . $delivery?->time . " - " . $delivery?->to_time . "\n";
        $message .= "<b>📍 موقع التسليم:</b> " . ($delivery?->location ?? "لا يوجد موقع تسليم بعد") . "\n";
        $message .= "<b>--------------------------------</b>\n";
        return $message;
    }

    public function formatDailySummaryMessage($orders)
    {
        $message = "<b>📊 ملخص يومي للقطع المضافة أو المحذوفة</b>\n\n";
        foreach ($orders as $order) {
            $itemsWithTrashed = $order->items()->withTrashed()->get();

            $message .= "<b>🔢 رقم الطلب:</b> " . $order->reference_id . "\n";
            $message .= "<b>👤 العميل:</b> " . $order->client?->fullname . "\n";
            $message .= "<b>📱 رقم الهاتف:</b> " . $order->client?->phone . "\n";
            $message .= "<b>--------------------------------</b>\n";
            foreach ($itemsWithTrashed as $item) {
                if($item->deleted_at){
                    $message .= "<b>❌ تم حذف القطعة</b> \n";
                    $message .= "<b>🏷️ اسم القطعة:</b> " . $item->product?->name . "\n";
                    $message .= "<b>💰 سعر القطعة:</b> " . $item->product_price . " ريال\n";
                    $message .= "<b>🔢 الكمية:</b> " . $item->quantity . "\n";
                    $message .= "<b>--------------------------------</b>\n";
                }elseif(!empty($item->update_by_admin)){
                    $message .= "<b>✏️ تم تحديث القطعة بواسطة الإدارة: " . $item->update_by_admin . "</b> \n";
                    $message .= "<b>🏷️ اسم القطعة:</b> " . $item->product?->name . "\n";
                    $message .= "<b>💰 سعر القطعة:</b> " . $item->product_price . " ريال\n";
                    $message .= "<b>🔢 الكمية:</b> " . $item->quantity . "\n";
                    $message .= "<b>--------------------------------</b>\n";
                }elseif(!empty($item->add_by_admin)){
                    $message .= "<b>➕ تم إضافة القطعة بواسطة الإدارة: " . $item->add_by_admin . "</b> \n";
                    $message .= "<b>🏷️ اسم القطعة:</b> " . $item->product?->name . "\n";
                    $message .= "<b>💰 سعر القطعة:</b> " . $item->product_price . " ريال\n";
                    $message .= "<b>🔢 الكمية:</b> " . $item->quantity . "\n";
                    $message .= "<b>--------------------------------</b>\n";
                }
            }
            $message .= "<b>--------------------------------</b>\n";
        }
        return $message;
    }

    // chanel @cleanstationfinancial
    // 💰 تقرير يومي يوضح: المدفوعات / تاريخ الطلب / تاريخ ووقت العملية / طريقة الدفع 
    // 💸 المسترجع – استخدام المحفظة – استخدام النقاط – استخدام الكود 
    // 📊 تقرير يومي لحساب: المغسلة أسبوعي – عدد القطع – المبلغ الإجمالي المطلوب 

}
