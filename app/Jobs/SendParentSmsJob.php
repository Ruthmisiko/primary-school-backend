<?php

namespace App\Jobs;

use App\Models\StudentParent;
use App\Services\TwilioService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendParentSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
{
    $twilio = new \App\Services\TwilioService();

    // ✅ Hardcode your verified number for testing
    $to = '+254713631923';
    $message = 'Hello! This is a test SMS from Laravel via Twilio.';

    $twilio->sendSms($to, $message);
}

}
