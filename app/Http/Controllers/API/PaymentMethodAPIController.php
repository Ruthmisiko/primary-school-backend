<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentMethodAPIRequest;
use App\Http\Requests\API\UpdatePaymentMethodAPIRequest;
use App\Models\PaymentMethod;
use App\Repositories\PaymentMethodRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class PaymentMethodAPIController
 */
class PaymentMethodAPIController extends AppBaseController
{
    private PaymentMethodRepository $paymentMethodRepository;

    public function __construct(PaymentMethodRepository $paymentMethodRepo)
    {
        $this->paymentMethodRepository = $paymentMethodRepo;
    }

    /**
     * Display a listing of the PaymentMethods.
     * GET|HEAD /payment-methods
     */
    public function index(Request $request): JsonResponse
    {
        $paymentMethods = $this->paymentMethodRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($paymentMethods->toArray(), 'Payment Methods retrieved successfully');
    }

    /**
     * Store a newly created PaymentMethod in storage.
     * POST /payment-methods
     */
    public function store(CreatePaymentMethodAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $input['school_id'] = auth()->user()->school_id;

        $paymentMethod = $this->paymentMethodRepository->create($input);

        return $this->sendResponse($paymentMethod->toArray(), 'Payment Method saved successfully');
    }

    /**
     * Display the specified PaymentMethod.
     * GET|HEAD /payment-methods/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->find($id);

        if (empty($paymentMethod)) {
            return $this->sendError('Payment Method not found');
        }

        return $this->sendResponse($paymentMethod->toArray(), 'Payment Method retrieved successfully');
    }

    /**
     * Update the specified PaymentMethod in storage.
     * PUT/PATCH /payment-methods/{id}
     */
    public function update($id, UpdatePaymentMethodAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->find($id);

        if (empty($paymentMethod)) {
            return $this->sendError('Payment Method not found');
        }

        $paymentMethod = $this->paymentMethodRepository->update($input, $id);

        return $this->sendResponse($paymentMethod->toArray(), 'PaymentMethod updated successfully');
    }

    /**
     * Remove the specified PaymentMethod from storage.
     * DELETE /payment-methods/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var PaymentMethod $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->find($id);

        if (empty($paymentMethod)) {
            return $this->sendError('Payment Method not found');
        }

        $paymentMethod->delete();

        return $this->sendSuccess('Payment Method deleted successfully');
    }
}
