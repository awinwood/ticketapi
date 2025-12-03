<?php
namespace App\Services\Tickets;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Service responsible for generating dummy support tickets.
 *
 * This class is intentionally stateless to make it simple to test and reuse
 * from console commands, HTTP controllers, or seeders.
 */
class TicketGeneratorService
{
    /**
     * Generate a number of dummy tickets for random users.
     *
     * If no users exist in the database, a small pool of users will be created
     * to attach tickets to. This avoids foreign key issues in a fresh database.
     *
     * @param  int  $count  Number of tickets to create.
     * @return \Illuminate\Support\Collection<\App\Models\Ticket>  The created tickets.
     */
    public function generate(int $count = 10): Collection
    {
        // Use existing users if available, otherwise create a small pool of fake users.
        $users = User::all()->count() ? User::all() : User::factory()->count($count)->create();

        $tickets = collect();

        for ($i = 0; $i < $count; $i++) {
            /** @var \App\Models\User $user  */
            $user = $users->random();

            $tickets->push(
                Ticket::factory()->for($user)->create([
                    'subject' => fake()->realText(60),
                    'content' => fake()->realText(200),
                    'status' => TicketStatus::OPEN,
                ])
            );
        }

        return $tickets;
    }
}
