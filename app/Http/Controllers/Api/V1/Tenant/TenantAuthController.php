<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Actions\Tenant\Auth\TenantLoginAction;
use App\DTOs\Tenant\Auth\TenantLoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Auth\TenantLoginRequest;
use App\Http\Resources\Tenant\UserResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

#[Group(name: 'Tenant / Autenticação', description: 'Autenticação e gerenciamento de sessão dos usuários do tenant')]
final class TenantAuthController extends Controller
{
    #[Endpoint(title: 'Login do Usuário Tenant', description: 'Autentica um usuário do tenant e retorna o Token JWT.')]
    public function login(TenantLoginRequest $request, TenantLoginAction $action): JsonResponse
    {
        $result = $action->execute(TenantLoginDTO::fromRequest($request));

        return response()->json([
            'access_token' => $result['access_token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => new UserResource($result['user']),
        ]);
    }

    #[Endpoint(title: 'Dados do Usuário Autenticado', description: 'Retorna as informações e permissões do usuário tenant autenticado.')]
    public function me(): UserResource
    {
        /** @var User $user */
        $user = Auth::guard('tenant_api')->user();

        return new UserResource($user);
    }

    #[Endpoint(title: 'Logout do Tenant', description: 'Invalida o token JWT do usuário tenant atual.')]
    public function logout(): JsonResponse
    {
        Auth::guard('tenant_api')->logout();

        return response()->json([
            'message' => 'Successfully logged out.',
        ]);
    }
}
