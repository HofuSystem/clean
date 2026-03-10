<?php

namespace Core\Admin\Services;

use Core\Users\Models\User;
use Core\Users\Models\Device;
use Core\Users\Models\Role;
use Core\Orders\Models\Order;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserAnalyticsService
{
    /**
     * Get user registration analysis with time period filtering
     */
    public function getUserRegistrationAnalysis($request)
    {

        // Get total user count
        $totalUsers = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)->count();

        // Get users by roles with order types
        $usersByRoles = Role::withCount(['users' => function ($query) use ($request) {
            $query->analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to);
        }])
            ->with(['users' => function ($query) use ($request) {
                $query->analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to);
            }])
            ->get()
            ->map(function ($role) {
                return [
                    'role' => $role->name,
                    'count' => $role->users_count,
                    'users' => $role->users
                ];
            })
            ->filter(function ($item) {
                return $item['count'] > 0;
            })
            ->sortByDesc('count');

        // Get role growth data for charts (monthly over the year)
        $roleGrowthData = [];
        $roles = Role::all();

        foreach ($roles as $role) {
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                $startDate = date('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));

                $count = User::whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count();

                $monthlyData[] = [
                    'month' => $month,
                    'month_name' => date('M', mktime(0, 0, 0, $month, 1)),
                    'count' => $count
                ];
            }

            $roleGrowthData[] = [
                'role' => $role->name,
                'role_title' => $role->title,
                'data' => $monthlyData,
                'total' => array_sum(array_column($monthlyData, 'count'))
            ];
        }

        return [
            'total_users' => $totalUsers,
            'users_by_roles' => $usersByRoles,
            'role_growth_data' => $roleGrowthData
        ];
    }

    /**
     * Get profile completion analysis
     */
    public function getProfileCompletionAnalysis($request)
    {
        $totalUsers = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)->count();

        // Define profile completion criteria
        $completeProfileUsers = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)->whereNotNull('fullname')
            ->has('profile')
            ->count();

        $incompleteProfileUsers = $totalUsers - $completeProfileUsers;

        return [
            'complete_profile' => [
                'count' => $completeProfileUsers,
                'percentage' => $totalUsers > 0 ? round(($completeProfileUsers / $totalUsers) * 100, 2) : 0
            ],
            'incomplete_profile' => [
                'count' => $incompleteProfileUsers,
                'percentage' => $totalUsers > 0 ? round(($incompleteProfileUsers / $totalUsers) * 100, 2) : 0
            ],
            'total_users' => $totalUsers
        ];
    }

    /**
     * Get device platform analysis (Android/iOS)
     */
    public function getDevicePlatformAnalysis($request)
    {
        $query = Device::whereHas('user', function ($userQuery) use ($request) {
            $userQuery->analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to);
        });
        $totalDevices = $query->count();
        // Get device counts by platform
        $deviceCounts = $query->select('type', DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->get()
            ->keyBy('type');


        // Get platform growth data (monthly)
        $platformGrowthData = [];
        $platforms = ['android', 'ios', 'huawei'];

        foreach ($platforms as $platform) {
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                $startDate = date('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));

                $count = Device::where('type', $platform)
                    ->whereHas('user', function ($userQuery) use ($startDate, $endDate) {
                        $userQuery->analysis($startDate, $endDate);
                    })
                    ->count();

                $monthlyData[] = [
                    'month' => $month,
                    'month_name' => date('M', mktime(0, 0, 0, $month, 1)),
                    'count' => $count
                ];
            }

            $platformGrowthData[] = [
                'platform' => $platform,
                'data' => $monthlyData,
                'total' => array_sum(array_column($monthlyData, 'count'))
            ];
        }

        // Prepare device counts data for sorting
        $deviceCountsData = [
            'android' => [
                'count' => $deviceCounts->get('android')->count ?? 0,
                'percentage' => $totalDevices > 0 ? round((($deviceCounts->get('android')->count ?? 0) / $totalDevices) * 100, 2) : 0
            ],
            'ios' => [
                'count' => $deviceCounts->get('ios')->count ?? 0,
                'percentage' => $totalDevices > 0 ? round((($deviceCounts->get('ios')->count ?? 0) / $totalDevices) * 100, 2) : 0
            ],
            'huawei' => [
                'count' => $deviceCounts->get('huawei')->count ?? 0,
                'percentage' => $totalDevices > 0 ? round((($deviceCounts->get('huawei')->count ?? 0) / $totalDevices) * 100, 2) : 0
            ]
        ];

        // Sort by count in descending order
        uasort($deviceCountsData, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        return [
            'device_counts' => $deviceCountsData,
            'total_devices' => $totalDevices,
            'platform_growth_data' => $platformGrowthData
        ];
    }

    /**
     * Get user order analytics
     */
    public function getUserOrderAnalysis($request)
    {
        $totalUsers = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)->count();

        // Get order counts by types
        $orderTypeCounts = Order::select('type', DB::raw('COUNT(*) as count'))
            ->whereHas('client', function ($userQuery) use ($request) {
                $userQuery->analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to);
            })
            ->groupBy('type')
            ->get()
            ->keyBy('type');

        // Get order type growth data (monthly)
        $orderTypeGrowthData = [];
        $orderTypes = Order::select('type', DB::raw('COUNT(*) as count'))
            ->whereHas('client', function ($userQuery) use ($request) {
                $userQuery->analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to);
            })
            ->groupBy('type')
            ->orderBy('count', 'desc')
            ->pluck('type');

        foreach ($orderTypes as $type) {
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                $startDate = date('Y') . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
                $endDate = date('Y-m-t', strtotime($startDate));

                $count = Order::where('type', $type)
                    ->whereHas('client', function ($userQuery) use ($startDate, $endDate) {
                        $userQuery->analysis($startDate, $endDate);
                    })
                    ->count();

                $monthlyData[] = [
                    'month' => $month,
                    'month_name' => date('M', mktime(0, 0, 0, $month, 1)),
                    'count' => $count
                ];
            }

            $orderTypeGrowthData[] = [
                'type' => $type,
                'data' => $monthlyData,
                'total' => array_sum(array_column($monthlyData, 'count'))
            ];
        }

        // Get user order status analytics
        $usersWithOrders = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
            ->whereHas('orders', function ($q) {
                $q->whereIn('status', ['delivered', 'finished']);
            })->count();

        $usersWithoutOrders = $totalUsers - $usersWithOrders;

        $usersWithCanceledOrders = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
            ->whereHas('orders', function ($q) {
                $q->where('status', 'canceled');
            })->count();

        $usersWithPendingOrders = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
            ->whereHas('orders', function ($q) {
                $q->whereIn('status', ['pending', 'order_has_been_delivered_to_admin','receiving_driver_accepted','ready_to_delivered','in_the_way']);
            })->count();

        return [
            'order_type_counts' => $orderTypeCounts,
            'order_type_growth_data' => $orderTypeGrowthData,
            'user_order_status' => [
                'total_users' => $totalUsers,
                'users_with_orders' => $usersWithOrders,
                'users_without_orders' => $usersWithoutOrders,
                'users_with_canceled_orders' => $usersWithCanceledOrders,
                'users_with_pending_orders' => $usersWithPendingOrders
            ]
        ];
    }

    /**
     * Get notification capability analysis
     */
    public function getNotificationAnalysis($request)
    {
        // Get users by order status and notification capability grouped by roles
        $notificationData = [];
        $roles              = Role::withCount(['users' => function ($query) use ($request) {
            $query->analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to);
        }])
            ->whereHas('users', function ($query) use ($request) {
                $query->analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to);
            })
            ->get()->filter(function ($role) {
                return $role->users_count > 0;
            });
        foreach ($roles as $role) {
            $totalUsers = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })->count();
            //has ordered
            $hasOrderTotal = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })->whereHas('orders', function ($q) {
                    $q->whereIn('status', ['delivered', 'finished']);
                })->count();

            $hasOrderedNotifiableFcmIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
            ->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role->name);
            })
            ->whereHas('orders', function ($q) {
                $q->whereIn('status', ['delivered', 'finished']);
            })->whereHas('devices', function ($q) {
                $q->whereNotNull('device_token');
            })->pluck('id');
            $hasOrderedNotifiableWhatsappIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
            ->whereNotIn('id', $hasOrderedNotifiableFcmIds)
            ->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role->name);
            })
            ->whereHas('orders', function ($q) {
                $q->whereIn('status', ['delivered', 'finished']);
            })->whereNotNull('phone')->pluck('id');
            $hasOrderedNotifiableFcm        = $hasOrderedNotifiableFcmIds->count();
            $hasOrderedNotifiableWhatsapp   = $hasOrderedNotifiableWhatsappIds->count();
            $hasOrderedNotifiable           = $hasOrderedNotifiableFcm + $hasOrderedNotifiableWhatsapp;
            $hasOrderedNotNotifiable         = $hasOrderTotal - $hasOrderedNotifiable;


            //has not ordered notifiable
            $hasNotOrderedTotal = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->count();
            $hasNotOrderedNotifiableFcmIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->whereHas('devices', function ($q) {
                    $q->whereNotNull('device_token');
                })->pluck('id');
                
            // Debug the specific query to see what's happening
            $hasNotOrderedNotifiableWhatsappIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereNotIn('users.id', $hasNotOrderedNotifiableFcmIds)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->whereNotNull('phone')->pluck('id');

           

            $hasNotOrderedNotifiableFcm     = $hasNotOrderedNotifiableFcmIds->count();
            $hasNotOrderedNotifiableWhatsapp = $hasNotOrderedNotifiableWhatsappIds->count();
            $hasNotOrderedNotifiable        = $hasNotOrderedNotifiableFcm + $hasNotOrderedNotifiableWhatsapp;
            $hasNotOrderedNotNotifiable      = $hasNotOrderedTotal - $hasNotOrderedNotifiable;

            //has not ordered with profile
            $hasNotOrderedWithProfileTotal = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->whereNotNull('fullname')
                ->whereHas('profile')
                ->count();
                
            $hasNotOrderedWithProfileNotifiableFcmIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->whereNotNull('fullname')
                ->whereHas('profile')
                ->whereHas('devices', function ($q) {
                    $q->whereNotNull('device_token');
                })->pluck('id');
                
            $hasNotOrderedWithProfileNotifiableWhatsappIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereNotIn('users.id', $hasNotOrderedWithProfileNotifiableFcmIds)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->whereNotNull('fullname')
                ->whereHas('profile')
                ->whereNotNull('phone')->pluck('id');

            $hasNotOrderedWithProfileNotifiableFcm     = $hasNotOrderedWithProfileNotifiableFcmIds->count();
            $hasNotOrderedWithProfileNotifiableWhatsapp = $hasNotOrderedWithProfileNotifiableWhatsappIds->count();
            $hasNotOrderedWithProfileNotifiable        = $hasNotOrderedWithProfileNotifiableFcm + $hasNotOrderedWithProfileNotifiableWhatsapp;
            $hasNotOrderedWithProfileNotNotifiable      = $hasNotOrderedWithProfileTotal - $hasNotOrderedWithProfileNotifiable;

            //has not ordered without profile
            $hasNotOrderedWithoutProfileTotal = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->where(function ($q) {
                    $q->whereNull('fullname')->orWhereDoesntHave('profile');
                })
                ->count();
                
            $hasNotOrderedWithoutProfileNotifiableFcmIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->where(function ($q) {
                    $q->whereNull('fullname')->orWhereDoesntHave('profile');
                })
                ->whereHas('devices', function ($q) {
                    $q->whereNotNull('device_token');
                })->pluck('id');
                
            $hasNotOrderedWithoutProfileNotifiableWhatsappIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereNotIn('users.id', $hasNotOrderedWithoutProfileNotifiableFcmIds)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereDoesntHave('orders')
                ->where(function ($q) {
                    $q->whereNull('fullname')->orWhereDoesntHave('profile');
                })
                ->whereNotNull('phone')->pluck('id');

            $hasNotOrderedWithoutProfileNotifiableFcm     = $hasNotOrderedWithoutProfileNotifiableFcmIds->count();
            $hasNotOrderedWithoutProfileNotifiableWhatsapp = $hasNotOrderedWithoutProfileNotifiableWhatsappIds->count();
            $hasNotOrderedWithoutProfileNotifiable        = $hasNotOrderedWithoutProfileNotifiableFcm + $hasNotOrderedWithoutProfileNotifiableWhatsapp;
            $hasNotOrderedWithoutProfileNotNotifiable      = $hasNotOrderedWithoutProfileTotal - $hasNotOrderedWithoutProfileNotifiable;

            //has order halfway notifiable
            $hasOrderHalfwayTotal = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereHas('orders', function ($q) {
                    $q->whereIn('status', ['pending', 'order_has_been_delivered_to_admin','receiving_driver_accepted','ready_to_delivered','in_the_way']);
                })->count();
            $hasOrderHalfwayNotifiableFcmIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereHas('orders', function ($q) {
                    $q->whereIn('status', ['pending', 'order_has_been_delivered_to_admin','receiving_driver_accepted','ready_to_delivered','in_the_way']);
                })->whereHas('devices', function ($q) {
                    $q->whereNotNull('device_token');
                })->pluck('id');
            $hasOrderHalfwayNotifiableWhatsappIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereNotIn('id', $hasOrderHalfwayNotifiableFcmIds)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereHas('orders', function ($q) {
                    $q->whereIn('status', ['pending', 'order_has_been_delivered_to_admin','receiving_driver_accepted','ready_to_delivered','in_the_way']);
                })->whereNotNull('phone')->pluck('id');
            $hasOrderHalfwayNotifiableFcm       = $hasOrderHalfwayNotifiableFcmIds->count();
            $hasOrderHalfwayNotifiableWhatsapp  = $hasOrderHalfwayNotifiableWhatsappIds->count();
            $hasOrderHalfwayNotifiable          = $hasOrderHalfwayNotifiableFcm + $hasOrderHalfwayNotifiableWhatsapp;
            $hasOrderHalfwayNotNotifiable        = $hasOrderHalfwayTotal - $hasOrderHalfwayNotifiable;

            //has order canceled notifiable
            $hasOrderCanceledTotal = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereHas('orders', function ($q) {
                    $q->where('status', 'canceled');
                })->count();

            $hasOrderCanceledNotifiableFcmIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereHas('orders', function ($q) {
                    $q->where('status', 'canceled');
                })->whereHas('devices', function ($q) {
                    $q->whereNotNull('device_token');
                })->pluck('id');
            $hasOrderCanceledNotifiableWhatsappIds = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)
                ->whereNotIn('id', $hasOrderCanceledNotifiableFcmIds)
                ->whereHas('roles', function ($q) use ($role) {
                    $q->where('name', $role->name);
                })
                ->whereHas('orders', function ($q) {
                    $q->where('status', 'canceled');
                })->whereNotNull('phone')->pluck('id');
            $hasOrderCanceledNotifiableFcm      = $hasOrderCanceledNotifiableFcmIds->count();
            $hasOrderCanceledNotifiableWhatsapp = $hasOrderCanceledNotifiableWhatsappIds->count();
            $hasOrderCanceledNotifiable         = $hasOrderCanceledNotifiableFcm + $hasOrderCanceledNotifiableWhatsapp;
            $hasOrderCanceledNotNotifiable       = $hasOrderCanceledTotal - $hasOrderCanceledNotifiable;

            $notificationData[] = [
                'role' => $role->name,
                'role_title' => $role->title,
                'data' => [
                    'has_ordered_notifiable' => [
                        'type' => 'has_ordered',
                        'title' => trans('Users with Orders'),
                        'color' => 'success',
                        'icon' => 'fas fa-shopping-cart',
                        'fcm_ids' => $hasOrderedNotifiableFcmIds,
                        'whatsapp_ids' => $hasOrderedNotifiableWhatsappIds,
                        'total' => $hasOrderTotal,
                        'notifiable_fcm' => $hasOrderedNotifiableFcm,
                        'notifiable_percentage_whatsapp' => $hasOrderTotal > 0 ? round(($hasOrderedNotifiableWhatsapp / $hasOrderTotal) * 100, 2) : 0,
                        'notifiable_whatsapp' => $hasOrderedNotifiableWhatsapp,
                        'notifiable_percentage_fcm' => $hasOrderTotal > 0 ? round(($hasOrderedNotifiableFcm / $hasOrderTotal) * 100, 2) : 0,
                        'not_notifiable' => $hasOrderedNotNotifiable,
                        'not_notifiable_percentage' => $hasOrderedNotNotifiable > 0 ? round(($hasOrderedNotNotifiable / $hasOrderTotal) * 100, 2) : 0
                    ],
                    'has_not_ordered_with_profile_notifiable' => [
                        'type' => 'has_not_ordered_with_profile',
                        'title' => trans('Users without Orders (With Profile)'),
                        'color' => 'info',
                        'icon' => 'fas fa-user-check',
                        'fcm_ids' => $hasNotOrderedWithProfileNotifiableFcmIds,
                        'whatsapp_ids' => $hasNotOrderedWithProfileNotifiableWhatsappIds,
                        'total' => $hasNotOrderedWithProfileTotal,
                        'notifiable_fcm' => $hasNotOrderedWithProfileNotifiableFcm,
                        'notifiable_fcm_ids' => $hasNotOrderedWithProfileNotifiableFcmIds,
                        'notifiable_whatsapp' => $hasNotOrderedWithProfileNotifiableWhatsapp,
                        'notifiable_percentage_whatsapp' => $hasNotOrderedWithProfileTotal > 0 ? round(($hasNotOrderedWithProfileNotifiableWhatsapp / $hasNotOrderedWithProfileTotal) * 100, 2) : 0,
                        'notifiable_whatsapp_ids' => $hasNotOrderedWithProfileNotifiableWhatsappIds,
                        'notifiable_percentage_fcm' => $hasNotOrderedWithProfileTotal > 0 ? round(($hasNotOrderedWithProfileNotifiableFcm / $hasNotOrderedWithProfileTotal) * 100, 2) : 0,
                        'not_notifiable' => $hasNotOrderedWithProfileNotNotifiable,
                        'not_notifiable_percentage' => $hasNotOrderedWithProfileNotNotifiable > 0 ? round(($hasNotOrderedWithProfileNotNotifiable / $hasNotOrderedWithProfileTotal) * 100, 2) : 0
                    ],
                    'has_not_ordered_without_profile_notifiable' => [
                        'type' => 'has_not_ordered_without_profile',
                        'title' => trans('Users without Orders (Without Profile)'),
                        'color' => 'secondary',
                        'icon' => 'fas fa-user-times',
                        'fcm_ids' => $hasNotOrderedWithoutProfileNotifiableFcmIds,
                        'whatsapp_ids' => $hasNotOrderedWithoutProfileNotifiableWhatsappIds,
                        'total' => $hasNotOrderedWithoutProfileTotal,
                        'notifiable_fcm' => $hasNotOrderedWithoutProfileNotifiableFcm,
                        'notifiable_fcm_ids' => $hasNotOrderedWithoutProfileNotifiableFcmIds,
                        'notifiable_whatsapp' => $hasNotOrderedWithoutProfileNotifiableWhatsapp,
                        'notifiable_percentage_whatsapp' => $hasNotOrderedWithoutProfileTotal > 0 ? round(($hasNotOrderedWithoutProfileNotifiableWhatsapp / $hasNotOrderedWithoutProfileTotal) * 100, 2) : 0,
                        'notifiable_whatsapp_ids' => $hasNotOrderedWithoutProfileNotifiableWhatsappIds,
                        'notifiable_percentage_fcm' => $hasNotOrderedWithoutProfileTotal > 0 ? round(($hasNotOrderedWithoutProfileNotifiableFcm / $hasNotOrderedWithoutProfileTotal) * 100, 2) : 0,
                        'not_notifiable' => $hasNotOrderedWithoutProfileNotNotifiable,
                        'not_notifiable_percentage' => $hasNotOrderedWithoutProfileNotNotifiable > 0 ? round(($hasNotOrderedWithoutProfileNotNotifiable / $hasNotOrderedWithoutProfileTotal) * 100, 2) : 0
                    ],
                    'has_order_halfway_notifiable' => [
                        'type' => 'has_order_halfway',
                        'title' => trans('Users with Pending Orders'),
                        'color' => 'warning',
                        'icon' => 'fas fa-hourglass-half',
                        'fcm_ids' => $hasOrderHalfwayNotifiableFcmIds,
                        'whatsapp_ids' => $hasOrderHalfwayNotifiableWhatsappIds,
                        'total' => $hasOrderHalfwayTotal,
                        'notifiable_fcm' => $hasOrderHalfwayNotifiableFcm,
                        'notifiable_fcm_ids' => $hasOrderHalfwayNotifiableFcmIds,
                        'notifiable_percentage_fcm' => $hasOrderHalfwayTotal > 0 ? round(($hasOrderHalfwayNotifiableFcm / $hasOrderHalfwayTotal) * 100, 2) : 0,
                        'notifiable_whatsapp' => $hasOrderHalfwayNotifiableWhatsapp,
                        'notifiable_whatsapp_ids' => $hasOrderHalfwayNotifiableWhatsappIds,
                        'notifiable_percentage_whatsapp' => $hasOrderHalfwayTotal > 0 ? round(($hasOrderHalfwayNotifiableWhatsapp / $hasOrderHalfwayTotal) * 100, 2) : 0,
                        'not_notifiable' => $hasOrderHalfwayNotNotifiable,
                        'not_notifiable_percentage' => $hasOrderHalfwayTotal > 0 ? round(($hasOrderHalfwayNotNotifiable / $hasOrderHalfwayTotal) * 100, 2) : 0
                    ],
                    'has_order_canceled_notifiable' => [
                        'type' => 'has_order_canceled',
                        'title' => trans('Users with Canceled Orders'),
                        'color' => 'danger',
                        'icon' => 'fas fa-times',
                        'fcm_ids' => $hasOrderCanceledNotifiableFcmIds,
                        'whatsapp_ids' => $hasOrderCanceledNotifiableWhatsappIds,
                        'total' => $hasOrderCanceledTotal,
                        'notifiable_fcm' => $hasOrderCanceledNotifiableFcm,
                        'notifiable_fcm_ids' => $hasOrderCanceledNotifiableFcmIds,
                        'notifiable_percentage_fcm' => $hasOrderCanceledTotal > 0 ? round(($hasOrderCanceledNotifiableFcm / $hasOrderCanceledTotal) * 100, 2) : 0,
                        'notifiable_whatsapp' => $hasOrderCanceledNotifiableWhatsapp,
                        'notifiable_whatsapp_ids' => $hasOrderCanceledNotifiableWhatsappIds,
                        'notifiable_percentage_whatsapp' => $hasOrderCanceledTotal > 0 ? round(($hasOrderCanceledNotifiableWhatsapp / $hasOrderCanceledTotal) * 100, 2) : 0,
                        'not_notifiable' => $hasOrderCanceledNotNotifiable,
                        'not_notifiable_percentage' => $hasOrderCanceledTotal > 0 ? round(($hasOrderCanceledNotNotifiable / $hasOrderCanceledTotal) * 100, 2) : 0
                    ],
                ],
                'total_users' => $totalUsers
            ];
        }

        return $notificationData;
    }

    /**
     * Get exportable user data for data table
     */
    public function getExportableUserData($request)
    {
        $query = User::analysis($request->from, $request->to, $request->city_id,$request->did_not_order_from,$request->did_not_order_to,$request->did_not_appear_from,$request->did_not_appear_to)->with(['roles', 'orders', 'devices', 'profile']);

        $users = $query->get()->map(function ($user) {
            $profileComplete = !empty($user->fullname) && $user->profile;

            $orderStatus = 'no_orders';
            if ($user->orders->count() > 0) {
                $hasFinished = $user->orders->whereIn('status', ['delivered', 'finished'])->count() > 0;
                $hasPending = $user->orders->whereIn('status', ['pending', 'order_has_been_delivered_to_admin','receiving_driver_accepted','ready_to_delivered','in_the_way'])->count() > 0;
                $hasCanceled = $user->orders->where('status', 'canceled')->count() > 0;

                if ($hasFinished) {
                    $orderStatus = 'has_ordered';
                } elseif ($hasPending) {
                    $orderStatus = 'order_halfway';
                } elseif ($hasCanceled) {
                    $orderStatus = 'order_canceled';
                }
            }

            return [
                'id' => $user->id,
                'fullname' => $user->fullname,
                'email' => $user->email,
                'phone' => $user->phone,
                'role' => $user->roles->first()->name ?? 'no_role',
                'registration_date' => $user->created_at->format('Y-m-d H:i'),
                'profile_complete' => $profileComplete ? 'Complete' : 'Incomplete',
                'device_type' => $user->devices->first()->type ?? 'Unknown',
                'total_orders' => $user->orders->count(),
                'order_status' => $orderStatus,
                'can_receive_notifications' => $user->is_allow_notify ? 'Yes' : 'No',
                'is_active' => $user->is_active ? 'Active' : 'Inactive',
                'wallet_balance' => $user->wallet ?? 0
            ];
        });

        return $users;
    }

    /**
     * Get comprehensive user analytics
     */
    public function getComprehensiveUserAnalytics($request)
    {
        if(!isset($request->from) and !isset($request->to)){
            $request->from = Carbon::now()->startOfMonth()->format('Y-m-d');
            $request->to = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        return [
            'user_registration_analysis' => $this->getUserRegistrationAnalysis($request),
            'profile_completion_analysis' => $this->getProfileCompletionAnalysis($request),
            'device_platform_analysis' => $this->getDevicePlatformAnalysis($request),
            'user_order_analysis' => $this->getUserOrderAnalysis($request),
            'notification_analysis' => $this->getNotificationAnalysis($request),
            'exportable_user_data' => $this->getExportableUserData($request)
        ];
    }
}
