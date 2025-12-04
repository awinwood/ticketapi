<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Services\Tickets\TicketProcessorService;

it('returns empty result when limit is zero or negative', function () {
    $service = app(TicketProcessorService::class);

    $resultZero = $service->process(0);
    $resultNeg  = $service->process(-5);

    expect($resultZero->hasProcessed())->toBeFalse();
    expect($resultNeg->hasProcessed())->toBeFalse();
});

it('processes up to the specified number of oldest open tickets', function () {
    // Arrange: create 5 open tickets and 2 closed tickets
    $openTickets   = Ticket::factory()->count(5)->create([
        'status' => TicketStatus::OPEN,
    ]);

    $closedTickets = Ticket::factory()->count(2)->create([
        'status' => TicketStatus::CLOSED,
    ]);

    // Make sure created_at ordering is stable
    $openTickets->each(function ($ticket, $index) {
        $ticket->update(['created_at' => now()->subMinutes(10 - $index)]);
    });

    $service = app(TicketProcessorService::class);

    // Act: process 3
    $result = $service->process(3);

    // Assert
    expect($result->processedCount)->toBe(3);
    expect($result->ticketIds)->toHaveCount(3);

    // First 3 open tickets should now be CLOSED, remaining 2 still OPEN
    $firstThreeIds = $openTickets->take(3)->pluck('id')->all();
    $lastTwoIds    = $openTickets->slice(3)->pluck('id')->all();

    expect(Ticket::whereIn('id', $firstThreeIds)->pluck('status')->all())
        ->each->toBe(TicketStatus::CLOSED);

    expect(Ticket::whereIn('id', $lastTwoIds)->pluck('status')->all())
        ->each->toBe(TicketStatus::OPEN);

    // closedTickets should remain CLOSED
    expect(Ticket::whereIn('id', $closedTickets->pluck('id'))->pluck('status')->all())
        ->each->toBe(TicketStatus::CLOSED);
});

it('does not fail when there are no open tickets to process', function () {
    Ticket::factory()->count(3)->create([
        'status' => TicketStatus::CLOSED,
    ]);

    $service = app(TicketProcessorService::class);

    $result = $service->process(5);

    expect($result->hasProcessed())->toBeFalse();
    expect($result->processedCount)->toBe(0);
});
