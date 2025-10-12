<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Services\TwilioService;

Route::get('/test-sms', function () {
    $twilio = new TwilioService();
    $twilio->sendSms('+254713631923', 'Hello Ruth! 👋 This is a test SMS from your Laravel + Twilio integration.');
    return '✅ SMS sent — check your phone and Twilio logs.';
});

Route::get('/', function () {
    return view('welcome');
});
