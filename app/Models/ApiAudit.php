<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiAudit extends Model
{
    protected $fillable = [
        'user_id',
        'token_id',
        'method',
        'path',
        'route_name',
        'status_code',
        'ip_address',
        'user_agent',
        'duration_ms',
        'query',
        'request_body',
    ];
}
