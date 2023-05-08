<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskCollection;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }


    public function index(Request $request)
    {

        // Get query parameters
        $perPage = $request->query('per_page', 10);
        $sortField = $request->query('sort_field', 'created_at');
        $sortDirection = $request->query('sort_direction', 'desc');
        $filter = $request->query('filter');
        // Get the tasks
        $tasks = Task::orderBy($sortField, $sortDirection);
        // Apply filters if any
        if ($filter) {
            $tasks->where(function ($query) use ($filter) {
                $query->where('name', 'LIKE',
                    '%' . $filter . '%'
                )
                ->orWhere('description', 'LIKE', '%' . $filter . '%');
            });
        }
        // Paginate the results
        $tasks = $tasks->paginate($perPage);
        // Return the tasks
        return response()->json([
            'tasks' => new TaskCollection($tasks),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $projectId)
    {
        //
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'assignee_id' => 'required|exists:users,id',
            'status' => 'required|in:todo,in_progress,done',
        ]);

        $project = Project::findOrFail($projectId);
        $user = Auth::user();
        $task =$user-> $project->tasks()->create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'assignee_id' => $validatedData['assignee_id'],
            'status' => $validatedData['status'],
        ]);

        return response()->json([
            'message' => 'Task added successfully',
            'task' => $task,
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($projectId, $taskId)
    {
        //
        $user = Auth::user();
        $task =$user-> Task::findOrFail($taskId);
        return response()->json([
            'task' => $task
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$projectId, $taskId)
    {
        //
        $validatedData = $request->validate([
            'name' => 'sometimes|required|max:255',
            'description' => 'nullable',
            'assignee_id' => 'nullable|exists:users,id',
            'status' => 'nullable|in:todo,in_progress,done',
        ]);

        $user = Auth::user();
        $project =$user-> Project::findOrFail($projectId);
        $task = $project->tasks()->findOrFail($taskId);

        $task->update([
            'assignee_id' => $validatedData['assignee_id'],
        ]);

        return response()->json([
                'message' => 'Task updated successfully',
                'task' => $task,
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($projectId, $taskId)
    {
        //
        $user = Auth::user();
        $task =$user-> Task::findOrFail($taskId);
        $task->delete();
        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }
}
