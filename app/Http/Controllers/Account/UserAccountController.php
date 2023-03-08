<?php

namespace App\Http\Controllers\Account;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;

class UserAccountController extends Controller
{
    use HttpResponses;

    /**
     * Display user account.
     * 
     * @param  Request
     * 
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = User::findOrFail($request->user()->id);

        return $this->successResponse(new UserResource($user));
    }
}
