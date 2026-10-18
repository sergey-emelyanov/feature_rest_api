<?php

namespace App\Services;

use App\Models\Task;

class TaskService
{

    public  function create(array $data)
    {
        $task = Task::create($data);
        return $task;

    }

    public function update(array $data, Task $task)
    {
        $task->update($data);
        return $task->refresh;
    }

    public  function delete(Task $task)
    {
        $task->delete();
    }

    public  function getList(array $filters=[])
    {
        $query = Task::query();

        $query->when(
                !empty($filters['search']),
                fn ($query) => $query->where('title', 'like', "%{$filters['search']}%")
                );

        $query->when(
            !empty($filters['sort']),
            fn ($q) => $this->applySorting($q, $filters['sort'])
        );

        return $query->get();
    }


    private function applySorting($query, string $sortString)
    {
        $allowedSorts = ['due_date', 'created_at'];

        $sorts = explode(',', $sortString);

        foreach ($sorts as $sort) {
            $direction = 'asc';

            if (str_starts_with($sort, '-')) {
                $direction = 'desc';
                $sort = substr($sort, 1);
            }

            if (in_array($sort, $allowedSorts, true)) {
                $query->orderBy($sort, $direction);
            }
        }

        return $query;
    }


}
