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
        new OA\Property(property: "title", type: "string", example: "Задача1"),
        new OA\Property(property: "description", type: "text", format: "text", example: "Описание задачи"),
        new OA\Property(property: "due_date", type: "string", format: "date-time", example: "2024-01-01T00:00:00Z"),
        new OA\Property(property: "create_date", type: "string", format: "date-time", example: "2024-01-01T00:00:00Z"),
        new OA\Property(property: "status", type: "string", example: "Завершена"),
        new OA\Property(property: "priority", type: "string", example: "Высокий"),
        new OA\Property(property: "category", type: "string", example: "Работа"),
    ]
)]
class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'create_date',
        'status',
        'priority',
        'category'
    ];
}
