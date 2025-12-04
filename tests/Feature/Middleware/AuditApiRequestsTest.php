<?php

use App\Models\ApiAudit;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('creates an audit log entry for API requests', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['read:tickets']);

    expect(ApiAudit::count())->toBe(0);

    $this->getJson('/api/tickets/open')->assertOk();

    expect(ApiAudit::count())->toBe(1);

    $audit = ApiAudit::first();

    expect($audit->user_id)->toBe($user->id);
    expect($audit->method)->toBe('GET');
    expect($audit->path)->toBe('api/tickets/open');
    expect($audit->status_code)->toBe(200);
});
