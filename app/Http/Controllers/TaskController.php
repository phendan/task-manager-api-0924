<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Exception;

class TaskController extends Controller
{
    // index, update, store, delete
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:5|max:255',
            'status' => ['required', Rule::in(['to-do', 'in-progress', 'done'])]
        ]);

        $task = new Task();

        $task->user_id = $request->user()->id;
        $task->title = $request->get('title');
        $task->status = $request->get('status');

        $task->save();

        return response()->json(status: 201);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'status' => ['required', Rule::in(['to-do', 'in-progress', 'done'])]
        ]);

        try {
            $task = Task::findOrFail($id);

            if ($task->user->id !== $request->user()->id) {
                return response()->json(status: 403);
            }

            $task->status = $request->get('status');
            $task->save();

            return response()->json(status: 200);
        } catch (Exception $e) {
            return response()->json(status: 404, data: [
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete(Request $request, int $id)
    {
        try {
            $task = Task::findOrFail($id);

            if ($task->user->id !== $request->user()->id) {
                return response()->json(status: 403);
            }

            $task->delete();
            return response()->json(status: 204);
        } catch (Exception $e) {
            return response()->json(status: 404, data: [
                'message' => $e->getMessage()
            ]);
        }
    }
}
