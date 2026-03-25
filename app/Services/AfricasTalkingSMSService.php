<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;

class AfricasTalkingSMSService
{
    protected $sms;

    public function __construct()
    {
        $AT = new AfricasTalking(
            config('services.africastalking.username'),
            config('services.africastalking.api_key')
        );

        $this->sms = $AT->sms();
    }

    public function send(string $phone, string $message)
    {
        try {
            return $this->sms->send([
                'to' => $phone,
                'message' => $message,
                'from' => config('services.africastalking.sender_id'),
            ]);
        } catch (\Throwable $e) {
            Log::error('AfricaTalking SMS failed', [
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
