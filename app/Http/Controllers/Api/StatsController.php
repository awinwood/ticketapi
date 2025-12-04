<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Aggregate ticket statistics.
 *
 * GET /api/stats
 */
class StatsController extends Controller
{
    public function index()
    {
        $totalTickets = Ticket::count();

        $totalOpen = Ticket::where('status', TicketStatus::OPEN)->count();

        $topUserRow = Ticket::select('user_id', DB::raw('COUNT(*) as total'))
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->first();

        $topUser = null;

        if ($topUserRow) {
            /** @var \App\Models\User|null $user */
            $user = User::find($topUserRow->user_id);

            if ($user) {
                $topUser = [
                    'id'            => $user->id,
                    'name'          => $user->name,
                    'email'         => $user->email,
                    'tickets_count' => (int) $topUserRow->total,
                ];
            }
        }

        $mostRecentProcessedAt = Ticket::where('status', TicketStatus::CLOSED)
            ->max('updated_at');

        return response()->json([
            'total_tickets'             => $totalTickets,
            'total_unprocessed_tickets' => $totalOpen,
            'top_user'                  => $topUser,
            'most_recent_processed_at'  => $mostRecentProcessedAt,
        ]);
    }
}
