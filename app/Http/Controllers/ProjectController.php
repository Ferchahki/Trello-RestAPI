<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectCollection;
use Illuminate\Http\Request;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;



class ProjectController extends Controller
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

        // Get the projects
        $projects = Project::orderBy($sortField, $sortDirection);

        // Apply filters if any
        if ($filter) {

            $projects->where(function ($query) use ($filter) {
                $query->where('name', 'LIKE', '%' . $filter . '%')
                    ->orWhere('description', 'LIKE', '%' . $filter . '%');
            });
        }
        // Paginate the results
        $projects = $projects->paginate($perPage);
        // Return the tasks/projects
        return response()->json([
            'projects' => new ProjectCollection($projects),
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'description' => 'required',
        ]);

         $user = Auth::user();

        $project =$user-> Project::create([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
        ]);

        return response()->json([
                'message' => 'Project created successfully',
                'project' => $project,
            ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $user = Auth::user();
        $project =$user-> Project::findOrFail($id);
        return response()->json([
            'project' => $project
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $validatedData = $request->validate([
            'name' => 'sometimes|required|max:255',
            'description' => 'nullable'
        ]);

        $user = Auth::user();
        $project =$user->Project::findOrFail($id);
        $project->update($validatedData);

        return response()->json([
            'project' => $project
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $user = Auth::user();
        $project =$user-> Project::findOrFail($id);
        $project->delete();

        return response()->json([
            'message' => 'Project deleted successfully'
        ]);
    }
}
