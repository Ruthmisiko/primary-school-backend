<?php

namespace App\Http\Controllers\API;

use Log;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use App\Repositories\PaymentRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreatePaymentAPIRequest;
use App\Http\Requests\API\UpdatePaymentAPIRequest;

class PaymentAPIController extends AppBaseController
{
    private PaymentRepository $paymentRepository;

    public function __construct(PaymentRepository $paymentRepo)
    {
        $this->paymentRepository = $paymentRepo;
    }

    public function index(Request $request): JsonResponse
    {
        $payments = $this->paymentRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        $payments = Payment::with(['student'])->get();

        return $this->sendResponse($payments->toArray(), 'Payments retrieved successfully');
    }

    public function store(CreatePaymentAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['school_id'] = auth()->user()->school_id;

        $payment = $this->paymentRepository->create($input);

        return $this->sendResponse($payment->toArray(), 'Payment saved successfully');
    }

    public function show($id): JsonResponse
    {
        $payment = Payment::with(['student.sclass'])->find($id);

        if (empty($payment)) {
            return $this->sendError('Payment not found');
        }

        return $this->sendResponse($payment->toArray(), 'Payment retrieved successfully');
    }

    public function update($id, UpdatePaymentAPIRequest $request): JsonResponse
    {
        $input = $request->all();
        $payment = $this->paymentRepository->find($id);

        if (empty($payment)) {
            return $this->sendError('Payment not found');
        }

        $payment = $this->paymentRepository->update($input, $id);

        return $this->sendResponse($payment->toArray(), 'Payment updated successfully');
    }

    private function getToken()
    {
        $url = env('PESAPAL_ENV') === 'sandbox'
            ? "https://cybqa.pesapal.com/pesapalv3/api/Auth/RequestToken"
            : "https://pay.pesapal.com/v3/api/Auth/RequestToken";

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->withBasicAuth(
            env('PESAPAL_CONSUMER_KEY'),
            env('PESAPAL_CONSUMER_SECRET')
        )->post($url);

        if ($response->failed()) {
            Log::error("Pesapal Auth Error: " . $response->body());
            return null;
        }

        return $response['token'] ?? null;
    }

    /**
     * Start a payment request
     */
    public function initiatePayment(Request $request)
    {
        $amount = $request->amount;
        $studentId = $request->student_id;

        $token = $this->getToken();
        $merchantRef = uniqid('ORDER-');

        $order = [
            "id" => $merchantRef,
            "currency" => "KES",
            "amount" => $amount,
            "description" => "School Fee Payment",
            "callback_url" => env('PESAPAL_CALLBACK_URL'),
            "notification_id" => "student-" . $studentId,
            "billing_address" => [
                "email_address" => "parent@example.com",
                "phone_number" => "2547XXXXXXXX",
                "first_name" => "Parent",
                "last_name" => "Name"
            ]
        ];

        $url = env('PESAPAL_ENV') === 'sandbox'
            ? "https://cybqa.pesapal.com/pesapalv3/api/Transactions/SubmitOrderRequest"
            : "https://pay.pesapal.com/v3/api/Transactions/SubmitOrderRequest";

        $response = Http::withToken($token)->post($url, $order);
        $resBody = $response->json();

        // Save transaction in DB
        Transaction::create([
            'student_id' => $studentId,
            'payment_id' => $request->payment_id ?? null,
            'pesapal_merchant_reference' => $merchantRef,
            'amount' => $amount,
            'currency' => 'KES',
            'status' => 'PENDING',
            'raw_response' => $resBody
        ]);

        return response()->json($resBody);
    }

    /**
     * Pesapal callback
     */
    public function handleCallback(Request $request)
    {
        $reference = $request->input('OrderMerchantReference'); // our unique id
        $trackingId = $request->input('OrderTrackingId');
        $status = $request->input('status');

        $transaction = Transaction::where('pesapal_merchant_reference', $reference)->first();

        if ($transaction) {
            $transaction->update([
                'pesapal_tracking_id' => $trackingId,
                'status' => strtoupper($status),
                'raw_response' => $request->all()
            ]);
        }

        return response()->json(['message' => 'Callback processed']);
    }

    public function destroy($id): JsonResponse
    {
        $payment = $this->paymentRepository->find($id);

        if (empty($payment)) {
            return $this->sendError('Payment not found');
        }

        $payment->delete();
        return $this->sendSuccess('Payment deleted successfully');
    }
}
