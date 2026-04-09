<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Http\Middleware;

use AlpDevelop\LivewirePanel\Auth\PanelAccessRegistry;
use AlpDevelop\LivewirePanel\Events\PanelAccessDenied;
use AlpDevelop\LivewirePanel\PanelContext;
use AlpDevelop\LivewirePanel\PanelResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PanelAuthMiddleware
{
    public function __construct(
        private readonly PanelResolver       $resolver,
        private readonly PanelContext        $context,
        private readonly PanelAccessRegistry $accessRegistry,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $panelId     = $this->resolver->resolveFromRequest($request);
        $panelConfig = $this->resolver->resolveById($panelId);
        $guard       = (string) ($panelConfig['guard'] ?? 'web');

        $this->context->set($panelId);

        if (!auth()->guard($guard)->check()) {
            return redirect()->route("panel.{$panelId}.auth.login");
        }

        if ($this->accessRegistry->has($panelId)) {
            $user = auth()->guard($guard)->user();

            if (!$this->accessRegistry->check($panelId, $user)) {
                $target = $this->accessRegistry->findPanel($user);

                if ($target !== null) {
                    $this->switchGuardIfNeeded($request, $guard, $target, $user);

                    return redirect(panel_route($target, 'home'));
                }

                event(new PanelAccessDenied($panelId, $user ? (int) $user->getAuthIdentifier() : null, 'access_denied', $request->ip()));

                return redirect()->route("panel.{$panelId}.auth.login");
            }
        }

        return $next($request);
    }

    private function switchGuardIfNeeded(Request $request, string $currentGuard, string $targetPanelId, mixed $user): void
    {
        $targetConfig = $this->resolver->resolveById($targetPanelId);
        $targetGuard  = (string) ($targetConfig['guard'] ?? 'web');

        if ($targetGuard === $currentGuard || $user === null) {
            return;
        }

        $targetProvider = (string) config("auth.guards.{$targetGuard}.provider", 'users');
        /** @var class-string $targetModel */
        $targetModel    = (string) config("auth.providers.{$targetProvider}.model", 'App\Models\User');
        $targetUser     = $targetModel::find($user->getAuthIdentifier());

        if ($targetUser !== null) {
            auth()->guard($targetGuard)->login($targetUser);
            $request->session()->regenerate();
        }
    }
}
