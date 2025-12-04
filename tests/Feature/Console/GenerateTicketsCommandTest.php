<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

it('generates tickets via the console command', function () {
    User::factory()->create();

    expect(Ticket::count())->toBe(0);

    $this->artisan('tickets:generate', [
        '--count' => 3,
    ])->assertExitCode(0);

    expect(Ticket::count())->toBe(3);

    Ticket::all()->each(function (Ticket $ticket) {
        expect($ticket->status)->toBe(TicketStatus::OPEN);
    });
});
