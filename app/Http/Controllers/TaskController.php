<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TaskController extends Controller
{
    /**
     * Display a listing of tasks, with optional status filtering.
     *
     * GET /api/tasks
     * Query Parameters:
     * - status: "todo", "in-progress", or "done"
     * - sort_by: field name (default: "created_at")
     * - sort_dir: "asc" or "desc" (default: "desc")
     * - search: keyword search in title or description
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::query();

        // Filter by status if provided
        if ($request->filled('status')) {
            $status = $request->query('status');
            if (in_array($status, ['todo', 'in-progress', 'done'])) {
                $query->where('status', $status);
            }
        }

        // Optional keyword search
        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $allowedSorts = ['id', 'title', 'status', 'due_date', 'created_at', 'updated_at'];
        $sortBy = in_array($request->query('sort_by'), $allowedSorts) ? $request->query('sort_by') : 'created_at';
        $sortDir = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $tasks = $query->orderBy($sortBy, $sortDir)->get();

        return response()->json([
            'success' => true,
            'message' => 'Tasks retrieved successfully',
            'count' => $tasks->count(),
            'filter' => [
                'status' => $request->query('status') ?? 'all',
                'search' => $request->query('search') ?? null,
            ],
            'data' => TaskResource::collection($tasks),
        ], 200);
    }

    /**
     * Store a newly created task in the database.
     *
     * POST /api/tasks
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Default status to 'todo' if not explicitly provided
        if (empty($validated['status'])) {
            $validated['status'] = 'todo';
        }

        $task = Task::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully',
            'data' => new TaskResource($task),
        ], 201);
    }

    /**
     * Display the specified task.
     *
     * GET /api/tasks/{id}
     */
    public function show(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => "Task with ID {$id} not found",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Task retrieved successfully',
            'data' => new TaskResource($task),
        ], 200);
    }

    /**
     * Update the specified task in the database.
     * Supports both PUT (full update) and PATCH (partial update).
     *
     * PUT/PATCH /api/tasks/{id}
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => "Task with ID {$id} not found",
            ], 404);
        }

        $validated = $request->validated();
        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully',
            'data' => new TaskResource($task),
        ], 200);
    }

    /**
     * Remove the specified task from the database.
     *
     * DELETE /api/tasks/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => "Task with ID {$id} not found",
            ], 404);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully',
            'deleted_id' => $id,
        ], 200);
    }

    /**
     * Get summary metrics and statistics for tasks.
     *
     * GET /api/tasks/stats
     */
    public function stats(): JsonResponse
    {
        $total = Task::count();
        $todo = Task::where('status', 'todo')->count();
        $inProgress = Task::where('status', 'in-progress')->count();
        $done = Task::where('status', 'done')->count();
        $overdue = Task::where('status', '!=', 'done')
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'todo' => $todo,
                'in_progress' => $inProgress,
                'done' => $done,
                'overdue' => $overdue,
                'completion_rate' => $total > 0 ? round(($done / $total) * 100, 1) : 0,
            ],
        ], 200);
    }
}
