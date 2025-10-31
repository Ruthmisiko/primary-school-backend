<?php

namespace App\Jobs;

use App\Models\StudentParent;
use App\Models\SmsLog;
use App\Services\SMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendParentSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $parentId;
    public string $message;

    public $tries = 3;        // retry policy
    public $backoff = 60;     // seconds between retries

    public function __construct(int $parentId, string $message)
    {
        $this->parentId = $parentId;
        $this->message = $message;
    }

    public function handle(SMSService $smsService): void
    {
        $parent = StudentParent::find($this->parentId);
        if (! $parent) {
            Log::warning("Parent not found for SMS job: {$this->parentId}");
            return;
        }

        $phone = toE164($parent->phone_number);
        // create log as queued
        $log = SmsLog::create([
            'parent_id'   => $parent->id,
            'phone_number'=> $phone,
            'message'     => $this->message,
            'status'      => 'queued',
        ]);

        $resp = $smsService->send($phone, $this->message);

        // optional: log success/failure
        if (is_null($resp)) {
            Log::error("SMS failed for parent {$this->parentId}", ['phone' => $phone]);
            $log->update([
                'status' => 'failed',
                'response_json' => null,
                'sent_at' => now(),
            ]);
            // throw new \Exception('SMS failed'); // if you want to trigger job retry
        } else {
            Log::info("SMS sent to parent {$this->parentId}", ['phone' => $phone, 'response' => $resp]);
            $log->update([
                'status' => 'sent',
                'response_json' => json_encode($resp),
                'sent_at' => now(),
            ]);
        }
    }
}
