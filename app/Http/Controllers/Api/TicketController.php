<?php

namespace App\Http\Controllers\Api;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Read-only ticket APIs.
 *
 * These endpoints are optimised for large datasets using pagination
 * and eager-loading to avoid N+1 queries.
 */
class TicketController extends Controller
{
    /**
     * GET /api/tickets/open
     * Oldest open tickets first.
     */
    public function open(Request $request)
    {
        $perPage = $this->perPage($request);

        $tickets = Ticket::query()
            ->where('status', TicketStatus::OPEN)
            ->with('user')
            ->orderBy('created_at')
            ->paginate($perPage);

        return TicketResource::collection($tickets);
    }

    /**
     * GET /api/tickets/closed
     * Most recently closed tickets first.
     */
    public function closed(Request $request)
    {
        $perPage = $this->perPage($request);

        $tickets = Ticket::query()
            ->where('status', TicketStatus::CLOSED)
            ->with('user')
            ->orderByDesc('updated_at')
            ->paginate($perPage);

        return TicketResource::collection($tickets);
    }

    /**
     * GET /api/users/{user}/tickets
     * Tickets for a specific user.
     */
    public function byUser(Request $request, User $user)
    {
        $perPage = $this->perPage($request);

        $tickets = Ticket::query()
            ->where('user_id', $user->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return TicketResource::collection($tickets);
    }

    /**
     * Clamp per_page between 1 and 200.
     */
    protected function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 50);

        return max(1, min($perPage, 200));
    }
}
