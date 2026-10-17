<?php

namespace App\Http\Resources\Task;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "TaskResource",
    required: ["title", "description", "due_date", "create_date", "status", "priority", "category"],
    properties: [
        new OA\Property(property: "title", type: "string", example: "Задача1"),
        new OA\Property(property: "description", type: "text", format: "text", example: "Описание задачи"),
        new OA\Property(property: "due_date", type: "string", format: "date-time", example: "2024-01-01T00:00:00"),
        new OA\Property(property: "create_date", type: "string", format: "date-time", example: "2024-01-01T00:00:00"),
        new OA\Property(property: "status", type: "string", example: "Завершена"),
        new OA\Property(property: "priority", type: "string", example: "Высокий"),
        new OA\Property(property: "category", type: "string", example: "Работа"),
    ],
    type: "object"
)]
class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'create_date' => $this->create_date,
            'status' => $this->status,
            'priority' => $this->priority,
            'category' => $this->category,
        ];

    }
}
