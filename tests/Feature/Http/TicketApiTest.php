<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->user = User::factory()->create();
    //Sanctum::actingAs($this->user, ['read:tickets']);
});

it('returns paginated open tickets', function () {
    Sanctum::actingAs($this->user, ['read:tickets']);

    Ticket::factory()->count(3)->create([
        'status' => TicketStatus::OPEN,
    ]);

    Ticket::factory()->count(2)->create([
        'status' => TicketStatus::CLOSED,
    ]);

    $response = $this->getJson('/api/tickets/open');

    $response->assertOk()
        ->assertJsonStructure([
            'data',
            'links',
            'meta',
        ]);

    $data = $response->json('data');

    expect($data)->toHaveCount(3);
    foreach ($data as $ticket) {
        expect($ticket['status'])->toBe('Open');
    }
});

it('returns paginated closed tickets', function () {
    Sanctum::actingAs($this->user, ['read:tickets']);

    Ticket::factory()->count(4)->create([
        'status' => TicketStatus::CLOSED,
    ]);

    Ticket::factory()->count(1)->create([
        'status' => TicketStatus::OPEN,
    ]);

    $response = $this->getJson('/api/tickets/closed');

    $response->assertOk();

    $data = $response->json('data');

    expect($data)->toHaveCount(4);
    foreach ($data as $ticket) {
        expect($ticket['status'])->toBe('Closed');
    }
});

it('returns tickets for a specific user', function () {
    Sanctum::actingAs($this->user, ['read:tickets']);

    $otherUser = User::factory()->create();

    $userTickets = Ticket::factory()->count(2)->create([
        'user_id' => $this->user->id,
        'status'  => TicketStatus::OPEN,
    ]);

    Ticket::factory()->count(3)->create([
        'user_id' => $otherUser->id,
        'status'  => TicketStatus::OPEN,
    ]);

    $response = $this->getJson("/api/users/{$this->user->id}/tickets");

    $response->assertOk();

    $data = $response->json('data');

    expect($data)->toHaveCount(2);
    $ids = collect($data)->pluck('id')->all();
    expect($ids)->toEqualCanonicalizing($userTickets->pluck('id')->all());
});

it('requires authentication for ticket endpoints', function () {
    $response = $this->getJson('/api/tickets/open');

    $response->assertStatus(401);
});
