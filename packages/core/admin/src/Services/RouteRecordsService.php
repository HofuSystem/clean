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

  /**
   * Get peak usage time analysis (derived from hourly and daily data)
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
    $totalRequests = RoutesRecord::inTimePeriod($timeDuration)->count();

    // Get requests per user with user details and last endpoint
    $requestsPerUser = RoutesRecord::inTimePeriod($timeDuration)
      ->whereNotNull('user_id')
      ->select(
        'routes_records.user_id',
        DB::raw('COUNT(*) as request_count'),
        'users.fullname as name',
        'users.email as email',
        'users.phone as phone',
        'users.image as avatar',
        DB::raw('MAX(routes_records.created_at) as last_request_time')
      )
      ->join('users', 'routes_records.user_id', '=', 'users.id')
      ->groupBy('routes_records.user_id', 'users.fullname', 'users.email', 'users.phone', 'users.image')
      ->orderBy('request_count', 'desc')
      ->limit(50)
      ->get();

    // Get the last endpoint for all these users in a single query using indexed MAX(id)
    $userIds = $requestsPerUser->pluck('user_id')->filter()->all();
    $latestRecordIds = !empty($userIds) ? RoutesRecord::whereIn('user_id', $userIds)
        ->selectRaw('MAX(id) as max_id')
        ->groupBy('user_id')
        ->pluck('max_id') : collect();

    $lastRequests = !empty($latestRecordIds) ? RoutesRecord::whereIn('id', $latestRecordIds)
        ->select('user_id', 'end_point', 'created_at', 'attributes')
        ->get()
        ->keyBy('user_id') : collect();

    foreach ($requestsPerUser as $user) {
      $lastRequest = $lastRequests->get($user->user_id);
      $user->last_endpoint = $lastRequest ? $lastRequest->end_point : null;
      $user->last_request_attributes = $lastRequest ? $lastRequest->attributes : null;
      $user->last_request_time = $lastRequest ? $lastRequest->created_at : null;
    }

    $topUsers = $requestsPerUser->take(10);
    $lestUsers = $requestsPerUser->sortBy('request_count')->take(10);

    $mostUsedEndpoints = RoutesRecord::inTimePeriod($timeDuration)
      ->select('end_point', DB::raw('COUNT(*) as request_count'))
      ->groupBy('end_point')
      ->orderBy('request_count', 'desc')
      ->limit(10)
      ->get();

    $mostUsedIpAddress = RoutesRecord::inTimePeriod($timeDuration)
      ->select('ip_address', DB::raw('COUNT(*) as request_count'))
      ->groupBy('ip_address')
      ->orderBy('request_count', 'desc')
      ->limit(10)
      ->get();

    // Get hourly and daily analysis
    $hourlyAnalysis = $this->getHourlyAnalysis($timeDuration);
    $dailyAnalysis = $this->getDailyAnalysis($timeDuration);
    $peakUsageAnalysis = $this->getPeakUsageAnalysis($hourlyAnalysis, $dailyAnalysis);

    $data = [
      'totalRequests' => $totalRequests,
      'requestsPerUser' => $requestsPerUser,
      'topUsers' => $topUsers,
      'lestUsers' => $lestUsers,
      'mostUsedEndpoints' => $mostUsedEndpoints,
      'mostUsedIpAddress' => $mostUsedIpAddress,
      'hourlyAnalysis' => $hourlyAnalysis,
      'dailyAnalysis' => $dailyAnalysis,
      'peakUsageAnalysis' => $peakUsageAnalysis,
    ];

    return $data;
  }
}
