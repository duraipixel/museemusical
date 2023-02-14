<?php

namespace App\Jobs;

use App\Mail\DynamicMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Mail;

class RegisterEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $templateMessage;
    public $email_title;
    public $email;

    public function __construct($templateMessage, $email_title, $email)
    {
        $this->templateMessage = $templateMessage;
        $this->email_title = $email_title;
        $this->email = $email;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        log('duirarkererj');
        $send_mail = new DynamicMail($this->templateMessage, $this->email_title);
        Mail::to($this->email)->send($send_mail);
    }
}
