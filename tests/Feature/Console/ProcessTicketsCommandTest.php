<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

beforeEach(function () {
    // Ensure there is at least one user so factories don't choke
    User::factory()->create();
});

it('processes up to the requested number of tickets via the console command', function () {
    $openTickets = Ticket::factory()->count(5)->create([
        'status' => TicketStatus::OPEN,
    ]);

    $this->artisan('tickets:process', [
        '--count' => 3,
    ])->assertExitCode(0);

    $closed = Ticket::where('status', TicketStatus::CLOSED)->get();
    $open   = Ticket::where('status', TicketStatus::OPEN)->get();

    expect($closed)->toHaveCount(3);
    expect($open)->toHaveCount(2);

    expect(Ticket::count())->toBe(5);
});

it('does nothing when there are no open tickets', function () {
    Ticket::factory()->count(2)->create([
        'status' => TicketStatus::CLOSED,
    ]);

    $this->artisan('tickets:process', [
        '--count' => 5,
    ])->assertExitCode(0);

    expect(Ticket::where('status', TicketStatus::OPEN)->count())->toBe(0);
    expect(Ticket::where('status', TicketStatus::CLOSED)->count())->toBe(2);
});
