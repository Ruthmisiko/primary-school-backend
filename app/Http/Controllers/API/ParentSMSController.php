<?php

namespace App\Http\Controllers\API;

use App\Jobs\SendParentSMSJob;
use App\Models\StudentParent;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

class ParentSMSController extends AppBaseController
{
    // Send to one parent immediately (queues job)
    public function sendToOne(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|max:320']);
        $message = $request->input('message');

        $parent = StudentParent::findOrFail($id);
        SendParentSMSJob::dispatch($parent->id, $message);

        return response()->json(['status' => 'queued', 'parent_id' => $parent->id]);
    }

    // Send to all parents (chunked to avoid memory issues)
    public function sendToAll(Request $request)
    {
        $request->validate(['message' => 'required|string|max:320']);
        $message = $request->input('message');

        StudentParent::chunk(100, function($parents) use ($message) {
            foreach ($parents as $parent) {
                SendParentSMSJob::dispatch($parent->id, $message);
            }
        });

        return response()->json(['status' => 'queued_all']);
    }

    // Example: send to parents of a class, or filtered list — adapt as needed
}
