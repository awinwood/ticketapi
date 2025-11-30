<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $subject
 * @property string $content
 * @property int $user_id
 * @property TicketStatus $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read User $user
 */
class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'content',
        'user_id',
        'status',
    ];

    protected $casts = [
        'status' => TicketStatus::class,
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
