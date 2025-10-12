<?php

namespace App\Http\Controllers;

use Flash;
use Illuminate\Http\Request;
use App\Models\StudentParent;
use App\Repositories\ParentRepository;
use App\Http\Requests\CreateParentRequest;
use App\Http\Requests\UpdateParentRequest;
use App\Http\Controllers\AppBaseController;

class ParentController extends AppBaseController
{
    /** @var ParentRepository $parentRepository*/
    private $parentRepository;

    public function __construct(ParentRepository $parentRepo)
    {
        $this->parentRepository = $parentRepo;
    }

    /**
     * Display a listing of the Parent.
     */
    public function index(Request $request)
    {
        $parents = StudentParent::with('student')->get();

        return response()->json([
            'success' => true,
            'data' => $parents,
            'message' => 'Parents retrieved successfully'
        ]);
    }

    /**
     * Show the form for creating a new Parent.
     */
    public function create()
    {
        return view('parents.create');
    }

    /**
     * Store a newly created Parent in storage.
     */
    public function store(CreateParentRequest $request)
    {
        $input = $request->all();

        $parent = $this->parentRepository->create($input);

        Flash::success('Parent saved successfully.');

        return redirect(route('parents.index'));
    }

    /**
     * Display the specified Parent.
     */
    public function show($id)
    {
        $parent = $this->parentRepository->find($id);

        if (empty($parent)) {
            Flash::error('Parent not found');

            return redirect(route('parents.index'));
        }

        return view('parents.show')->with('parent', $parent);
    }

    /**
     * Show the form for editing the specified Parent.
     */
    public function edit($id)
    {
        $parent = $this->parentRepository->find($id);

        if (empty($parent)) {
            Flash::error('Parent not found');

            return redirect(route('parents.index'));
        }

        return view('parents.edit')->with('parent', $parent);
    }

    /**
     * Update the specified Parent in storage.
     */
    public function update($id, UpdateParentRequest $request)
    {
        $parent = $this->parentRepository->find($id);

        if (empty($parent)) {
            Flash::error('Parent not found');

            return redirect(route('parents.index'));
        }

        $parent = $this->parentRepository->update($request->all(), $id);

        Flash::success('Parent updated successfully.');

        return redirect(route('parents.index'));
    }

    /**
     * Remove the specified Parent from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $parent = $this->parentRepository->find($id);

        if (empty($parent)) {
            Flash::error('Parent not found');

            return redirect(route('parents.index'));
        }

        $this->parentRepository->delete($id);

        Flash::success('Parent deleted successfully.');

        return redirect(route('parents.index'));
    }
}
