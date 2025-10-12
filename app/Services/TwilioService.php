<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client(
            env('TWILIO_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
    }

    public function sendSms($to, $message)
    {
        try {
            $this->client->messages->create($to, [
                'from' => env('TWILIO_PHONE_NUMBER'),
                'body' => $message,
            ]);

            Log::info("SMS sent successfully to {$to}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send SMS to {$to}: " . $e->getMessage());
            return false;
        }
    }
}
