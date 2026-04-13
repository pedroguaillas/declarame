<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsCentralAdmin
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('web');

        if ($user && $user->tenant_id) {
            $url = $this->authService->getRedirectionUrl($user);

            return redirect()->away($url);
        }

        return $next($request);
    }
}
