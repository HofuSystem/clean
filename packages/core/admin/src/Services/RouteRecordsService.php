<?php

namespace Core\Admin\Services;


use Core\Admin\Models\RoutesRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RM\Devices\Models\Device;

class RouteRecordsService
{


  public static function registerRequest($request)
  {
    //start Scan
    $uri                = $request->route()->uri();
    $attributes         = array_merge($request->route()->originalParameters(), $request->all());
    $user               = Auth::check() ? Auth::user() : null;
    $headers           = $request->header();
    $method            = $request->method();
    if($user){
      $user->update(['appear_at' => Carbon::now()]);
    }
    RoutesRecord::create([
      'end_point'   => $uri,
      'attributes'  => json_encode($attributes),
      'user_id'     => isset($user) ? $user->id : null,
      'ip_address'  => $request->ip(),
      'headers'     => json_encode($headers),
      'method'      => $method,
      'version'     => $request->header('app-version'),
    ]);

    //end scan
  }

  /**
   * Get hourly analysis of app usage
   */
  private function getHourlyAnalysis($timeDuration = 'all-time')
  {
    $hourlyData = RoutesRecord::inTimePeriod($timeDuration)
      ->select(
        DB::raw('HOUR(created_at) as hour'),
        DB::raw('COUNT(*) as request_count'),
        DB::raw('COUNT(DISTINCT user_id) as unique_users')
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
        'request_count' => $hourData ? $hourData->request_count : 0,
        'unique_users' => $hourData ? $hourData->unique_users : 0
      ];
    }

    // Get top 5 most active hours
    $topActiveHours = collect($hourlyAnalysis)
      ->sortByDesc('request_count')
      ->take(5)
      ->values();

