<?php

namespace Core\Notification\Services;

use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Models\LastSentToToken;
use Core\Users\Models\Device;
use Google\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FCMService
{
    private $path = null;

    public function __construct() {}

    public static function getInstance(): self
    {
        return new static();
    }

    public function getFcmConfigFromFile()
    {
        return Cache::rememberForever('fcm_config_file', function () {
            $filePath = base_path('fcm.json');
            if (file_exists($filePath)) {
                return json_decode(file_get_contents($filePath), true);
            }
            return [];
        });
    }

    public function getAccessToken($config)
    {
        if (empty($config)) {
            return null;
        }

        // Cache access token for 50 minutes (Google tokens expire in 60 min)
        return Cache::remember('fcm_access_token', 50 * 60, function () use ($config) {
            $client = new Client();
            $client->setAuthConfig($config);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->fetchAccessTokenWithAssertion();
            $token = $client->getAccessToken();
            return $token['access_token'] ?? null;
        });
    }

    public function convertIntegersToStrings($array)
    {
        if (empty($array)) {
            return is_array($array) ? [] : $array;
        }
        if (is_string($array)) {
            $decoded = json_decode($array, true);
            if (is_array($decoded)) {
                $array = $decoded;
            } else {
                return $array;
            }
        }
        if (!is_array($array)) {
            return (string)$array;
        }

        $formatted = [];
        foreach ($array as $key => $value) {
            $strKey = (string)$key;
            if (is_array($value) || is_object($value)) {
                $formatted[$strKey] = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            } elseif (is_bool($value)) {
                $formatted[$strKey] = $value ? '1' : '0';
            } elseif (is_null($value)) {
                $formatted[$strKey] = '';
            } else {
                $formatted[$strKey] = (string)$value;
            }
        }

        return $formatted;
    }

    /**
     * Legacy topic subscription manager for IID Google API.
     */
    public function manageTopicSubscription(string $type, array $deviceTokens, string $topic, string $accessToken)
    {
        $url = ($type == "add") ? "https://iid.googleapis.com/iid/v1:batchAdd" : "https://iid.googleapis.com/iid/v1:batchRemove";
        $data = [
            'to' => "/topics/" . $topic,
            'registration_tokens' => $deviceTokens
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
                'access_token_auth' => "true",
            ])->post($url, $data);
            return $response->json()['results'] ?? [];
        } catch (\Throwable $e) {
            report($e);
            return [];
        }
    }

    /**
     * Legacy custom topic sender (active when direct_fcm_enabled is false).
     */
    public function sendToCustomTopic($notificationId, string $customTopic, array $tokens, string $title, string $message, array $payload = [])
    {
        try {
            if (empty($tokens)) {
                return;
            }
            $fcmConfig = $this->getFcmConfigFromFile();
            $accessToken = $this->getAccessToken($fcmConfig);
            if (!$accessToken) {
                return;
            }

            // Remove old assignment
            if (class_exists(LastSentToToken::class)) {
                LastSentToToken::where('topic', $customTopic)->get()->pluck('token')->chunk(1000)->each(function ($batch) use ($accessToken, $customTopic) {
                    $this->manageTopicSubscription('remove', $batch->toArray(), $customTopic, $accessToken);
                });
                LastSentToToken::where('topic', $customTopic)->delete();
            }

            $notWorkingTokens = [];
            $workingTokens = [];
            $chunks = array_chunk($tokens, 1000);
            foreach ($chunks as $chunk) {
                $registerTokensResult = $this->manageTopicSubscription('add', $chunk, $customTopic, $accessToken);
                foreach ($registerTokensResult as $index => $result) {
                    if (!empty($result)) {
                        $notWorkingTokens[] = $chunk[$index];
                    } else {
                        $workingTokens[] = ['token' => $chunk[$index], 'topic' => $customTopic, 'created_at' => now(), 'updated_at' => now()];
                    }
                }
            }

            if (class_exists(LastSentToToken::class) && !empty($workingTokens)) {
                LastSentToToken::insert($workingTokens);
            }

            $this->sendToTopic($customTopic, $title, $message, $payload);
            $this->manageTopicSubscription('remove', $notWorkingTokens, $customTopic, $accessToken);

            // Mark invalid tokens instead of destructive forceDelete
            if (!empty($notWorkingTokens)) {
                Device::whereIn('device_token', $notWorkingTokens)->update([
                    'token_status' => 'invalid',
                    'invalidated_at' => now(),
                    'invalid_reason' => 'Legacy topic subscription failure'
                ]);
            }

            // Update users_notifications status and notification sent_count
            if (!empty($notificationId)) {
                $workingTokensOnly = array_column($workingTokens, 'token');
                if (!empty($workingTokensOnly)) {
                    $workingUserIds = Device::whereIn('device_token', $workingTokensOnly)
                        ->pluck('user_id')
                        ->filter()
                        ->unique()
                        ->toArray();

                    if (!empty($workingUserIds)) {
                        DB::table('users_notifications')
                            ->where('notifications_type', Notification::class)
                            ->where('notifications_id', $notificationId)
                            ->whereIn('user_id', $workingUserIds)
                            ->update([
                                'status' => 'sent',
                                'response' => 'Sent successfully via FCM'
                            ]);
                    }
                }

                if (!empty($notWorkingTokens)) {
                    $failedUserIds = Device::whereIn('device_token', $notWorkingTokens)
                        ->pluck('user_id')
                        ->filter()
                        ->unique()
                        ->toArray();

                    if (!empty($failedUserIds)) {
                        DB::table('users_notifications')
                            ->where('notifications_type', Notification::class)
                            ->where('notifications_id', $notificationId)
                            ->whereIn('user_id', $failedUserIds)
                            ->where('status', 'pending')
                            ->update([
                                'status' => 'failed',
                                'response' => 'Failed topic subscription'
                            ]);
                    }
                }

                $sentCount = DB::table('users_notifications')
                    ->where('notifications_type', Notification::class)
                    ->where('notifications_id', $notificationId)
                    ->where('status', 'sent')
                    ->count();

                $notif = Notification::find($notificationId);
                if ($notif) {
                    $payload = is_array($notif->payload) ? $notif->payload : (json_decode($notif->payload ?? '{}', true) ?: []);
                    $payload['sent_count'] = $sentCount;
                    $notif->update(['payload' => json_encode($payload)]);
                }
            }
        } catch (\Throwable $e) {
            report($e);
            DB::table('users_notifications')
                ->join('devices', 'users_notifications.user_id', '=', 'devices.user_id')
                ->whereIn('devices.device_token', $tokens)
                ->where('users_notifications.notifications_type', Notification::class)
                ->where('users_notifications.notifications_id', $notificationId)
                ->update([
                    'status' => 'failed',
                    'response' => $e->getMessage(),
                ]);
        }
    }

    /**
     * Send to single FCM topic via HTTP v1.
     */
    public function sendToTopic(string $topic, string $title, string $message, array $payload = [])
    {
        try {
            $fcmConfig = $this->getFcmConfigFromFile();
            $bearerToken = $this->getAccessToken($fcmConfig);
            if (!$bearerToken || empty($fcmConfig['project_id'])) {
                return;
            }

            $formattedData = !empty($payload) ? $this->convertIntegersToStrings($payload) : null;

            $data = [
                'message' => [
                    'topic' => $topic,
                    'notification' => [
                        'title' => $title,
                        'body' => $message,
                    ],
                    'data' => !empty($formattedData) ? $formattedData : null
                ]
            ];
            $url = "https://fcm.googleapis.com/v1/projects/{$fcmConfig['project_id']}/messages:send";
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$bearerToken}",
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            if ($response->failed()) {
                try {
                    app(TelegramNotificationService::class)->sendMessage(
                        '@itcleanstation',
                        "⚠️ خطأ في إرسال إشعار FCM للموضوع [{$topic}]:\n" . $response->body()
                    );
                } catch (\Throwable $e) {
                    // Ignore telegram failure
                }
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Send direct HTTP v1 push notifications to a batch of devices concurrently via Http::pool.
     *
     * @param Notification $notification
     * @param array $devicesBatch Array of [device_id, user_id, token, platform, installation_id, app_context]
     * @return array Batch execution summary
     */
    public function sendBatchDirect(Notification $notification, array $devicesBatch): array
    {
        if (empty($devicesBatch)) {
            return ['accepted' => 0, 'permanent_failed' => 0, 'transient_failed' => 0];
        }

        $fcmConfig = $this->getFcmConfigFromFile();
        if (empty($fcmConfig)) {
            report(new \Exception("FCM config file not found or invalid at fcm.json"));
            return ['accepted' => 0, 'permanent_failed' => 0, 'transient_failed' => count($devicesBatch), 'error' => 'No FCM credentials'];
        }

        $projectId = $fcmConfig['project_id'] ?? null;
        $accessToken = $this->getAccessToken($fcmConfig);
        if (!$accessToken) {
            report(new \Exception("Could not obtain Google access token for FCM project: {$projectId}"));
            return ['accepted' => 0, 'permanent_failed' => 0, 'transient_failed' => count($devicesBatch), 'error' => 'Auth failure'];
        }

        // 1. Idempotency: filter out devices that are ALREADY accepted for this notification
        $tokensInBatch = array_filter(array_column($devicesBatch, 'token'));
        $alreadyAcceptedTokens = [];
        if (!empty($tokensInBatch)) {
            $alreadyAcceptedTokens = NotificationToken::where('notification_id', $notification->id)
                ->whereIn('status', ['accepted', 'received', 'opened'])
                ->whereIn('token', $tokensInBatch)
                ->pluck('token')
                ->flip()
                ->toArray();
        }

        $filteredBatch = [];
        foreach ($devicesBatch as $dev) {
            $tok = $dev['token'] ?? null;
            if (!empty($tok) && !isset($alreadyAcceptedTokens[$tok])) {
                $filteredBatch[] = $dev;
            }
        }

        if (empty($filteredBatch)) {
            return ['accepted' => 0, 'permanent_failed' => 0, 'transient_failed' => 0, 'skipped' => count($devicesBatch)];
        }

        // 2. Prepare payload
        $rawPayload = [];
        if (!empty($notification->payload)) {
            $rawPayload = is_string($notification->payload) ? json_decode($notification->payload, true) : (array)$notification->payload;
        }
        // Defensive fallback for direct calls and retries that bypass the
        // manager payload preparation.
        $rawPayload['notification_id'] = (string) $notification->id;
        $rawPayload['delivery_channel'] = 'direct_fcm';
        $payload = $this->convertIntegersToStrings($rawPayload);

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // 3. Concurrent requests with a strict per-worker socket limit. A large
        // campaign can run many queue jobs together; pooling every device in every
        // job at once previously exhausted the server's open-file limit.
        $responses = [];
        $httpConcurrency = max(1, (int) config('notification.fcm_http_concurrency', 10));

        foreach (array_chunk($filteredBatch, $httpConcurrency, true) as $httpChunk) {
            $chunkResponses = Http::pool(function ($pool) use ($httpChunk, $url, $accessToken, $notification, $payload) {
                foreach ($httpChunk as $idx => $dev) {
                    $body = [
                        'message' => [
                            'token' => $dev['token'],
                            'notification' => [
                                'title' => (string)$notification->title,
                                'body' => (string)$notification->body,
                            ],
                            // Keep every delivery attempt for the same campaign under one
                            // stable system notification without breaking legacy apps.
                            'android' => [
                                'collapse_key' => 'notification-' . $notification->id,
                                'notification' => [
                                    'tag' => 'notification-' . $notification->id,
                                ],
                            ],
                            'apns' => [
                                'headers' => [
                                    'apns-collapse-id' => 'notification-' . $notification->id,
                                ],
                            ],
                        ]
                    ];
                    if (!empty($payload)) {
                        $body['message']['data'] = $payload;
                    }

                    $pool->as((string) $idx)->withHeaders([
                        'Authorization' => "Bearer {$accessToken}",
                        'Content-Type' => 'application/json',
                    ])->timeout(10)->connectTimeout(4)->post($url, $body);
                }
            });

            foreach ($chunkResponses as $idx => $response) {
                if ($response instanceof \Illuminate\Http\Client\Response) {
                    // Never retain Laravel/Guzzle response objects for the whole
                    // 50-device job. Their PSR streams may keep sockets/file
                    // descriptors open until garbage collection, which is fatal
                    // on shared hosts with a low open-files limit.
                    $json = $response->json();
                    $responses[(int) $idx] = [
                        'successful' => $response->successful(),
                        'status' => $response->status(),
                        'json' => is_array($json) ? $json : [],
                        'body' => substr($response->body(), 0, 1000),
                    ];

                    try {
                        $response->toPsrResponse()->getBody()->close();
                    } catch (\Throwable $e) {
                        // The response data is already snapshotted. Some stream
                        // implementations may already be closed by the handler.
                    }
                } else {
                    $responses[(int) $idx] = null;
                }
            }

            unset($chunkResponses);
        }

        $now = now();
        $acceptedItems = [];
        $permFailedItems = [];
        $transFailedItems = [];
        $invalidDevicePairs = [];
        $affectedUserIds = [];

        // Ensure all devices in filteredBatch have an explicit notification_token_id scoped to this notification
        $missingTokenItems = [];
        foreach ($filteredBatch as $dev) {
            if (empty($dev['notification_token_id']) && !empty($dev['token'])) {
                $missingTokenItems[] = $dev;
            }
        }

        if (!empty($missingTokenItems)) {
            $missingTokens = array_values(array_unique(array_column($missingTokenItems, 'token')));
            $existingTokensMap = DB::table('notification_tokens')
                ->where('notification_id', $notification->id)
                ->whereIn('token', $missingTokens)
                ->pluck('id', 'token')
                ->toArray();

            $tokensToInsert = [];
            foreach ($missingTokenItems as $mDev) {
                $mTok = $mDev['token'];
                if (!isset($existingTokensMap[$mTok])) {
                    $tokensToInsert[] = [
                        'notification_id' => $notification->id,
                        'token' => $mTok,
                        'user_id' => $mDev['user_id'] ?? null,
                        'device_id' => $mDev['device_id'] ?? null,
                        'installation_id' => $mDev['installation_id'] ?? null,
                        'platform' => $mDev['platform'] ?? null,
                        'topic' => 'direct',
                        'status' => 'queued',
                        'title' => (string)$notification->title,
                        'attempts' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
            if (!empty($tokensToInsert)) {
                DB::table('notification_tokens')->insert($tokensToInsert);
                $existingTokensMap = DB::table('notification_tokens')
                    ->where('notification_id', $notification->id)
                    ->whereIn('token', $missingTokens)
                    ->pluck('id', 'token')
                    ->toArray();
            }

            foreach ($filteredBatch as &$dev) {
                if (empty($dev['notification_token_id']) && isset($existingTokensMap[$dev['token']])) {
                    $dev['notification_token_id'] = $existingTokensMap[$dev['token']];
                }
            }
            unset($dev);
        }

        // Process FCM responses in memory
        foreach ($filteredBatch as $idx => $dev) {
            $response = $responses[$idx] ?? null;
            $userId = $dev['user_id'] ?? null;
            $deviceId = $dev['device_id'] ?? null;
            $token = $dev['token'] ?? null;
            $tokenId = $dev['notification_token_id'] ?? null;

            if ($userId) {
                $affectedUserIds[$userId] = true;
            }

            $fcmMsgId = null;
            $errorCode = null;
            $errorMessage = null;

            if (is_array($response) && ($response['successful'] ?? false)) {
                $fcmMsgId = $response['json']['name'] ?? null;
                $acceptedItems[] = [
                    'id' => $tokenId,
                    'token' => $token,
                    'user_id' => $userId,
                    'device_id' => $deviceId,
                    'installation_id' => $dev['installation_id'] ?? null,
                    'platform' => $dev['platform'] ?? null,
                    'fcm_message_id' => $fcmMsgId,
                ];
            } elseif (is_array($response)) {
                $json = $response['json'] ?? [];
                $errorCode = $json['error']['details'][0]['errorCode'] ?? $json['error']['status'] ?? 'HTTP_' . ($response['status'] ?? 0);
                $errorMessage = substr($json['error']['message'] ?? ($response['body'] ?? ''), 0, 500);

                $permanentCodes = ['UNREGISTERED', 'NOT_FOUND', 'INVALID_ARGUMENT'];
                if (in_array($errorCode, $permanentCodes) || str_contains($errorMessage, 'Requested entity was not found')) {
                    $permFailedItems[] = [
                        'id' => $tokenId,
                        'token' => $token,
                        'user_id' => $userId,
                        'device_id' => $deviceId,
                        'installation_id' => $dev['installation_id'] ?? null,
                        'platform' => $dev['platform'] ?? null,
                        'error_code' => $errorCode,
                        'error_message' => $errorMessage,
                    ];
                    if ($deviceId && $token) {
                        $invalidDevicePairs[] = [
                            'device_id' => $deviceId,
                            'token' => $token,
                        ];
                    }
                } else {
                    $transFailedItems[] = [
                        'id' => $tokenId,
                        'token' => $token,
                        'user_id' => $userId,
                        'device_id' => $deviceId,
                        'installation_id' => $dev['installation_id'] ?? null,
                        'platform' => $dev['platform'] ?? null,
                        'error_code' => $errorCode,
                        'error_message' => $errorMessage,
                    ];
                }
            } else {
                // Connection exception or timeout
                $errorCode = 'TIMEOUT_OR_CONNECTION_ERROR';
                $errorMessage = 'Connection timeout or pool error';
                $transFailedItems[] = [
                    'id' => $tokenId,
                    'token' => $token,
                    'user_id' => $userId,
                    'device_id' => $deviceId,
                    'installation_id' => $dev['installation_id'] ?? null,
                    'platform' => $dev['platform'] ?? null,
                    'error_code' => $errorCode,
                    'error_message' => $errorMessage,
                ];
            }
        }

        $acceptedCount = count($acceptedItems);
        $permFailedCount = count($permFailedItems);
        $transFailedCount = count($transFailedItems);

        // Bulk DB operations wrapped in transaction
        DB::transaction(function () use (
            $notification,
            $acceptedItems,
            $permFailedItems,
            $transFailedItems,
            $invalidDevicePairs,
            $affectedUserIds,
            $acceptedCount,
            $permFailedCount,
            $transFailedCount,
            $now
        ) {
            $pdo = DB::getPdo();
            $nowStr = $pdo->quote($now->toDateTimeString());

            // 1. Bulk Update Accepted Items
            if (!empty($acceptedItems)) {
                $acceptedIds = array_filter(array_column($acceptedItems, 'id'));
                if (!empty($acceptedIds)) {
                    $hasMsgIds = false;
                    foreach ($acceptedItems as $item) {
                        if (!empty($item['fcm_message_id'])) {
                            $hasMsgIds = true;
                            break;
                        }
                    }

                    $idList = implode(',', array_map('intval', $acceptedIds));

                    if ($hasMsgIds) {
                        $caseParts = [];
                        foreach ($acceptedItems as $item) {
                            if (!empty($item['id'])) {
                                $itemMsg = $item['fcm_message_id'] ? $pdo->quote((string)$item['fcm_message_id']) : 'NULL';
                                $caseParts[] = "WHEN id = " . (int)$item['id'] . " THEN {$itemMsg}";
                            }
                        }
                        $caseSql = implode(' ', $caseParts);
                        $msgIdSql = "fcm_message_id = CASE {$caseSql} ELSE fcm_message_id END,";
                    } else {
                        $msgIdSql = "";
                    }

                    DB::statement("
                        UPDATE notification_tokens 
                        SET status = 'accepted',
                            {$msgIdSql}
                            attempts = attempts + 1,
                            accepted_at = {$nowStr},
                            failed_at = NULL,
                            error_code = NULL,
                            error_message = NULL,
                            updated_at = {$nowStr}
                        WHERE notification_id = " . (int)$notification->id . "
                          AND id IN ({$idList})
                    ");
                }
            }

            // 2. Bulk Update Permanent Failed Items
            if (!empty($permFailedItems)) {
                $permIds = array_filter(array_column($permFailedItems, 'id'));
                if (!empty($permIds)) {
                    $idList = implode(',', array_map('intval', $permIds));
                    $codeParts = [];
                    $msgParts = [];
                    foreach ($permFailedItems as $item) {
                        if (!empty($item['id'])) {
                            $codeVal = $item['error_code'] ? $pdo->quote((string)$item['error_code']) : 'NULL';
                            $msgVal = $item['error_message'] ? $pdo->quote((string)$item['error_message']) : 'NULL';
                            $codeParts[] = "WHEN id = " . (int)$item['id'] . " THEN {$codeVal}";
                            $msgParts[] = "WHEN id = " . (int)$item['id'] . " THEN {$msgVal}";
                        }
                    }
                    $codeCaseSql = implode(' ', $codeParts);
                    $msgCaseSql = implode(' ', $msgParts);

                    DB::statement("
                        UPDATE notification_tokens 
                        SET status = 'permanent_failed',
                            error_code = CASE {$codeCaseSql} ELSE error_code END,
                            error_message = CASE {$msgCaseSql} ELSE error_message END,
                            attempts = attempts + 1,
                            failed_at = {$nowStr},
                            accepted_at = NULL,
                            updated_at = {$nowStr}
                        WHERE notification_id = " . (int)$notification->id . "
                          AND id IN ({$idList})
                    ");
                }
            }

            // 3. Bulk Update Transient Failed Items
            if (!empty($transFailedItems)) {
                $transIds = array_filter(array_column($transFailedItems, 'id'));
                if (!empty($transIds)) {
                    $idList = implode(',', array_map('intval', $transIds));
                    $codeParts = [];
                    $msgParts = [];
                    foreach ($transFailedItems as $item) {
                        if (!empty($item['id'])) {
                            $codeVal = $item['error_code'] ? $pdo->quote((string)$item['error_code']) : 'NULL';
                            $msgVal = $item['error_message'] ? $pdo->quote((string)$item['error_message']) : 'NULL';
                            $codeParts[] = "WHEN id = " . (int)$item['id'] . " THEN {$codeVal}";
                            $msgParts[] = "WHEN id = " . (int)$item['id'] . " THEN {$msgVal}";
                        }
                    }
                    $codeCaseSql = implode(' ', $codeParts);
                    $msgCaseSql = implode(' ', $msgParts);

                    DB::statement("
                        UPDATE notification_tokens 
                        SET status = 'transient_failed',
                            error_code = CASE {$codeCaseSql} ELSE error_code END,
                            error_message = CASE {$msgCaseSql} ELSE error_message END,
                            attempts = attempts + 1,
                            failed_at = {$nowStr},
                            accepted_at = NULL,
                            updated_at = {$nowStr}
                        WHERE notification_id = " . (int)$notification->id . "
                          AND id IN ({$idList})
                    ");
                }
            }

            // 4. Invalidate Devices (With Token Refresh Race Protection)
            if (!empty($invalidDevicePairs)) {
                DB::table('devices')
                    ->where(function ($query) use ($invalidDevicePairs) {
                        foreach ($invalidDevicePairs as $pair) {
                            if (!empty($pair['device_id']) && !empty($pair['token'])) {
                                $query->orWhere(function ($sub) use ($pair) {
                                    $sub->where('id', $pair['device_id'])
                                        ->where('device_token', $pair['token']);
                                });
                            }
                        }
                    })
                    ->update([
                        'token_status' => 'invalid',
                        'invalidated_at' => $now,
                        'invalid_reason' => 'FCM permanent unregistered',
                    ]);
            }

            // 5. Rebuild snapshots from the per-device source of truth. Incrementing
            // these columns is incorrect during retries because the previous transient
            // failure must be removed when a device moves to another status.
            DB::table('notifications')
                ->where('id', $notification->id)
                ->lockForUpdate()
                ->first();

            $deliveryCounts = DB::table('notification_tokens')
                ->where('notification_id', $notification->id)
                ->selectRaw("SUM(CASE WHEN status IN ('accepted', 'received', 'opened') THEN 1 ELSE 0 END) as accepted_count")
                ->selectRaw("SUM(CASE WHEN status = 'permanent_failed' THEN 1 ELSE 0 END) as permanent_failed_count")
                ->selectRaw("SUM(CASE WHEN status = 'transient_failed' THEN 1 ELSE 0 END) as transient_failed_count")
                ->first();

            DB::table('notifications')->where('id', $notification->id)->update([
                'accepted_by_fcm_count' => (int) ($deliveryCounts->accepted_count ?? 0),
                'permanent_failed_count' => (int) ($deliveryCounts->permanent_failed_count ?? 0),
                'transient_failed_count' => (int) ($deliveryCounts->transient_failed_count ?? 0),
            ]);

            // 6. Aggregate and Bulk Update users_notifications
            if (!empty($affectedUserIds)) {
                $uIds = array_map('intval', array_keys($affectedUserIds));

                $userDeviceCounts = DB::table('notification_tokens')
                    ->where('notification_id', $notification->id)
                    ->whereIn('user_id', $uIds)
                    ->select('user_id')
                    ->selectRaw("SUM(CASE WHEN status IN ('accepted', 'received', 'opened') THEN 1 ELSE 0 END) as accepted_count")
                    ->selectRaw("SUM(CASE WHEN status IN ('permanent_failed', 'transient_failed') THEN 1 ELSE 0 END) as failed_count")
                    ->groupBy('user_id')
                    ->get()
                    ->keyBy('user_id');

                $statusCases = [];
                $acceptedCases = [];
                $failedCases = [];
                $responseCases = [];

                foreach ($uIds as $uId) {
                    $counts = $userDeviceCounts->get($uId);
                    $userAccepted = $counts ? (int)$counts->accepted_count : 0;
                    $userFailed = $counts ? (int)$counts->failed_count : 0;

                    $userStatus = $userAccepted > 0 ? 'sent' : ($userFailed > 0 ? 'failed' : 'pending');
                    $resp = $userAccepted > 0 ? "Accepted on {$userAccepted} device(s)" : "Failed on {$userFailed} device(s)";

                    $statusCases[] = "WHEN user_id = {$uId} THEN " . $pdo->quote($userStatus);
                    $acceptedCases[] = "WHEN user_id = {$uId} THEN {$userAccepted}";
                    $failedCases[] = "WHEN user_id = {$uId} THEN {$userFailed}";
                    $responseCases[] = "WHEN user_id = {$uId} THEN " . $pdo->quote($resp);
                }

                $userIdList = implode(',', $uIds);
                $statusCaseSql = implode(' ', $statusCases);
                $acceptedCaseSql = implode(' ', $acceptedCases);
                $failedCaseSql = implode(' ', $failedCases);
                $responseCaseSql = implode(' ', $responseCases);
                $morphType = $pdo->quote(Notification::class);
                $notifId = (int)$notification->id;

                DB::statement("
                    UPDATE users_notifications
                    SET status = CASE {$statusCaseSql} ELSE status END,
                        accepted_devices_count = CASE {$acceptedCaseSql} ELSE accepted_devices_count END,
                        failed_devices_count = CASE {$failedCaseSql} ELSE failed_devices_count END,
                        response = CASE {$responseCaseSql} ELSE response END
                    WHERE notifications_type = {$morphType}
                      AND notifications_id = {$notifId}
                      AND user_id IN ({$userIdList})
                ");
            }
        });

        return [
            'accepted' => $acceptedCount,
            'permanent_failed' => $permFailedCount,
            'transient_failed' => $transFailedCount,
        ];
    }
}
