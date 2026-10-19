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
        parameters:[
            new OA\Parameter(
                name: "search",
                description: "Параметр для поиска по названию (title)",
                in: "query",
                required: false,
                schema: new OA\Schema(type:"string")

            ),
            new OA\Parameter(
                name: "sort",
                description: "Сортировка. Доступные поля: due_date, create_date.
                Для сортировки по убыванию используйте префикс '-' (например, -due_date)",
                in: "query",
                required: false,
                schema: new OA\Schema(type:"string")
            ),
            new OA\Parameter(
                name: "per_page",
                description: "Количество задач на страницу",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "page",
                description: "Номер страницы",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses : [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "current_page", type: "integer", example: 1),

                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(ref: "#/components/schemas/Task")
                        ),

                        new OA\Property(property: "first_page_url", type: "string", example: "http://127.0.0.1:8876/api/tasks?page=1"),
                        new OA\Property(property: "from", type: "integer", example: 1),
                        new OA\Property(property: "last_page", type: "integer", example: 1),
                        new OA\Property(property: "last_page_url", type: "string", example: "http://127.0.0.1:8876/api/tasks?page=1"),

                        new OA\Property(
                            property: "links",
                            type: "array",
                            items: new OA\Items(
                                type: "object",
                                properties: [
                                    new OA\Property(property: "url", type: "string", nullable: true, example: null),
                                    new OA\Property(property: "label", type: "string", example: "Previous"),
                                    new OA\Property(property: "page", type: "integer", nullable: true, example: null),
                                    new OA\Property(property: "active", type: "boolean", example: false)
                                ]
                            )
                        ),

                        new OA\Property(property: "next_page_url", type: "string", nullable: true, example: null),
                        new OA\Property(property: "path", type: "string", example: "http://127.0.0.1:8876/api/tasks"),
                        new OA\Property(property: "per_page", type: "integer", example: 10),
                        new OA\Property(property: "prev_page_url", type: "string", nullable: true, example: null),
                        new OA\Property(property: "to", type: "integer", example: 3),
                        new OA\Property(property: "total", type: "integer", example: 3),
                    ]
                )
            )
        ]
    )]
    public function index(Request $request, TaskService $taskService)
    {

        return $taskService->getList($request->query());
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
    public function store(StoreRequest $request, TaskService $taskService)
    {
        $data = $request->validated();

        try {
            $task = $taskService->create($data);
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
    public function update(StoreRequest $request, int $id, TaskService $taskService)
    {
        $data = $request->validated();
        $task = Task::where('id', $id)->first();

        if($task instanceof Task){
            try {
                $task = $taskService->update($data, $task);
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
    public function destroy(int $id, TaskService $taskService)
    {
        $task = Task::where('id', $id)->first();
        if($task instanceof Task){

            try {
                $taskService->delete($task);
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
