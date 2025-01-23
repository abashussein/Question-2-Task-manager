<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TaskController extends Controller
{
    // POST /api/tasks
    public function store(Request $request)
    {
        // Validate request data
        $request->validate([
            'title' => 'required|string|max:255',
            'completed' => 'boolean',
        ]);

        // Store task locally
        $task = Task::create([
            'title' => $request->input('title'),
            'completed' => $request->input('completed', false),
        ]);

        // Post task to external API
        $externalResponse = Http::post(env('EXTERNAL_API_URL'), [
            'title' => $task->title,
            'completed' => $task->completed,
        ]);

        if ($externalResponse->successful()) {
            $task->update(['external_id' => $externalResponse->json()['id']]);
        }

        return response()->json($task, 201);
    }

    // GET /api/tasks
    public function index()
    {
        // Fetch local tasks
        $localTasks = Task::all();

        // Fetch tasks from external API
        $externalTasks = Http::get(env('EXTERNAL_API_URL'))->json();

        // Combine and return tasks
        return response()->json([
            'local_tasks' => $localTasks,
            'external_tasks' => $externalTasks,
        ]);
    }
}
