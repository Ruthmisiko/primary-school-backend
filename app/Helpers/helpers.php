<?php

if (! function_exists('toE164')) {
    /**
     * Convert Kenyan phone number to E.164 format (+254...).
     */
    function toE164(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone); // remove non-digits

        if (str_starts_with($phone, '254')) {
            return '+'.$phone;
        }

        if (str_starts_with($phone, '0')) {
            return '+254'.substr($phone, 1);
        }

        // Fallback
        return '+'.$phone;
    }
}
