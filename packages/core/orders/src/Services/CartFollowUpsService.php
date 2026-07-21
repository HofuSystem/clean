<?php

namespace Core\Orders\Services;

use Carbon\Carbon;
use Core\Orders\Models\Cart;
use Core\Orders\Models\CartFollowUp;
use Core\Orders\Models\Order;
use Core\Settings\Services\SettingsService;
use Illuminate\Validation\ValidationException;

class CartFollowUpsService
{
    /**
     * Return the configured follow-up window in hours (default 24).
     */
    public function getFollowUpHoursDiff(): int
    {
        return (int) (SettingsService::getDataBaseSetting('follow_up_hours_diff') ?? 24);
    }

    /**
     * Check whether a cart already has a pending (active) follow-up.
     */
    public function hasActiveFollowUp(int $cartId): bool
    {
        return CartFollowUp::where('cart_id', $cartId)
            ->where('status', 'pending')
            ->exists();
    }

    /**
     * Create a new follow-up for a cart. Throws if one already exists.
     */
    public function create(array $data): CartFollowUp
    {
        $cartId = $data['cart_id'];

        if ($this->hasActiveFollowUp($cartId)) {
            throw ValidationException::withMessages([
                'cart_id' => [trans('This cart already has an active follow-up.')],
            ]);
        }

        return CartFollowUp::create([
            'cart_id'        => $cartId,
            'admin_id'       => auth('web')->id(),
            'phone'          => $data['phone'] ?? null,
            'notes'          => $data['notes'] ?? null,
            'status'         => 'pending',
            'followed_up_at' => now(),
        ]);
    }

    /**
     * Mark a follow-up as sale when the user places an order within the window.
     * Called from OrderObserver after a new order is created.
     */
    public function checkAndMarkAsSale(Order $order): void
    {
        $userId = $order->client_id;
        if (!$userId) {
            return;
        }

        $cart = Cart::where('user_id', $userId)->first();
        if (!$cart) {
            return;
        }

        $hoursDiff = $this->getFollowUpHoursDiff();

        // Find a pending follow-up that was created within the window
        $followUp = CartFollowUp::where('cart_id', $cart->id)
            ->where('status', 'pending')
            ->where('followed_up_at', '>=', Carbon::now()->subHours($hoursDiff))
            ->latest()
            ->first();

        if ($followUp) {
            $followUp->update([
                'status'   => 'sale',
                'order_id' => $order->id,
                'order_at' => $order->created_at ?? now(),
            ]);
        }
    }

    /**
     * DataTable list of follow-ups with optional filters.
     */
    public function dataTable($draw)
    {
        $query = CartFollowUp::with(['cart.user', 'admin', 'order']);

        // Filter by admin
        if (request()->has('filters.admin_id') && !empty(request('filters.admin_id'))) {
            $query->where('admin_id', request('filters.admin_id'));
        }

        // Filter by cart user
        if (request()->has('filters.user_id') && !empty(request('filters.user_id'))) {
            $query->whereHas('cart', fn($q) => $q->where('user_id', request('filters.user_id')));
        }

        // Filter by status
        if (request()->has('filters.status') && !empty(request('filters.status'))) {
            $query->where('status', request('filters.status'));
        }

        // Filter by date range
        if (request()->has('filters.from_created_at') && !empty(request('filters.from_created_at'))) {
            $query->whereDate('followed_up_at', '>=', Carbon::parse(request('filters.from_created_at')));
        }
        if (request()->has('filters.to_created_at') && !empty(request('filters.to_created_at'))) {
            $query->whereDate('followed_up_at', '<=', Carbon::parse(request('filters.to_created_at')));
        }

        $recordsTotal    = CartFollowUp::count();
        $recordsFiltered = (clone $query)->count();

        if (request()->has('start') && request()->has('length') && request()->input('length') != -1) {
            $query->skip(request()->input('start'))->take(request()->input('length'));
        }

        $records = $query->latest()->get();

        return [
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $records->map(fn($r) => $this->format($r)),
        ];
    }

