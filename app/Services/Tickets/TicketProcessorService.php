<?php

namespace App\Services\Tickets;

use App\Data\TicketProcessingResult;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;


/**
 * Service responsible for processing (closing) open tickets.
 *
 * The implementation is designed to be safe under concurrent execution by:
 * - Selecting the oldest open tickets up to a configurable limit.
 * - Locking the selected rows FOR UPDATE inside a transaction.
 *
 * Tickets are still updated one-by-one in a loop (no bulk update query),
 * but they are processed in a single transactional batch for consistency.
 */
class TicketProcessorService
{
    /**
     * Process (close) up to a given number of open tickets.
     *
     * Tickets are processed in FIFO order (oldest first) to simulate a real
     * support queue. The method returns a value object describing the outcome
     * of the run, which is convenient for logging and testing.
     *
     * @param  int  $count  Maximum number of tickets to process in this run.
     * @return \App\Data\TicketProcessingResult
     */
    public function process(int $count = 1): TicketProcessingResult
    {
        if ($count <= 0) {
            return TicketProcessingResult::empty();
        }

        return DB::transaction(function () use ($count): TicketProcessingResult {
            // Lock the selected tickets to avoid double-processing in concurrent runs.
            $tickets = Ticket::query()
                ->where('status', TicketStatus::OPEN)
                ->orderBy('created_at')
                ->limit($count)
                ->lockForUpdate()
                ->get();

            if ($tickets->isEmpty()) {
                return TicketProcessingResult::empty();
            }

            $processedIds = [];

            // Process the tickets one at a time (no bulk update).
            foreach ($tickets as $ticket) {
                // In case status somehow changed between select and loop.
                if ($ticket->status !== TicketStatus::OPEN) {
                    continue;
                }

                $updated = $ticket->update([
                    'status'     => TicketStatus::CLOSED,
                    'updated_at' => now(),
                ]);

                if ($updated) {
                    $processedIds[] = $ticket->id;
                }
            }

            if (empty($processedIds)) {
                return TicketProcessingResult::empty();
            }

            return new TicketProcessingResult(
                processedCount: count($processedIds),
                ticketIds: $processedIds,
            );
        });
    }
}
