<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Community;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Report::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text'    => $this->faker->sentence(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * @return ReportFactory
     */
    public function community()
    {
        return $this->state(function (array $attributes) {
            return [
                'reportable_type' => Community::class,
                'reportable_id'   => Community::factory(),
            ];
        });
    }

    /**
     * @return ReportFactory
     */
    public function post()
    {
        return $this->state(function (array $attributes) {
            return [
                'reportable_type' => Post::class,
                'reportable_id'   => Post::factory(),
            ];
        });
    }

    /**
     * @return ReportFactory
     */
    public function comment()
    {
        return $this->state(function (array $attributes) {
            return [
                'reportable_type' => PostComment::class,
                'reportable_id'   => PostComment::factory(),
            ];
        });
    }

    /**
     * @return ReportFactory
     */
    public function deleted()
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => $this->faker->dateTime,
            ];
        });
    }
}