    return [
      'hourly_data' => $hourlyAnalysis,
      'top_active_hours' => $topActiveHours
    ];
  }

  /**
   * Get daily analysis of app usage
   */
  private function getDailyAnalysis($timeDuration = 'all-time')
  {
    $dailyData = RoutesRecord::inTimePeriod($timeDuration)
      ->select(
        DB::raw('DAYOFWEEK(created_at) as day_of_week'),
        DB::raw('DAYNAME(created_at) as day_name'),
        DB::raw('COUNT(*) as request_count'),
        DB::raw('COUNT(DISTINCT user_id) as unique_users')
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
      7 => 'Saturday'
    ];

    $dailyAnalysis = [];
    foreach ($dayNames as $dayNum => $dayName) {
      $dayData = $dailyData->where('day_of_week', $dayNum)->first();
      $dailyAnalysis[] = [
        'day_number' => $dayNum,
        'day_name' => $dayName,
        'request_count' => $dayData ? $dayData->request_count : 0,
        'unique_users' => $dayData ? $dayData->unique_users : 0
      ];
    }

    // Get top 3 most active days
    $topActiveDays = collect($dailyAnalysis)
      ->sortByDesc('request_count')
      ->take(3)
      ->values();

    return [
      'daily_data' => $dailyAnalysis,
      'top_active_days' => $topActiveDays
    ];
  }

  /**   * Get peak usage time analysis (derived from hourly and daily data)
   */
  private function getPeakUsageAnalysis($hourlyAnalysis, $dailyAnalysis)
  {
    $hourlyCollect = collect($hourlyAnalysis['hourly_data'] ?? [])->sortByDesc('request_count');
    $dailyCollect = collect($dailyAnalysis['daily_data'] ?? [])->sortByDesc('request_count');

    $peakHour = $hourlyCollect->first();
    $peakDay = $dailyCollect->first();

    $totalRequests = $hourlyCollect->sum('request_count');

    return [
      'peak_hour' => ($peakHour && $peakHour['request_count'] > 0) ? [
        'hour' => $peakHour['hour'],
        'hour_label' => $peakHour['hour_label'],
        'request_count' => $peakHour['request_count']
      ] : null,
      'peak_day' => ($peakDay && $peakDay['request_count'] > 0) ? [
        'day_name' => $peakDay['day_name'],
        'request_count' => $peakDay['request_count']
      ] : null,
      'avg_requests_per_hour' => round($totalRequests / 24, 2)
    ];
  }

  public function getRoutesAnalysis($timeDuration = 'all-time')
  {
    if ($timeDuration === 'all-time' || empty($timeDuration)) {
      $timeDuration = 'last-month';
    }

    $startTime = null;
    $endTime = Carbon::now();
    switch ($timeDuration) {
      case 'last-minute': $startTime = Carbon::now()->subMinute(); break;
      case '10-minute': $startTime = Carbon::now()->subMinutes(10); break;
      case '30-minute': $startTime = Carbon::now()->subMinutes(30); break;
      case 'last-hour': $startTime = Carbon::now()->subHour(); break;
      case 'last-day': $startTime = Carbon::now()->subDay(); break;
      case 'last-week': $startTime = Carbon::now()->subWeek(); break;
      case 'last-month': $startTime = Carbon::now()->subMonth(); break;
      case 'last-year': $startTime = Carbon::now()->subYear(); break;
    }

    $applyTimeFilter = function ($query) use ($startTime, $endTime) {
      if ($startTime) {
        $query->whereBetween('created_at', [$startTime, $endTime]);
      }
      return $query;
    };

    $totalRequests = $applyTimeFilter(DB::table('routes_records'))->count();

    // 1. Get requests per user (using DB::table to prevent Eloquent model hydration)
    $requestsPerUserRaw = $applyTimeFilter(DB::table('routes_records'))
      ->whereNotNull('user_id')
      ->selectRaw('user_id, COUNT(*) as request_count, MAX(id) as max_id')
      ->groupBy('user_id')
      ->orderByDesc('request_count')
      ->limit(50)
      ->get();

    $userIds = $requestsPerUserRaw->pluck('user_id')->filter()->toArray();
    $maxIds = $requestsPerUserRaw->pluck('max_id')->filter()->toArray();

    $usersMap = !empty($userIds)
      ? DB::table('users')->whereIn('id', $userIds)->select(['id', 'fullname', 'email', 'phone', 'image'])->get()->keyBy('id')
      : collect();

    $lastRequestsMap = !empty($maxIds)
      ? DB::table('routes_records')->whereIn('id', $maxIds)->select(['id', 'user_id', 'end_point', 'created_at', 'attributes'])->get()->keyBy('user_id')
      : collect();

    $requestsPerUser = $requestsPerUserRaw->map(function ($item) use ($usersMap, $lastRequestsMap) {
      $user = $usersMap->get($item->user_id);
      $lastReq = $lastRequestsMap->get($item->user_id);

      return (object) [
        'user_id' => $item->user_id,
        'request_count' => $item->request_count,
        'name' => $user ? $user->fullname : 'Unknown',
        'email' => $user ? $user->email : '',
        'phone' => $user ? $user->phone : '',
        'avatar' => $user ? $user->image : null,
        'last_endpoint' => $lastReq ? $lastReq->end_point : null,
        'last_request_attributes' => $lastReq ? $lastReq->attributes : null,
        'last_request_time' => $lastReq ? $lastReq->created_at : null,
      ];
    });

    $topUsers = $requestsPerUser->take(10);
    $lestUsers = $requestsPerUser->sortBy('request_count')->take(10);

    // 2. Most used endpoints & IP addresses
    $mostUsedEndpoints = $applyTimeFilter(DB::table('routes_records'))
      ->selectRaw('end_point, COUNT(*) as request_count')
      ->groupBy('end_point')
      ->orderByDesc('request_count')
      ->limit(10)
      ->get();

    $mostUsedIpAddress = $applyTimeFilter(DB::table('routes_records'))
      ->selectRaw('ip_address, COUNT(*) as request_count')
      ->groupBy('ip_address')
      ->orderByDesc('request_count')
      ->limit(10)
      ->get();

    // 3. Combined Single Query for Hourly & Daily Pattern Analysis
    $hourlyAndDailyData = $applyTimeFilter(DB::table('routes_records'))
      ->selectRaw('HOUR(created_at) as hour, DAYOFWEEK(created_at) as day_of_week, COUNT(*) as request_count, COUNT(DISTINCT user_id) as unique_users')
      ->groupBy('hour', 'day_of_week')
      ->get();

    // Process hourly analysis from combined data
    $hourlyAnalysis = [];
    for ($hour = 0; $hour < 24; $hour++) {
      $hourRows = $hourlyAndDailyData->where('hour', $hour);
      $hourlyAnalysis[] = [
        'hour' => $hour,
        'hour_label' => sprintf('%02d:00', $hour),
        'request_count' => $hourRows->sum('request_count'),
        'unique_users' => $hourRows->max('unique_users') ?? 0,
      ];
    }
    $topActiveHours = collect($hourlyAnalysis)->sortByDesc('request_count')->take(5)->values();

    // Process daily analysis from combined data
    $dayNames = [
      1 => 'Sunday',
      2 => 'Monday',
      3 => 'Tuesday',
      4 => 'Wednesday',
      5 => 'Thursday',
      6 => 'Friday',
      7 => 'Saturday'
    ];
    $dailyAnalysis = [];
    foreach ($dayNames as $dayNum => $dayName) {
      $dayRows = $hourlyAndDailyData->where('day_of_week', $dayNum);
      $dailyAnalysis[] = [
        'day_number' => $dayNum,
        'day_name' => $dayName,
        'request_count' => $dayRows->sum('request_count'),
        'unique_users' => $dayRows->max('unique_users') ?? 0,
      ];
    }
    $topActiveDays = collect($dailyAnalysis)->sortByDesc('request_count')->take(3)->values();

    // Peak Usage Analysis
    $peakHour = collect($hourlyAnalysis)->sortByDesc('request_count')->first();
    $peakDay = collect($dailyAnalysis)->sortByDesc('request_count')->first();
    $sumRequests = collect($hourlyAnalysis)->sum('request_count');

    $peakUsageAnalysis = [
      'peak_hour' => ($peakHour && $peakHour['request_count'] > 0) ? [
        'hour' => $peakHour['hour'],
        'hour_label' => $peakHour['hour_label'],
        'request_count' => $peakHour['request_count']
      ] : null,
      'peak_day' => ($peakDay && $peakDay['request_count'] > 0) ? [
        'day_name' => $peakDay['day_name'],
        'request_count' => $peakDay['request_count']
      ] : null,
      'avg_requests_per_hour' => round($sumRequests / 24, 2)
    ];

    return [
      'totalRequests' => $totalRequests,
      'requestsPerUser' => $requestsPerUser,
      'topUsers' => $topUsers,
      'lestUsers' => $lestUsers,
      'mostUsedEndpoints' => $mostUsedEndpoints,
      'mostUsedIpAddress' => $mostUsedIpAddress,
      'hourlyAnalysis' => [
        'hourly_data' => $hourlyAnalysis,
        'top_active_hours' => $topActiveHours,
      ],
      'dailyAnalysis' => [
        'daily_data' => $dailyAnalysis,
        'top_active_days' => $topActiveDays,
      ],
      'peakUsageAnalysis' => $peakUsageAnalysis,
    ];
  }
}
