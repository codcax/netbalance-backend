<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;

class LoginUserController extends Controller
{
    use HttpResponses;

    /**
     * Handle an incoming authentication request.
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function __invoke(LoginRequest $request): JsonResponse
    {
        $request->validated($request->all());

        $user = rescue(function () use ($request) {
            $user = User::withTrashed()
                ->where('email', $request->email)
                ->first();

            if ($user && $user->trashed() && Hash::check($request->password, $user->password)) {
                $user->restore();
                $user->forceFill(
                    [
                        'remember_token' => Str::random(60),
                        'is_active' => true
                    ]
                )->save();
            }

            return true;
        }, false);

        if ($user || !Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            return $this->unauthorizedResponse([], __('auth.failed'));
        }

        $request->session()->regenerate();

        return $this->successResponse(new UserResource(Auth::user()));
    }
}
