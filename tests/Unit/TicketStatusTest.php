<?php

use App\Enums\TicketStatus;

it('provides human readable labels for each status', function () {
    expect(TicketStatus::OPEN->label())->toBe('Open');
    expect(TicketStatus::CLOSED->label())->toBe('Closed');
});

it('can be created from its integer values', function () {
    expect(TicketStatus::from(1))->toBe(TicketStatus::OPEN);
    expect(TicketStatus::from(2))->toBe(TicketStatus::CLOSED);
});

it('returns an array of all statuses', function () {
   expect(TicketStatus::values())->toBeArray()->toHaveCount(2);
});
