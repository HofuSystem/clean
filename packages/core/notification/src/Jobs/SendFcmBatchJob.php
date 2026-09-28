<?php

namespace Core\Notification\Jobs;

use Core\Notification\Models\Notification;
use Core\Notification\Models\NotificationToken;
use Core\Notification\Services\FCMService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendFcmBatchJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;
    public ?string $executionStatus = null;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Notification $notification,
        public array $devicesBatch
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            $this->executionStatus = 'cancelled';
            return;
        }

        // Job-Level Kill Switch: Evaluated dynamically at job execution time, NOT just dispatch time
        if (!config('notification.direct_fcm_enabled', false)) {
            $this->executionStatus = 'skipped_by_feature_flag';

            $tokenRecordIds = collect($this->devicesBatch)
                ->pluck('notification_token_id')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (!empty($tokenRecordIds)) {
                NotificationToken::where('notification_id', $this->notification->id)
                    ->where('status', 'queued')
                    ->whereIn('id', $tokenRecordIds)
                    ->update([
                        'status' => 'skipped_by_feature_flag',
                        'error_code' => 'DIRECT_FCM_DISABLED',
                        'updated_at' => now(),
                    ]);

                Log::warning("SendFcmBatchJob: Kill switch activated for notification {$this->notification->id}. Exact batch updated by notification_token_id (" . count($tokenRecordIds) . " tokens).");
                return;
            }

            // Legacy fallback: no notification_token_id provided (e.g. pre-existing serialized jobs in queue).
            // Match strictly using (device_id, token) pairs to prevent cross-pair matching.
            $validPairs = [];
            $tokensOnly = [];
            foreach ($this->devicesBatch as $dev) {
                $devId = $dev['device_id'] ?? null;
                $tok = $dev['token'] ?? null;
                if (!empty($devId) && !empty($tok)) {
                    $validPairs[] = ['device_id' => $devId, 'token' => $tok];
                } elseif (empty($devId) && !empty($tok)) {
                    $tokensOnly[] = $tok;
                }
            }

            if (!empty($validPairs) || !empty($tokensOnly)) {
                NotificationToken::where('notification_id', $this->notification->id)
                    ->where('status', 'queued')
                    ->where(function ($query) use ($validPairs, $tokensOnly) {
                        foreach ($validPairs as $pair) {
                            $query->orWhere(function ($sub) use ($pair) {
                                $sub->where('device_id', $pair['device_id'])
                                    ->where('token', $pair['token']);
                            });
                        }
                        if (!empty($tokensOnly)) {
                            $query->orWhere(function ($sub) use ($tokensOnly) {
                                $sub->whereNull('device_id')
                                    ->whereIn('token', array_unique($tokensOnly));
                            });
                        }
                    })
                    ->update([
                        'status' => 'skipped_by_feature_flag',
                        'error_code' => 'DIRECT_FCM_DISABLED',
                        'updated_at' => now(),
                    ]);

                Log::warning("SendFcmBatchJob: Kill switch activated for notification {$this->notification->id}. Legacy batch updated using exact (device_id, token) pairs (" . (count($validPairs) + count($tokensOnly)) . " pairs).");
                return;
            }

            // Fail-closed: No valid identifiers could be determined.
            // DO NOT execute broad update. Update 0 rows.
            Log::error("SendFcmBatchJob: Kill switch fail-closed activated for notification {$this->notification->id}. Batch contains no valid identifiers (missing notification_token_id, device_id, and token). Zero rows updated.");
            return;
        }

        $this->executionStatus = 'executed';
        FCMService::getInstance()->sendBatchDirect($this->notification, $this->devicesBatch);
    }
}
