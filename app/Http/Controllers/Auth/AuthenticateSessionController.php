<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;

class AuthenticateSessionController extends Controller
{
    use HttpResponses;

    /**
     * Handle an incoming authentication request.
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->validated($request->all());

        $user = User::withTrashed()
            ->where('email', $request->email)
            ->first();

        if (!$user || !Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return $this->unauthorizedResponse([], __('auth.failed'));
        }

        if ($user->trashed()) {
            $user->restore();
        }

        $request->session()->regenerate();

        return $this->successResponse(new UserResource(Auth::user()));
    }

    /**
     * Destroy an authenticated session.
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $this->noContentResponse();
    }
}
