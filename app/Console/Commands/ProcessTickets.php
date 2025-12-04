<?php

namespace App\Console\Commands;

use App\Services\Tickets\TicketProcessorService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Console command to process (close) open tickets.
 *
 * Each run will iterate through the specified number of open tickets and close them
 * one at a time, starting with the oldest one first.
 */
class ProcessTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:process {--count=1 : Number of tickets to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process N open tickets, resolving them one by one';

    /**
     * Execute the console command.
     */
    public function handle(TicketProcessorService $processor): int
    {
        $count = (int) $this->option('count');

        $result = $processor->process($count);

        if (!$result->hasProcessed()) {
            $this->info('No tickets to process');
            return self::SUCCESS;
        }

        $this->info("Processed " . $result->processedCount . " " . Str::plural('ticket', $result->processedCount));
        $this->line("Ticket ID" . ($result->processedCount === 1 ? '' : 's') . ": " . implode(', ', $result->ticketIds));

        return self::SUCCESS;
    }
}
