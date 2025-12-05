<?php

use App\Data\TicketProcessingResult;

it('can represent an empty ticket processing result', function () {
    $result = TicketProcessingResult::empty();

    expect($result->processedCount)->toBe(0)
        ->and($result->ticketIds)->toBeArray()->toBeEmpty()
        ->and($result->hasProcessed())->toBeFalse();
});

it('can represent a non-empty ticket processing result', function () {
    $result = new TicketProcessingResult(
        processedCount: 3,
        ticketIds: [1, 2, 3],
    );

    expect($result->processedCount)->toBe(3)
        ->and($result->ticketIds)->toEqual([1, 2, 3])
        ->and($result->hasProcessed())->toBeTrue();
});
