<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateResultRequest;
use App\Http\Requests\UpdateResultRequest;
use App\Http\Controllers\AppBaseController;
use App\Repositories\ResultRepository;
use Illuminate\Http\Request;
use Flash;

class ResultController extends AppBaseController
{
    /** @var ResultRepository $resultRepository*/
    private $resultRepository;

    public function __construct(ResultRepository $resultRepo)
    {
        $this->resultRepository = $resultRepo;
    }

    /**
     * Display a listing of the Result.
     */
    public function index(Request $request)
    {
        $results = $this->resultRepository->paginate(10);

        return view('results.index')
            ->with('results', $results);
    }

    /**
     * Show the form for creating a new Result.
     */
    public function create()
    {
        return view('results.create');
    }

    /**
     * Store a newly created Result in storage.
     */
    // public function store(CreateResultRequest $request)
    // {
    //     $input = $request->all();

    //     $result = $this->resultRepository->create($input);

    //     Flash::success('Result saved successfully.');

    //     return redirect(route('results.index'));
    // }
    public function store(Request $request)
    {
        dd($request->all());
        $validated = $request->validate([
            'class_id' => 'required|exists:sclasses,id',
            'student_id' => 'required|exists:students,id',
            'exam_id' => 'required|exists:exams,id',
            'result_items' => 'required|array|min:1',
            'result_items.*.subject_id' => 'required|exists:subjects,id',
            'result_items.*.marks_obtained' => 'required|integer|min:0|max:100',
            'result_items.*.grade' => 'nullable|string',
            'result_items.*.remarks' => 'nullable|string',
        ]);

        foreach ($validated['result_items'] as $item) {
            \App\Models\Result::create([
                'class_id' => $validated['class_id'],
                'student_id' => $validated['student_id'],
                'exam_id' => $validated['exam_id'],
                'subject_id' => $item['subject_id'],
                'marks_obtained' => $item['marks_obtained'],
                'grade' => $item['grade'] ?? null,
                'remarks' => $item['remarks'] ?? null,
            ]);
        }

        \Flash::success('Results saved successfully.');

        return redirect(route('results.index'));
    }


    /**
     * Display the specified Result.
     */
    public function show($id)
    {
        $result = $this->resultRepository->find($id);

        if (empty($result)) {
            Flash::error('Result not found');

            return redirect(route('results.index'));
        }

        return view('results.show')->with('result', $result);
    }

    /**
     * Show the form for editing the specified Result.
     */
    public function edit($id)
    {
        $result = $this->resultRepository->find($id);

        if (empty($result)) {
            Flash::error('Result not found');

            return redirect(route('results.index'));
        }

        return view('results.edit')->with('result', $result);
    }

    /**
     * Update the specified Result in storage.
     */
    public function update($id, UpdateResultRequest $request)
    {
        $result = $this->resultRepository->find($id);

        if (empty($result)) {
            Flash::error('Result not found');

            return redirect(route('results.index'));
        }

        $result = $this->resultRepository->update($request->all(), $id);

        Flash::success('Result updated successfully.');

        return redirect(route('results.index'));
    }

    /**
     * Remove the specified Result from storage.
     *
     * @throws \Exception
     */
    public function destroy($id)
    {
        $result = $this->resultRepository->find($id);

        if (empty($result)) {
            Flash::error('Result not found');

            return redirect(route('results.index'));
        }

        $this->resultRepository->delete($id);

        Flash::success('Result deleted successfully.');

        return redirect(route('results.index'));
    }
}
