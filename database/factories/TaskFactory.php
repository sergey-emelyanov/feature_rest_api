<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'due_date' => $this->faker->dateTimeBetween('now', '+30 days'),
            'create_date' => $this->faker->dateTimeBetween('-30 days', 'now'),
            'status' => $this->faker->randomElement(['Новая', 'В работе', 'На проверке', 'Завершена']),
            'priority' => $this->faker->randomElement(['Низкий', 'Средний', 'Высокий']),
            'category' => $this->faker->randomElement(['Работа', 'Личное', 'Учеба', 'Финансы', 'Дом']),
        ];
    }
}
