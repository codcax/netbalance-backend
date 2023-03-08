<?php

namespace App\Http\Controllers\Account;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\HttpResponses;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Account\ChangePasswordRequest;

class ChangePasswordController extends Controller
{
    use HttpResponses;

    /**
     * Update the user account.
     *
     * @param  ChangePasswordRequest
     * 
     * @return JsonResponse
     */
    public function store(ChangePasswordRequest $request): JsonResponse
    {
        $request->validated($request->all());

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return $this->unprocessableResponse([], 'The password is incorrect.');
        }

        User::where('id', Auth::user()->id)->first()->update(['password' => $request->new_password]);

        return $this->noContentResponse();
    }
}