    private function format(CartFollowUp $f): array
    {
        return [
            'id'             => $f->id,
            'cart_id'        => $f->cart_id,
            'user_name'      => $f->cart?->user?->fullname ?? '-',
            'user_phone'     => $f->cart?->user?->phone ?? $f->phone,
            'admin_name'     => $f->admin?->fullname ?? '-',
            'status'         => $f->status,
            'notes'          => $f->notes,
            'followed_up_at' => $f->followed_up_at?->format('Y-m-d H:i'),
            'order_at'       => $f->order_at?->format('Y-m-d H:i'),
            'order_ref'      => $f->order?->reference_id,
            'created_at'     => $f->created_at?->format('Y-m-d'),
            'actions'        => $this->getActionsHtml($f),
        ];
    }

    private function getActionsHtml(CartFollowUp $f): string
    {
        $actions = '<div class="d-flex justify-content-center align-items-center">';
        $actions .= '
            <button type="button"
                class="btn-operation d-flex justify-content-center align-items-center mx-1 edit-status-btn"
                data-id="' . $f->id . '"
                data-status="' . e($f->status) . '"
                data-notes="' . e($f->notes ?? '') . '"
                title="' . trans('Update Status') . '">
                <i class="fas fa-edit"></i><span> ' . trans('Update Status') . ' </span>
            </button>';
        $actions .= '</div>';
        return $actions;
    }

    public function updateStatus(int $id, string $status, ?string $notes = null): CartFollowUp
    {
        $followUp = CartFollowUp::findOrFail($id);
        $statuses = ['pending', 'sale', 'no_answer', 'not_interested'];
        if (!in_array($status, $statuses)) {
            throw ValidationException::withMessages([
                'status' => [trans('Invalid status.')],
            ]);
        }

        $followUp->update([
            'status' => $status,
            'notes'  => $notes,
        ]);

        return $followUp;
    }

