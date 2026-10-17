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

    public static function update(array $data, Task $task)
    {
        $task->update($data);
        return $task->refresh;
    }


}
