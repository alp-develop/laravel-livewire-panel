<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Http\Middleware;

use AlpDevelop\LivewirePanel\PanelResolver;
use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PanelRateLimitMiddleware
{
    public function __construct(
        private readonly PanelResolver $resolver,
        private readonly RateLimiter   $limiter,
    ) {}

    public function handle(Request $request, Closure $next, string $action = 'login'): Response
    {
        $panelId     = $this->resolver->resolveFromRequest($request);
        $panelConfig = $this->resolver->resolveById($panelId);

        /** @var array{attempts?: int, decay_minutes?: int} $rateConfig */
        $rateConfig = $panelConfig['rate_limiting'][$action] ?? ['attempts' => 5, 'decay_minutes' => 1];

        $maxAttempts  = (int) ($rateConfig['attempts'] ?? 5);
        $decayMinutes = (int) ($rateConfig['decay_minutes'] ?? 1);
        $key          = 'panel.' . $panelId . '.' . $action . '.' . ($request->ip() ?? '');

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            $seconds = $this->limiter->availableIn($key);

            return response()->json([
                'message' => 'Too many attempts. Please wait ' . $seconds . ' seconds.',
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        if ($response->getStatusCode() < 400) {
            $this->limiter->clear($key);
        }

        return $response;
    }
}
