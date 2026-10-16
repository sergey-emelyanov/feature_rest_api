<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;



class TaskController extends Controller
{
    #[OA\Get(
        path: '/api/tasks/',
        summary: 'Получить список задач',
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
        $tasks = Task::all();
        return response()->json($tasks);
    }
}
