<?php

namespace App\Http\Requests\Task;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;


#[OA\Schema(
    schema: "StoreRequest",
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
class StoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "title" => "required|string",
            "description" => "required|string",
            "due_date" => "required|date",
            "create_date" => "required|date",
            "status" => "required|string",
            "priority" => "required|string",
            "category" => "required|string"
        ];
    }
}
