<?php

namespace App\Jobs;

use App\Models\StudentParent;
use App\Services\AfricasTalkingSMSService;
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

    public function __construct(int $parentId, string $message)
    {
        $this->parentId = $parentId;
        $this->message = $message;
    }

    public function handle(AfricasTalkingSMSService $smsService)
    {
        $parent = StudentParent::find($this->parentId);

        if (!$parent || !$parent->phone_number) {
            return;
        }

        $phone = $this->normalizePhone($parent->phone_number);

        $response = $smsService->send($phone, $this->message);

        Log::info('Parent SMS sent', [
            'parent_id' => $this->parentId,
            'phone' => $phone,
            'response' => $response,
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', $phone);

        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        if (str_starts_with($phone, '+')) {
            return ltrim($phone, '+');
        }

        return $phone;
    }
}
