<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\Auth\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    use HttpResponses;

    /**
     * Handle an incoming reset password request.
     * 
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(ResetPasswordRequest $request): JsonResponse
    {
        $request->validated($request->all());

        $user = User::where('email', $request->email)->first();

        if (Hash::check($request->password, $user->password)) {
            return $this->unprocessableResponse([], 'Your new password cannot be same as current password.');
        }

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => $request->password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );


        if ($status != Password::PASSWORD_RESET) {
            return $this->unprocessableResponse([], __($status));
        }


        return $this->noContentResponse();
    }
}
