<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;



class TaskController extends Controller
{
    #[OA\Get(
        path: "/api/tasks/",
        summary: "Получить список задач",
        tags: ['Task'],
        responses : [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(ref: "#/components/schemas/Task")
                )
            ),
        ]
    )]
    public function index()
    {
        return TaskResource::collection(Task::all())->resolve();
    }


    public function store(StoreRequest $request)
    {
        $data = $request->validated();
        $task = TaskService::create($data);

        return response()->json([
            "id" => $task->id,
            "message" => 'Task created successfully'
        ]);
    }
}
