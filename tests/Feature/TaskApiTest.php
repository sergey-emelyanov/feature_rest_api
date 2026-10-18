<?php

namespace Tests\Feature;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskApiTest extends TestCase
{

    use RefreshDatabase;


    public function test_can_get_list_of_tasks(): void
    {
        Task::factory()->count(3)->create();

        $response = $this->getJson('/api/tasks');
        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data');
    }


    public function test_can_create_a_task(): void
    {
        $data = [
            'title' => 'Задача для тестирования',
            'description' => 'Описание',
            'due_date' => '2025-01-20T15:00:00',
            'create_date' => '2025-01-25T15:00:00',
            'priority' => 'Высокий',
            'category' => 'Работа',
            'status' => 'Не выполнена'
        ];

        $response = $this->postJson('/api/tasks', $data);

        $response->assertStatus(201)
            ->assertJsonPath('message', 'Task created successfully');

        $this->assertDatabaseHas('tasks', ['title' => 'Задача для тестирования']);
    }


    public function test_validation_error_on_create(): void
    {
        $data = [
            'due_date' => '2025-01-20T15:00:00',
            'create_date' => '2025-01-25T15:00:00',
            'priority' => 'Высокий',
            'category' => 'Работа',
            'status' => 'Не выполнена'
        ];

        $response = $this->postJson('/api/tasks', $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);
    }


    public function test_can_delete_a_task():void
    {

        $task = Task::factory()->create();

        $response = $this->deleteJson("/api/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_404_on_delete():void
    {
        $response = $this->deleteJson("/api/tasks/99999");
        $response->assertStatus(404);
    }

    public function test_can_show_a_task(): void
    {
        $task = Task::factory()->create();
        $response = $this->getJson("/api/tasks/{$task->id}");
        $response->assertStatus(200)
                ->assertJsonPath('id', $task->id)
                ->assertJsonPath('title', $task->title);
    }

    public function test_404_on_show():void
    {
        $response = $this->getJson("/api/tasks/999999");
        $response->assertStatus(404);
    }

    public function test_can_update_a_task(): void
    {
        $task = Task::factory()->create([
            'title' => 'Старое название',
            'status' => 'Новая'
        ]);

        $data = [
            'title' => 'Новое название',
            'description' => 'Новое описание',
            'due_date' => '2025-12-31 23:59:59',
            'create_date' => '2025-01-25T15:00:00',
            'status' => 'В работе',
            'priority' => 'Высокий',
            'category' => 'Работа'
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $data);

        $response->assertStatus(204);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Новое название',
            'status' => 'В работе',
        ]);
    }


    public function test_validation_error_on_update()
    {
        $task = Task::factory()->create();

        $data = [
            'due_date' => '2025-01-20T15:00:00',
            'create_date' => '2025-01-25T15:00:00',
            'priority' => 'Высокий',
            'category' => 'Работа',
            'status' => 'Не выполнена'
        ];

        $response = $this->putJson("/api/tasks/{$task->id}", $data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'description']);
    }


    public function test_404_on_update():void
    {
        $data = [
            'title' => 'Новое название',
            'description' => 'Новое описание',
            'due_date' => '2025-01-20T15:00:00',
            'create_date' => '2025-01-25T15:00:00',
            'priority' => 'Высокий',
            'category' => 'Работа',
            'status' => 'Не выполнена'
        ];

        $response = $this->putJson("/api/tasks/99999999", $data);
        $response->assertStatus(404);
    }
}
