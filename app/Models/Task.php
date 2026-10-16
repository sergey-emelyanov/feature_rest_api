<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "Task",
    title: "Task",
    description: "Task model",
    required: [],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Задача1"),
        new OA\Property(property: "description", type: "text", format: "text", example: "Описание задачи"),
        new OA\Property(property: "deadline", type: "string", format: "date-time", example: "2024-01-01T00:00:00Z"),
        new OA\Property(property: "status", type: "string", example: "Завершена"),
        new OA\Property(property: "priority", type: "string", example: "Приоритет"),
        new OA\Property(property: "category", type: "string", example: "Категория"),
    ]
)]
class Task extends Model
{
    //
}
