<?php

namespace App\Services;

use AfricasTalking\SDK\AfricasTalking;
use Illuminate\Support\Facades\Log;

class SMSService
{
    protected $sms;

    public function __construct()
    {
        $username = config('services.africas_talking.username')
            ?? config('services.africastalking.username')
            ?? env('AT_USERNAME')
            ?? env('AFRICASTALKING_USERNAME');
        $apiKey = config('services.africas_talking.api_key')
            ?? config('services.africastalking.key')
            ?? env('AT_API_KEY')
            ?? env('AFRICASTALKING_API_KEY');

        $AT = new AfricasTalking($username, $apiKey);
        $this->sms = $AT->sms();
    }

    /**
     * Send SMS via Africa's Talking.
     *
     * @param string|array $to  // single number or array of numbers in international format
     * @param string $message
     * @return array|null
     */
    public function send($to, string $message)
    {
        try {
            $payload = [
                'to' => is_array($to) ? implode(',', $to) : $to,
                'message' => $message,
            ];

            // Optional sender ID
            $senderId = config('services.africas_talking.sender_id')
                ?? env('AT_SENDER_ID');
            if (!empty($senderId)) {
                $payload['from'] = $senderId;
            }

            $response = $this->sms->send($payload);
            return $response;
        } catch (\Throwable $e) {
            Log::error('SMS sending failed: '.$e->getMessage(), ['to' => $to]);
            return null;
        }
    }
}
