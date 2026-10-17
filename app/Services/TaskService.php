<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{

    public static function create(array $data)
    {
        $task = Task::create($data);
        return $task;

    }
}
