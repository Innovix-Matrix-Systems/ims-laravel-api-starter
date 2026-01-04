<?php

namespace Database\Factories;

use App\Enums\DataProcessingJobStatus;
use App\Enums\DataProcessingJobType;
use App\Models\DataProcessingJob;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class DataProcessingJobFactory extends Factory
{
    protected $model = DataProcessingJob::class;

    public function definition(): array
    {
        return [
            'job_id' => Str::uuid()->toString(),
            'type' => $this->faker->randomElement(DataProcessingJobType::cases()),
            'status' => $this->faker->randomElement(DataProcessingJobStatus::cases()),
            'entity_type' => 'User',
            'filters' => null,
            'file_name' => $this->faker->optional()->word . '.xlsx',
            'file_path' => $this->faker->optional()->word . '.xlsx',
            'original_file_name' => $this->faker->optional()->word . '.xlsx',
            'total_rows' => $this->faker->optional()->numberBetween(1, 1000),
            'processed_rows' => $this->faker->optional()->numberBetween(0, 1000),
            'success_count' => $this->faker->optional()->numberBetween(0, 1000),
            'error_count' => $this->faker->optional()->numberBetween(0, 100),
            'errors' => null,
            'error_message' => $this->faker->optional()->sentence(),
            'started_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-1 week', 'now'),
            'user_id' => User::factory(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DataProcessingJobStatus::PENDING,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DataProcessingJobStatus::PROCESSING,
            'started_at' => now(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DataProcessingJobStatus::COMPLETED,
            'completed_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => DataProcessingJobStatus::FAILED,
            'error_message' => $this->faker->sentence(),
            'completed_at' => now(),
        ]);
    }

    public function import(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => DataProcessingJobType::IMPORT,
        ]);
    }

    public function export(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => DataProcessingJobType::EXPORT,
        ]);
    }
}
