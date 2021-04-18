<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Community;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'community_id' => Community::factory(),
            'user_id'      => User::factory(),
            'title'        => $this->faker->unique()->sentence,
            'text'         => $this->faker->text,
            'url'          => $this->faker->url,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
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
