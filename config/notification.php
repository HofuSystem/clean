<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification System Feature Flags
    |--------------------------------------------------------------------------
    |
    | Controls rollout of the new notification and device architecture.
    | Safe production defaults:
    | - device_sync_enabled: true (allows apps to begin syncing metadata)
    | - direct_fcm_enabled: false (uses legacy topic path until staging verification)
    | - permission_mode: 'observe' (logs permission status without blocking delivery)
    | - marketing_preference_mode: 'observe' (logs preference without blocking legacy delivery)
    | - delivery_events_enabled: false (events API disabled until app rollout)
    | - new_dashboard_metrics_enabled: true (displays new metrics & health dashboard)
    |
    */

    'device_sync_enabled' => env('NOTIFICATION_DEVICE_SYNC_ENABLED', true),

    'direct_fcm_enabled' => env('NOTIFICATION_DIRECT_FCM_ENABLED', false),

    'permission_mode' => env('NOTIFICATION_PERMISSION_MODE', 'observe'), // 'observe' | 'enforce'

    'marketing_preference_mode' => env('NOTIFICATION_MARKETING_PREFERENCE_MODE', 'observe'), // 'observe' | 'enforce'

    'delivery_events_enabled' => env('NOTIFICATION_DELIVERY_EVENTS_ENABLED', false),

    'new_dashboard_metrics_enabled' => env('NOTIFICATION_NEW_DASHBOARD_METRICS_ENABLED', true),
];
