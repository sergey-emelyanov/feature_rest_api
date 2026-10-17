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
        path: "/api/tasks",
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
    public function index(Request $request)
    {
        dd($request->query());
        return TaskResource::collection(Task::all())->resolve();
    }

    #[OA\Get(
        path: "/api/tasks/{id}",
        summary: "Получить задачу по идентификатору",
        tags: ['Task'],
        parameters: [
            new OA\Parameter(
            name: "id",
            description: "Task ID",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(ref: "#/components/schemas/TaskResource")
            ),
            new OA\Response(
                response: 404,
                description: "Not found",
                content: new OA\JsonContent(
                    type: "object",
                    properties : [
                        new OA\Property(property: "message", type: "message", example: "Not found"),
                        new OA\Property(property: "errors", type: "object", example: "Задачи с таким id не существует")
                    ]
                )
            ),
        ]
    )]
    public function show(int $id)
    {
        $task = Task::where('id', $id)->first();
        if($task instanceof Task){
            return TaskResource::make($task)->resolve();
        }
        return response()->json([
                "error" => "Not found",
                "message" => "Задачи с таким id не существует"
            ], 404);
    }


    #[OA\Post(
        path: "/api/tasks",
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

    #[OA\Put(
        path: "/api/tasks/{id}",
        summary: "Обновить задачу",
        tags: ['Task'],
        parameters: [
            new OA\Parameter(
            name: "id",
            description: "Task ID",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: "Данные для обновления задачи",
            content: new OA\JsonContent(
                ref: "#/components/schemas/StoreRequest"
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Task updated successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Task update successfully")
                    ]
                )
            ),

            new OA\Response(
                response: 404,
                description: "Not found",
                content: new OA\JsonContent(
                    type: "object",
                    properties : [
                        new OA\Property(property: "message", type: "message", example: "Not found"),
                        new OA\Property(property: "errors", type: "object", example: "Задачи с таким id не существует")
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
    public function update(StoreRequest $request, int $id)
    {
        $data = $request->validated();
        $task = Task::where('id', $id)->first();

        if($task instanceof Task){
            try {
                $task = TaskService::update($data, $task);
                return response()->json([
                    "message" => "Task updated successfully"
                ],204);
            } catch (Throwable $e) {
                Log::error("Ошибка при обновлении задачи {$task->id} в БД: " . $e->getMessage(), [
                    "data" => $data,
                    "task" => $task,
                    "exception" => $e
                ]);

                return response()->json([
                "error" => "Eternal server error"
                ], 500);
            }
        }

        return response()->json([
                "error" => "Not found",
                "message" => "Задачи с таким id не существует"
            ], 404);
    }

    #[OA\Delete(
        path: "/api/tasks/{id}",
        summary: "Удаление задачи по идентификатору",
        tags: ["Task"],
        parameters: [
            new OA\Parameter(
            name: "id",
            description: "Task ID",
            in: "path",
            required: true,
            schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Task delete successfully",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Task delete successfully")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Not found",
                content: new OA\JsonContent(
                    type: "object",
                    properties : [
                        new OA\Property(property: "message", type: "message", example: "Not found"),
                        new OA\Property(property: "errors", type: "object", example: "Задачи с таким id не существует")
                    ]
                )
            )
        ]
    )]
    public function destroy(int $id)
    {
        $task = Task::where('id', $id)->first();
        if($task instanceof Task){

            try {
                TaskService::delete($task);
                return response()->json([
                    "message" => "Task deleted successfully"
                ], 204);
            } catch (Throwable $e) {

                Log::error("Ошибка при обновлении задачи {$task->id} в БД: " . $e->getMessage(), [
                    "task" => $task,
                    "exception" => $e
                ]);
            }
        }

        return response()->json([
                "error" => "Not found",
                "message" => "Задачи с таким id не существует"
            ], 404);
    }
}
