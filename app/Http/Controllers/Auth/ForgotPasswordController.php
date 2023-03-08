<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\Auth\ForgotPasswordRequest;

class ForgotPasswordController extends Controller
{
    use HttpResponses;
    /**
     * Handle an incoming password reset link request.
     * 
     * @param ForgotPasswordRequestRequest $request
     * @return JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ForgotPasswordRequest $request): JsonResponse
    {
        $request->validated($request->all());

        $user = User::withTrashed()
            ->where('email', $request->email)
            ->first();

        if ($user->trashed()) {
            $user->restore();
            $user->forceFill(['remember_token' => Str::random(60)])->save();
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status != Password::RESET_LINK_SENT) {
            return $this->unprocessableResponse([], __($status));
        }

        return $this->noContentResponse();
    }
}
