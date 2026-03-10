<?php

namespace Core\Notification\Jobs;

use Core\Notification\Helpers\NotificationsSender;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


class SendMails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries;
    /**
     * Create a new job instance.
     *
     * @return void
     */
 


    public function __construct(protected array $receivers,protected string $title,protected string $message)
    {
        $this->tries        = 3;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        NotificationsSender::smtpSender($this->receivers,['title' => $this->title,'msg'=>$this->message]);
      
    }


}