    public function getAnalysisData(?string $fromDate = null, ?string $toDate = null): array
    {
        $fromDate = $fromDate ?: request('from_date');
        $toDate = $toDate ?: request('to_date');

        $totalQuery = CartFollowUp::query();
        $salesCountQuery = CartFollowUp::where('status', 'sale');
        $avgTimeQuery = \DB::table('cart_follow_ups')
            ->where('status', 'sale')
            ->whereNotNull('followed_up_at')
            ->whereNotNull('order_at');
        
        $statusesQuery = \DB::table('cart_follow_ups')
            ->select('status', \DB::raw('count(id) as count'))
            ->groupBy('status');

        $cityStatsQuery = \DB::table('cart_follow_ups')
            ->leftJoin('carts', 'cart_follow_ups.cart_id', '=', 'carts.id')
            ->leftJoin('users', 'carts.user_id', '=', 'users.id')
            ->leftJoin('profiles', 'users.id', '=', 'profiles.user_id')
            ->leftJoin('cities', 'profiles.city_id', '=', 'cities.id')
            ->leftJoin('city_translations', function($join) {
                $join->on('cities.id', '=', 'city_translations.city_id')
                     ->where('city_translations.locale', '=', config('app.locale', 'ar'));
            })
            ->select('city_translations.name as city_name', \DB::raw('count(cart_follow_ups.id) as total_follow_ups'))
            ->selectRaw('sum(case when cart_follow_ups.status = "sale" then 1 else 0 end) as sales_count')
            ->groupBy('profiles.city_id', 'city_translations.name')
            ->orderByDesc('sales_count')
            ->limit(10);

        $adminStatsQuery = \DB::table('cart_follow_ups')
            ->join('users as admins', 'cart_follow_ups.admin_id', '=', 'admins.id')
            ->select('admins.fullname as admin_name', \DB::raw('count(cart_follow_ups.id) as total_follow_ups'))
            ->selectRaw('sum(case when cart_follow_ups.status = "sale" then 1 else 0 end) as sales_count')
            ->groupBy('cart_follow_ups.admin_id', 'admins.fullname')
            ->orderByDesc('sales_count')
            ->limit(10);

        if ($fromDate) {
            $parsedFrom = Carbon::parse($fromDate)->startOfDay();
            $totalQuery->where('followed_up_at', '>=', $parsedFrom);
            $salesCountQuery->where('followed_up_at', '>=', $parsedFrom);
            $avgTimeQuery->where('followed_up_at', '>=', $parsedFrom);
            $statusesQuery->where('followed_up_at', '>=', $parsedFrom);
            $cityStatsQuery->where('cart_follow_ups.followed_up_at', '>=', $parsedFrom);
            $adminStatsQuery->where('cart_follow_ups.followed_up_at', '>=', $parsedFrom);
        }

        if ($toDate) {
            $parsedTo = Carbon::parse($toDate)->endOfDay();
            $totalQuery->where('followed_up_at', '<=', $parsedTo);
            $salesCountQuery->where('followed_up_at', '<=', $parsedTo);
            $avgTimeQuery->where('followed_up_at', '<=', $parsedTo);
            $statusesQuery->where('followed_up_at', '<=', $parsedTo);
            $cityStatsQuery->where('cart_follow_ups.followed_up_at', '<=', $parsedTo);
            $adminStatsQuery->where('cart_follow_ups.followed_up_at', '<=', $parsedTo);
        }

        $total = $totalQuery->count();
        $salesCount = $salesCountQuery->count();
        $conversionRate = $total > 0 ? round(($salesCount / $total) * 100, 2) : 0;

        // Average Time between Follow-Up and Order (in minutes)
        $avgTimeMinutes = null;
        try {
            $avgTimeSeconds = $avgTimeQuery
                ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, followed_up_at, order_at)) as avg_diff')
                ->value('avg_diff');
            if ($avgTimeSeconds !== null) {
                $avgTimeMinutes = round($avgTimeSeconds / 60, 1);
            }
        } catch (\Throwable $e) {
            $sales = $avgTimeQuery->select(['followed_up_at', 'order_at'])->get();
            if ($sales->count() > 0) {
                $totalDiff = $sales->sum(function($r) {
                    return Carbon::parse($r->order_at)->diffInSeconds(Carbon::parse($r->followed_up_at));
                });
                $avgTimeMinutes = round(($totalDiff / $sales->count()) / 60, 1);
            }
        }

        // Status Distribution
        $statuses = $statusesQuery
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // City Stats (sales by place)
        $cityStats = $cityStatsQuery->get();

        // Best Admins in Sales
        $adminStats = $adminStatsQuery->get();

        // Monthly trend for progress
        try {
            $monthlyTrendQuery = \DB::table('cart_follow_ups')
                ->selectRaw('DATE_FORMAT(followed_up_at, "%Y-%m") as month')
                ->selectRaw('count(id) as total_follow_ups')
                ->selectRaw('sum(case when status = "sale" then 1 else 0 end) as sales_count')
                ->whereNotNull('followed_up_at');
            if ($fromDate) {
                $monthlyTrendQuery->where('followed_up_at', '>=', Carbon::parse($fromDate)->startOfDay());
            }
            if ($toDate) {
                $monthlyTrendQuery->where('followed_up_at', '<=', Carbon::parse($toDate)->endOfDay());
            }
            $monthlyTrend = $monthlyTrendQuery
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->limit(6)
                ->get();
        } catch (\Throwable $e) {
            $monthlyTrendQuery = \DB::table('cart_follow_ups')
                ->selectRaw('strftime("%Y-%m", followed_up_at) as month')
                ->selectRaw('count(id) as total_follow_ups')
                ->selectRaw('sum(case when status = "sale" then 1 else 0 end) as sales_count')
                ->whereNotNull('followed_up_at');
            if ($fromDate) {
                $monthlyTrendQuery->where('followed_up_at', '>=', Carbon::parse($fromDate)->startOfDay());
            }
            if ($toDate) {
                $monthlyTrendQuery->where('followed_up_at', '<=', Carbon::parse($toDate)->endOfDay());
            }
            $monthlyTrend = $monthlyTrendQuery
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->limit(6)
                ->get();
        }

        return [
            'total'          => $total,
            'sales_count'    => $salesCount,
            'conversion_rate'=> $conversionRate,
            'avg_time_mins'  => $avgTimeMinutes,
            'statuses'       => $statuses,
            'city_stats'     => $cityStats,
            'admin_stats'    => $adminStats,
            'monthly_trend'  => $monthlyTrend,
        ];
    }

    public function totalCount(): int
    {
        return CartFollowUp::count();
    }
}
