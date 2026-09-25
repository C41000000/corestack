<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Auth;

use App\Actions\Auth\AuthenticateCentralUser;
use App\DTOs\Auth\LoginDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Dedoc\Scramble\Attributes\ExcludeAllRoutesFromDocs;

#[ExcludeAllRoutesFromDocs]
final class AuthController extends Controller
{
    public function login(LoginRequest $request, AuthenticateCentralUser $action)
    {
        return response()->json([
            $action->execute(LoginDTO::fromRequest($request)),
        ]);
    }
}
