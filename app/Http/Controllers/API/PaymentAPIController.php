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

     /**
     * 🚀 Initialize Paystack Payment
     *
     * This method sends a payment initialization request to Paystack
     * and returns the authorization URL for frontend redirection.
     */
    public function initializePaystackPayment(Request $request): JsonResponse
    {
        $student = $request->student_id;
        $amount = $request->amount; // Amount in KES or NGN (convert to kobo if NGN)
        $email = $request->email;   // Payer’s email

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->post('https://api.paystack.co/transaction/initialize', [
                'email' => $email,
                'amount' => $amount * 100, // Paystack uses kobo
                'currency' => $request->currency ?? 'KES',
                'callback_url' => url('/api/payments/verify'),
                'metadata' => [
                    'student_id' => $student,
                    'school_id' => auth()->user()->school_id,
                ]
            ]);

        $data = $response->json();

        if (!$response->successful()) {
            Log::error('Paystack Init Failed', $data);
            return $this->sendError('Payment initialization failed.', $data, 400);
        }

        // Save initial payment record
        Payment::create([
            'student_id' => $student,
            'amount' => $amount,
            'currency' => $request->currency ?? 'KES',
            'status' => 'PENDING',
            'description' => 'Tuition Payment Initialization',
            'callback_data' => $data,
            'school_id' => auth()->user()->school_id,
        ]);

        return $this->sendResponse($data['data'], 'Redirect user to authorization_url to complete payment.');
    }

     /**
     * ✅ Verify Paystack Payment
     *
     * This method verifies the transaction status from Paystack
     * using the transaction reference after payment is completed.
     */
    public function verifyPaystackPayment(Request $request): JsonResponse
    {
        $reference = $request->reference;

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        $data = $response->json();

        if (!$response->successful()) {
            Log::error('Paystack Verification Failed', $data);
            return $this->sendError('Payment verification failed.', $data, 400);
        }

        $paymentData = $data['data'];

        $payment = Payment::where('student_id', $paymentData['metadata']['student_id'])
            ->where('status', 'PENDING')
            ->latest()
            ->first();

        if ($payment) {
            $payment->update([
                'status' => strtoupper($paymentData['status']),
                'transaction_id' => $paymentData['id'],
                'callback_data' => $paymentData,
            ]);
        }

        return $this->sendResponse($paymentData, 'Payment verified successfully');
    }

    /**
     * 🔄 Handle Paystack Webhook (Optional)
     *
     * This endpoint automatically updates payment status when Paystack
     * sends a webhook notification to your system.
     */
    public function handlePaystackWebhook(Request $request)
    {
        $payload = $request->all();
        Log::info('Paystack Webhook Received:', $payload);

        $reference = $payload['data']['reference'] ?? null;

        if ($reference) {
            $response = Http::withToken(config('services.paystack.secret_key'))
                ->get("https://api.paystack.co/transaction/verify/{$reference}")
                ->json();

            $paymentData = $response['data'] ?? null;

            if ($paymentData) {
                Payment::where('transaction_id', $paymentData['id'])
                    ->update([
                        'status' => strtoupper($paymentData['status']),
                        'callback_data' => $paymentData,
                    ]);
            }
        }

        return response()->json(['status' => 'success'], 200);
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
