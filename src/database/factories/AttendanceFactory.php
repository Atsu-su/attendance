<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, User::count()),
            'status' => $this->faker->randomElement(['on_duty', 'off_duty', 'break']),
            'date' => $this->faker->date(),
            'start_time' => $this->faker->time('H:i', '08:30', '11:00'),
            'end_time' => $this->faker->time('H:i', '16:00', '18:00'),
            'break_time' => $this->faker->time('H:i', '00:20', '01:30'),
        ];
    }
}
