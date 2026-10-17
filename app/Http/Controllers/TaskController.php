<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\StoreRequest;
use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;
use Throwable;

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

    #[OA\Post(
        path: "/api/tasks/",
        summary: "Создать задачу",
        tags: ['Task'],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Данные для создания задачи",
            content: new OA\JsonContent(
                ref: "#/components/schemas/StoreRequest"
            )
        ),
        responses : [
            new OA\Response(
                response: 201,
                description: "Task created successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "message", type: "string", example: "Task created successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: "Validation error",
                content: new OA\JsonContent(
                    type: "object",
                    properties : [
                        new OA\Property(property: "message", type: "message", example: "The title field is required"),
                        new OA\Property(property: "errors", type: "object", example: " {title: ['The title field is required.']}")
                    ]
                )
            ),
            new OA\Response(
                response: 500, 
                description: "Eternal server error",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "error", type: "string", example: "Eternal server error")
                    ]
                )
            )
        ]
    )]
    public function store(StoreRequest $request)
    {
        $data = $request->validated();

        try {
            $task = TaskService::create($data);
            return response()->json([
                "id" => $task->id,
                "message" => "Task created successfully"
            ], 201);
        } catch(Throwable $e) {

            Log::error("Ошибка при создании задачи в БД: " . $e->getMessage(), [
                "data" => $data,
                "exception" => $e
            ]);

            return response()->json([
                "error" => "Eternal server error"
            ], 500);
        }

    }
}
