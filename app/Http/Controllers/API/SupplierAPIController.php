<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierAPIRequest;
use App\Http\Requests\API\UpdateSupplierAPIRequest;
use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class SupplierAPIController
 */
class SupplierAPIController extends AppBaseController
{
    private SupplierRepository $supplierRepository;

    public function __construct(SupplierRepository $supplierRepo)
    {
        $this->supplierRepository = $supplierRepo;
    }

    /**
     * Display a listing of the Suppliers.
     * GET|HEAD /suppliers
     */
    public function index(Request $request): JsonResponse
    {
        $suppliers = $this->supplierRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($suppliers->toArray(), 'Suppliers retrieved successfully');
    }

    /**
     * Store a newly created Supplier in storage.
     * POST /suppliers
     */
    public function store(CreateSupplierAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $supplier = $this->supplierRepository->create($input);

        return $this->sendResponse($supplier->toArray(), 'Supplier saved successfully');
    }

    /**
     * Display the specified Supplier.
     * GET|HEAD /suppliers/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Supplier $supplier */
        $supplier = $this->supplierRepository->find($id);

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        return $this->sendResponse($supplier->toArray(), 'Supplier retrieved successfully');
    }

    /**
     * Update the specified Supplier in storage.
     * PUT/PATCH /suppliers/{id}
     */
    public function update($id, UpdateSupplierAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Supplier $supplier */
        $supplier = $this->supplierRepository->find($id);

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        $supplier = $this->supplierRepository->update($input, $id);

        return $this->sendResponse($supplier->toArray(), 'Supplier updated successfully');
    }

    /**
     * Remove the specified Supplier from storage.
     * DELETE /suppliers/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Supplier $supplier */
        $supplier = $this->supplierRepository->find($id);

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        $supplier->delete();

        return $this->sendSuccess('Supplier deleted successfully');
    }
}
