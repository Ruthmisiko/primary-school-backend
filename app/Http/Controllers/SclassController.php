<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSclassRequest;
use App\Http\Requests\UpdateSclassRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\SclassRepository;
use Illuminate\Http\Request;
use Flash;

class SclassController extends AppBaseController
{
    /** @var SclassRepository $sclassRepository*/
    private $sclassRepository;

    public function __construct(SclassRepository $sclassRepo)
    {
        $this->sclassRepository = $sclassRepo;
    }

    /**
     * Display a listing of the Sclass.
     */
    public function index(Request $request)
    {
        $sclasses = $this->sclassRepository->paginate(10);

        return view('sclasses.index')
            ->with('sclasses', $sclasses);
    }

    /**
     * Show the form for creating a new Sclass.
     */
    public function create()
    {
        return view('sclasses.create');
    }

    /**
     * Store a newly created Sclass in storage.
     */
    public function store(CreateSclassRequest $request)
    {
        $input = $request->all();

        $sclass = $this->sclassRepository->create($input);

        Flash::success('Sclass saved successfully.');

        return redirect(route('sclasses.index'));
    }

    /**
     * Display the specified Sclass.
     */
    public function show($id)
    {
        $sclass = $this->sclassRepository->find($id);

        if (empty($sclass)) {
            Flash::error('Sclass not found');

            return redirect(route('sclasses.index'));
        }

        return view('sclasses.show')->with('sclass', $sclass);
    }

    /**
     * Show the form for editing the specified Sclass.
     */
    public function edit($id)
    {
        $sclass = $this->sclassRepository->find($id);

        if (empty($sclass)) {
            Flash::error('Sclass not found');

            return redirect(route('sclasses.index'));
        }

        return view('sclasses.edit')->with('sclass', $sclass);
    }

    /**
     * Update the specified Sclass in storage.
     */
    public function update($id, UpdateSclassRequest $request)
    {
        $sclass = $this->sclassRepository->find($id);

        if (empty($sclass)) {
            Flash::error('Sclass not found');

            return redirect(route('sclasses.index'));
        }

        $sclass = $this->sclassRepository->update($request->all(), $id);

        Flash::success('Sclass updated successfully.');

        return redirect(route('sclasses.index'));
    }

    /**
     * Remove the specified Sclass from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $sclass = $this->sclassRepository->find($id);

        if (empty($sclass)) {
            Flash::error('Sclass not found');

            return redirect(route('sclasses.index'));
        }

        $this->sclassRepository->delete($id);

        Flash::success('Sclass deleted successfully.');

        return redirect(route('sclasses.index'));
    }
}
