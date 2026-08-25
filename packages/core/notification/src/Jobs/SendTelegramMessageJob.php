<?php

namespace Core\Notification\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTelegramMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 15;

    public function __construct(
        protected string $chatId,
        protected string $message
    ) {}

    public function handle(): void
    {
        try {
            $botToken = "7970295502:AAHmfUgGNGPyHp8RoDKiEZ4G6vdrdiMg0B0";
            $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

            Http::timeout(10)->post($url, [
                'chat_id'    => $this->chatId,
                'text'       => $this->message,
                'parse_mode' => 'HTML',
            ]);
        } catch (\Throwable $e) {
            Log::error('SendTelegramMessageJob failed: ' . $e->getMessage());
        }
    }
}