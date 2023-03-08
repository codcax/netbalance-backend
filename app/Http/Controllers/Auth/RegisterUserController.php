<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterUserController extends Controller
{
    use HttpResponses;

    /**
     * Handle an incoming registration request.
     *
     * @param RegisterRequest $request
     * @return JsonResponse
     * 
     * @throws \Illuminate\Validation\ValidationException
     */
    public function __invoke(RegisterRequest $request): JsonResponse
    {
        $request->validated($request->all());

        $user = rescue(function () use ($request) {
            return User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'email' => $request->email,
                'password' => $request->password
            ]);
        }, false);

        if (!$user) {
            return $this->unprocessableResponse([], 'Record cannot be created.');
        }

        event(new Registered($user));

        return $this->successResponse(new UserResource($user));
    }
}
