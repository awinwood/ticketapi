<?php

namespace App\Http\Middleware;

use App\Models\ApiAudit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditApiRequests
{
    /**
     * Handle an incoming request and record audit information.
     *
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $next($request);

        try {
            $this->recordAudit($request, $response, $startedAt);
        } catch (\Throwable $e) {
            // Do NOT break the API if auditing fails.
            Log::warning('Failed to write API audit log', [
                'exception' => $e->getMessage(),
            ]);
        }

        return $response;
    }

    /**
     * Persist audit information for the given request/response pair.
     */
    protected function recordAudit(Request $request, Response $response, float $startedAt): void
    {
        $user = $request->user();
        $token = method_exists($user, 'currentAccessToken')
            ? $user?->currentAccessToken()
            : null;

        $durationMs = (int) round((microtime(true) - $startedAt) * 1000);

        // Trim query/body to avoid huge blobs in the DB
        $query = $request->query();
        $body  = $request->all();

        ApiAudit::create([
            'user_id'      => $user?->id,
            'token_id'     => $token?->id,
            'method'       => $request->getMethod(),
            'path'         => $request->path(),
            'route_name'   => optional($request->route())->getName(),
            'status_code'  => $response->getStatusCode(),
            'ip_address'   => $request->ip(),
            'user_agent'   => substr((string) $request->userAgent(), 0, 255),
            'duration_ms'  => $durationMs,
            'query'        => $query ? json_encode($query) : null,
            'request_body' => $body ? json_encode($body) : null,
        ]);
    }
}
