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
 * - Selecting the oldest open tickets.
 * - Chunking the results into batches of a configurable size to limit the number stored in memory.
 * - Locking the selected rows FOR UPDATE inside a transaction.
 *
 * This ensures that two concurrent runs do not process the same tickets twice.
 */
class TicketProcessorService
{
    /**
     * Process (close) a batch of open tickets.
     *
     * Tickets are processed in FIFO order (oldest first) to simulate a real
     * support queue. The method returns a value object describing the outcome
     * of the run, which is convenient for logging and testing.
     *
     * @param  int  $chunkSize  Number of tickets to read into memory at o time.
     * @return \App\Data\TicketProcessingResult
     */
    public function process(int $chunkSize = 100): TicketProcessingResult
    {
        if ($chunkSize <= 0) {
            return TicketProcessingResult::empty();
        }

        $processedIds = [];

        Ticket::query()
            ->where('status', TicketStatus::OPEN)
            ->orderBy('created_at')
            ->chunkById($chunkSize, function ($tickets) use (&$processedIds) {
                foreach ($tickets as $ticket) {
                    DB::transaction(function () use ($ticket, &$processedIds) {
                        // Re-fetch & lock the row to be safe under concurrency.
                        $locked = Ticket::query()
                            ->whereKey($ticket->id)
                            ->lockForUpdate()
                            ->first();

                        if (! $locked) {
                            return;
                        }

                        // Skip if another process already closed it.
                        if ($locked->status !== TicketStatus::OPEN) {
                            return;
                        }

                        $locked->update(['status' => TicketStatus::CLOSED]);

                        $processedIds[] = $locked->id;
                    });
                }
            });

        if (empty($processedIds)) {
            return TicketProcessingResult::empty();
        }

        return new TicketProcessingResult(
            processedCount: count($processedIds),
            ticketIds: $processedIds,
        );
    }
}
