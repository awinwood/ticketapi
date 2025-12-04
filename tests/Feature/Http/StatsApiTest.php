<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns aggregate ticket stats', function () {
    $user   = User::factory()->create();
    $other  = User::factory()->create();

    Sanctum::actingAs($user, ['read:tickets']);

    Ticket::factory()->count(3)->create([
        'user_id' => $user->id,
        'status'  => TicketStatus::OPEN,
    ]);

    Ticket::factory()->count(2)->create([
        'user_id' => $user->id,
        'status'  => TicketStatus::CLOSED,
    ]);

    Ticket::factory()->count(5)->create([
        'user_id' => $other->id,
        'status'  => TicketStatus::CLOSED,
    ]);

    $response = $this->getJson('/api/stats');

    $response->assertOk()
        ->assertJsonStructure([
            'total_tickets',
            'total_unprocessed_tickets',
            'top_user',
            'most_recent_processed_at',
        ]);

    $json = $response->json();

    expect($json['total_tickets'])->toBe(10);
    expect($json['total_unprocessed_tickets'])->toBe(3);
    expect($json['top_user']['tickets_count'])->toBe(5);
});
