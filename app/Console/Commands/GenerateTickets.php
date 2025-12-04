<?php

namespace App\Console\Commands;

use App\Services\Tickets\TicketGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Console command to generate dummy support tickets.
 *
 * This is intended for simulation and test data only. In a real system,
 * tickets would typically be created via user actions or integrations.
 */
class GenerateTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:generate {--count=1 : Number of tickets to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy support tickets for simulation and testing';

    /**
     * Execute the console command.
     */
    public function handle(TicketGeneratorService $generator)
    {
        $count = (int) $this->option('count');

        if ($count <= 0) {
            $this->error('Invalid ticket count');
            return self::INVALID;
        }

        $tickets = $generator->generate($count);

        $this->info("Generated " . $tickets->count() . " " . Str::plural('ticket', $tickets->count()));

        return self::SUCCESS;
    }
}
