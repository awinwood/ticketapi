<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Services\Tickets\TicketGeneratorService;

it('generates the requested number of tickets', function () {

    $existingUsers = User::factory()->count(3)->create();
    $service       = app(TicketGeneratorService::class);

    $tickets = $service->generate(5);

    expect($tickets)->toHaveCount(5);
    expect(Ticket::count())->toBe(5);

    $tickets->each(function (Ticket $ticket) use ($existingUsers) {
        expect($ticket->status)->toBe(TicketStatus::OPEN);
        expect($existingUsers->pluck('id'))->toContain($ticket->user_id);
    });
});

it('creates users if none exist when generating tickets', function () {
    expect(User::count())->toBe(0);

    $service = app(TicketGeneratorService::class);

    $tickets = $service->generate(3);

    expect($tickets)->toHaveCount(3);
    expect(User::count())->toBeGreaterThan(0);
    expect(Ticket::count())->toBe(3);
});
