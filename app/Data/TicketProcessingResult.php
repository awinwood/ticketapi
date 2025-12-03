<?php

namespace App\Data;

/**
 * Immutable class representing the result of processing a ticket.
 */
class TicketProcessingResult
{
    /**
     * @param  int    $processedCount  Number of tickets that were processed.
     * @param  int[]  $ticketIds       IDs of the tickets that were processed.
     */
    public function __construct(public int $processedCount, public array $ticketIds) {
        //
    }

    /**
     * Create an empty result representing a run where nothing was processed.
     *
     * @return static
     */
    public static function empty(): self
    {
        return new self(0, []);
    }

    /**
     * Determine if any tickets were processed in this run.
     */
    public function hasProcessed(): bool
    {
        return $this->processedCount > 0;
    }
}
