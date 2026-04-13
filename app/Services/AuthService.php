<?php

namespace App\Services;

use App\Models\Tenant\User as TenantUser;
use App\Models\User as CentralUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    public function __construct(
        private SSOTokenService $ssoTokenService,
    ) {}

    public function getRedirectionUrl(CentralUser $user): string
    {
        $domain = $user->tenant->domains()->first()->domain;

        $token = $this->ssoTokenService->generate(
            userId: $user->id,
            tenantId: $user->tenant_id,
            email: $user->email,
            name: $user->name,
            username: $user->username,
            is_active: $user->is_active,
        );

        return "http://{$domain}/auth/sso?token={$token}";
    }

    public function verifyAccess(Request $request): TenantUser|false
    {
        $token = $request->query('token', '');

        if (! $token) {
            return false;
        }

        $payload = $this->ssoTokenService->validate($token);

        if (! $payload) {
            return false;
        }

        if ($payload['tenant_id'] !== tenant()->getTenantKey()) {
            return false;
        }

        $tenantUser = TenantUser::updateOrCreate(
            ['central_user_id' => $payload['user_id']],
            [
                'name' => $payload['name'],
                'email' => $payload['email'],
                'username' => $payload['username'],
                'is_active' => $payload['is_active'],
            ],
        );

        return $tenantUser;
    }

    public function logout(Request $request, string $guard = 'web'): void
    {
        Auth::guard($guard)->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function loginInTenant(Request $request, TenantUser $user): void
    {
        Auth::guard('tenant')->login($user, true);
        $request->session()->regenerate();
    }
}
