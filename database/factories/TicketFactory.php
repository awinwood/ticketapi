<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject' => fake()->realText(60),
            'content' => fake()->realText(200),
            'status' => TicketStatus::OPEN,
        ];
    }


    /**
     * Indicate that the ticket is closed.
     *
     * @return static
     */
    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::CLOSED,
        ]);
    }
}
