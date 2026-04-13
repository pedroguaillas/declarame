<?php

namespace App\Http\Controllers;

use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SsoController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function handle(Request $request): RedirectResponse
    {
        $user = $this->authService->verifyAccess($request);

        if ($user === false) {
            return redirect()->away(config('app.url').'/login')
                ->with('error', 'Token SSO inválido o expirado.');
        }

        $this->authService->logout($request, 'web');
        $this->authService->loginInTenant($request, $user);

        return redirect()->route('tenant.dashboard');
    }
}
