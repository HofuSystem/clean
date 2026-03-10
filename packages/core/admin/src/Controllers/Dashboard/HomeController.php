<?php

namespace Core\Admin\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Core\Coupons\Services\CouponsService;
use Core\Info\Services\CitiesService;
use Core\Orders\Models\Order;
use Core\Orders\Services\OrderItemsService;
use Core\Orders\Services\OrdersService;
use Core\Products\Services\ProductsService;
use Core\Users\Models\User;
use Core\Users\Services\UsersService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function __construct(
        protected OrdersService $ordersService,
        protected UsersService $usersService,
        protected CouponsService $couponsService,
        protected ProductsService $productsService,
        protected OrderItemsService $orderItemsService,
        protected CitiesService $citiesService
    ) {}

    public function index(Request $request)
    {
        $title = trans('dashboard-home');
        $screen = 'dashboard-home';
        $total = $this->ordersService->totalCount();
        $trash = $this->ordersService->trashCount();
        $clients = $this->usersService->selectable('id', 'fullname');
        $coupons = $this->couponsService->selectable('id', 'code');
        $orders = $this->ordersService->selectable('id', 'reference_id');
        $products = $this->productsService->selectable('id', 'name');
        $representatives = $this->usersService->selectable('id', 'fullname');
        $items = $this->orderItemsService->selectable('id', 'product_id');
        $users = $this->usersService->selectable('id', 'fullname');
        $pendingTypes = Order::select('type')->where('status', 'pending')->groupBy('type')->get()->pluck('type');
        $types = Order::groupBy('type')->select('type')->get()->pluck('type');
        $statuses = Order::groupBy('status')->select('status')->get()->pluck('status');
        $operators = $this->usersService->selectable('id', 'fullname', ['wallet'], 'operator');
        $representatives = $this->usersService->selectable('id', 'fullname', ['wallet'], ['driver', 'technical']);
        $cities = $this->citiesService->selectable('id', 'name');

        return view('admin::pages.index', compact('title', 'screen', 'pendingTypes', 'types', 'operators', 'representatives', 'types', 'statuses', 'clients', 'coupons', 'orders', 'products', 'representatives', 'items', 'users', 'cities', 'total', 'trash'));
    }

    /**
     * Get hourly analysis of order patterns
     */
    private function getOrderHourlyAnalysis($request)
    {
        $query = Order::analysis($request->city_id, $request->from, $request->to, ['delivered', 'finished'])
            ->testAccounts(false);

        $hourlyData = $query->select(
            DB::raw('HOUR(created_at) as hour'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('COUNT(DISTINCT client_id) as unique_customers'),
            DB::raw('COALESCE(SUM(total_price), 0) as total_revenue')
        )
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Fill missing hours with zero counts
        $hourlyAnalysis = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $hourData = $hourlyData->where('hour', $hour)->first();
            $hourlyAnalysis[] = [
                'hour' => $hour,
                'hour_label' => sprintf('%02d:00', $hour),
                'order_count' => $hourData ? $hourData->order_count : 0,
                'unique_customers' => $hourData ? $hourData->unique_customers : 0,
                'total_revenue' => $hourData ? $hourData->total_revenue : 0,
            ];
        }

        // Get top 5 most active hours
        $topActiveHours = collect($hourlyAnalysis)
            ->sortByDesc('order_count')
            ->take(5)
            ->values();

        return [
            'hourly_data' => $hourlyAnalysis,
            'top_active_hours' => $topActiveHours,
        ];
    }

    /**
     * Get daily analysis of order patterns
     */
    private function getOrderDailyAnalysis($request)
    {
        $query = Order::analysis($request->city_id, $request->from, $request->to, ['delivered', 'finished'])
            ->testAccounts(false);

        $dailyData = $query->select(
            DB::raw('DAYOFWEEK(created_at) as day_of_week'),
            DB::raw('DAYNAME(created_at) as day_name'),
            DB::raw('COUNT(*) as order_count'),
            DB::raw('COUNT(DISTINCT client_id) as unique_customers'),
            DB::raw('COALESCE(SUM(total_price), 0) as total_revenue')
        )
            ->groupBy('day_of_week', 'day_name')
            ->orderBy('day_of_week')
            ->get();

        // Map day numbers to names for consistent ordering
        $dayNames = [
            1 => 'Sunday',
            2 => 'Monday',
            3 => 'Tuesday',
            4 => 'Wednesday',
            5 => 'Thursday',
            6 => 'Friday',
            7 => 'Saturday',
        ];

        $dailyAnalysis = [];
        foreach ($dayNames as $dayNum => $dayName) {
            $dayData = $dailyData->where('day_of_week', $dayNum)->first();
            $dailyAnalysis[] = [
                'day_number' => $dayNum,
                'day_name' => $dayName,
                'order_count' => $dayData ? $dayData->order_count : 0,
                'unique_customers' => $dayData ? $dayData->unique_customers : 0,
                'total_revenue' => $dayData ? $dayData->total_revenue : 0,
            ];
        }

        // Get top 3 most active days
        $topActiveDays = collect($dailyAnalysis)
            ->sortByDesc('order_count')
            ->take(3)
            ->values();

        return [
            'daily_data' => $dailyAnalysis,
            'top_active_days' => $topActiveDays,
        ];
    }

    /**
     * Get peak order usage analysis
     */
    private function getOrderPeakUsageAnalysis($request)
    {
        // Get peak hour (hour with most orders)
        $peakHour = Order::analysis($request->city_id, $request->from, $request->to, ['delivered', 'finished'])
            ->testAccounts(false)
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('COALESCE(SUM(total_price), 0) as total_revenue')
            )
            ->groupBy('hour')
            ->orderBy('order_count', 'desc')
            ->first();

        // Get peak day (day with most orders)
        $peakDay = Order::analysis($request->city_id, $request->from, $request->to, ['delivered', 'finished'])
            ->testAccounts(false)
            ->select(
                DB::raw('DAYOFWEEK(created_at) as day_of_week'),
                DB::raw('DAYNAME(created_at) as day_name'),
                DB::raw('COUNT(*) as order_count'),
                DB::raw('COALESCE(SUM(total_price), 0) as total_revenue')
            )
            ->groupBy('day_of_week', 'day_name')
            ->orderBy('order_count', 'desc')
            ->first();

        // Get average orders per hour
        $avgOrdersPerHour = Order::analysis($request->city_id, $request->from, $request->to, ['delivered', 'finished'])
            ->testAccounts(false)
            ->select(DB::raw('COUNT(*) / 24 as avg_orders_per_hour'))
            ->first();

        // Get total orders and revenue
        $totalStats = Order::analysis($request->city_id, $request->from, $request->to, ['delivered', 'finished'])
            ->testAccounts(false)
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('COALESCE(SUM(total_price), 0) as total_revenue'),
                DB::raw('COUNT(DISTINCT client_id) as unique_customers')
            )->first();

        return [
            'peak_hour' => $peakHour ? [
                'hour' => $peakHour->hour,
                'hour_label' => sprintf('%02d:00', $peakHour->hour),
                'order_count' => $peakHour->order_count,
                'total_revenue' => $peakHour->total_revenue,
            ] : null,
            'peak_day' => $peakDay ? [
                'day_name' => $peakDay->day_name,
                'order_count' => $peakDay->order_count,
                'total_revenue' => $peakDay->total_revenue,
            ] : null,
            'avg_orders_per_hour' => $avgOrdersPerHour ? round($avgOrdersPerHour->avg_orders_per_hour, 2) : 0,
            'total_orders' => $totalStats ? $totalStats->total_orders : 0,
            'total_revenue' => $totalStats ? $totalStats->total_revenue : 0,
            'unique_customers' => $totalStats ? $totalStats->unique_customers : 0,
        ];
    }

    public function analysis(Request $request)
    {
        $title = trans('dashboard-home');
        $screen = 'dashboard-home';
        $cities = $this->citiesService->selectable('id', 'name');
        $timePeriod = (isset($request->from) or isset($request->to)) ? trans('from').' '.$request->from.' - '.trans('to').' '.$request->to : null;
        $ordersCount = Order::analysis($request->city_id, $request->from, $request->to)->testAccounts(false)->count();
        $ordersPayTypeCounts = Order::analysis($request->city_id, $request->from, $request->to)->testAccounts(false)
            ->select('pay_type', DB::raw('COUNT(*) as count'))->groupBy('pay_type')->get();
        $ordersStatusCounts = Order::analysis($request->city_id, $request->from, $request->to)->testAccounts(false)
            ->select('status', DB::raw('COUNT(*) as count'))->groupBy('status')->get();
        $ordersTypeCounts = Order::analysis($request->city_id, $request->from, $request->to)->testAccounts(false)
            ->select('type', DB::raw('COUNT(*) as count'))->groupBy('type')->get();

        $orderStatusValues = [
            'canceled' => ['icon' => 'fas fa-ban', 'color' => '#cf1a02'],
            'delivered' => ['icon' => 'fas fa-truck-loading', 'color' => '#cfb702'],
            'finished' => ['icon' => 'fas fa-clipboard-list', 'color' => '#03ad03'],
            'issue' => ['icon' => 'fas fa-exclamation-triangle', 'color' => '#cf1a02'],
            'pending' => ['icon' => 'fas fa-calendar-plus', 'color' => '#b3b2b1'],
            'receiving_driver_accepted' => ['icon' => 'fas fa-calendar-plus', 'color' => '#b3b2b1'],
            'in_the_way' => ['icon' => 'fas fa-user-cog', 'color' => '#2d91b5'],
            'order_has_been_delivered_to_admin' => ['icon' => 'fas fa-user-cog', 'color' => '#03ad03'],
            'pending_payment' => ['icon' => 'fas fa-user-cog', 'color' => '#f2f21f'],
            'failed_payment' => ['icon' => 'fas fa-user-cog', 'color' => '#cf1a02'],
            'cancel_payment' => ['icon' => 'fas fa-user-cog', 'color' => '#cf1a02'],
        ];
        $ordersStatusCounts->map(function ($item) use ($orderStatusValues, $ordersCount) {
            $value = $orderStatusValues[$item->status] ?? null;
            if ($value) {
                $item->color = $value['color'];
                $item->icon = $value['icon'];
            }
            $item->percentage = $item->count / $ordersCount * 100;
            $item->label = trans($item->status);

            return $item;
        });
        $orderTypesValues = [
            'clothes' => ['icon' => 'fas fa-tshirt', 'color' => '#32a852'],
            'fastorder' => ['icon' => 'fas fa-shipping-fast', 'color' => '#f2f21f'],
            'maidflex' => ['icon' => 'fas fa-female', 'color' => '#1f97f2'],
            'maidoffer' => ['icon' => 'fas fa-female', 'color' => '#1ff2c8'],
            'sales' => ['icon' => 'fas fa-project-diagram', 'color' => '#891ff2'],
            'selfcare' => ['icon' => 'fas fa-user', 'color' => '#1f7ef2'],
            'services' => ['icon' => 'fab fa-servicestack', 'color' => '#ff9419'],
            'care' => ['icon' => 'fab fa-servicestack', 'color' => '#2d91b5'],
            'maidscheduled' => ['icon' => 'fab fa-servicestack', 'color' => '#2d91b5'],
        ];
        $ordersTypeCounts->map(function ($item) use ($orderTypesValues, $ordersCount) {
            $value = $orderTypesValues[$item->type] ?? null;
            if ($value) {
                $item->color = $value['color'];
                $item->icon = $value['icon'];
            }
            $item->percentage = $item->count / $ordersCount * 100;
            $item->label = trans($item->type);

            return $item;
        });
        $orderPayTypesValues = [
            'card' => ['icon' => 'fas fa-tshirt', 'color' => '#32a852'],
            'cash' => ['icon' => 'fas fa-shipping-fast', 'color' => '#f2f21f'],
            'wallet' => ['icon' => 'fas fa-wallet', 'color' => '#2d91b5'],
        ];
        $ordersPayTypeCounts->map(function ($item) use ($orderPayTypesValues, $ordersCount) {
            $value = $orderPayTypesValues[$item->pay_type] ?? null;
            if ($value) {
                $item->color = $value['color'];
                $item->icon = $value['icon'];
            }
            $item->percentage = $item->count / $ordersCount * 100;
            $item->pay_type = trans($item->pay_type);
            $item->label = trans($item->pay_type);

            return $item;
        });

        $ordersAnalysis = Order::analysis($request->city_id, $request->from, $request->to, ['delivered', 'finished'])
            ->testAccounts(false)
            ->selectRaw('type,COUNT(*)as type_count,AVG(total_price)as order_average,SUM(total_price)as total_revenue,SUM(total_cost)as total_cost,SUM(total_coupon)as total_discount,SUM(delivery_price)as total_delivery')
            ->groupBy('type')
            ->get()
            ->map(function ($item) {
                $item->type = trans($item->type);
                $item->order_average = number_format($item->order_average, 2);
                $item->total_revenue = number_format($item->total_revenue, 2);
                $item->total_cost = number_format($item->total_cost, 2);
                $item->total_discount = number_format($item->total_discount, 2);
                $item->total_delivery = number_format($item->total_delivery, 2);

                return $item;
            });
        $ordersRepresentativeAnalysis = User::query()
            ->underMyControl()
            ->has('representativeOrders')
            ->withCount([
                'representativeOrders as count_orders' => function ($query) use ($request) {
                    $query->analysis($request->city_id, null, null)
                        ->where('order_representatives.type', 'delivery')
                        ->when(isset($request->from), function ($query) use ($request) {
                            $query->where('order_representatives.date', '>=', $request->from);
                        })
                        ->when(isset($request->to), function ($query) use ($request) {
                            $query->where('order_representatives.date', '<=', $request->to);
                        })
                        ->testAccounts(false);
                },
                'representativeOrders as count_finished_orders' => function ($query) use ($request) {
                    $query->analysis($request->city_id, null, null, ['delivered', 'finished'])
                        ->where('order_representatives.type', 'delivery')
                        ->when(isset($request->from), function ($query) use ($request) {
                            $query->where('order_representatives.date', '>=', $request->from);
                        })
                        ->when(isset($request->to), function ($query) use ($request) {
                            $query->where('order_representatives.date', '<=', $request->to);
                        })
                        ->testAccounts(false);
                },
                'representativeOrders as count_issue_orders' => function ($query) use ($request) {
                    $query->analysis($request->city_id, null, null, ['issue'])
                        ->where('order_representatives.type', 'delivery')
                        ->when(isset($request->from), function ($query) use ($request) {
                            $query->where('order_representatives.date', '>=', $request->from);
                        })
                        ->when(isset($request->to), function ($query) use ($request) {
                            $query->where('order_representatives.date', '<=', $request->to);
                        })
                        ->testAccounts(false);
                },
                'representativeOrders as count_canceled_orders' => function ($query) use ($request) {
                    $query->analysis($request->city_id, null, null, ['canceled'])
                        ->where('order_representatives.type', 'delivery')
                        ->when(isset($request->from), function ($query) use ($request) {
                            $query->where('order_representatives.date', '>=', $request->from);
                        })
                        ->when(isset($request->to), function ($query) use ($request) {
                            $query->where('order_representatives.date', '<=', $request->to);
                        })
                        ->testAccounts(false);
                },
            ])
            ->withSum(['representativeOrders as total_orders' => function ($query) use ($request) {
                $query->analysis($request->city_id, null, null, ['delivered', 'finished'])
                    ->where('order_representatives.type', 'delivery')
                    ->when(isset($request->from), function ($query) use ($request) {
                        $query->where('order_representatives.date', '>=', $request->from);
                    })
                    ->when(isset($request->to), function ($query) use ($request) {
                        $query->where('order_representatives.date', '<=', $request->to);
                    })
                    ->testAccounts(false);
            }], 'total_price')
            ->get();
        $operatorsAnalysis = User::query()
            ->underMyControl()
            ->has('operatorOrders')
            ->withCount([
                'operatorOrders as count_orders' => function ($query) use ($request) {
                    $query->analysis($request->city_id, null, null)
                        ->whereHas('orderRepresentatives', function ($query) use ($request) {
                            $query->where('type', 'delivery')
                                ->when(isset($request->from), function ($query) use ($request) {
                                    $query->where('order_representatives.date', '>=', $request->from);
                                })
                                ->when(isset($request->to), function ($query) use ($request) {
                                    $query->where('order_representatives.date', '<=', $request->to);
                                });
                        })
                        ->testAccounts(false);
                },
                'operatorOrders as count_finished_orders' => function ($query) use ($request) {
                    $query->analysis($request->city_id, null, null, ['delivered', 'finished'])
                        ->whereHas('orderRepresentatives', function ($query) use ($request) {
                            $query->where('type', 'delivery')
                                ->when(isset($request->from), function ($query) use ($request) {
                                    $query->where('order_representatives.date', '>=', $request->from);
                                })
                                ->when(isset($request->to), function ($query) use ($request) {
                                    $query->where('order_representatives.date', '<=', $request->to);
                                });
                        })
                        ->testAccounts(false);
                },
                'operatorOrders as count_issue_orders' => function ($query) use ($request) {
                    $query->analysis($request->city_id, null, null, ['issue'])
                        ->whereHas('orderRepresentatives', function ($query) use ($request) {
                            $query->where('type', 'delivery')
                                ->when(isset($request->from), function ($query) use ($request) {
                                    $query->where('order_representatives.date', '>=', $request->from);
                                })
                                ->when(isset($request->to), function ($query) use ($request) {
                                    $query->where('order_representatives.date', '<=', $request->to);
                                });
                        })
                        ->testAccounts(false);
                },
                'operatorOrders as count_canceled_orders' => function ($query) use ($request) {
                    $query->analysis($request->city_id, null, null, ['canceled'])
                        ->whereHas('orderRepresentatives', function ($query) use ($request) {
                            $query->where('type', 'delivery')
                                ->when(isset($request->from), function ($query) use ($request) {
                                    $query->where('order_representatives.date', '>=', $request->from);
                                })
                                ->when(isset($request->to), function ($query) use ($request) {
                                    $query->where('order_representatives.date', '<=', $request->to);
                                });
                        })
                        ->testAccounts(false);
                },
            ])
            ->withSum(['operatorOrders as total_orders' => function ($query) use ($request) {
                $query->analysis($request->city_id, null, null, ['delivered', 'finished'])
                    ->whereHas('orderRepresentatives', function ($query) use ($request) {
                        $query->where('type', 'delivery')
                            ->when(isset($request->from), function ($query) use ($request) {
                                $query->where('order_representatives.date', '>=', $request->from);
                            })
                            ->when(isset($request->to), function ($query) use ($request) {
                                $query->where('order_representatives.date', '<=', $request->to);
                            });
                    })
                    ->testAccounts(false);
            }], 'total_price')
            ->get();
        $revenuesAnalysis = Order::analysis($request->city_id, null, null, ['delivered', 'finished'])
            ->join('order_representatives', 'orders.id', '=', 'order_representatives.order_id')
            ->where('order_representatives.type', 'delivery')
            ->when(isset($request->from), function ($query) use ($request) {
                $query->where('order_representatives.date', '>=', $request->from);
            })
            ->when(isset($request->to), function ($query) use ($request) {
                $query->where('order_representatives.date', '<=', $request->to);
            })
            ->testAccounts(false)
            ->selectRaw('order_representatives.date as date, COUNT(orders.id) as count, COALESCE(SUM(orders.order_price), 0) as total, COALESCE(SUM(orders.total_cost), 0) as cost')
            ->groupBy('order_representatives.date')
            ->orderBy('order_representatives.date', 'asc')
            ->get();
        // Monthly Analysis - 12 months data showing revenue, cost, and net profit
        $currentYear = date('Y');
        $monthlyAnalysis = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = $currentYear.'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-01';
            $endDate = date('Y-m-t', strtotime($startDate));

            $monthData = Order::analysis($request->city_id, null, null, ['delivered', 'finished'])
                ->whereHas('orderRepresentatives', function ($query) use ($startDate, $endDate) {
                    $query->where('type', 'delivery')
                        ->whereBetween('date', [$startDate, $endDate]);
                })
                ->testAccounts(false)
                ->selectRaw('
                    COUNT(*) as orders_count,
                    COALESCE(SUM(total_price), 0) as total_revenue,
                    COALESCE(SUM(total_cost), 0) as total_cost,
                    COALESCE(SUM(total_price - total_cost), 0) as total_profit
                ')
                ->first();

            $monthName = date('F', mktime(0, 0, 0, $month, 1));
            $monthAbbr = date('M', mktime(0, 0, 0, $month, 1));

            // Calculate profit percentage
            $profitPercentage = $monthData->total_revenue > 0 ?
                (($monthData->total_profit / $monthData->total_revenue) * 100) : 0;

            // Determine profit color based on positive/negative profit
            $profitColor = $monthData->total_profit >= 0 ? '#03ad03' : '#cf1a02';

            $monthlyAnalysis[] = [
                'month' => $month,
                'month_name' => $monthName,
                'month_abbr' => $monthAbbr,
                'orders_count' => $monthData->orders_count,
                'total_revenue' => number_format($monthData->total_revenue, 2),
                'total_cost' => number_format($monthData->total_cost, 2),
                'total_profit' => number_format($monthData->total_profit, 2),
                'profit_percentage' => number_format($profitPercentage, 2),
                'profit_color' => $profitColor,
            ];
        }

        // Get order pattern analysis
        $orderHourlyAnalysis = $this->getOrderHourlyAnalysis($request);
        $orderDailyAnalysis = $this->getOrderDailyAnalysis($request);
        $orderPeakUsageAnalysis = $this->getOrderPeakUsageAnalysis($request);

        return view('admin::pages.analysis', compact(
            'title',
            'screen',
            'cities',
            'timePeriod',
            'ordersCount',
            'ordersStatusCounts',
            'ordersTypeCounts',
            'ordersPayTypeCounts',
            'ordersAnalysis',
            'ordersRepresentativeAnalysis',
            'operatorsAnalysis',
            'revenuesAnalysis',
            'monthlyAnalysis',
            'orderHourlyAnalysis',
            'orderDailyAnalysis',
            'orderPeakUsageAnalysis',
        ));
    }
}
