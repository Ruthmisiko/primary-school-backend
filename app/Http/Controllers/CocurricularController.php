<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateCocurricularRequest;
use App\Http\Requests\UpdateCocurricularRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\CocurricularRepository;
use Illuminate\Http\Request;
use Flash;

class CocurricularController extends AppBaseController
{
    /** @var CocurricularRepository $cocurricularRepository*/
    private $cocurricularRepository;

    public function __construct(CocurricularRepository $cocurricularRepo)
    {
        $this->cocurricularRepository = $cocurricularRepo;
    }

    /**
     * Display a listing of the Cocurricular.
     */
    public function index(Request $request)
    {
        $cocurriculars = $this->cocurricularRepository->paginate(10);

        return view('cocurriculars.index')
            ->with('cocurriculars', $cocurriculars);
    }

    /**
     * Show the form for creating a new Cocurricular.
     */
    public function create()
    {
        return view('cocurriculars.create');
    }

    /**
     * Store a newly created Cocurricular in storage.
     */
    public function store(CreateCocurricularRequest $request)
    {
        $input = $request->all();

        $cocurricular = $this->cocurricularRepository->create($input);

        Flash::success('Cocurricular saved successfully.');

        return redirect(route('cocurriculars.index'));
    }

    /**
     * Display the specified Cocurricular.
     */
    public function show($id)
    {
        $cocurricular = $this->cocurricularRepository->find($id);

        if (empty($cocurricular)) {
            Flash::error('Cocurricular not found');

            return redirect(route('cocurriculars.index'));
        }

        return view('cocurriculars.show')->with('cocurricular', $cocurricular);
    }

    /**
     * Show the form for editing the specified Cocurricular.
     */
    public function edit($id)
    {
        $cocurricular = $this->cocurricularRepository->find($id);

        if (empty($cocurricular)) {
            Flash::error('Cocurricular not found');

            return redirect(route('cocurriculars.index'));
        }

        return view('cocurriculars.edit')->with('cocurricular', $cocurricular);
    }

    /**
     * Update the specified Cocurricular in storage.
     */
    public function update($id, UpdateCocurricularRequest $request)
    {
        $cocurricular = $this->cocurricularRepository->find($id);

        if (empty($cocurricular)) {
            Flash::error('Cocurricular not found');

            return redirect(route('cocurriculars.index'));
        }

        $cocurricular = $this->cocurricularRepository->update($request->all(), $id);

        Flash::success('Cocurricular updated successfully.');

        return redirect(route('cocurriculars.index'));
    }

    /**
     * Remove the specified Cocurricular from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $cocurricular = $this->cocurricularRepository->find($id);

        if (empty($cocurricular)) {
            Flash::error('Cocurricular not found');

            return redirect(route('cocurriculars.index'));
        }

        $this->cocurricularRepository->delete($id);

        Flash::success('Cocurricular deleted successfully.');

        return redirect(route('cocurriculars.index'));
    }
}
