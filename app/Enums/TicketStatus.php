<?php

namespace App\Enums;

/**
 * Represents the status of a support ticket.
 */
enum TicketStatus: int
{
    /**
     * The ticket is currently open and active.
     */
    case OPEN = 1;

    /**
     * The ticket has been resolved or closed.
     */
    case CLOSED = 2;

    /**
     * Get the human-readable label for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            TicketStatus::OPEN => __('Open'),
            TicketStatus::CLOSED => __('Closed'),
        };
    }

    /**
     * Get all the values of the enum.
     *
     * @return array<int>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
