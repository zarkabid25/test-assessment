<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Role-Based API",
 *     description="API documentation for the role-based application"
 * )
 *
 * @OA\Tag(
 *     name="Tasks",
 *     description="API Endpoints of Tasks"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer"
 * )
 */
class TaskController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Get list of tasks",
     *     description="Returns list of tasks for authenticated user",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter by task status (Pending or Completed)"
     *     ),
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Filter tasks by user ID (Admin only)"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Task"))
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function index(Request $request)
    {
        try {
            $query = Task::query();

            if (Auth::user()->hasRole('Client')) {
                $query->where('user_id', Auth::id());
            } elseif (Auth::user()->hasRole('Admin')) {
                if ($request->has('user_id')) {
                    $query->where('user_id', $request->user_id);
                }
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            $tasks = $query->paginate(10);

            return response()->json([
                'status' => 200,
                'message' => 'Tasks retrieved successfully.',
                'data' => $tasks
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => 'Failed to retrieve tasks.',
                'data' => ['error' => $th->getMessage()]
            ], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     tags={"Tasks"},
     *     summary="Create a new task",
     *     description="Creates a new task for a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"title", "status", "user_id"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="string", enum={"Pending", "Completed"}),
     *             @OA\Property(property="user_id", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task created",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function store(TaskRequest $request)
    {
        try {
            $task = Task::create($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Successfully created',
                'data' => $task
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 400,
                'message' => 'Validation failed',
                'data' => $e->errors()
            ], 400);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400);
        }

    }

    /**
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Get task details",
     *     description="Get details of a specific task",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Task ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);

        $this->authorize('view', $task);

        return response()->json([
            'status' => 200,
            'message' => 'Successfully retrieved',
            'data' => $task
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Update task",
     *     description="Update details of an existing task",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Task ID"
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="string", enum={"Pending", "Completed"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated",
     *         @OA\JsonContent(ref="#/components/schemas/Task")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function update(Request $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $this->authorize('update', $task);

            $task->update($request->all());

            return response()->json([
                'status' => 200,
                'message' => 'Successfully updated',
                'data' => $task
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Task not found.',
                'data' => []
            ], 404);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'status' => 400,
                'message' => 'This action is unauthorized.',
                'data' => []
            ], 400);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => $th->getMessage(),
                'data' => []
            ], 400);
        }

    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     tags={"Tasks"},
     *     summary="Delete task",
     *     description="Delete a specific task",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="Task ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted",
     *         @OA\JsonContent(type="object", @OA\Property(property="message", type="string"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found"
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Successfully deleted',
            'data' => []
        ], 200);
    }
}
